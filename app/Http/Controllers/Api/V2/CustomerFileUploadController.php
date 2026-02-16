<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\UploadedFileCollection;
use Illuminate\Http\Request;
use Image;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CustomerFileUploadController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->user_type == 'customer') {
            $all_uploads = Media::where('model_type', 'App\Models\User')->where('model_id', auth()->user()->id);

            if ($request->search != null) {
                $all_uploads->where('name', 'like', '%'.$request->search.'%');
            }
            if ($request->type != null) {
                // Media doesn't have a 'type' column by default, usually filtered by mime
                // $all_uploads->where('type', $request->type);
            }

            switch ($request->sort) {
                case 'newest':
                    $all_uploads->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $all_uploads->orderBy('created_at', 'asc');
                    break;
                case 'smallest':
                    $all_uploads->orderBy('size', 'asc');
                    break;
                case 'largest':
                    $all_uploads->orderBy('size', 'desc');
                    break;
                default:
                    $all_uploads->orderBy('created_at', 'desc');
                    break;
            }

            $all_uploads = $all_uploads->paginate(30)->appends(request()->query());

            return new UploadedFileCollection($all_uploads);
        }

        return response()->json([
            'result' => false,
            'data' => [],
        ]);
    }

    public function upload(Request $request)
    {
        $type = [
            'jpg' => 'image',
            'jpeg' => 'image',
            'png' => 'image',
            'svg' => 'image',
            'webp' => 'image',
            'gif' => 'image',
            'mp4' => 'video',
            'mpg' => 'video',
            'mpeg' => 'video',
            'webm' => 'video',
            'ogg' => 'video',
            'avi' => 'video',
            'mov' => 'video',
            'flv' => 'video',
            'swf' => 'video',
            'mkv' => 'video',
            'wmv' => 'video',
            'wma' => 'audio',
            'aac' => 'audio',
            'wav' => 'audio',
            'mp3' => 'audio',
            'zip' => 'archive',
            'rar' => 'archive',
            '7z' => 'archive',
            'doc' => 'document',
            'txt' => 'document',
            'docx' => 'document',
            'pdf' => 'document',
            'csv' => 'document',
            'xml' => 'document',
            'ods' => 'document',
            'xlr' => 'document',
            'xls' => 'document',
            'xlsx' => 'document',
        ];
        if (auth()->user()->user_type == 'customer') {
            if ($request->hasFile('aiz_file')) {
                $file = $request->file('aiz_file');
                $extension = strtolower($file->getClientOriginalExtension());

                if (env('DEMO_MODE') == 'On' && in_array($extension, ['zip', 'rar', '7z'])) {
                    return $this->failed(translate('File has been inserted successfully'));
                }

                try {
                    if (in_array($extension,
                        ['jpg', 'jpeg', 'png', 'webp', 'gif']) && get_setting('disable_image_optimization') != 1) {
                        $img = Image::make($file->getRealPath());
                        $img->resize(1500, 1500, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })->save();
                    }

                    $media = auth()->user()->addMedia($file)->toMediaCollection('uploads');

                    return $this->success(translate('File has been inserted successfully'));
                } catch (\Exception $e) {
                    return $this->failed(translate('File upload failed'));
                }
            } else {
                return $this->failed(translate('Upload file is missing'));
            }
        }

        return $this->failed(translate("You can't upload the file"));
    }

    public function destroy($id)
    {
        try {
            $media = Media::findOrFail($id);

            if (auth()->user()->user_type == 'customer' && $media->model_id != auth()->user()->id) {
                return $this->failed(translate("You don't have permission for deleting this!"));
            }
            $media->delete();

            return $this->success(translate('File deleted successfully'));
        } catch (\Exception $e) {
            return $this->failed(translate('File deleted Failed'));
        }
    }

    public function bulk_uploaded_files_delete(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $file_id) {
                $this->destroy($file_id);
            }

            return 1;
        } else {
            return 0;
        }
    }

    public function get_preview_files(Request $request)
    {
        $ids = explode(',', $request->ids);
        $files = Media::whereIn('id', $ids)->get();
        $new_file_array = [];
        foreach ($files as $file) {
            $file['file_name'] = $file->getUrl();
            $new_file_array[] = $file;
        }

        return $new_file_array;
    }

    public function all_file()
    {
        $uploads = Media::all();
        foreach ($uploads as $upload) {
            $upload->delete();
        }

        return back();
    }

    // Download project attachment
    public function attachment_download($id)
    {
        try {
            $media = Media::find($id);

            return response()->download($media->getPath(), $media->file_name);
        } catch (\Exception $e) {
            flash(translate('File does not exist!'))->error();

            return back();
        }
    }

    public function file_info(Request $request)
    {
        $file = Media::findOrFail($request['id']);

        return (auth()->user()->user_type == 'seller')
            ? view('seller.uploads.info', compact('file'))
            : view('backend.uploaded_files.info', compact('file'));
    }
}
