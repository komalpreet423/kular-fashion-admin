<?php

namespace App\Imports;

use App\Models\Size;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;

class SizeCodeImport implements ToCollection
{
    public function collection(Collection $rows)
    {

        foreach ($rows as $index => $row) {
            // Skip the header row
            if ($index === 0) {
                continue;
            }
            $sizeValue = isset($row[0]) ? trim(Str::upper($row[0])) : null;
            $newCodeRaw = $row[1] ?? null;

            //echo 'Size: ' . $sizeValue . ' | New Code: ' . $newCodeRaw . '<br/>';

            if ($sizeValue) {
                $sizeDetails = Size::where('size', $sizeValue)->first();

                if ($sizeDetails) {
                    $sizeDetails->new_code = $newCodeRaw;
                    $sizeDetails->save();
                }
            }
        }
    }

}