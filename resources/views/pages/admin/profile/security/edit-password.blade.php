<?php
use function Laravel\Folio\name;
use function Livewire\Volt\{rules, state};
name('admin.profile.edit-password');

state(['kata_sandi' => '', 'kata_sandi_baru' => '']);
rules(fn () => [
    'kata_sandi' => ['required','string','min:8'],
    'kata_sandi_baru' => ['required','string','min:8','confirmed'],
]);
?>
<x-app>
    <x-toolbar-mobile 
        :breadcrumbs="[
            ['icon' => 'arrow-left', 'url' => route('admin.profile.security')],
            ['text' => config('app.name'), 'active' => true]
        ]"
    />
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="row mb-1">
            <p>Membuat kata sandi membantu Anda menjaga keamanan akun dan transaksi di {{ config('app.name') }}</p>
            <div class="col-lg-4">
                <div class="fv-row mb-0 fv-plugins-icon-container">
                    <label for="newpassword" class="form-label fs-6 fw-bold mb-3">Kata Sandi Baru</label>
                    <input type="password" class="form-control form-control-lg form-control-solid" name="newpassword" id="newpassword">
                <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div></div>
            </div>
            <div class="col-lg-4">
                <div class="fv-row mb-0 fv-plugins-icon-container">
                    <label for="confirmpassword" class="form-label fs-6 fw-bold mb-3">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" class="form-control form-control-lg form-control-solid" name="confirmpassword" id="confirmpassword">
                <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div></div>
            </div>
        </div>
        <x-button class="btn btn-success btn-block w-100 mt-3" disabled id="tombol_mulai_verifikasi" href="mulaiVerifikasi" type="button" indicator="Harap tunggu..." label="Simpan Kata Sandi" />
    </div>
    @endvolt
    @section('custom_js')
    <script data-navigate-once>
        function checkInputs() {
            const newPassword = $('#newpassword').val();
            const confirmPassword = $('#confirmpassword').val();
            
            if (newPassword && confirmPassword) {
                $('#tombol_mulai_verifikasi').prop('disabled', false);
            } else {
                $('#tombol_mulai_verifikasi').prop('disabled', true);
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            $('#newpassword, #confirmpassword').on('input', checkInputs);
        });
        document.addEventListener('livewire:navigated', function() {
            $('#newpassword, #confirmpassword').on('input', checkInputs);
        });
    </script>
@endsection
</x-app>