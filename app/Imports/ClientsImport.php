<?php

namespace App\Imports;

use App\Models\Client;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;

class ClientsImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {

            // Skip header row
            if ($index === 0) {
                continue;
            }

            Client::create([
                'payment_date'  => $this->excelDate($row[0]),
                'client_name'   => $row[1],
                'mobile'        => $this->nullIfDash($row[2]),
                'email'         => $this->nullIfDash($row[3]),
                'father_name'   => $row[4],
                'pan_card'      => $this->nullIfDash($row[5]),
                'aadhaar_card'  => $this->nullIfDash($row[6]),
                'dob'           => $this->excelDate($row[7]),
                'city'          => $row[8],
                'state'         => $row[9],
                'gross_amount'  => $row[10],
                'net_amount'    => $row[11],
                'amount_type'   => $row[12],
                // segment may come in column 13 (preferred) or column 18 (legacy/service)
                'segment'       => $this->nullIfDash($row[13] ?? null) ?? $this->nullIfDash($row[18] ?? null),
                'assigned_to'   => $row[14],
                'plan'          => $row[15],
                'service_start' => $this->excelDate($row[16]),
                'service_end'   => $this->excelDate($row[17]),
                // 'service' column mapped to segment; we don't store service separately
                'bank'          => $this->nullIfDash($row[19] ?? null),
                'remark'        => $this->nullIfDash($row[20] ?? null),
            ]);
        }
    }

    /**
     * Convert Excel date or string date to Y-m-d
     */
    private function excelDate($value)
    {
        if (empty($value)) {
            return null;
        }

        // If numeric (45870 type)
        if (is_numeric($value)) {
            return Carbon::instance(
                ExcelDate::excelToDateTimeObject($value)
            )->format('Y-m-d');
        }

        // If already string like 1-Aug-25
        return Carbon::parse($value)->format('Y-m-d');
    }

    /**
     * Convert '-' to NULL
     */
    private function nullIfDash($value)
    {
        return ($value === '-' || empty($value)) ? null : $value;
    }
}
