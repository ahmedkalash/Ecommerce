<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    //

    public function image_upload(Request $request)
    {
        $user = User::find(auth()->user()->id);
        if (! $user) {
            return response()->json([
                'result' => false,
                'message' => translate('User not found.'),
                'path' => '',
            ]);
        }

        $type = [
            'jpg' => 'image',
            'jpeg' => 'image',
            'png' => 'image',
            'svg' => 'image',
            'webp' => 'image',
            'gif' => 'image',
        ];

        try {
            $image = $request->image;
            $filename = $request->filename;

            // Extract extension if not provided in filename
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (! in_array($extension, ['jpg', 'jpeg', 'png', 'svg', 'webp', 'gif'])) {
                return response()->json([
                    'result' => false,
                    'message' => 'Only image can be uploaded',
                    'path' => '',
                ]);
            }

            $media = $user->addMediaFromBase64($image)
                ->usingFileName($filename)
                ->toMediaCollection('uploads');

            $user->avatar_original = $media->id;
            $user->save();

            return response()->json([
                'result' => true,
                'message' => translate('Image updated'),
                'path' => get_file_by_id($media->id),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage(),
                'path' => '',
            ]);
        }
    }
}
