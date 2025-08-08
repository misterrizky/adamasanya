<!DOCTYPE html>
<!--
Author: CV. Yada Ekidanta
Product Name: Yada Ekidanta Super Apps
Contact: hello@yadaekidanta.com
Follow: www.twitter.com/yadaekidanta
Dribbble: www.dribbble.com/yadaekidanta
Like: www.facebook.com/yadaekidanta
-->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<!--begin::Head-->
	<x-partials.style/>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="app-blank">
		<!--begin::Root-->
		<div class="d-flex flex-column flex-root" id="kt_app_root">
			<!--begin::Page -->
			<div class="d-flex flex-column flex-lg-row flex-column-fluid">
				<style>body { background-image: url("{{asset('media/auth/bg10.jpeg')}}"); } [data-bs-theme="dark"] body { background-image: url("{{asset('media/auth/bg10-dark.jpeg')}}"); }</style>
				<!--begin::Authentication - Sign-in -->
				<div class="d-flex flex-column flex-lg-row flex-column-fluid">
					<!--begin::Aside-->
					<div class="d-flex flex-lg-row-fluid">
						<!--begin::Content-->
						<div class="d-flex flex-column flex-center pb-0 pb-lg-10 p-10 w-100">
							<!--begin::Image-->
							<img class="theme-light-show mx-auto mw-100 w-150px w-lg-300px mb-10 mb-lg-20" src="{{asset('media/illustrations/welcome.png')}}" alt="" />
							<img class="theme-dark-show mx-auto mw-100 w-150px w-lg-300px mb-10 mb-lg-20" src="{{asset('media/illustrations/welcome-dark.png')}}" alt="" />
							<!--end::Image-->
							<!--begin::Title-->
							<h1 class="text-gray-800 fs-2qx fw-bold text-center mb-7">{{ config('app.name') }}</h1>
							<!--end::Title-->
							<!--begin::Text-->
							<div class="text-gray-600 fs-base text-center fw-semibold">
								Adamasanya destinasi para pencinta fotografi dan teknologi, <br/>
								adalah surga bagi semua yang mencari kamera berkualitas tinggi <br/>
								dan iPhone terbaik dengan harga yang sungguh menakjubkan.
							</div>
							<!--end::Text-->
						</div>
						<!--end::Content-->
					</div>
					<!--begin::Aside-->
					{{ $slot }}
				</div>
			</div>
			<!--end::Page -->
		</div>
		<!--end::Root-->
		<livewire:modal.toc/>
		<!--begin::Javascript-->
		<x-partials.script/>
		<!--end::Javascript-->
	</body>
	<!--end::Body-->
</html>