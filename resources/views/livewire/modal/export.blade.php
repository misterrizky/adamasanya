<?php
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use function Livewire\Volt\{rules, state};
state(['class', 'model', 'nama'])->locked();
state(['format' => '']);
rules(fn () => [
    'format' => ['required']
]);
$export = function(){
    $exportClass = "\\App\\Exports\\" .$this->class;
    $modelClass = "\\App\\Models\\" .$this->model;
    $data = $modelClass::all();
    $this->validate();
    if($this->format == "excel"){
        $this->dispatch('toast-success', message: "Data berhasil di ekspor!");
        return Excel::download(new $exportClass($data), $this->nama . '-' . date('Ymd').'.xlsx');
    }
    elseif($this->format == "pdf"){
        $item = [
            'title' => 'Contoh PDF',
            'content' => mb_convert_encoding($data, 'UTF-8', 'UTF-8'),
        ];
        $this->dispatch('toast-success', message: "Data berhasil di ekspor!");
        $pdf = PDF::loadView('pdf.block', $item);
        // $pdf = PDF::loadView('pdf.' . $this->nama, $item);
        // $pdf = Pdf::loadView('pdf.' . $this->nama, [
        //     'data' => $data,
        //     'title' => 'Data ' . $this->nama,
        //     'date' => date('m/d/Y'),
        // ])->setOptions([
        //     'defaultFont' => 'sans-serif',
        //     'isHtml5ParserEnabled' => true,
        //     'isRemoteEnabled' => true
        // ]);
        
        return $pdf->download($this->nama . '.pdf');
    }
}
?>
<div class="modal fade" id="ModalExport" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header">
                <!--begin::Modal title-->
                <h2 class="fw-bold">Ekspor Data</h2>
                <!--end::Modal title-->
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-outline ki-cross fs-1"></i>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <!--begin::Form-->
                <x-form action="export">
                    <!--begin::Input group-->
                    <div class="fv-row mb-10">
                        <!--begin::Label-->
                        <label class="required fs-6 fw-semibold form-label mb-2">Pilih Format Ekspor:</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        @php
                        $formats = [
                            '' => 'Pilih Format',
                            'excel' => 'Excel',
                            'pdf' => 'PDF'
                        ];
                        @endphp
                        <x-form-select 
                            name="format" 
                            class="form-select form-select-solid fw-bold"
                            :options="$formats"
                        />
                        <!--end::Input-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Actions-->
                    <div class="text-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batalkan</button>
                        <x-button class="btn btn-primary" submit="true" indicator="Harap tunggu..." label="Kirim" />
                    </div>
                    <!--end::Actions-->
                </x-form>
                <!--end::Form-->
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>