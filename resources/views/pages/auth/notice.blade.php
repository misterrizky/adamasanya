<?php
 
use function Laravel\Folio\{middleware, name};
middleware(['auth']);
name('verification.notice');
?>
<x-layouts.app>
    @volt('auth.notice')
    <div class="adamasanya-wrap z-index-0 position-relative">
        <main class="adminuiux-content">
            <!--Page body-->
            <div class="container-fluid">
                <div class="row gx-3 align-items-center justify-content-center py-3 mt-auto z-index-1 height-dynamic" style="--h-dynamic: calc(100vh - 120px)">
                    <div class="col login-box maxwidth-400">
                        <div class="mb-4">
                            <img alt="image" class="w-100" src="{{asset('media/illustrations/30.svg')}}">
                            <h3 class="text-theme-1 fw-normal mb-0">Periksa email Anda, Silakan klik tautan yang dikirim ke email Anda.</h3>
                        </div>
                        {{-- <div class="text-center mb-3">
                            <a class="btn btn-square btn-primary mx-1" href="{{ route('login') }}" wire:navigate>Masuk</a>
                        </div> --}}
                    </div>
                </div>
            </div>
        </main>
        <!-- fullscreen Modal -->
    </div>
    @endvolt
</x-layouts.app>