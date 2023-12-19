<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\Url;
use App\Models\UrlVisit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\V2\UrlCollection;
use App\Http\Requests\Api\V2\StoreUrlRequest;
use App\Models\User;

class UrlController extends Controller
{
    public function index(Request $request)
    {
        $urls = Url::with('user', 'url_visit')->where('user_id', $request->user()->id)->paginate(10);

        return  new UrlCollection($urls);
    }

    public function store(StoreUrlRequest $request)
    {
        $validatedData = $request->validated();

        $appUrl = env('APP_URL');
        $longUrl = Url::where('user_id', $request->user_id)->where('original_url', $validatedData['original_url'])->exists();

        // check if original url exists for given URL
        if ($longUrl) {
            $url = Url::select('short_code')->first();

            return response()->json([
                'status' => 'error',
                'message' => 'already exists',
                'short-url' => $appUrl . '/' . $url->short_code
            ]);
        }

        // check if user exists for given user id
        $urlExists = Url::where('user_id', $request->user_id)->exists();

        if(!$urlExists){
            return response()->json([
                'status' => 'failed',
                'message' => 'User not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $shortCode = Str::random(6);

        $validatedData['short_code'] = $shortCode;

        Url::create($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Url Shorted Successfully',
            'short-url' => $appUrl . '/' . $validatedData['short_code']
        ], Response::HTTP_CREATED);
    }

    public function redirectToOriginalUrl(Request $request, $shortUrl)
    {
        $appUrl = env('APP_URL');
        $url = Url::where('short_code', $shortUrl)->firstOrFail();

        // Check if a UrlStat record already exists for this URL
        $urlStat = UrlVisit::where('url_id', $url->id)->first();

        if (!$urlStat) {
            UrlVisit::create([
                'url_id' => $url->id
            ]);
        } else {
            $urlStat->increment('visitor_count');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'redirect_url' => $appUrl . '/' . $url->original_url
            ]);
        }

        return redirect($url->original_url);
    }
}
