<?php
use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\Master\Brand;
use App\Models\Master\Branch;
use App\Models\ProductBranch;
use App\Models\Master\Category;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use function Livewire\Volt\{mount, rules, state, usesFileUploads};

usesFileUploads();

state(['slug'])->locked();
state([
    'product' => fn () => Product::with(['brand', 'category', 'attributes'])->where('slug', request()->slug)->first(),
    'brands' => fn () => Brand::all(),
    'categories' => fn () => Category::all(),
    'branches' => fn () => Branch::all(),
    'atribut' => [],
    'product_branches' => [],
]);

state([
    'brand_id' => '',
    'category_id' => '',
    'name' => '',
    'code' => '',
    'thumbnail' => null,
    'thumbnail_preview' => '',
    'description_rent' => '',
    'status' => 'a',
    'colors' => [],
    'storages' => [],
    'selected_color' => '',
    'selected_storage' => '',
    'branch_id' => '',
    'rent_price' => 0,
    'sale_price' => 0,
    'icloud' => '',
    'imei' => '',
    'is_publish' => true,
    'new_color_title' => 'color', // Default title untuk color
    'new_color_value' => '',
    'new_storage_title' => 'storage', // Default title untuk storage
    'new_storage_value' => '',
]);

rules(fn () => [
    'brand_id' => 'required|exists:brands,id',
    'category_id' => 'required|exists:categories,id',
    'name' => 'required|string|max:255',
    'code' => 'required|string|max:255|unique:products,code,' . (isset($this->product) ? $this->product->id : 'NULL'),
    'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    'description_rent' => 'required|string',
    'status' => 'required|in:a,i',
    'branch_id' => 'required|exists:branches,id',
    'rent_price' => 'required|numeric|min:0',
    'sale_price' => 'nullable|numeric|min:0',
    'icloud' => 'nullable|string|max:255',
    'imei' => 'nullable|string|max:255',
    'is_publish' => 'boolean',
    'new_color_value' => 'required|string|max:255',
    'new_storage_value' => 'required|string|max:255',
]);

mount(function () {
    if ($this->product) {
        $this->brand_id = $this->product->brand_id;
        $this->category_id = $this->product->category_id;
        $this->name = $this->product->name;
        $this->code = $this->product->code;
        $this->thumbnail_preview = $this->product->thumbnail ? Storage::url($this->product->thumbnail) : '';
        $this->description_rent = $this->product->description_rent;
        $this->status = $this->product->st;
        
        // Load attributes
        $this->atribut = $this->product->attributes->groupBy('title')->map(function ($items) {
            return collect($items);
        });
        $this->colors = $this->atribut['color'] ?? [];
        $this->storages = $this->atribut['storage'] ?? [];
        
        // Load product branches
        $this->product_branches = $this->product->productBranches;
    }
});

$save = function () {
    $this->validate([
        'brand_id' => 'required|exists:brands,id',
        'category_id' => 'required|exists:categories,id',
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:255|unique:products,code,' . (isset($this->product) ? $this->product->id : 'NULL'),
        'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'description_rent' => 'required|string',
    ]);

    // Handle thumbnail upload
    $thumbnailPath = $this->product->thumbnail ?? null;
    if ($this->thumbnail) {
        // Delete old thumbnail if exists
        if ($thumbnailPath) {
            Storage::delete($thumbnailPath);
        }
        
        $slug = Str::slug($this->name);
        $extension = $this->thumbnail->extension();
        $thumbnailPath = $this->thumbnail->storeAs(
            'public/product', 
            $slug . '-' . time() . '.' . $extension
        );
        $thumbnailPath = str_replace('public/', '', $thumbnailPath);
    }

    // Save or update product
    $productData = [
        'brand_id' => $this->brand_id,
        'category_id' => $this->category_id,
        'name' => $this->name,
        'slug' => Str::slug($this->name),
        'code' => $this->code,
        'thumbnail' => $thumbnailPath,
        'description_rent' => $this->description_rent
    ];

    if ($this->product) {
        $this->product->castAndUpdate($productData);
        $this->dispatch('toast-success', message: "Produk berhasil dirubah");
        return $this->redirect(route('admin.product'), navigate: true);
    } else {
        $product = Product::castAndCreate($productData);
        $this->dispatch('toast-success', message: "Produk berhasil disimpan");
        return $this->redirect(route('admin.product.edit', ['slug' => $product->slug]), navigate: true);
    }

    // Save product branch data if provided
    // if ($this->branch_id && $this->selected_color && $this->selected_storage) {
    //     $product->productBranches()->updateOrCreate(
    //         [
    //             'branch_id' => $this->branch_id,
    //             'color_id' => $this->selected_color,
    //             'storage_id' => $this->selected_storage,
    //         ],
    //         [
    //             'rent_price' => $this->rent_price,
    //             'sale_price' => $this->sale_price,
    //             'icloud' => $this->icloud,
    //             'imei' => $this->imei,
    //             'is_publish' => $this->is_publish,
    //         ]
    //     );
    // }

};
$addToBranch = function(){
    \App\Models\ProductBranch::castAndCreate(
        [
            'product_id' => $this->product->id,
            'branch_id' => $this->branch_id,
            'color_id' => $this->selected_color,
            'storage_id' => $this->selected_storage,
            'rent_price' => $this->rent_price,
            'sale_price' => $this->sale_price,
            'icloud' => $this->icloud,
            'imei' => $this->imei,
            'is_publish' => $this->is_publish,
        ]
    );
    $this->product_branches = $this->product->productBranches()->get();
    $this->dispatch('toast-success', message: "Branch Product saved successfully");
};

$deleteThumbnail = function () {
    if ($this->product->thumbnail) {
        Storage::delete($this->product->thumbnail);
        $this->product->update(['thumbnail' => null]);
        $this->thumbnail_preview = '';
    }
};

$removeBranch = function ($id) {
    ProductBranch::find($id)->delete();
    $this->product_branches = $this->product->productBranches()->get();
    $this->dispatch('toast-success', message: "Branch product removed");
};
$addColor = function () {
    $this->validate([
        'new_color_value' => 'required|string|max:255',
    ]);

    // Simpan color ke database (asumsi menggunakan model Attribute)
    $attribute = $this->product->attributes()->create([
        'title' => $this->new_color_title,
        'value' => $this->new_color_value,
    ]);

    // Refresh data colors
    $this->colors = $this->product->attributes()->where('title', 'color')->get();
    $this->new_color_value = '';
    $this->dispatch('toast-success', message: "Color added successfully");
};

$addStorage = function () {
    $this->validate([
        'new_storage_value' => 'required|string|max:255',
    ]);

    // Simpan storage ke database
    $attribute = $this->product->attributes()->create([
        'title' => $this->new_storage_title,
        'value' => $this->new_storage_value,
    ]);

    // Refresh data storages
    $this->storages = $this->product->attributes()->where('title', 'storage')->get();
    $this->new_storage_value = '';
    $this->dispatch('toast-success', message: "Storage added successfully");
};

$removeAttribute = function ($id) {
    // Hapus attribute
    $this->product->attributes()->where('id', $id)->delete();
    
    // Refresh data
    $this->colors = $this->product->attributes()->where('title', 'color')->get();
    $this->storages = $this->product->attributes()->where('title', 'storage')->get();
    $this->dispatch('toast-success', message: "Attribute removed");
};
?>

<x-form action="save" hasFiles class="form d-flex flex-column flex-lg-row">
    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
        <!-- Thumbnail settings -->
        <div class="card card-flush py-4">
            <div class="card-header">
                <div class="card-title">
                    <h2>Thumbnail</h2>
                </div>
            </div>
            <div class="card-body text-center pt-0">
                <div class="image-input image-input-outline {{ !$this->thumbnail_preview ? 'image-input-empty' : '' }}" data-kt-image-input="true">
                    @if($this->thumbnail_preview)
                        <div class="image-input-wrapper w-150px h-150px" style="background-image: url('{{ $this->thumbnail_preview }}')"></div>
                    @else
                        <div class="image-input-wrapper w-150px h-150px"></div>
                    @endif
                    
                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change">
                        <i class="ki-outline ki-pencil fs-7"></i>
                        <input type="file" wire:model="thumbnail" accept=".png, .jpg, .jpeg" />
                        <input type="hidden" name="avatar_remove" />
                    </label>
                    
                    @if($this->thumbnail_preview)
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" wire:click="deleteThumbnail">
                            <i class="ki-outline ki-cross fs-2"></i>
                        </span>
                    @endif
                </div>
                <div class="text-muted fs-7">Set the product thumbnail image. Only *.png, *.jpg and *.jpeg image files are accepted</div>
            </div>
        </div>

        <!-- Status -->
        <div class="card card-flush py-4">
            <div class="card-header">
                <div class="card-title">
                    <h2>Status</h2>
                </div>
                <div class="card-toolbar">
                    <div class="rounded-circle bg-{{ $this->status == 'a' ? 'success' : 'danger' }} w-15px h-15px"></div>
                </div>
            </div>
            <div class="card-body pt-0">
                <select class="form-select mb-2" wire:model="status">
                    <option value="a">Active</option>
                    <option value="i">Inactive</option>
                </select>
                <div class="text-muted fs-7">Set the product status.</div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="card card-flush py-4">
            <div class="card-header">
                <div class="card-title">
                    <h2>Product Details</h2>
                </div>
            </div>
            <div class="card-body pt-0">
                <label class="form-label">Brand</label>
                <select class="form-select mb-5" wire:model="brand_id">
                    <option value="">Select Brand</option>
                    @foreach($this->brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </select>

                <label class="form-label">Category</label>
                <select class="form-select mb-2" wire:model="category_id">
                    <option value="">Select Category</option>
                    @foreach($this->categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
        @if($this->product)
        <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-n2">
            <li class="nav-item">
                <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#general">General</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#variants">Variants</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#branches">Branches</a>
            </li>
        </ul>
        @endif

        <div class="tab-content">
            <!-- General Tab -->
            <div class="tab-pane fade show active" id="general" role="tab-panel">
                <div class="d-flex flex-column gap-7 gap-lg-10">
                    <div class="card card-flush py-4">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>General Information</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="mb-10 fv-row">
                                <x-form-group name="name" label="Product Name" required>
                                    <x-form-input type="text" name="name" wire:model="name" placeholder="Product Name" />
                                </x-form-group>
                            </div>

                            <div class="mb-10 fv-row">
                                <x-form-group name="code" label="Product Code" required>
                                    <x-form-input type="text" name="code" wire:model="code" placeholder="Product Code" />
                                </x-form-group>
                            </div>

                            <div class="mb-10 fv-row">
                                <x-form-group name="description_rent" label="Description" required>
                                    <textarea class="form-control" wire:model="description_rent" rows="5" placeholder="Product description"></textarea>
                                </x-form-group>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Variants Tab -->
            <div class="tab-pane fade" id="variants" role="tab-panel">
                <div class="d-flex flex-column gap-7 gap-lg-10">
                    <!-- Colors Card -->
                    <div class="card card-flush py-4">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Colors</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <!-- Form Tambah Color -->
                            <div class="mb-5">
                                <div class="row g-5">
                                    <div class="col-md-8">
                                        <x-form-group name="new_color_value" label="New Color">
                                            <x-form-input type="text" name="new_color_value" wire:model="new_color_value" placeholder="Enter color name (e.g. Black, Red)" />
                                        </x-form-group>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="button" class="btn btn-primary" wire:click="addColor" wire:loading.attr="disabled">
                                            <span wire:loading.remove>Add Color</span>
                                            <span wire:loading>Adding...</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            @if(count($this->colors) > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Color</th>
                                                <th>Value</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($this->colors as $color)
                                                <tr>
                                                    <td>{{ $color->title }}</td>
                                                    <td>{{ $color->value }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-danger" wire:click="removeAttribute({{ $color->id }})">
                                                            Remove
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">No colors added for this product</div>
                            @endif
                        </div>
                    </div>

                    <!-- Storage Card -->
                    <div class="card card-flush py-4">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Storage Options</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <!-- Form Tambah Storage -->
                            <div class="mb-5">
                                <div class="row g-5">
                                    <div class="col-md-8">
                                        <x-form-group name="new_storage_value" label="New Storage">
                                            <x-form-input type="text" name="new_storage_value" wire:model="new_storage_value" placeholder="Enter storage capacity (e.g. 64GB, 128GB)" />
                                        </x-form-group>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="button" class="btn btn-primary" wire:click="addStorage" wire:loading.attr="disabled">
                                            <span wire:loading.remove>Add Storage</span>
                                            <span wire:loading>Adding...</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            @if(count($this->storages) > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Storage</th>
                                                <th>Value</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($this->storages as $storage)
                                                <tr>
                                                    <td>{{ $storage->title }}</td>
                                                    <td>{{ $storage->value }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-danger" wire:click="removeAttribute({{ $storage->id }})">
                                                            Remove
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">No storage options added for this product</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Branches Tab -->
            <div class="tab-pane fade" id="branches" role="tab-panel">
                <div class="d-flex flex-column gap-7 gap-lg-10">
                    <div class="card card-flush py-4">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Add to Branch</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row g-5">
                                <div class="col-md-4">
                                    <label class="form-label">Branch</label>
                                    <select class="form-select" wire:model="branch_id">
                                        <option value="">Select Branch</option>
                                        @foreach($this->branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Color</label>
                                    <select class="form-select" wire:model="selected_color">
                                        <option value="">Select Color</option>
                                        @foreach($this->colors as $color)
                                            <option value="{{ $color->id }}">{{ $color->value }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Storage</label>
                                    <select class="form-select" wire:model="selected_storage">
                                        <option value="">Select Storage</option>
                                        @foreach($this->storages as $storage)
                                            <option value="{{ $storage->id }}">{{ $storage->value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row g-5 mt-3">
                                <div class="col-md-3">
                                    <x-form-group name="rent_price" label="Rent Price" required>
                                        <x-form-input type="number" name="rent_price" wire:model="rent_price" placeholder="Rent Price" />
                                    </x-form-group>
                                </div>

                                <div class="col-md-3">
                                    <x-form-group name="sale_price" label="Sale Price">
                                        <x-form-input type="number" name="sale_price" wire:model="sale_price" placeholder="Sale Price" />
                                    </x-form-group>
                                </div>

                                <div class="col-md-3">
                                    <x-form-group name="icloud" label="iCloud">
                                        <x-form-input type="text" name="icloud" wire:model="icloud" placeholder="iCloud" />
                                    </x-form-group>
                                </div>

                                <div class="col-md-3">
                                    <x-form-group name="imei" label="IMEI">
                                        <x-form-input type="text" name="imei" wire:model="imei" placeholder="IMEI" />
                                    </x-form-group>
                                </div>
                            </div>

                            <div class="mt-5">
                                <button type="button" class="btn btn-primary" wire:click="addToBranch" wire:loading.attr="disabled">
                                    <span wire:loading.remove>Add to Branch</span>
                                    <span wire:loading>Processing...</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    @if(count($this->product_branches) > 0)
                        <div class="card card-flush py-4">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Branch Inventory</h2>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Branch</th>
                                                <th>Color</th>
                                                <th>Storage</th>
                                                <th>Rent Price</th>
                                                <th>Sale Price</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($this->product_branches as $branch)
                                                <tr>
                                                    <td>{{ $branch->branch->name }}</td>
                                                    <td>
                                                        @php
                                                            $color = collect($this->colors)->firstWhere('id', $branch->color_id);
                                                        @endphp
                                                        {{ $color ? $color->value : 'N/A' }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            $storage = collect($this->storages)->firstWhere('id', $branch->storage_id);
                                                        @endphp
                                                        {{ $storage ? $storage->value : 'N/A' }}
                                                    </td>
                                                    <td>{{ number_format($branch->rent_price) }}</td>
                                                    <td>{{ number_format($branch->sale_price) }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-danger" wire:click="removeBranch({{ $branch->id }})">
                                                            Remove
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.product') }}" wire:navigate class="btn btn-light me-5">Cancel</a>
            <x-button class="btn btn-primary" submit="true" indicator="Saving..." label="Save Product" />
        </div>
    </div>
</x-form>