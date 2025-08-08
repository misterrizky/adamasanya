<?php
use App\Models\Promo;
use App\Models\Product;
use App\Models\Master\Branch;
use App\Models\Master\Category;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use function Livewire\Volt\{computed, mount, rules, state, usesFileUploads};

usesFileUploads();
state(['slug'])->locked();
state(['promo' => fn () => Promo::where('slug', request()->slug)->first() ?? ''])->locked();
state([
    'name' => '',
    'code' => '',
    'description' => '',
    'thumbnail' => '',
    'type' => '',
    'value' => '',
    'buy_quantity' => '',
    'get_quantity' => '',
    'free_product_id' => '',
    'min_order_amount' => '',
    'max_uses' => '',
    'max_uses_per_user' => '',
    'start_date' => '',
    'end_date' => '',
    'is_active' => true,
    'scope' => 'all',
    'day_restriction' => 'all',
    'applicable_for' => 'all',
    'selected_branches' => [],
    'selected_categories' => [],
    'selected_products' => [],
]);

$branches = computed(fn () => Branch::pluck('name', 'id')->toArray());
$categories = computed(fn () => Category::pluck('name', 'id')->toArray());
$products = computed(fn () => Product::pluck('name', 'id')->toArray());

rules(fn () => [
    'name' => ['required', 'string', 'max:255', $this->promo ? 'unique:promos,name,'.$this->promo->id : 'unique:promos,name'],
    'code' => ['required', 'string', 'max:50', $this->promo ? 'unique:promos,code,'.$this->promo->id : 'unique:promos,code'],
    'description' => ['nullable', 'string'],
    'thumbnail' => [$this->promo ? 'nullable' : 'required', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
    'type' => ['required', 'in:percentage,fixed_amount,buy_x_get_y,free_shipping'],
    'value' => ['nullable', 'numeric', 'min:0', 'required_if:type,percentage,fixed_amount'],
    'buy_quantity' => ['nullable', 'integer', 'min:1', 'required_if:type,buy_x_get_y'],
    'get_quantity' => ['nullable', 'integer', 'min:1', 'required_if:type,buy_x_get_y'],
    'free_product_id' => ['nullable', 'exists:products,id', 'required_if:type,buy_x_get_y'],
    'min_order_amount' => ['nullable', 'numeric', 'min:0'],
    'max_uses' => ['nullable', 'integer', 'min:1'],
    'max_uses_per_user' => ['nullable', 'integer', 'min:1'],
    'start_date' => ['required', 'date', 'after_or_equal:today'],
    'end_date' => ['required', 'date', 'after:start_date'],
    'is_active' => ['required', 'boolean'],
    'scope' => ['required', 'in:all,products,categories,branches'],
    'day_restriction' => ['required', 'in:all,weekday,weekend'],
    'applicable_for' => ['required', 'in:all,rent,sale'],
    'selected_branches' => ['required_if:scope,branches', 'array'],
    'selected_categories' => ['required_if:scope,categories', 'array'],
    'selected_products' => ['required_if:scope,products', 'array'],
]);

mount(function () {
    if ($this->promo) {
        $this->name = $this->promo->name;
        $this->code = $this->promo->code;
        $this->description = $this->promo->description;
        $this->type = $this->promo->type;
        $this->value = $this->promo->value;
        $this->buy_quantity = $this->promo->buy_quantity;
        $this->get_quantity = $this->promo->get_quantity;
        $this->free_product_id = $this->promo->free_product_id;
        $this->min_order_amount = $this->promo->min_order_amount;
        $this->max_uses = $this->promo->max_uses;
        $this->max_uses_per_user = $this->promo->max_uses_per_user;
        $this->start_date = $this->promo->start_date?->format('Y-m-d\TH:i');
        $this->end_date = $this->promo->end_date?->format('Y-m-d\TH:i');
        $this->is_active = $this->promo->is_active;
        $this->scope = $this->promo->scope;
        $this->day_restriction = $this->promo->day_restriction;
        $this->applicable_for = $this->promo->applicable_for;
        $this->selected_branches = $this->promo->branches->pluck('id')->toArray();
        $this->selected_categories = $this->promo->categories->pluck('id')->toArray();
        $this->selected_products = $this->promo->products->pluck('id')->toArray();
    }
});

$save = function () {
    $this->validate();

    $dbPath = null;
    if ($this->thumbnail) {
        $slug = \Str::slug($this->name);
        $extension = $this->thumbnail->extension();
        $customPath = $this->thumbnail->storeAs('public/promo', $slug . '.' . $extension);
        $dbPath = 'promo/' . $slug . '.' . $extension;

        if (File::exists($this->thumbnail->getRealPath())) {
            File::delete($this->thumbnail->getRealPath());
        }
    }

    if ($this->promo) {
        if ($this->thumbnail && $this->promo->thumbnail) {
            Storage::delete('public/' . $this->promo->thumbnail);
        }
        $this->promo->update([
            'name' => $this->name,
            'code' => $this->code,
            'slug' => \Str::slug($this->name),
            'description' => $this->description,
            'thumbnail' => $dbPath ?? $this->promo->thumbnail,
            'type' => $this->type,
            'value' => $this->type === 'free_shipping' ? null : $this->value,
            'buy_quantity' => $this->type === 'buy_x_get_y' ? $this->buy_quantity : null,
            'get_quantity' => $this->type === 'buy_x_get_y' ? $this->get_quantity : null,
            'free_product_id' => $this->type === 'buy_x_get_y' ? $this->free_product_id : null,
            'min_order_amount' => $this->min_order_amount,
            'max_uses' => $this->max_uses,
            'max_uses_per_user' => $this->max_uses_per_user,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
            'scope' => $this->scope,
            'day_restriction' => $this->day_restriction,
            'applicable_for' => $this->applicable_for,
        ]);
        $this->promo->branches()->sync($this->selected_branches);
        $this->promo->categories()->sync($this->selected_categories);
        $this->promo->products()->sync($this->selected_products);
    } else {
        $promo = Promo::create([
            'name' => $this->name,
            'code' => $this->code,
            'slug' => \Str::slug($this->name),
            'description' => $this->description,
            'thumbnail' => $dbPath,
            'type' => $this->type,
            'value' => $this->type === 'free_shipping' ? null : $this->value,
            'buy_quantity' => $this->type === 'buy_x_get_y' ? $this->buy_quantity : null,
            'get_quantity' => $this->type === 'buy_x_get_y' ? $this->get_quantity : null,
            'free_product_id' => $this->type === 'buy_x_get_y' ? $this->free_product_id : null,
            'min_order_amount' => $this->min_order_amount,
            'max_uses' => $this->max_uses,
            'max_uses_per_user' => $this->max_uses_per_user,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
            'scope' => $this->scope,
            'day_restriction' => $this->day_restriction,
            'applicable_for' => $this->applicable_for,
        ]);
        $promo->branches()->sync($this->selected_branches);
        $promo->categories()->sync($this->selected_categories);
        $promo->products()->sync($this->selected_products);
    }

    $this->cleanupLivewireTempFiles();
    $this->dispatch('toast-success', message: "Promo berhasil disimpan");
    return $this->redirect(route('admin.promo'), navigate: true);
};

$cleanupLivewireTempFiles = function() {
    $tempDirectory = storage_path('app/livewire-tmp');
    if (File::isDirectory($tempDirectory)) {
        File::cleanDirectory($tempDirectory);
    }
};
?>
<x-form action="save" hasFiles class="form d-flex flex-column flex-lg-row">
    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
        <!-- Thumbnail Section -->
        <div class="card card-flush py-4">
            <div class="card-header">
                <div class="card-title">
                    <h2>Thumbnail</h2>
                </div>
            </div>
            <div class="card-body text-center pt-0">
                <style>
                    .image-input-placeholder { 
                        background-image: url('assets/media/svg/files/blank-image.svg'); 
                    }
                    [data-bs-theme="dark"] .image-input-placeholder { 
                        background-image: url('assets/media/svg/files/blank-image-dark.svg'); 
                    }
                </style>
                <div class="image-input image-input-outline image-input-placeholder mb-3" data-kt-image-input="true">
                    <div class="image-input-wrapper w-150px h-150px" 
                         style="{{ $this->promo && $this->promo->thumbnail ? 'background-image: url(' . asset('storage/' . $this->promo->thumbnail) . ');' : '' }}">
                    </div>
                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" 
                           data-kt-image-input-action="change" 
                           data-bs-toggle="tooltip" 
                           title="Change thumbnail">
                        <i class="ki-outline ki-pencil fs-7"></i>
                        <input type="file" wire:model="thumbnail" accept=".png,.jpg,.jpeg" />
                        <input type="hidden" name="thumbnail_remove" />
                    </label>
                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" 
                          data-kt-image-input-action="cancel" 
                          data-bs-toggle="tooltip" 
                          title="Cancel thumbnail">
                        <i class="ki-outline ki-cross fs-2"></i>
                    </span>
                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" 
                          data-kt-image-input-action="remove" 
                          data-bs-toggle="tooltip" 
                          title="Remove thumbnail">
                        <i class="ki-outline ki-cross fs-2"></i>
                    </span>
                </div>
                <div class="text-muted fs-7">Atur gambar mini promo. Hanya file gambar *.png, *.jpg, dan *.jpeg yang diterima.</div>
            </div>
        </div>

        <!-- Status Section -->
        <div class="card card-flush py-4">
            <div class="card-header">
                <div class="card-title">
                    <h2>Status</h2>
                </div>
                <div class="card-toolbar">
                    <div class="rounded-circle bg-success w-15px h-15px" id="kt_ecommerce_add_promo_status"></div>
                </div>
            </div>
            <div class="card-body pt-0">
                @php
                $statusOptions = [
                    true => 'Aktif',
                    false => 'Tidak Aktif'
                ];
                @endphp
                <x-form-select 
                    name="is_active" 
                    class="form-select form-select-solid fw-bold"
                    :options="$statusOptions"
                />
                <div class="text-muted fs-7">Tetapkan status promo</div>
            </div>
        </div>
    </div>
    <!-- Main Form Section -->
    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
        <div class="card card-flush py-4">
            <div class="card-header">
                <div class="card-title">
                    <h2>Info Promo</h2>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="mb-10 fv-row">
                    <x-form-input-group>
                        <x-form-group name="name" label="Nama Promo" required>
                            <x-form-input type="text" name="name" class="bg-transparent" placeholder="Nama Promo">
                                @slot('help')
                                <small class="form-text text-muted">
                                    Nama promo wajib diisi dan sebaiknya unik.
                                </small>
                                @endslot
                            </x-form-input>
                        </x-form-group>
                    </x-form-input-group>
                </div>
                <div class="mb-10 fv-row">
                    <x-form-input-group>
                        <x-form-group name="code" label="Kode Promo" required>
                            <x-form-input type="text" name="code" class="bg-transparent" placeholder="Kode Promo">
                                @slot('help')
                                <small class="form-text text-muted">
                                    Kode promo wajib diisi dan harus unik.
                                </small>
                                @endslot
                            </x-form-input>
                        </x-form-group>
                    </x-form-input-group>
                </div>
                <div class="mb-10 fv-row">
                    <x-form-input-group>
                        <x-form-group name="description" label="Deskripsi">
                            <x-form-textarea name="description" class="bg-transparent" placeholder="Deskripsi Promo" />
                        </x-form-group>
                    </x-form-input-group>
                </div>
                <div class="mb-10 fv-row">
                    <x-form-input-group>
                        <x-form-group name="type" label="Tipe Promo" required>
                            @php
                            $typeOptions = [
                                '' => 'Pilih Tipe Promo',
                                'percentage' => 'Persentase',
                                'fixed_amount' => 'Nominal Tetap',
                                'buy_x_get_y' => 'Beli X Dapat Y',
                                'free_shipping' => 'Gratis Ongkir'
                            ];
                            @endphp
                            <x-form-select 
                                name="type" 
                                class="form-select form-select-solid fw-bold"
                                :options="$typeOptions"
                            />
                        </x-form-group>
                    </x-form-input-group>
                </div>
                @if($type && in_array($type, ['percentage', 'fixed_amount']))
                <div class="mb-10 fv-row">
                    <x-form-input-group>
                        <x-form-group name="value" label="Nilai Diskon" required>
                            <x-form-input type="number" name="value" class="bg-transparent" placeholder="Nilai Diskon" step="0.01">
                                @slot('help')
                                <small class="form-text text-muted">
                                    Masukkan nilai diskon (persentase atau nominal).
                                </small>
                                @endslot
                            </x-form-input>
                        </x-form-group>
                    </x-form-input-group>
                </div>
                @endif
                @if($type === 'buy_x_get_y')
                <div class="mb-10 fv-row">
                    <x-form-input-group>
                        <x-form-group name="buy_quantity" label="Jumlah Beli" required>
                            <x-form-input type="number" name="buy_quantity" class="bg-transparent" placeholder="Jumlah Beli" min="1">
                                @slot('help')
                                <small class="form-text text-muted">
                                    Jumlah item yang harus dibeli untuk mendapatkan promo.
                                </small>
                                @endslot
                            </x-form-input>
                        </x-form-group>
                    </x-form-input-group>
                </div>
                <div class="mb-10 fv-row">
                    <x-form-input-group>
                        <x-form-group name="get_quantity" label="Jumlah Gratis" required>
                            <x-form-input type="number" name="get_quantity" class="bg-transparent" placeholder="Jumlah Gratis" min="1">
                                @slot('help')
                                <small class="form-text text-muted">
                                    Jumlah item gratis yang diberikan.
                                </small>
                                @endslot
                            </x-form-input>
                        </x-form-group>
                    </x-form-input-group>
                </div>
                <div class="mb-10 fv-row">
                    <x-form-input-group>
                        <x-form-group name="free_product_id" label="Produk Gratis" required>
                            <x-form-select 
                                name="free_product_id" 
                                class="form-select form-select-solid fw-bold"
                                :options="['' => 'Pilih Produk Gratis'] + $this->products"
                            />
                            @slot('help')
                            <small class="form-text text-muted">
                                Pilih produk yang diberikan gratis.
                            </small>
                            @endslot
                        </x-form-group>
                    </x-form-input-group>
                </div>
                @endif
                <div class="mb-10 fv-row">
                    <x-form-input-group>
                        <x-form-group name="min_order_amount" label="Minimal Pembelian">
                            <x-form-input type="number" name="min_order_amount" class="bg-transparent" placeholder="Minimal Pembelian" step="0.01">
                                @slot('help')
                                <small class="form-text text-muted">
                                    Jumlah minimal pembelian untuk menggunakan promo.
                                </small>
                                @endslot
                            </x-form-input>
                        </x-form-group>
                    </x-form-input-group>
                </div>
                <div class="mb-10 fv-row">
                    <x-form-input-group>
                        <x-form-group name="max_uses" label="Maksimal Penggunaan">
                            <x-form-input type="number" name="max_uses" class="bg-transparent" placeholder="Maksimal Penggunaan" min="1">
                                @slot('help')
                                <small class="form-text text-muted">
                                    Jumlah maksimal penggunaan promo secara keseluruhan.
                                </small>
                                @endslot
                            </x-form-input>
                        </x-form-group>
                    </x-form-input-group>
                </div>
                <div class="mb-10 fv-row">
                    <x-form-input-group>
                        <x-form-group name="max_uses_per_user" label="Maksimal Penggunaan per Pengguna">
                            <x-form-input type="number" name="max_uses_per_user" class="bg-transparent" placeholder="Maksimal Penggunaan per Pengguna" min="1">
                                @slot('help')
                                <small class="form-text text-muted">
                                    Jumlah maksimal penggunaan promo per pengguna.
                                </small>
                                @endslot
                            </x-form-input>
                        </x-form-group>
                    </x-form-input-group>
                </div>
                <div class="mb-10 fv-row">
                    <x-form-input-group>
                        <x-form-group name="start_date" label="Tanggal Mulai" required>
                            <x-form-input type="datetime-local" name="start_date" class="bg-transparent" />
                        </x-form-group>
                    </x-form-input-group>
                </div>
                <div class="mb-10 fv-row">
                    <x-form-input-group>
                        <x-form-group name="end_date" label="Tanggal Berakhir" required>
                            <x-form-input type="datetime-local" name="end_date" class="bg-transparent" />
                        </x-form-group>
                    </x-form-input-group>
                </div>
                <div class="mb-10 fv-row">
                    <x-form-input-group>
                        <x-form-group name="scope" label="Cakupan Promo" required>
                            @php
                            $scopeOptions = [
                                'all' => 'Semua',
                                'products' => 'Produk Tertentu',
                                'categories' => 'Kategori Tertentu',
                                'branches' => 'Cabang Tertentu'
                            ];
                            @endphp
                            <x-form-select 
                                name="scope"
                                modifier="live" 
                                class="form-select form-select-solid fw-bold"
                                :options="$scopeOptions"
                            />
                        </x-form-group>
                    </x-form-input-group>
                </div>
                @if($this->scope === 'branches')
                <div class="mb-10 fv-row">
                    <x-form-input-group>
                        <x-form-group name="selected_branches" label="Pilih Cabang" required>
                            <x-form-select 
                                name="selected_branches" 
                                class="form-select form-select-solid fw-bold"
                                :options="$this->branches"
                                multiple
                            />
                            @slot('help')
                            <small class="form-text text-muted">
                                Pilih cabang yang berlaku untuk promo ini.
                            </small>
                            @endslot
                        </x-form-group>
                    </x-form-input-group>
                </div>
                @endif
                @if($this->scope === 'categories')
                <div class="mb-10 fv-row">
                    <x-form-input-group>
                        <x-form-group name="selected_categories" label="Pilih Kategori" required>
                            <x-form-select 
                                name="selected_categories" 
                                class="form-select form-select-solid fw-bold"
                                :options="$this->categories"
                                multiple
                            />
                            @slot('help')
                            <small class="form-text text-muted">
                                Pilih kategori yang berlaku untuk promo ini.
                            </small>
                            @endslot
                        </x-form-group>
                    </x-form-input-group>
                </div>
                @endif
                @if($this->scope === 'products')
                <div class="mb-10 fv-row">
                    <x-form-input-group>
                        <x-form-group name="selected_products" label="Pilih Produk" required>
                            <x-form-select 
                                name="selected_products" 
                                class="form-select form-select-solid fw-bold"
                                :options="$this->products"
                                multiple
                            />
                            @slot('help')
                            <small class="form-text text-muted">
                                Pilih produk yang berlaku untuk promo ini.
                            </small>
                            @endslot
                        </x-form-group>
                    </x-form-input-group>
                </div>
                @endif
                <div class="mb-10 fv-row">
                    <x-form-input-group>
                        <x-form-group name="day_restriction" label="Hari Berlaku" required>
                            @php
                            $dayOptions = [
                                'all' => 'Semua Hari',
                                'weekday' => 'Hari Kerja',
                                'weekend' => 'Akhir Pekan'
                            ];
                            @endphp
                            <x-form-select 
                                name="day_restriction" 
                                class="form-select form-select-solid fw-bold"
                                :options="$dayOptions"
                            />
                        </x-form-group>
                    </x-form-input-group>
                </div>
                <div class="mb-10 fv-row">
                    <x-form-input-group>
                        <x-form-group name="applicable_for" label="Berlaku Untuk" required>
                            @php
                            $applicableOptions = [
                                'all' => 'Semua (Sewa & Jual)',
                                'rent' => 'Sewa',
                                'sale' => 'Jual'
                            ];
                            @endphp
                            <x-form-select 
                                name="applicable_for" 
                                class="form-select form-select-solid fw-bold"
                                :options="$applicableOptions"
                            />
                        </x-form-group>
                    </x-form-input-group>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.promo') }}" wire:navigate class="btn btn-light me-5">Batalkan</a>
            <x-button class="btn btn-primary" submit="true" indicator="Harap tunggu..." label="Simpan" />
        </div>
    </div>
</x-form>