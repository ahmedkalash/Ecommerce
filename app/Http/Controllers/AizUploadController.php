<?php

namespace App\Http\Controllers;

use App\Models\App as AppModel;
use enshrined\svgSanitize\Sanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Image;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AizUploadController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->user_type == 'admin' || $user->user_type == 'staff') {
            $all_uploads = Media::query();
        } else {
            $all_uploads = Media::where('model_id', $user->id);
        }

        $search = null;
        $sort_by = null;

        if ($request->search != null) {
            $search = $request->search;
            $all_uploads->where('file_name', 'like', '%'.$request->search.'%');
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

        // Note: 'seller.uploads.index' might rely on specific variables, ensuring compatibility.
        return ($user->user_type == 'seller')
            ? view('seller.uploads.index', compact('all_uploads', 'search', 'sort_by'))
            : view('backend.uploaded_files.index', compact('all_uploads', 'search', 'sort_by'));
    }

    public function create()
    {
        if (env('DEMO_MODE') == 'On') {
            flash(translate('Data can not change in demo mode.'))->info();

            return back();
        }

        return (auth()->user()->user_type == 'seller')
            ? view('seller.uploads.create')
            : view('backend.uploaded_files.create');
    }

    public function show_uploader(Request $request)
    {
        return view('uploader.aiz-uploader');
    }

    public function upload(Request $request)
    {
        $user = auth()->user();
        if ($request->hasFile('aiz_file')) {

            $file = $request->file('aiz_file');
            $extension = strtolower($file->getClientOriginalExtension());

            if (env('DEMO_MODE') == 'On' && in_array($extension, ['zip', 'rar', '7z'])) {
                return '{}';
            }

            try {
                // Handle SVG Sanitization
                if ($extension == 'svg') {
                    $sanitizer = new Sanitizer;
                    $dirtySVG = file_get_contents($file);
                    $cleanSVG = $sanitizer->sanitize($dirtySVG);
                    file_put_contents($file->getRealPath(), $cleanSVG);
                }

                // Handle Image Optimization and Watermarking
                // Note: Spatie Media Library can handle conversions, but here we do it before adding to media
                // for simplicity in matching existing complex logic.
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']) && $extension != 'svg') {
                    $img = Image::make($file->getRealPath());

                    if (get_setting('use_image_watermark') == 'on') {
                        $watermark_position = get_setting('watermark_position', 'top-left');
                        if (get_setting('image_watermark_type') == 'image') {
                            $watermarkImg = Image::make(get_file_by_id(get_setting('watermark_image')));
                            $width = $img->width();
                            $height = $img->height();
                            if ($width > $height) {
                                $watermarkImg->resize(null, $height / 2, function ($constraint) {
                                    $constraint->aspectRatio();
                                });
                            } else {
                                $watermarkImg->resize($width / 2, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                });
                            }
                            $img->insert($watermarkImg, $watermark_position, 10, 10);
                        } elseif (get_setting('image_watermark_type') == 'text') {
                            $width = $img->width();
                            $height = $img->height();
                            if ($watermark_position == 'center') {
                                $valign = 'middle';
                                $align = 'center';
                                $x = round($width / 2);
                                $y = round($height / 2);
                            } else {
                                $valign = explode('-', $watermark_position)[0];
                                $align = explode('-', $watermark_position)[1];
                                $x = ($align == 'right') ? ($width - 20) : 20;
                                $y = ($valign == 'bottom') ? ($height - 20) : 20;
                            }
                            $img->text(get_setting('watermark_text', 'Watermark Text Here'), $x, $y,
                                function ($font) use ($valign, $align) {
                                    $font->file(base_path('public/assets/fonts/robotoMedium.ttf'));
                                    $font->size(get_setting('watermark_text_size', 20));
                                    $font->color(get_setting('watermark_text_color', '#e1e1e1'));
                                    $font->align($align);
                                    $font->valign($valign);
                                });
                        }
                    }

                    if (get_setting('disable_image_optimization') != 1) {
                        $img->resize(1500, 1500, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }
                    $img->save($file->getRealPath());
                }

                if ($user->user_type == 'admin' || $user->user_type == 'staff') {
                    $systemAsset = AppModel::firstOrCreate(['id' => 1]);
                    $media = $systemAsset->addMedia($file)->toMediaCollection('uploads');
                } else {

                    $media = $user->addMedia($file)->toMediaCollection('uploads');
                }

                return $media->id;

            } catch (\Exception $e) {
                return '{}'; // Or handle error
            }
        }

        return '{}';
    }

    public function get_uploaded_files(Request $request)
    {
        $user = auth()->user();
        $uploads = Media::query();

        if ($user->user_type != 'admin' && $user->user_type != 'staff') {
            $uploads->where('model_type', $user->getMorphClass())->where('model_id', $user->id);
        }

        if ($request->search != null) {
            $uploads->where('file_name', 'like', '%'.$request->search.'%');
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

        return $uploads->paginate(60)->appends(request()->query())->through(function ($media) {
            return $this->formatMedia($media, true); // true for list view (relative)
        });
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
        foreach ($files as $media) {
            $new_file_array[] = $this->formatMedia($media, false); // false for preview (absolute)
        }

        return $new_file_array;
    }

    private function formatMedia($media, $relative = false)
    {
        $file = [];
        $file['id'] = $media->id;
        $file['file_original_name'] = $media->name;

        $url = $media->getUrl();

        if ($relative) {
            // JS prepends fileBaseUrl, so we need the relative part.
            // Spatie returns full URL. We strip the base URL part.
            $baseUrl = getFileBaseURL(); // This helper returns URL with trailing slash handling?
            // getFileBaseURL returns e.g. http://site.test or http://site.test/
            // Let's be safe.

            if (strpos($url, $baseUrl) === 0) {
                $file['file_name'] = substr($url, strlen($baseUrl));
                // Ensure it doesn't start with / if base ends with /
                if (Str::endsWith($baseUrl, '/') && Str::startsWith($file['file_name'], '/')) {
                    $file['file_name'] = substr($file['file_name'], 1);
                } elseif (! Str::endsWith($baseUrl, '/') && ! Str::startsWith($file['file_name'], '/')) {
                    $file['file_name'] = '/'.$file['file_name'];
                }
            } else {
                // Determine if remote or mismatch
                $file['file_name'] = $url;
            }
            // For local storage, if getFileBaseURL is just base URL, we might need adjustments.
            // If getFileBaseURL() returns `http://site.test`, and url is `http://site.test/storage/1/a.jpg`
            // Res: `/storage/1/a.jpg`
        } else {
            $file['file_name'] = $url;
        }

        $file['file_size'] = $media->size;
        $file['extension'] = $media->extension ?? pathinfo($media->file_name, PATHINFO_EXTENSION);

        $mime = $media->mime_type;
        if (str_contains($mime, 'image')) {
            $file['type'] = 'image';
        } elseif (str_contains($mime, 'video')) {
            $file['type'] = 'video';
        } else {
            $file['type'] = 'document';
        }

        return $file;
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
