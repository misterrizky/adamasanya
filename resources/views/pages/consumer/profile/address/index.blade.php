<?php
use function Livewire\Volt\{mount, state};
use function Laravel\Folio\name;
name('profile.address');
state([
    'user' => fn() => \App\Models\User::where('id', Auth::user()->id)->first(),
    'address' => fn() => \App\Models\UserAddress::where('user_id', Auth::user()->id)->get(),
]);
$setDefaultAddress = function($id){
    try {
        \App\Models\UserAddress::where('user_id', Auth::user()->id)->where('id', '!=', $id)->update(['is_primary' => 0]);
        $data = \App\Models\UserAddress::findOrFail($id);
        $data->is_primary = 1; // Perbaikan: gunakan assignment biasa, bukan method
        $data->save(); // Perbaikan: tambahkan tanda kurung untuk method save
        return $this->redirect(route('profile.address'), navigate: true);
    } catch (\Exception $e) {
        $this->dispatch('toast-warning', message: $e->getMessage());
    }
};
$deleteAddress = function($id){
    try {
        $data = \App\Models\UserAddress::findOrFail($id);
        $data->delete();
        
        return $this->redirect(route('profile.address'), navigate: true);
    } catch (\Exception $e) {
        $this->dispatch('toast-warning', message: $e->getMessage());
    }
};
?>
<x-app>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('profile.setting')],
            ['text' => 'Daftar Alamat', 'active' => true]
        ]"
        :buttons="[
            ['url' => route('profile.create-address'), 'type' => 'success' , 'text' => 'Tambah Alamat']
        ]"
    />
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid py-10">
        <x-advance-search/>
        @foreach($this->address as $address)
            <div class="card card-flush address-card @if($address->is_primary) border border-2 border-primary @endif" data-address-id="{{ $address->id }}">
                <div class="card-header {{ $address->is_primary ? 'ribbon ribbon-start' : ''}}">
                    <div class="card-title">
                        {{ $address->label ?: 'Alamat #'.($loop->iteration) }}
                        @if($address->is_primary)
                            <span class="badge badge-light-primary ms-5 float-end">Utama</span>
                        @endif
                    </div>
                    <div class="ribbon-label bg-primary"></div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <span class="fw-semibold text-gray-700">{{ $address->address }}</span>
                    </div>
                    <div class="d-flex mb-5">
                        <div class="w-100">
                            <div class="fw-semibold text-gray-700">
                                {{ $address->village->name ?? '' }}, 
                                {{ $address->subdistrict->name ?? '' }}
                            </div>
                            <div class="text-muted">
                                {{ $address->city->type ?? '' }} {{ $address->city->name ?? '' }}, 
                                {{ $address->state->name ?? '' }}
                            </div>
                            <div class="text-muted">
                                Kode Pos: {{ $address->village->poscode ?? '' }}
                            </div>
                            <div class="text-muted">
                                {{ $address->notes ? '('. $address->notes . ')' : '' }}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="ki-filled ki-geolocation fs-3x {{ $address->lat && $address->lng ? 'text-success' : 'text-danger' }}"></i>
                        <span class="fw-bold ms-3">{{ $address->lat && $address->lng ? 'Sudah Pinpoint' : 'Belum Pinpoint' }}</span>
                    </div>
                    @if(Auth::user()->st == "pending")
                    <div class="d-flex flex-column pt-4 border-top">
                        @if($address->is_primary)
                            <a href="{{ route('profile.edit-address', ['userAddress' => $address->id]) }}" class="btn btn-block btn-bg-dark text-inverse-dark mb-2" wire:navigate>Ubah Alamat</a>
                        @else
                            <div class="d-flex flex-column gap-2">
                                <a href="{{ route('profile.edit-address', ['userAddress' => $address->id]) }}" class="btn btn-block btn-bg-dark text-inverse-dark" wire:navigate>Ubah Alamat</a>
                                <div class="d-flex gap-2">
                                    <button onclick="setDefaultAddress('{{ $address->id }}')" class="btn btn-block btn-primary flex-grow-1" {{ $address->lat && $address->lng ? '' : 'disabled' }}>Jadikan Alamat Utama</button>
                                    <button onclick="deleteAddress('{{ $address->id }}')" class="btn btn-block btn-danger flex-grow-1" {{ $address->lat && $address->lng ? '' : 'disabled' }}>Hapus Alamat</button>
                                </div>
                            </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        @endforeach
        @section('custom_js')
            <script data-navigate-once>
                function setDefaultAddress(id) {
                    Swal.fire({
                        title: 'Jadikan alamat utama?',
                        text: "Alamat ini akan menjadi alamat utama Anda",
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, jadikan utama',
                        cancelButtonText: 'Batalkan',
                        reverseButtons: true,
                        backdrop: true,
                        allowOutsideClick: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Mengatur Alamat Utama...',
                                html: 'Sedang memproses permintaan Anda',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                    @this.setDefaultAddress(id).then(() => {
                                        Swal.fire({
                                            title: 'Berhasil!',
                                            text: 'Alamat berhasil dijadikan utama',
                                            icon: 'success',
                                            timer: 2000,
                                            timerProgressBar: true
                                        });
                                    }).catch(error => {
                                        Swal.fire({
                                            title: 'Gagal!',
                                            html: `Gagal menjadikan alamat utama: <br><span class="text-red-500">${error.message}</span>`,
                                            icon: 'error'
                                        });
                                    });
                                }
                            });
                        }
                    });
                }
                function deleteAddress(id) {
                    Swal.fire({
                        title: 'Hapus Alamat?',
                        text: "Alamat ini akan terhapus dari sistem",
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batalkan',
                        reverseButtons: true,
                        backdrop: true,
                        allowOutsideClick: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Menghapus Alamat...',
                                html: 'Sedang memproses permintaan Anda',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                    @this.deleteAddress(id).then(() => {
                                        Swal.fire({
                                            title: 'Berhasil!',
                                            text: 'Alamat berhasil dihapus',
                                            icon: 'success',
                                            timer: 2000,
                                            timerProgressBar: true
                                        });
                                    }).catch(error => {
                                        Swal.fire({
                                            title: 'Gagal!',
                                            html: `Gagal menghapus alamat: <br><span class="text-red-500">${error.message}</span>`,
                                            icon: 'error'
                                        });
                                    });
                                }
                            });
                        }
                    });
                }
            </script>
        @endsection
    </div>
    @endvolt
</x-app>