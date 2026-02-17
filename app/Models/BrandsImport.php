<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

// class ProductsImport implements ToModel, WithHeadingRow, WithValidation
class BrandsImport implements ToCollection, ToModel, WithHeadingRow
{
    use PreventDemoModeChanges;

    private $rows = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            Brand::create([
                'name' => $row['name'],
                'logo' => $this->downloadLogo($row['logo']),
                'slug' => preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $row['name'])).'-'.Str::random(5),
                'meta_title' => $row['meta_title'],
                'meta_description' => $row['meta_description'],
            ]);
        }

        flash(translate('Brands imported successfully'))->success();

    }

    public function model(array $row)
    {
        $this->rows++;
    }

    public function downloadLogo($url)
    {
        try {
            $upload = new Upload;
            $upload->external_link = $url;
            $upload->type = 'image';
            $upload->save();

            return $upload->id;
        } catch (\Exception $e) {
        }

        return null;
    }
}
