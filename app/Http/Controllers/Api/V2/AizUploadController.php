<?php

namespace App\Http\Controllers\Api\V2;

use Auth;
use Illuminate\Http\Request;
use Image;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AizUploadController extends Controller
{
    public function index(Request $request)
    {

        $all_uploads = (auth()->user()->user_type == 'seller')
            ? Media::where('model_type', 'App\Models\User')->where('model_id', auth()->user()->id)
            : Media::query();
        $search = null;
        $sort_by = null;

        if ($request->search != null) {
            $search = $request->search;
            $all_uploads->where('name', 'like', '%'.$request->search.'%');
        }

        $sort_by = $request->sort;
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

        $all_uploads = $all_uploads->paginate(60)->appends(request()->query());

        return (auth()->user()->user_type == 'seller')
            ? view('seller.uploads.index', compact('all_uploads', 'search', 'sort_by'))
            : view('backend.uploaded_files.index', compact('all_uploads', 'search', 'sort_by'));
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('aiz_file')) {
            $file = $request->file('aiz_file');
            $extension = strtolower($file->getClientOriginalExtension());

            if (env('DEMO_MODE') == 'On' && in_array($extension, ['zip', 'rar', '7z'])) {
                return $this->failed(translate('File has been inserted successfully'));
            }

            try {
                // Handling optimization before addMedia to match previous behavior if needed,
                // but direct addMedia is cleaner.
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
        }
    }

    public function get_uploaded_files(Request $request)
    {
        $uploads = Media::where('model_type', 'App\Models\User')->where('model_id', Auth::user()->id);
        if ($request->search != null) {
            $uploads->where('name', 'like', '%'.$request->search.'%');
        }
        if ($request->sort != null) {
            switch ($request->sort) {
                case 'newest':
                    $uploads->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $uploads->orderBy('created_at', 'asc');
                    break;
                case 'smallest':
                    $uploads->orderBy('size', 'asc');
                    break;
                case 'largest':
                    $uploads->orderBy('size', 'desc');
                    break;
                default:
                    $uploads->orderBy('created_at', 'desc');
                    break;
            }
        }

        return $uploads->paginate(60)->appends(request()->query());
    }

    public function destroy($id)
    {
        try {
            $media = Media::findOrFail($id);
            if (auth()->user()->user_type == 'seller' && $media->model_id != auth()->user()->id) {
                flash(translate("You don't have permission for deleting this!"))->error();

                return back();
            }
            $media->delete();
            flash(translate('File deleted successfully'))->success();
        } catch (\Exception $e) {
            flash(translate('File deleted successfully'))->success();
        }

        return back();
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
