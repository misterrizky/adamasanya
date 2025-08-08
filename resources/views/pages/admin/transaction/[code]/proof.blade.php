<?php
use App\Models\Transaction\Rent;
use App\Models\Transaction\Sale;
use function Laravel\Folio\name;
use Illuminate\Support\Facades\Storage;
use function Livewire\Volt\{mount, rules, state, usesFileUploads};
usesFileUploads();

name('admin.transaction.proof');

state(['code', 'transaction', 'type', 'bukti_pengambilan']);
rules(fn () => [
    'bukti_pengambilan' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
]);

mount(function ($code) {
    // Fetch transaction (Rent or Sale) with related data
    $this->transaction = Rent::with(['user', 'rentItems.productBranch.product'])
        ->where('code', $code)
        ->first();

    if ($this->transaction) {
        $this->type = 'rent';
    } else {
        $this->transaction = Sale::with(['user', 'saleItems.productBranch.product'])
            ->where('code', $code)
            ->firstOrFail();
        $this->type = 'sale';
    }

    // Fetch branch details
    $this->transaction->branch = $this->transaction->branch;
});
$save = function () {
    // $this->validate();
    if($this->bukti_pengambilan){
        $filename = $this->bukti_pengambilan->getClientOriginalName(); // Nama file
        $extension = $this->bukti_pengambilan->extension(); // Ekstensi file
        $size = $this->bukti_pengambilan->getSize(); // Ukuran file dalam bytes
        
        // Simpan file secara permanen
        // $path = $this->bukti_pengambilan->store('expense', 'public');
        
        // Atau simpan dengan nama custom
        $customPath = $this->bukti_pengambilan->storeAs(
            'public/proof_of_collection', 
            $this->transaction->code . '.' . $this->bukti_pengambilan->extension()
        );
        $dbPath = 'proof_of_collection/' . $this->transaction->code . '.' . $this->bukti_pengambilan->extension();
        // Hapus file temporary spesifik yang baru diupload
        if (File::exists($this->bukti_pengambilan->getRealPath())) {
            File::delete($this->bukti_pengambilan->getRealPath());
        }
    }else{
        $dbPath = null;
    }
    // dd($this->bukti_pengambilan);
    if($this->transaction){
        if($this->bukti_pengambilan && $this->transaction->proof_of_collection){
            Storage::delete($this->transaction->proof_of_collection);
        }
        $this->transaction->castAndUpdate([
            'proof_of_collection' => $dbPath,
            'status' => 'on_rent',
        ]);
    }
    $this->cleanupLivewireTempFiles();
    $this->dispatch('toast-success', message: "Bukti pengambilan berhasil disimpan");
    // return $this->redirect(route('admin.transaction'), navigate: true);
};
$cleanupLivewireTempFiles = function() {
    $tempDirectory = storage_path('app/livewire-tmp');
    
    if (File::isDirectory($tempDirectory)) {
        // Hapus semua file dalam direktori temporary
        File::cleanDirectory($tempDirectory);
    }
};
?>
<x-app>
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <x-form hasFiles action="save">
            <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                <div class="card card-flush py-4">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Unggah Bukti Pengambilan</h2>
                        </div>
                    </div>
                    <div class="card-body text-center pt-0">
                        <style>.image-input-placeholder { background-image: url('assets/media/svg/files/blank-image.svg'); } [data-bs-theme="dark"] .image-input-placeholder { background-image: url('assets/media/svg/files/blank-image-dark.svg'); }</style>
                        <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3" data-kt-image-input="true">
                            <div class="image-input-wrapper w-150px h-150px"></div>
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Ganti Bukti Pengambilan">
                                <i class="ki-outline ki-pencil fs-7"></i>
                                <input type="file" wire:model="bukti_pengambilan" name="avatar" accept=".png, .jpg, .jpeg" />
                                <input type="hidden" name="avatar_remove" />
                            </label>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Batalkan">
                                <i class="ki-outline ki-cross fs-2"></i>
                            </span>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Hapus Bukti Pengambilan">
                                <i class="ki-outline ki-cross fs-2"></i>
                            </span>
                        </div>
                        <div class="text-muted fs-7">Atur gambar pengambilan. Hanya file gambar *.png, *.jpg, dan *.jpeg yang diterima.</div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.transaction') }}" wire:navigate class="btn btn-light me-5">Kembali</a>
                    <x-button class="btn btn-primary" submit="true" indicator="Harap tunggu..." label="Simpan" />
                </div>
            </div>
        </x-form>
    </div>
    @endvolt
</x-app>