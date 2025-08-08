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
	<body id="kt_app_body" data-kt-app-header-fixed="true" data-kt-app-header-fixed-mobile="true" class="app-default">
		<!--begin::App-->
		<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
			<!--begin::Page-->
			<div class="app-page flex-column flex-column-fluid" id="kt_app_page">
				<!--begin::Header-->
				<livewire:shared.header/>
				<!--end::Header-->
				<!--begin::Wrapper-->
				<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
					<!--begin::Hero-->
					<!--end::Hero-->
					<!--begin::Main-->
					<div class="app-container container-fluid d-flex flex-row-fluid">
						<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
							{{ $slot }}
							<!--begin::Footer-->
							<livewire:shared.navbar/>
							<livewire:shared.footer/>
							<!--end::Footer-->
						</div>
					</div>
					<!--end:::Main-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Page-->
		</div>
		<!--end::App-->
		<!--begin::Drawers-->
		<!--begin::Activities drawer-->
		@auth
		<livewire:drawers.activity/>
		<!--end::Activities drawer-->
		<!--begin::Chat drawer-->
		<livewire:drawers.chat/>
		<!--end::Chat drawer-->
		@endauth
		<!--begin::Cart drawer-->
		<livewire:drawers.cart/>
		<!--end::Cart drawer-->
		<!--end::Drawers-->
		<!--begin::Scrolltop-->
		<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
			<i class="ki-outline ki-arrow-up"></i>
		</div>
		<!--end::Scrolltop-->
		<!--begin::Modals-->
		<livewire:modal.toc/>
		<!--end::Modals-->
		<!--begin::Javascript-->
		<x-partials.script/>
		@yield('custom_js')
		<!--end::Javascript-->
	</body>
	<!--end::Body-->
</html>