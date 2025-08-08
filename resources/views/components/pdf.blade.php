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
	<x-partials.styles/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="app-blank">
		<!--begin::Root-->
		<div class="d-flex flex-column flex-root" id="kt_app_root">
			<!--begin::Page -->
			<div class="d-flex flex-column flex-lg-row flex-column-fluid">
				<div class="d-flex flex-column flex-lg-row flex-column-fluid">
					<table class="table align-middle table-row-dashed fs-6 gy-5" id="table_block">
						<thead>
							<tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
								<th class="min-w-100px">Nama Blok</th>
								<th class="text-end min-w-100px">Total Bangunan</th>
							</tr>
						</thead>
						<tbody class="text-gray-600 fw-semibold">
							@foreach ($data as $item)
							<tr>
								<td>{{ $item->name }}</td>
								<td class="text-end">{{ number_format($item->house->count()) }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
			<!--end::Page -->
		</div>
		<!--end::Root-->
	</body>
	<!--end::Body-->
</html>