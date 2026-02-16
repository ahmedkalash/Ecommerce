<?php

namespace App\Http\Controllers\Api\V2\Seller;

use App\Http\Resources\V2\UploadedFileCollection;
use Illuminate\Http\Request;
use Image;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SellerFileUploadController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->user_type == 'seller') {
            $all_uploads = Media::where('model_type', 'App\Models\User')->where('model_id', auth()->user()->id);

            if ($request->search != null) {
                $all_uploads->where('name', 'like', '%'.$request->search.'%');
            }
            // Type filtering might need adjustment as Spatie stores mime_type.
            // Legacy 'type' was 'image', 'video' etc.
            // We can try to map or ignore for now if strict compatibility not critical.
            if ($request->type != null) {
                if ($request->type == 'image') {
                    $all_uploads->where('mime_type', 'like', 'image/%');
                } else {
                    // simplistic fallback
                    $all_uploads->where('mime_type', 'like', $request->type.'/%');
                }
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
        if (auth()->user()->user_type == 'seller') {
            if ($request->hasFile('aiz_file')) {
                if (env('DEMO_MODE') == 'On') {
                    // Simple logic for demo mode check if needed
                }

                try {
                    $media = auth()->user()->addMedia($request->file('aiz_file'))->toMediaCollection('uploads');

                    return $this->success(translate('File has been inserted successfully'));
                } catch (\Exception $e) {
                    return $this->failed(translate('Upload failed: '.$e->getMessage()));
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
            // Check ownership
            if (auth()->user()->user_type == 'seller' && $media->model_id != auth()->user()->id) {
                return $this->failed(translate("You don't have permission for deleting this!"));
            }
            $media->delete(); // Spatie handles file deletion from disk automatically

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
            $file['file_name'] = $file->getUrl(); // Map to URL
            $new_file_array[] = $file;
        }

        return $new_file_array;
    }

    public function all_file()
    {
        // Careful with this - it deletes ALL files?
        // Legacy code: Upload::all(), deletes files, truncate.
        // We probably shouldn't nuke the Media table this easily.
        // But for parity:
        $uploads = Media::all();
        foreach ($uploads as $upload) {
            $upload->delete();
        }

        return back();
    }

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

    // Download project attachment
    public function file_info(Request $request)
    {
        $file = Media::findOrFail($request['id']);

        return (auth()->user()->user_type == 'seller')
            ? view('seller.uploads.info', compact('file'))
            : view('backend.uploaded_files.info', compact('file'));
    }
}
