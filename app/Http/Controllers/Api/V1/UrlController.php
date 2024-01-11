<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Url;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UrlCollection;
use App\Http\Requests\Api\V1\StoreUrlRequest;

class UrlController extends Controller
{
    public function index(Request $request)
    {
        $urls = Url::with('user')->where('user_id', $request->user()->id)->paginate(10);

        return  new UrlCollection($urls);
    }

    public function store(StoreUrlRequest $request)
    {
        $validatedData = $request->validated();

        $appUrl = config('app.name');
        $longUrl = Url::where('original_url', $validatedData['original_url'])->exists();

        // check if original URL exists for given URL
        if ($longUrl) {
            $url = Url::select('short_code')->first();

            return response()->json([
                'status' => 'error',
                'message' => 'already exists',
                'short-url' => $appUrl . '/' . $url->short_code
            ]);
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
        $appUrl = config('app.name');
        $url = Url::where('short_code', $shortUrl)->firstOrFail();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'redirect_url' => $appUrl . '/' . $url->original_url
            ]);
        }

        return redirect($url->original_url);
    }
}
