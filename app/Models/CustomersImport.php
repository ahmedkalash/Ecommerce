<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomersImport implements ToModel, WithHeadingRow
{
    use PreventDemoModeChanges;

    public function model(array $row)
    {
        return new Customer([
            'user_id' => $row['user_id'],
        ]);
    }
}
