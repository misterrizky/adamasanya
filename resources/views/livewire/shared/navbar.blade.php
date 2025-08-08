<?php
use function Livewire\Volt\{state};
?>
<nav class="bottom-nav">
    @guest
    <a href="{{ route('home') }}" wire:navigate
    class="bottom-nav-item {{ request()->is('home*') ? 'active pulse pulse-warning' : '' }}">
        <div class="bottom-nav-icon">
            <i class="ki-{{ request()->is('home*') ? 'solid' : 'outline' }}
                ki-{{ request()->is('home*') ? 'like' : 'home' }} 
                fs-3x {{ request()->is('home*') ? 'text-warning' : '' }}"></i>
            <span class="{{ request()->is('home*') ? 'pulse-ring border-5' : '' }}"></span>
        </div>
        <span class="bottom-nav-label {{ request()->is('home*') ? 'text-warning' : '' }}">{{ request()->is('home*') ? 'Buat Kamu' : 'Home' }}</span>
    </a>
    <a href="{{ route('promo') }}" wire:navigate
    class="bottom-nav-item {{ request()->is('promo*') ? 'active pulse pulse-danger' : '' }}">
        <div class="bottom-nav-icon">
            <i class="ki-{{ request()->is('promo*') ? 'solid' : 'outline' }}
                ki-{{ request()->is('promo*') ? 'discount' : 'discount' }}
                fs-3x {{ request()->is('promo*') ? 'text-danger' : '' }}"></i>
            <span class="{{ request()->is('promo*') ? 'pulse-ring border-5' : '' }}"></span>
        </div>
        <span class="bottom-nav-label {{ request()->is('promo*') ? 'text-danger' : '' }}">Promo</span>
    </a>
    <a href="{{ route('login') }}" wire:navigate
    class="bottom-nav-item {{ request()->is('wishlist*') ? 'active pulse pulse-danger' : '' }}">
        <div class="bottom-nav-icon">
            <i class="ki-outline ki-{{ request()->is('wishlist*') ? 'heart' : 'heart' }}
                fs-3x {{ request()->is('wishlist*') ? 'text-danger' : '' }}"></i>
            <span class="{{ request()->is('wishlist*') ? 'pulse-ring border-5' : '' }}"></span>
        </div>
        <span class="bottom-nav-label {{ request()->is('wishlist*') ? 'text-danger' : '' }}">Wishlist</span>
    </a>
    <a href="{{ route('login') }}" wire:navigate
    class="bottom-nav-item {{ request()->is('transaction*') ? 'active pulse pulse-success' : '' }}">
        <div class="bottom-nav-icon">
            <i class="ki-outline ki-{{ request()->is('transaction*') ? 'cheque' : 'cheque' }}
                fs-3x {{ request()->is('transaction*') ? 'text-success' : '' }}"></i>
            <span class="{{ request()->is('transaction*') ? 'pulse-ring border-5' : '' }}"></span>
        </div>
        <span class="bottom-nav-label {{ request()->is('transaction*') ? 'text-success' : '' }}">Transaksi</span>
    </a>
    @endguest
    @role('Konsumen|Onboarding')
    <a href="{{ route('home') }}" wire:navigate
    class="bottom-nav-item {{ request()->is('home*') ? 'active pulse pulse-warning' : '' }}">
        <div class="bottom-nav-icon">
            <i class="ki-{{ request()->is('home*') ? 'solid' : 'outline' }}
                ki-{{ request()->is('home*') ? 'like' : 'home' }} 
                fs-3x {{ request()->is('home*') ? 'text-warning' : '' }}"></i>
            <span class="{{ request()->is('home*') ? 'pulse-ring border-5' : '' }}"></span>
        </div>
        <span class="bottom-nav-label {{ request()->is('home*') ? 'text-warning' : '' }}">{{ request()->is('home*') ? 'Buat Kamu' : 'Home' }}</span>
    </a>
    <a href="{{ route('promo') }}" wire:navigate
    class="bottom-nav-item {{ request()->is('promo*') ? 'active pulse pulse-danger' : '' }}">
        <div class="bottom-nav-icon">
            <i class="ki-{{ request()->is('promo*') ? 'solid' : 'outline' }}
                ki-{{ request()->is('promo*') ? 'discount' : 'discount' }}
                fs-3x {{ request()->is('promo*') ? 'text-danger' : '' }}"></i>
            <span class="{{ request()->is('promo*') ? 'pulse-ring border-5' : '' }}"></span>
        </div>
        <span class="bottom-nav-label {{ request()->is('promo*') ? 'text-danger' : '' }}">Promo</span>
    </a>
    <a href="{{ route('wishlist') }}" wire:navigate
    class="bottom-nav-item {{ request()->is('consumer/wishlist*') ? 'active pulse pulse-danger' : '' }}">
        <div class="bottom-nav-icon">
            <i class="ki-{{ request()->is('consumer/wishlist*') ? 'solid' : 'outline' }}
                ki-{{ request()->is('consumer/wishlist*') ? 'heart' : 'heart' }}
                fs-3x {{ request()->is('consumer/wishlist*') ? 'text-pink' : '' }}"></i>
            <span class="{{ request()->is('consumer/wishlist*') ? 'pulse-ring border-5' : '' }}"></span>
        </div>
        <span class="bottom-nav-label {{ request()->is('consumer/wishlist*') ? 'text-pink' : '' }}">Wishlist</span>
    </a>
    <a href="{{ route('consumer.transaction') }}" wire:navigate
    class="bottom-nav-item {{ request()->is('consumer/transaction*') ? 'active pulse pulse-success' : '' }}">
        <div class="bottom-nav-icon">
            <i class="ki-{{ request()->is('consumer/transaction*') ? 'solid' : 'outline' }}
                ki-{{ request()->is('consumer/transaction*') ? 'cheque' : 'cheque' }}
                fs-3x {{ request()->is('consumer/transaction*') ? 'text-success' : '' }}"></i>
            <span class="{{ request()->is('consumer/transaction*') ? 'pulse-ring border-5' : '' }}"></span>
        </div>
        <span class="bottom-nav-label {{ request()->is('consumer/transaction*') ? 'text-success' : '' }}">Transaksi</span>
    </a>
    @elserole('Super Admin|Owner')
        <a href="{{ route('admin.dashboard') }}" wire:navigate
        class="bottom-nav-item {{ request()->is('admin/dashboard*') ? 'active pulse pulse-warning' : '' }}">
            <div class="bottom-nav-icon">
                <i class="ki-{{ request()->is('admin/dashboard*') ? 'solid' : 'outline' }}
                    ki-{{ request()->is('admin/dashboard*') ? 'like' : 'category' }} 
                    fs-3x {{ request()->is('admin/dashboard*') ? 'text-warning' : '' }}"></i>
                <span class="{{ request()->is('admin/dashboard*') ? 'pulse-ring border-5' : '' }}"></span>
            </div>
            <span class="bottom-nav-label {{ request()->is('admin/dashboard*') ? 'text-warning' : '' }}">{{ request()->is('admin/dashboard*') ? 'Buat Kamu' : 'Dasbor' }}</span>
        </a>

        <a href="{{ route('admin.product') }}" wire:navigate
        class="bottom-nav-item {{ request()->is('admin/product*') ? 'active pulse pulse-danger' : '' }}">
            <div class="bottom-nav-icon">
                <i class="ki-{{ request()->is('admin/product*') ? 'solid' : 'outline' }}
                    ki-{{ request()->is('admin/product*') ? 'basket' : 'basket' }}
                    fs-3x {{ request()->is('admin/product*') ? 'text-danger' : '' }}"></i>
                <span class="{{ request()->is('admin/product*') ? 'pulse-ring border-5' : '' }}"></span>
            </div>
            <span class="bottom-nav-label {{ request()->is('admin/product*') ? 'text-danger' : '' }}">Produk</span>
        </a>

        <a href="{{ route('admin.consumer') }}" wire:navigate
        class="bottom-nav-item {{ request()->is('admin/user/consumer*') ? 'active pulse pulse-primary' : '' }}">
            <div class="bottom-nav-icon">
                <i class="ki-{{ request()->is('admin/user/consumer*') ? 'solid' : 'outline' }}
                    ki-{{ request()->is('admin/user/consumer*') ? 'user' : 'user' }}
                    fs-3x {{ request()->is('admin/user/consumer*') ? 'text-primary' : '' }}"></i>
                <span class="{{ request()->is('admin/user/consumer*') ? 'pulse-ring border-5' : '' }}"></span>
            </div>
            <span class="bottom-nav-label {{ request()->is('admin/user/consumer*') ? 'text-primary' : '' }}">Konsumen</span>
        </a>
        <a href="{{ route('admin.transaction') }}" wire:navigate
        class="bottom-nav-item {{ request()->is('admin/transaction*') ? 'active pulse pulse-success' : '' }}">
            <div class="bottom-nav-icon">
                <i class="ki-{{ request()->is('admin/transaction*') ? 'solid' : 'outline' }}
                    ki-{{ request()->is('admin/transaction*') ? 'cheque' : 'cheque' }}
                    fs-3x {{ request()->is('admin/transaction*') ? 'text-success' : '' }}"></i>
                <span class="{{ request()->is('admin/transaction*') ? 'pulse-ring border-5' : '' }}"></span>
            </div>
            <span class="bottom-nav-label {{ request()->is('admin/transaction*') ? 'text-success' : '' }}">Transaksi</span>
        </a>
    @elserole('Cabang|Pegawai')
        <a href="{{ route('admin.dashboard') }}" wire:navigate
        class="bottom-nav-item {{ request()->is('admin/dashboard*') ? 'active pulse pulse-warning' : '' }}">
            <div class="bottom-nav-icon">
                <i class="ki-{{ request()->is('admin/dashboard*') ? 'solid' : 'outline' }}
                    ki-{{ request()->is('admin/dashboard*') ? 'like' : 'category' }} 
                    fs-3x {{ request()->is('admin/dashboard*') ? 'text-warning' : '' }}"></i>
                <span class="{{ request()->is('admin/dashboard*') ? 'pulse-ring border-5' : '' }}"></span>
            </div>
            <span class="bottom-nav-label {{ request()->is('admin/dashboard*') ? 'text-warning' : '' }}">{{ request()->is('admin/dashboard*') ? 'Buat Kamu' : 'Dashboard' }}</span>
        </a>
        <a href="{{ route('admin.consumer') }}" wire:navigate
        class="bottom-nav-item {{ request()->is('admin/user/consumer*') ? 'active pulse pulse-primary' : '' }}">
            <div class="bottom-nav-icon">
                <i class="ki-{{ request()->is('admin/user/consumer*') ? 'solid' : 'outline' }}
                    ki-{{ request()->is('admin/user/consumer*') ? 'user' : 'user' }}
                    fs-3x {{ request()->is('admin/user/consumer*') ? 'text-primary' : '' }}"></i>
                <span class="{{ request()->is('admin/user/consumer*') ? 'pulse-ring border-5' : '' }}"></span>
            </div>
            <span class="bottom-nav-label {{ request()->is('admin/user/consumer*') ? 'text-primary' : '' }}">Konsumen</span>
        </a>
        @if(Auth::user()->branch_id == 1 || 12)
        <a href="{{ route('admin.product-branch') }}" wire:navigate
        class="bottom-nav-item {{ request()->is('admin/product-branch*') ? 'active pulse pulse-danger' : '' }}">
            <div class="bottom-nav-icon">
                <i class="ki-{{ request()->is('admin/product-branch*') ? 'solid' : 'outline' }}
                    ki-{{ request()->is('admin/product-branch*') ? 'basket' : 'basket' }}
                    fs-3x {{ request()->is('admin/product-branch*') ? 'text-danger' : '' }}"></i>
                <span class="{{ request()->is('admin/product-branch*') ? 'pulse-ring border-5' : '' }}"></span>
            </div>
            <span class="bottom-nav-label {{ request()->is('admin/product-branch*') ? 'text-danger' : '' }}">Produk</span>
        </a>
        <a href="{{ route('admin.transaction') }}" wire:navigate
        class="bottom-nav-item {{ request()->is('admin/transaction*') ? 'active pulse pulse-success' : '' }}">
            <div class="bottom-nav-icon">
                <i class="ki-{{ request()->is('admin/transaction*') ? 'solid' : 'outline' }}
                    ki-{{ request()->is('admin/transaction*') ? 'cheque' : 'cheque' }}
                    fs-3x {{ request()->is('admin/transaction*') ? 'text-success' : '' }}"></i>
                <span class="{{ request()->is('admin/transaction*') ? 'pulse-ring border-5' : '' }}"></span>
            </div>
            <span class="bottom-nav-label {{ request()->is('admin/transaction*') ? 'text-success' : '' }}">Transaksi</span>
        </a>
        @else
        <a href="{{ route('admin.product-branch') }}" wire:navigate
        class="bottom-nav-item {{ request()->is('product*') ? 'active pulse pulse-danger' : '' }}">
            <div class="bottom-nav-icon">
                <i class="ki-{{ request()->is('product*') ? 'solid' : 'outline' }}
                    ki-{{ request()->is('product*') ? 'basket' : 'basket' }}
                    fs-3x {{ request()->is('product*') ? 'text-danger' : '' }}"></i>
                <span class="{{ request()->is('product*') ? 'pulse-ring border-5' : '' }}"></span>
            </div>
            <span class="bottom-nav-label {{ request()->is('product*') ? 'text-danger' : '' }}">Produk</span>
        </a>
        <a href="{{ route('admin.transaction') }}" wire:navigate
        class="bottom-nav-item {{ request()->is('transaction*') ? 'active pulse pulse-success' : '' }}">
            <div class="bottom-nav-icon">
                <i class="ki-{{ request()->is('transaction*') ? 'solid' : 'outline' }}
                    ki-{{ request()->is('transaction*') ? 'cheque' : 'cheque' }}
                    fs-3x {{ request()->is('transaction*') ? 'text-success' : '' }}"></i>
                <span class="{{ request()->is('transaction*') ? 'pulse-ring border-5' : '' }}"></span>
            </div>
            <span class="bottom-nav-label {{ request()->is('transaction*') ? 'text-success' : '' }}">Transaksi</span>
        </a>
        @endif
    @endrole
    @auth
        @role('Konsumen|Onboarding')
        <a href="{{ route('profile') }}" wire:navigate
        class="bottom-nav-item {{ request()->is('consumer/profile*') ? 'active pulse pulse-info' : '' }}">
            <div class="bottom-nav-icon">
                <i class="ki-{{ request()->is('consumer/profile*') ? 'solid' : 'outline' }}
                    ki-{{ request()->is('consumer/profile*') ? 'user-square' : 'user-square' }}
                    fs-3x {{ request()->is('consumer/profile*') ? 'text-info' : '' }}"></i>
                <span class="{{ request()->is('consumer/profile*') ? 'pulse-ring border-5' : '' }}"></span>
            </div>
            <span class="bottom-nav-label {{ request()->is('consumer/profile*') ? 'text-info' : '' }}">Profile</span>
        </a>
        @else
        <a href="{{ route('admin.profile') }}" wire:navigate
        class="bottom-nav-item {{ request()->is('admin/profile*') ? 'active pulse pulse-info' : '' }}">
            <div class="bottom-nav-icon">
                <i class="ki-{{ request()->is('admin/profile*') ? 'solid' : 'outline' }}
                    ki-{{ request()->is('admin/profile*') ? 'user-square' : 'user-square' }}
                    fs-3x {{ request()->is('admin/profile*') ? 'text-info' : '' }}"></i>
                <span class="{{ request()->is('admin/profile*') ? 'pulse-ring border-5' : '' }}"></span>
            </div>
            <span class="bottom-nav-label {{ request()->is('admin/profile*') ? 'text-info' : '' }}">Profile</span>
        </a>
        @endrole
    @endauth
</nav>