<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\UploadedFileCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FileController extends Controller
{
    public function index()
    {
        $all_uploads = (auth()->user()->user_type == 'seller')
            ? Media::where('model_type', 'App\Models\User')->where('model_id', auth()->user()->id)
            : Media::query();

        $all_uploads = $all_uploads->paginate(20)->appends(request()->query());

        return new UploadedFileCollection($all_uploads);

    }

    // any  base 64 image through uploader
    // any  base 64 image through uploader
    public function imageUpload(Request $request)
    {
        try {
            $image = $request->image;
            $realImage = base64_decode($image);
            $dir = public_path('uploads/all');
            $filename = rand(10000000000,
                9999999999).date('YmdHis').'.png'; // Default to png or extract from base64 header if possible

            // Temporary file creation to leverage Spatie
            $tempPath = sys_get_temp_dir().'/'.$filename;
            file_put_contents($tempPath, $realImage);

            $media = auth()->user()->addMedia($tempPath)
                ->usingName($request->filename ?? $filename)
                ->toMediaCollection('uploads');

            return response()->json([
                'result' => true,
                'message' => translate('Image updated'),
                'path' => $media->getUrl(),
                'upload_id' => $media->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage(),
                'path' => '',
                'upload_id' => 0,
            ]);
        }
    }
}
