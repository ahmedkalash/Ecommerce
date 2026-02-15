<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class MediaService
{
    /**
     * Sync media from request data to the model.
     *
     * @param  array  $collections  Map of data_key => collection_name
     */
    public function syncMedia(Model $model, array $data, array $collections = []): void
    {
        foreach ($collections as $key => $collectionName) {
            if (empty($data[$key])) {
                continue;
            }

            try {
                // If data is UploadedFile (direct upload)
                if ($data[$key] instanceof UploadedFile) {
                    $model->addMedia($data[$key])->toMediaCollection($collectionName);

                    continue;
                }

                // If data is string (comma separated IDs or single ID)
                // This handles the existing "uploads" system integration where we map ID to the new model
                $mediaIdentifiers = is_array($data[$key]) ? $data[$key] : explode(',', $data[$key]);

                foreach ($mediaIdentifiers as $identifier) {
                    if (is_numeric($identifier)) {
                        // Reassign logic (moving from temp/upload holding to this model)
                        // This assumes there's a way to find the media by ID.
                        // In existing ProductService, it was using Media::find($id)->update(...)
                        $this->reassignMedia((int) $identifier, $model, $collectionName);
                    }
                }
            } catch (\Exception $e) {
                Log::error("Media sync failed for {$key} on ".get_class($model)." #{$model->id}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    protected function reassignMedia(int $mediaId, Model $model, string $collection): void
    {
        $media = Media::find($mediaId);
        if ($media) {
            $media->update([
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'collection_name' => $collection,
            ]);
        }
    }
}
