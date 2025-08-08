<?php

use App\Models\Product;
use function Livewire\Volt\{state};

state('product')->locked();
state(['data' => fn() => Product::where('slug', $this->product)->first()]);

?>
<style>
    .animate-float {
        animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
        100% { transform: translateY(0px); }
    }
    .bg-gradient {
        background-size: 200% 200%;
        animation: gradient 15s ease infinite;
    }
    @keyframes gradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
</style>
<div class="hero-section py-10 py-lg-15">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-10 mb-lg-0">
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('home') }}" wire:navigate class="text-gray-600 hover-text-primary me-2">
                        <i class="ki-outline ki-arrow-left fs-2"></i>
                    </a>
                    <span class="text-gray-600">Kembali ke Home</span>
                </div>
                <h1 class="display-4 fw-bold text-gray-800 mb-3">{{ $this->data->name }}</h1>
                @if($this->data->description)
                    <p class="fs-3 text-gray-600 mb-8">{{ $this->data->description }}</p>
                @endif
            </div>
            <div class="col-lg-6">
                <img src="{{ $this->data->image ?? asset('media/illustrations/sigma-1/17.png') }}" alt="{{ $this->data->name }}" class="img-fluid rounded-4 animate-float w-75 float-end">
            </div>
        </div>
    </div>
</div>