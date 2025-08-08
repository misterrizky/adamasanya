<?php

namespace App\Exports\Master;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BlockExport implements FromCollection, WithMapping, ShouldAutoSize, WithHeadings
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function map($data): array
    {
        return [
            $data->name,
            number_format($data->house->count())
        ];
    }    public function collection()
    {
        return $this->data;
    }
    public function headings(): array
    {
        return [
            'Nama Blok',
            'Jumlah Rumah'
        ];
    }
    // public function export() 
    // {
    //     return Excel::download(new TransactionExport($data), 'income-'.date('Ymd').'.xlsx');
    // }
}
