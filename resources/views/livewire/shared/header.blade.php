<?php
use function Livewire\Volt\{state};

state([
    'unreadCounts' => fn() => Auth::check() ? Auth::user()->unreadNotifications->count() : 0,
    'notifications' => fn() => Auth::check() ? Auth::user()->notifications()->latest()->take(10)->get() : collect(),
]);
$headerloadNotification = function() {
    if (Auth::check()) {
        $this->unreadCounts = Auth::user()->unreadNotifications->count();
        $this->notifications = Auth::user()->notifications()->latest()->take(10)->get();
    }
};
// Fungsi untuk menandai notifikasi sebagai dibaca
$markAsRead = function($notificationId) {
    if (Auth::check()) {
        $notification = Auth::user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            $this->headerloadNotification();
        }
    }
};

// Fungsi untuk menandai semua notifikasi sebagai dibaca
$markAllAsReads = function() {
    if (Auth::check()) {
        Auth::user()->unreadNotifications->markAsRead();
        $this->headerloadNotification();
    }
};

// Fungsi untuk menghapus notifikasi
$deleteNotifications = function($notificationId) {
    if (Auth::check()) {
        Auth::user()->notifications()->where('id', $notificationId)->delete();
        $this->headerloadNotification();
    }
};
$hapus = function(){
    Auth::user()->st = 'deactive';
    Auth::user()->save();
    auth()->logout();
    $route = route('home');
    return $this->redirect($route, navigate: true);
};
$logout = function(){
    auth()->logout();
    $route = route('home');
    return $this->redirect($route, navigate: true);
};
?>
<div id="kt_app_header" class="app-header">
    <!--begin::Header container-->
    <div class="app-container container-fluid d-flex align-items-stretch justify-content-between" id="kt_app_header_container">
        <!--begin::Header logo-->
        <div class="app-header-logo d-flex align-items-center me-lg-20 gap-1 gap-lg-2">
            @auth
            @role('Super Admin')
            <!--begin::Drawer toggle-->
            <button class="btn btn-icon ms-n2 ms-lg-n4" id="kt_activities_toggle">
                <i class="ki-outline ki-burger-menu-2 fs-1 lh-0"></i>
            </button>
            <!--end::Drawer toggle-->
            @endrole
            @endauth
            <!--begin::Logo image-->
            <a href="{{ Auth::check() ? Auth::user()->getRoleNames()[0] == "Konsumen" || Auth::user()->getRoleNames()[0] == "Onboarding" ? route('home') : route('admin.dashboard') : route('home') }}" wire:navigate>
                <img alt="Logo" src="{{ asset('media/icons/logo.png') }}" class="h-60px" />
            </a>
            <!--end::Logo image-->
        </div>
        <!--end::Header logo-->
        <!--begin::Header wrapper-->
        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
            <!--begin::Menu wrapper-->
            <div class="d-flex align-items-stretch" id="kt_app_header_menu_wrapper">
                <!--begin::Menu holder-->
                <div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper=    "true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_menu_wrapper'}">
                    <!--begin::Menu-->
                    <div class="menu menu-rounded menu-column menu-lg-row menu-active-bg menu-title-gray-600 menu-state-gray-900 menu-arrow-gray-500 fw-semibold fw-semibold fs-6 align-items-stretch my-5 my-lg-0 px-2 px-lg-0" id="#kt_app_header_menu" data-kt-menu="true">
                        <!--begin:Menu item-->
                        @role('Super Admin|Owner')
                            <x-header-menu 
                                type="submenu"
                                title="Produk"
                                active="{{ request()->is('product*') }}"
                                :items="[
                                    ['title' => 'Kategori Produk', 'url' => route('admin.category'), 'active' => request()->is('product/category*')],
                                    ['title' => 'Merk Produk', 'url' => route('admin.brand'), 'active' => request()->is('product/brand')],
                                    ['title' => 'Master Produk', 'url' => route('admin.product'), 'active' => request()->is('product/master*')],
                                    ['title' => 'Inventaris Cabang', 'url' => route('admin.product-branch'), 'active' => request()->is('product/rent*')],
                                    // ... tambahkan item lainnya
                                ]"
                                collapsible="true"
                                showMoreText="Tampilkan Lebih"
                            />
                            <x-header-menu 
                                type="submenu"
                                title="HRM"
                                active="{{ request()->is('hrm*') }}"
                                :items="[
                                    ['title' => 'Cabang', 'url' => route('admin.branch'), 'active' => request()->is('hrm/branch*')],
                                    ['title' => 'Pengguna', 'url' => route('admin.user'), 'active' => request()->is('hrm/user')],
                                    ['title' => 'Konsumen', 'url' => route('admin.consumer'), 'active' => request()->is('hrm/customer*')]
                                    // ... tambahkan item lainnya
                                ]"
                                collapsible="true"
                                showMoreText="Tampilkan Lebih"
                            />
                            <x-header-menu 
                                type="submenu"
                                title="Keuangan"
                                active="{{ request()->is('promo*') }}"
                                :items="[
                                    ['title' => 'Promo', 'url' => route('admin.promo'), 'active' => request()->is('promo*')],
                                    ['title' => 'Transaksi', 'url' => route('admin.transaction'), 'active' => request()->is('admin/transaction*')]
                                    // ... tambahkan item lainnya
                                ]"
                                collapsible="true"
                                showMoreText="Tampilkan Lebih"
                            />
                        @elserole('Cabang|Pegawai')
                        <x-header-menu 
                            title="Konsumen"
                            url="{{ route('admin.consumer') }}"
                            active="{{request()->is('admin/user*')}}"
                        />
                        @if(Auth::user()->branch_id === 1 || Auth::user()->branch_id === 12)
                            <x-header-menu 
                                type="submenu"
                                title="Manajemen Produk"
                                active="{{ request()->is('product*') }}"
                                :items="[
                                    ['title' => 'Kategori Produk', 'url' => route('admin.category'), 'active' => request()->is('product/category*')],
                                    ['title' => 'Merk Produk', 'url' => route('admin.brand'), 'active' => request()->is('product/brand')],
                                    ['title' => 'Produk', 'url' => route('admin.product-branch'), 'active' => request()->is('product/rent')]
                                ]"
                                collapsible="true"
                                showMoreText="Tampilkan Lebih"
                            />
                            <x-header-menu 
                                type="submenu"
                                title="Transaksi"
                                active="{{ request()->is('transaction*') }}"
                                :items="[
                                    ['title' => 'Promo', 'url' => route('admin.promo'), 'active' => request()->is('finance/coupon*')],
                                    ['title' => 'Sewa', 'url' => route('admin.transaction'), 'active' => request()->is('transaction/rent')]
                                ]"
                                collapsible="true"
                                showMoreText="Tampilkan Lebih"
                            />
                        @else
                            <x-header-menu 
                                type="submenu"
                                title="Manajemen Cabang"
                                active="{{ request()->is('admin/branch-schedule*') || request()->is('admin/product-branch*') }}"
                                :items="[
                                    ['title' => 'Jadwal Cabang', 'url' => route('admin.branch-schedule'), 'active' => request()->is('admin/branch-schedule')],
                                    ['title' => 'Produk', 'url' => route('admin.product-branch'), 'active' => request()->is('admin/product-branch*')],
                                ]"
                                collapsible="true"
                                showMoreText="Tampilkan Lebih"
                            />
                            <x-header-menu 
                                title="Transaksi"
                                url="{{ route('admin.transaction') }}"
                                active="{{request()->is('admin/transaction*')}}"
                            />
                        @endif
                        @endrole
                        <!--end:Menu item-->
                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Menu holder-->
            </div>
            <!--end::Menu wrapper-->
            <!--begin::Navbar-->
            <div class="app-navbar flex-shrink-0 gap-2 gap-lg-5">
                @auth
                <!--begin::Notifications-->
                <div class="app-navbar-item">
                    <!--begin::Menu- wrapper-->
                    <div class="btn btn-icon rounded-circle w-35px h-35px bg-light-info border-clarity-info" data-kt-menu-trigger="{default: 'click', lg: 'click'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end" id="kt_menu_item_wow">
                        <i class="ki-filled ki-notification-on text-info fs-3"></i>
                    </div>
                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true" id="kt_menu_notifications">
                        <!--begin::Heading-->
                        <div class="d-flex flex-column bgi-no-repeat rounded-top" style="background-image:url('{{asset('media/bg/menu-header-dark.png')}}')">
                            <!--begin::Title-->
                            <h3 class="text-white fw-semibold px-9 mt-10 mb-6">
                                Notifikasi
                                @if($this->unreadCounts > 0)
                                <span class="fs-8 opacity-75 ps-3">
                                    {{ $this->unreadCounts }} notifikasi yang belum dibaca
                                </span>
                                @endif
                            </h3>
                            <!--end::Title-->
                            <!--begin::Tabs-->
                            @role('Super Admin|Owner|Cabang|Pegawai')
                            <ul class="nav nav-line-tabs nav-line-tabs-2x nav-stretch fw-semibold px-9">
                                @role('Super Admin|Owner')
                                <li class="nav-item">
                                    <a class="nav-link text-white opacity-75 opacity-state-100 pb-4 active" data-bs-toggle="tab" href="#kt_topbar_notifications_1">Alerts</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-white opacity-75 opacity-state-100 pb-4" data-bs-toggle="tab" href="#kt_topbar_notifications_2">Updates</a>
                                </li>
                                @endrole
                                @role('Super Admin')
                                <li class="nav-item">
                                    <a class="nav-link text-white opacity-75 opacity-state-100 pb-4" data-bs-toggle="tab" href="#kt_topbar_notifications_3">Logs</a>
                                </li>
                                @endrole
                            </ul>
                            @endrole
                            <!--end::Tabs-->
                        </div>
                        <!--end::Heading-->
                        <!--begin::Tab content-->
                        <div class="tab-content">
                            <!--begin::Tab panel-->
                            <div class="tab-pane fade show active" id="kt_topbar_notifications_1" role="tabpanel">
                                <!--begin::Items-->
                                <div class="scroll-y mh-325px my-5 px-8">
                                    @forelse($this->notifications as $notification)
                                    <div class="d-flex flex-stack py-4">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-35px me-4">
                                                <span class="symbol-label bg-light-{{ $notification->data['severity'] ?? 'primary' }}">
                                                    <i class="ki-outline ki-{{ $notification->data['icon'] ?? 'notification' }} fs-2 text-{{ $notification->data['severity'] ?? 'primary' }}"></i>
                                                </span>
                                            </div>
                                            <div class="mb-0 me-2">
                                                <a href="#" class="fs-6 text-gray-800 text-hover-{{ $notification->data['severity'] ?? 'primary' }} fw-bold">{{ $notification->data['title'] ?? 'Notification' }}</a>
                                                <div class="text-gray-500 fs-7">{{ $notification->data['message'] ?? '' }}</div>
                                            </div>
                                        </div>
                                        <span class="badge badge-light fs-8">{{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                    @empty
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-200px theme-light-show" alt="image" src="{{asset('media/illustrations/there-is-nothing-here.png')}}" />
                                        <img class="mw-100 mh-200px theme-dark-show" alt="image" src="{{asset('media/illustrations/there-is-nothing-here-dark.png')}}" />
                                    </div>
                                    @endforelse
                                </div>
                                @if($this->unreadCounts > 0)
                                <div class="py-3 text-center border-top">
                                    <button wire:click="markAllAsReads" class="btn btn-color-gray-600 btn-active-color-primary">
                                        Tandai semua sudah dibaca
                                        <i class="ki-outline ki-check fs-5"></i>
                                    </button>
                                </div>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="kt_topbar_notifications_2" role="tabpanel">
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-column px-9">
                                    <!--begin::Section-->
                                    <div class="pt-10 pb-0">
                                        <!--begin::Title-->
                                        <h3 class="text-gray-900 text-center fw-bold">Get Pro Access</h3>
                                        <!--end::Title-->
                                        <!--begin::Text-->
                                        <div class="text-center text-gray-600 fw-semibold pt-1">Outlines keep you honest. They stoping you from amazing poorly about drive</div>
                                        <!--end::Text-->
                                        <!--begin::Action-->
                                        <div class="text-center mt-5 mb-9">
                                            <a href="#" class="btn btn-sm btn-primary px-6" data-bs-toggle="modal" data-bs-target="#kt_modal_upgrade_plan">Upgrade</a>
                                        </div>
                                        <!--end::Action-->
                                    </div>
                                    <!--end::Section-->
                                    <!--begin::Illustration-->
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-200px" alt="image" src="" />
                                    </div>
                                    <!--end::Illustration-->
                                </div>
                                <!--end::Wrapper-->
                            </div>
                            <!--end::Tab panel-->
                            <!--begin::Tab panel-->
                            <div class="tab-pane fade" id="kt_topbar_notifications_3" role="tabpanel">
                                <!--begin::Items-->
                                <div class="scroll-y mh-325px my-5 px-8">
                                    <!--begin::Item-->
                                    <div class="d-flex flex-stack py-4">
                                        <!--begin::Section-->
                                        <div class="d-flex align-items-center me-2">
                                            <!--begin::Code-->
                                            <span class="w-70px badge badge-light-success me-4">200 OK</span>
                                            <!--end::Code-->
                                            <!--begin::Title-->
                                            <a href="#" class="text-gray-800 text-hover-primary fw-semibold">New order</a>
                                            <!--end::Title-->
                                        </div>
                                        <!--end::Section-->
                                        <!--begin::Label-->
                                        <span class="badge badge-light fs-8">Just now</span>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Item-->
                                </div>
                                <!--end::Items-->
                                <!--begin::View more-->
                                <div class="py-3 text-center border-top">
                                    <a href="pages/user-profile/activity.html" class="btn btn-color-gray-600 btn-active-color-primary">View All 
                                    <i class="ki-outline ki-arrow-right fs-5"></i></a>
                                </div>
                                <!--end::View more-->
                            </div>
                            <!--end::Tab panel-->
                        </div>
                        <!--end::Tab content-->
                    </div>
                    <!--end::Menu-->
                    <!--end::Menu wrapper-->
                </div>
                <!--end::Notifications-->
                <!--begin::Chat-->
                <div class="app-navbar-item">
                    <!--begin::Menu wrapper-->
                    <div class="btn btn-icon rounded-circle w-35px h-35px bg-light-primary border-clarity-primary" id="kt_drawer_chat_toggle">
                        <i class="ki-filled ki-message-text-2 text-primary fs-3"></i>
                    </div>
                    <!--end::Menu wrapper-->
                </div>
                <!--end::Chat-->
                @endauth
                <!--begin::Cart-->
                @guest
                <div class="app-navbar-item">
                    <!--begin::Menu wrapper-->
                    <div class="btn btn-icon rounded-circle w-35px h-35px bg-light-success border-clarity-success" id="kt_drawer_shopping_cart_toggle">
                        <i class="ki-filled ki-handcart text-success fs-3"></i>
                    </div>
                    <!--end::Menu wrapper-->
                </div>
                @endguest
                @role('Konsumen|Onboarding')
                <div class="app-navbar-item">
                    <!--begin::Menu wrapper-->
                    <div class="btn btn-icon rounded-circle w-35px h-35px bg-light-success border-clarity-success" id="kt_drawer_shopping_cart_toggle">
                        <i class="ki-filled ki-handcart text-success fs-3"></i>
                    </div>
                    <!--end::Menu wrapper-->
                </div>
                @endrole
                <!--end::Cart-->
                @auth
                <!--begin::User menu-->
                <div class="app-navbar-item d-none d-xl-flex" id="header_user">
                    <!--begin::Menu wrapper-->
                    <div class="d-none d-xl-flex align-items-center" data-kt-menu-trigger="{default: 'click', lg: 'click'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <!--begin:Info-->
                        <div class="text-end d-none d-sm-flex flex-column justify-content-center me-3">
                            <span class="text-gray-500 fs-8 fw-bold">
                                Hello
                            </span>
                            @role('Konsumen|Onboarding')
                            <a href="{{ route('profile') }}" wire:navigate class="text-gray-800 text-hover-primary fs-7 fw-bold d-block">
                                {{ auth()->user()->name }}
                            </a>
                            @else
                            <a href="{{ route('admin.profile') }}" wire:navigate class="text-gray-800 text-hover-primary fs-7 fw-bold d-block">
                                {{ auth()->user()->name }}
                            </a>
                            @endif
                        </div>
                        <!--end:Info-->
                        <!--begin::User-->
                        <div class="cursor-pointer symbol symbol symbol-circle symbol-35px symbol-md-40px">
                            <img class="" src="{{ auth()->user()->image }}" alt="user" />
                            <div class="position-absolute translate-middle bottom-0 mb-1 start-100 ms-n1 bg-success rounded-circle h-8px w-8px"></div>
                        </div>
                        <!--end::User-->
                    </div>
                    <!--begin::User account menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                        <!--begin::Menu item-->
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex align-items-center px-3">
                                <!--begin::Avatar-->
                                <div class="symbol symbol-50px me-5">
                                    <img alt="Logo" src="{{ auth()->user()->image }}" />
                                </div>
                                <!--end::Avatar-->
                                <!--begin::Username-->
                                <div class="d-flex flex-column">
                                    <div class="fw-bold d-flex align-items-center fs-5">
                                        {{ auth()->user()->name }} 
                                        <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2">
                                            {{ auth()->user()->getRoleNames()->first() }}
                                        </span>
                                    </div>
                                    <div class="text-truncate">
                                        <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">
                                            {{ \Illuminate\Support\Str::limit(auth()->user()->email, 23, $end='...') }}
                                        </a>
                                    </div>
                                </div>
                                <!--end::Username-->
                            </div>
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu separator-->
                        <div class="separator my-2"></div>
                        <!--end::Menu separator-->
                        @role('Konsumen|Onboarding')
                        <!--begin::Menu item-->
                        <div class="menu-item px-5">
                            <a href="{{ route('profile') }}" wire:navigate class="menu-link px-5">Profil Saya</a>
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-5">
                            <a href="{{ route('consumer.transaction') }}" wire:navigate class="menu-link px-5">
                                <span class="menu-text">Transaksi Saya</span>
                                <span class="menu-badge">
                                    <span class="badge badge-light-danger badge-circle fw-bold fs-7">3</span>
                                </span>
                            </a>
                        </div>
                        @else
                        <div class="menu-item px-5">
                            <a href="{{ route('admin.profile') }}" wire:navigate class="menu-link px-5">Profil Saya</a>
                        </div>
                        @endif
                        <!--end::Menu item-->
                        <!--begin::Menu separator-->
                        <div class="separator my-2"></div>
                        <!--end::Menu separator-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-5">
                            <a wire:click="logout" class="menu-link px-5">Keluar</a>
                        </div>
                        <!--end::Menu item-->
                    </div>
                    <!--end::User account menu-->
                    <!--end::Menu wrapper-->
                </div>
                <!--end::User menu-->
                @endauth
                @guest
                <a href="{{ route('login') }}" wire:navigate class="app-navbar-item d-none d-md-flex">
                    <!--begin::Menu wrapper-->
                    <div class="btn btn-sm btn-outline btn-outline-dark">
                        Masuk
                    </div>
                    <!--end::Menu wrapper-->
                </a>
                <a href="{{ route('sign-up') }}" wire:navigate class="app-navbar-item d-none d-md-flex">
                    <!--begin::Menu wrapper-->
                    <div class="btn btn-sm btn-light-dark">
                        Daftar
                    </div>
                    <!--end::Menu wrapper-->
                </a>
                @endguest
            </div>
            <!--end::Navbar-->
        </div>
        <!--end::Header wrapper-->
    </div>
    <!--end::Header container-->
</div>