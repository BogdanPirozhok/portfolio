<?php

namespace App\Http\Middleware;

use App\Exceptions\RegularException;
use App\Models\Common\File;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Spatie\Image\Image;

class HandleFileMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return Response|RedirectResponse
     * @throws RegularException
     */
    public function handle(Request $request, Closure $next)
    {
        $request['file_id'] = null;

        if ($request->file) {
            $uploadedFile = $request->file('file');

            if ($uploadedFile) {
                $type = null;
                if (str_contains($uploadedFile->getMimeType(), 'image')) {
                    $type = File::FILE_TYPE_IMAGE;
                }
                if (str_contains($uploadedFile->getMimeType(), 'video')) {
                    $type = File::FILE_TYPE_VIDEO;
                }

                if ($type) {
                    $validator = Validator::make(
                        ['image' => $uploadedFile],
                        ['image' => 'max:10000'],
                    );

                    if ($validator->fails()) {
                        throw new RegularException(
                            __('validation.image_max'),
                            500,
                        );
                    }

                    $user = auth()->user();
                    $filename = md5($user->id . time());

                    $savedFile = Storage::disk('public')->putFileAs(
                        'files',
                        $uploadedFile,
                        $filename . '.' . $uploadedFile->extension(),
                    );
                    $pathToStorage = Storage::disk('public')->path($savedFile);

                    $imageWidth = getimagesize($uploadedFile)[0];
                    $imageSave = Image::load($pathToStorage);
                    if ($imageWidth > 1280) {
                        $imageSave->width(1280);
                    }
                    $imageSave->optimize()->save();

                    $path = Storage::url('public/' . $savedFile);
                    $link = url('/') . $path;
                    $file = $user->files()->create([
                        'type' => $type,
                        'link' => $link,
                    ]);

                    if ($file) {
                        $request['file_id'] = $file->id;
                    }
                }
            } else {
                $usedFile = File::query()
                    ->where('link', $request->file)
                    ->first();

                if ($usedFile) {
                    $request['file_id'] = $usedFile->id;
                }
            }
        }

        return $next($request);
    }
}
