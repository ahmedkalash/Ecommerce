<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UploadedFileCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => $data->id,
                    'file_original_name' => $data->name, // Spatie uses 'name' for original name usually
                    'file_name' => $data->file_name,
                    'url' => $data->getUrl(),
                    'file_size' => $data->size,
                    'extension' => $data->extension ?? pathinfo($data->file_name, PATHINFO_EXTENSION),
                    'type' => 'image', // mostly images, can check mime
                ];
            }),
        ];
    }

    public function with($request)
    {
        return [
            'result' => true,
            'status' => 200,
        ];
    }
}
