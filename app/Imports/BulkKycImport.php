<?php

namespace App\Imports;

use App\Models\BulkKycBatch;
use App\Models\BulkKycRecord;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;

class BulkKycImport implements ToCollection, WithHeadingRow
{
    private BulkKycBatch $batch;

    public function __construct(BulkKycBatch $batch)
    {
        $this->batch = $batch;
    }

    /**
     * Process the collection of rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Skip empty rows
            if (empty($row['pan_number']) && empty($row['pan'])) {
                continue;
            }

            // Get PAN - support multiple column names
            $pan = $this->getValue($row, ['pan_number', 'pan', 'pan_no', 'pancard']);
            
            // Get DOB - support multiple column names
            $dob = $this->getDobValue($row);

            if ($pan && $dob) {
                BulkKycRecord::create([
                    'batch_id' => $this->batch->id,
                    'pan' => strtoupper(trim($pan)),
                    'dob' => $dob,
                    'status' => BulkKycRecord::STATUS_PENDING,
                ]);
            }
        }

        // Update batch total records count
        $this->batch->update([
            'total_records' => $this->batch->records()->count(),
        ]);
    }

    /**
     * Get value from row with multiple possible column names
     */
    private function getValue(Collection $row, array $possibleKeys): ?string
    {
        foreach ($possibleKeys as $key) {
            $normalizedKey = strtolower(str_replace([' ', '_'], '', $key));
            foreach ($row as $rowKey => $value) {
                $normalizedRowKey = strtolower(str_replace([' ', '_'], '', $rowKey));
                if ($normalizedRowKey === $normalizedKey && !empty($value)) {
                    return (string) $value;
                }
            }
        }
        return null;
    }

    /**
     * Get DOB value and format it
     * Accepts only these formats:
     * - dmyyyy (e.g., 891997)
     * - ddmmyyyy (e.g., 08091997)
     * - dd-mm-yyyy (e.g., 08-09-1997)
     * - d-m-yyyy (e.g., 8-9-1997)
     */
    private function getDobValue(Collection $row): ?string
    {
        $dob = $this->getValue($row, ['date_of_birth', 'dob', 'birth_date', 'birthdate']);
        
        if (!$dob) {
            return null;
        }

        // If numeric (Excel date)
        if (is_numeric($dob)) {
            try {
                return Carbon::instance(
                    ExcelDate::excelToDateTimeObject($dob)
                )->format('d-m-Y');
            } catch (\Exception $e) {
                return null;
            }
        }

        // Clean the DOB string
        $dob = trim($dob);

        // Try to parse only the allowed formats
        $formats = [
            'd-m-Y',      // 08-09-1997 (dd-mm-yyyy)
            'j-n-Y',      // 8-9-1997 (d-m-yyyy)
            'dmY',        // 08091997 (ddmmyyyy)
            'jnY',        // 891997 (dmyyyy)
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $dob);
                if ($date) {
                    // Validate it's a reasonable date (not in future, not too old)
                    if ($date->year > 1900 && $date->lte(now())) {
                        return $date->format('d-m-Y');
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'pan_number' => ['nullable', 'string', 'size:10', 'regex:/^[A-Za-z]{5}[0-9]{4}[A-Za-z]$/'],
            'pan' => ['nullable', 'string', 'size:10', 'regex:/^[A-Za-z]{5}[0-9]{4}[A-Za-z]$/'],
            'dob' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'string'],
        ];
    }
}
