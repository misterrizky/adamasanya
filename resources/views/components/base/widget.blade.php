{{-- resources/views/components/widget.blade.php --}}

@props([
    'type' => 'default',
    'title' => '',
    'subtitle' => '',
    'value' => '',
    'time' => '',
    'description' => '',
    'name' => '',
    'position' => '',
    'avatar' => '',
    'icon' => '',
    'iconClass' => '',
    'bgColor' => '',
    'textColor' => '',
    'progress' => 0,
    'chart' => false,
    'chartColor' => 'success',
    'href' => '#',
    'borderDashed' => false,
    'prefix' => '',
    'countUp' => false,
    'countUpValue' => 0,
])

@php
    $cardClasses = 'card-xl-stretch mb-8';
    $cardClasses .= $bgColor ? ' bg-'.$bgColor : '';
    $cardClasses .= $textColor ? ' text-'.$textColor : '';
    $cardClasses .= $borderDashed ? ' border border-gray-300 border-dashed rounded' : '';
    
    $hoverable = $type === 'shopping-cart' ? ' hoverable' : '';
@endphp

@if($type === 'meeting')
<div class="card bgi-no-repeat bgi-position-y-top bgi-position-x-end statistics-widget-1 {{ $cardClasses }}">
    <div class="card-body">
        <a href="{{ $href }}" wire:navigate class="card-title fw-bold text-muted text-hover-primary fs-4">{{ $title }}</a>
        <div class="fw-bold text-primary my-6">{{ $time }}</div>
        <p class="text-gray-900-75 fw-semibold fs-5 m-0">{{ $description }}</p>
    </div>
</div>

@elseif($type === 'profile')
<a href="{{ $href }}" wire:navigate class="card hover-elevate-up shadow-sm parent-hover {{ $cardClasses }} {{ $attributes->get('class')}}">
    <div class="card-body d-flex align-items-center pt-3 pb-0">
        <div class="d-flex flex-column flex-grow-1 py-2 py-lg-13 me-2">
            <div class="fw-bold text-gray-900 fs-4 mb-2 text-hover-primary">{{ $name }}</div>
            <span class="fw-semibold text-muted fs-5">{{ $position }}</span>
        </div>
        <img src="{{ $avatar }}" alt="{{ $name }}" class="align-self-end h-100px" />
    </div>
</a>

@elseif($type === 'sales-chart')
<div class="card {{ $cardClasses }} {{ $attributes->get('class')}}">
    <div class="card-body d-flex flex-column p-0">
        <div class="d-flex flex-stack flex-grow-1 card-p">
            <div class="d-flex flex-column me-2">
                <a href="{{ $href }}" wire:navigate class="text-gray-900 text-hover-primary fw-bold fs-3">{{ $title }}</a>
                <span class="text-muted fw-semibold mt-1">{{ $subtitle }}</span>
            </div>
            <span class="symbol symbol-50px">
                <span class="symbol-label fs-5 fw-bold bg-light-{{ $chartColor }} text-{{ $chartColor }}">+{{ $value }}</span>
            </span>
        </div>
        <div class="statistics-widget-3-chart card-rounded-bottom" data-kt-chart-color="{{ $chartColor }}" style="height: 150px"></div>
    </div>
</div>

@elseif($type === 'sales-change')
<div class="card {{ $cardClasses }}">
    <div class="card-body p-0">
        <div class="d-flex flex-stack card-p flex-grow-1">
            <span class="symbol symbol-50px me-2">
                <span class="symbol-label bg-light-{{ $chartColor }}">
                    <i class="ki-outline ki-{{ $icon }} fs-2x text-{{ $chartColor }}"></i>
                </span>
            </span>
            <div class="d-flex flex-column text-end">
                <span class="text-gray-900 fw-bold fs-2">+{{ $value }}</span>
                <span class="text-muted fw-semibold mt-1">{{ $title }}</span>
            </div>
        </div>
        <div class="statistics-widget-4-chart card-rounded-bottom" data-kt-chart-color="{{ $chartColor }}" style="height: 150px"></div>
    </div>
</div>

@elseif($type === 'shopping-cart')
<a href="{{ $href }}" class="card bg-{{ $bgColor }} hoverable {{ $cardClasses }}">
    <div class="card-body">
        <i class="ki-outline ki-{{ $icon }} {{ $textColor ? 'text-'.$textColor : '' }} fs-2x ms-n1"></i>
        <div class="{{ $textColor ? 'text-'.$textColor : '' }} fw-bold fs-2 mb-2 mt-5">{{ $title }}</div>
        <div class="fw-semibold {{ $textColor ? 'text-'.$textColor : '' }}">{{ $description }}</div>
    </div>
</a>

@elseif($type === 'progress')
<div class="card bg-light-{{ $bgColor }} {{ $cardClasses }}">
    <div class="card-body my-3">
        <a href="{{ $href }}" wire:navigate class="card-title fw-bold text-{{ $bgColor }} fs-5 mb-3 d-block">{{ $title }}</a>
        <div class="py-1">
            <span class="text-gray-900 fs-1 fw-bold me-2">{{ $progress }}%</span>
            <span class="fw-semibold text-muted fs-7">{{ $subtitle }}</span>
        </div>
        <div class="progress h-7px bg-{{ $bgColor }} bg-opacity-50 mt-7">
            <div class="progress-bar bg-{{ $bgColor }}" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>
</div>

@elseif($type === 'statistic')
<div class="{{ $cardClasses }} min-w-125px py-3 px-4 me-6 mb-5">
    <div class="d-flex align-items-center">
        <i class="ki-outline ki-{{ $icon }} fs-3 text-{{ $textColor }} me-2"></i>
        <div class="fs-2 fw-bold" @if($countUp) data-kt-countup="true" data-kt-countup-value="{{ $countUpValue }}" data-kt-countup-prefix="{{ $prefix }}" @endif>
            {{ $prefix }}{{ $value }}
        </div>
    </div>
    <div class="fw-semibold fs-6 text-gray-500">{{ $title }}</div>
</div>

@else
{{-- Default widget --}}
<div class="card {{ $cardClasses }}">
    <div class="card-body">
        <a href="{{ $href }}" wire:navigate class="card-title fw-bold text-muted text-hover-primary fs-4">{{ $title }}</a>
        <div class="fw-bold text-primary my-6">{{ $value }}</div>
        <p class="text-gray-900-75 fw-semibold fs-5 m-0">{{ $description }}</p>
    </div>
</div>
@endif