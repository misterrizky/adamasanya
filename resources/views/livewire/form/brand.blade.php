<?php
use App\Models\Master\Brand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use function Livewire\Volt\{mount, rules, state, usesFileUploads};
usesFileUploads();
state(['slug'])->locked();
state(['brand' => fn () => Brand::where('slug', request()->slug)->first() ?? ''])->locked();
state([
    'nama' => '',
    'thumbnail' => '',
    'status' => ''
]);
rules(fn () => [
    'nama' => 'required|unique:brands,name',
    'thumbnail' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
    'status' => 'required'
]);
mount(function () {
    $this->nama = $this->brand->name ?? '';
    $this->status = $this->brand->st ?? '';
});
$save = function () {
    $this->validate();
    if($this->thumbnail){
        $filename = $this->thumbnail->getClientOriginalName(); // Nama file
        $extension = $this->thumbnail->extension(); // Ekstensi file
        $size = $this->thumbnail->getSize(); // Ukuran file dalam bytes
        $slug = Str::slug($this->name);
        
        // Simpan file secara permanen
        // $path = $this->thumbnail->store('expense', 'public');
        
        // Atau simpan dengan nama custom
        $customPath = $this->thumbnail->storeAs(
            'public/brand', 
            $slug . '.' . $this->thumbnail->extension()
        );
        $dbPath = 'brand/' . $slug . '.' . $this->thumbnail->extension();
        // Hapus file temporary spesifik yang baru diupload
        if (File::exists($this->thumbnail->getRealPath())) {
            File::delete($this->thumbnail->getRealPath());
        }
    }else{
        $dbPath = null;
    }
    if($this->brand){
        if($this->file && $this->brand->file){
            Storage::delete($this->brand->file);
        }
        $this->brand->castAndUpdate([
            'name' => $this->nama,
            'slug' => Str::slug($this->nama),
            'thumbnail' => $dbPath,
            'st' => $this->status,
        ]);
    }else{
        Brand::castAndCreate([
            'name' => $this->nama,
            'slug' => Str::slug($this->nama),
            'thumbnail' => $dbPath,
            'st' => $this->status
        ]);
    }
    $this->cleanupLivewireTempFiles();
    $this->dispatch('toast-success', message: "Data berhasil disimpan");
    return $this->redirect(route('admin.brand'), navigate: true);
};
$cleanupLivewireTempFiles = function() {
    $tempDirectory = storage_path('app/livewire-tmp');
    
    if (File::isDirectory($tempDirectory)) {
        // Hapus semua file dalam direktori temporary
        File::cleanDirectory($tempDirectory);
    }
};
?>
<x-form action="save" hasFiles class="form d-flex flex-column flex-lg-row">
    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
        <div class="card card-flush py-4">
            <div class="card-header">
                <div class="card-title">
                    <h2>Thumbnail</h2>
                </div>
            </div>
            <div class="card-body text-center pt-0">
                <style>.image-input-placeholder { background-image: url('assets/media/svg/files/blank-image.svg'); } [data-bs-theme="dark"] .image-input-placeholder { background-image: url('assets/media/svg/files/blank-image-dark.svg'); }</style>
                <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3" data-kt-image-input="true">
                    <div class="image-input-wrapper w-150px h-150px"></div>
                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                        <i class="ki-outline ki-pencil fs-7"></i>
                        <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                        <input type="hidden" name="avatar_remove" />
                    </label>
                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                        <i class="ki-outline ki-cross fs-2"></i>
                    </span>
                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                        <i class="ki-outline ki-cross fs-2"></i>
                    </span>
                </div>
                <div class="text-muted fs-7">Atur gambar mini merek. Hanya file gambar *.png, *.jpg, dan *.jpeg yang diterima.</div>
            </div>
        </div>
        <div class="card card-flush py-4">
            <div class="card-header">
                <div class="card-title">
                    <h2>Status</h2>
                </div>
                <div class="card-toolbar">
                    <div class="rounded-circle bg-success w-15px h-15px" id="kt_ecommerce_add_brand_status"></div>
                </div>
            </div>
            <div class="card-body pt-0">
                @php
                $data = [
                    '' => 'Pilih Status merek',
                    'a' => 'Aktif',
                    'i' => 'Tidak Aktif'
                ];
                @endphp
                <x-form-select 
                    name="status" 
                    class="form-select form-select-solid fw-bold"
                    :options="$data"
                />
                <div class="text-muted fs-7">Tetapkan status merek</div>
            </div>
        </div>
    </div>
    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
        <div class="card card-flush py-4">
            <div class="card-header">
                <div class="card-title">
                    <h2>Info Umum</h2>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="mb-10 fv-row">
                    <x-form-input-group>
                        <x-form-group name="nama" label="Nama Merek" required>
                            <x-form-input type="text" name="nama" class="bg-transparent" placeholder="Nama Merek">
                                @slot('help')
                                <small class="form-text text-muted">
                                    Nama merek wajib diisi dan sebaiknya unik.
                                </small>
                                @endslot
                            </x-form-input>
                        </x-form-group>
                    </x-form-input-group>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.brand') }}" wire:navigate class="btn btn-light me-5">Batalkan</a>
            <x-button class="btn btn-primary" submit="true" indicator="Harap tunggu..." label="Simpan" />
        </div>
    </div>
</x-form>