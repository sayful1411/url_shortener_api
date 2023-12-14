<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Str;
use App\Models\Url;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreUrlRequest;
use App\Http\Resources\V1\UrlCollection;
use App\Http\Resources\V1\UrlResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UrlController extends Controller
{
    public function index(Request $request)
    {
        $urls = Url::paginate(10);

        // return  UrlResource::collection($urls);
        return  new UrlCollection($urls);
    }

    public function store(StoreUrlRequest $request)
    {
        $validatedData = $request->validated();

        $appUrl = env('APP_URL');
        $longUrl = Url::where('original_url', $validatedData['original_url'])->exists();

        // if original url exists
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

    public function redirectToOriginalUrl($shortUrl)
    {
        $user_id = Auth::user()->id;
        $appUrl = env('APP_URL');
        $url = Url::where('user_id', $user_id)
                    ->where('short_code', $shortUrl)
                    ->firstOrFail();

        // return redirect($url->original_url);
        return response()->json([
            'status' => 'success',
            'redirect_url' => $appUrl . '/' . $url->original_url
        ]);
    }
}
