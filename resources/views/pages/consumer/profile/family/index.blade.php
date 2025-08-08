<?php
use function Livewire\Volt\{mount, state};
use function Laravel\Folio\name;
name('profile.family');
state([
    'role' => fn() => Auth::user()->getRoleNames()[0],
    'family' => fn() => \App\Models\UserFamily::where('user_id', Auth::user()->id)->get(),
]);
$deleteFamily = function($id){
    try {
        $data = \App\Models\UserFamily::findOrFail($id);
        $data->delete();
        
        return $this->redirect(route('profile.family'), navigate: true);
    } catch (\Exception $e) {
        $this->dispatch('toast-warning', message: $e->getMessage());
    }
};
?>
<x-app>
    <!-- Mobile Toolbar -->
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('profile.setting')],
            ['text' => 'Daftar Keluarga', 'active' => true]
        ]"
        :buttons="[
            ['url' => route('profile.create-family'), 'type' => 'success' , 'text' => 'Tambah Keluarga']
        ]"
    />
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid py-6 py-lg-10">
        <div class="container-xxl">
            <x-advance-search/>
            
            @if($family->isEmpty())
                <!-- Empty state -->
                <div class="card">
                    <div class="card-body text-center py-10">
                        <div class="mb-4">
                            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="text-gray-800 mb-3">Belum Ada Data Keluarga</h4>
                        <p class="text-muted mb-4">Tambahkan anggota keluarga Anda untuk memudahkan proses administrasi</p>
                        <a href="{{ route('profile.create-family') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Keluarga
                        </a>
                    </div>
                </div>
            @else
                <div class="row g-6">
                    @foreach($family as $family)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card card-flush border border-2 border-primary h-100" data-family-id="{{ $family->id }}">
                                <div class="card-header ribbon ribbon-start">
                                    <div class="card-title fs-5 fw-bold text-gray-800">
                                        {{ $family->type ?: 'Keluarga #'.($loop->iteration) }}
                                    </div>
                                    <div class="ribbon-label bg-primary">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="fw-semibold fs-6 text-gray-700">{{ $family->name }}</span>
                                        </div>
                                        <div class="d-flex">
                                            <div class="w-100">
                                                @if($family->phone)
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="bi bi-telephone text-primary me-2"></i>
                                                    <span class="fw-semibold text-gray-700">+62{{ $family->phone }}</span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if(Auth::user()->st == "pending")
                                    <div class="mt-auto pt-4 border-top">
                                        <div class="d-flex gap-2">
                                            @role('Super Admin|Owner|Branch|Pegawai')
                                            <button onclick="deleteFamily('{{ $family->id }}')" class="btn btn-danger flex-grow-1">
                                                <i class="bi bi-trash me-2"></i>Hapus
                                            </button>
                                            @elserole('Onboarding')
                                            <a href="{{ route('profile.edit-family', ['userFamily' => $family->id]) }}" wire:navigate class="btn btn-warning flex-grow-1">
                                                <i class="bi bi-pencil me-2"></i>Ubah
                                            </a>
                                            @endrole
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    
    @section('custom_js')
        <script data-navigate-once>
            function deleteFamily(id) {
                Swal.fire({
                    title: 'Konfirmasi Penghapusan',
                    text: "Apakah Anda yakin ingin menghapus anggota keluarga ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    backdrop: true,
                    allowOutsideClick: false,
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Sedang Memproses',
                            html: 'Menghapus data keluarga...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                                @this.deleteFamily(id).then(() => {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Data keluarga berhasil dihapus',
                                        icon: 'success',
                                        timer: 2000,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    });
                                }).catch(error => {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        html: `Terjadi kesalahan: <br><span class="text-danger">${error.message}</span>`,
                                        icon: 'error',
                                        confirmButtonText: 'Mengerti'
                                    });
                                });
                            }
                        });
                    }
                });
            }
        </script>
    @endsection
    @endvolt
</x-app>