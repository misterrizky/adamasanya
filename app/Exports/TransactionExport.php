<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class TransactionExport implements FromCollection, WithMapping, ShouldAutoSize, WithHeadings
{
    protected $data;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function map($data): array
    {
        return [
            $data->category->name,
            $data->code,
            $data->date->format('Y-m-d'),
            $data->referrence_number,
            'Rp.'.number_format($data->total),
            Str::title($data->st),
            $data->notes
            // Date::dateTimeToExcel($data->created_at),
            // $data->total
        ];
    }    public function collection()
    {
        return $this->data;
    }
    public function headings(): array
    {
        return [
            'Kategori Transaksi',
            'Kode',
            'Tanggal',
            'Nomor Referensi',
            'Total',
            'Status',
            'Catatan',
        ];
    }
    // public function export() 
    // {
    //     return Excel::download(new TransactionExport($data), 'income-'.date('Ymd').'.xlsx');
    // }
}
