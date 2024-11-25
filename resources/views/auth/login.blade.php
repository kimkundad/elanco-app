<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<!--begin::Head-->
	<head>
		<title>Metronic</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="canonical" href="https://preview.keenthemes.com/metronic8" />
		<link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
		<!--begin::Fonts(mandatory for all pages)-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
		<link href="{{ url('home/login/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ url('home/login/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="app-blank app-blank">

		<div class="d-flex flex-column flex-root" id="kt_app_root">
			<!--begin::Authentication - Sign-in -->
			<div class="d-flex flex-column flex-lg-row flex-column-fluid">
				<!--begin::Body-->
				<div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
					<!--begin::Form-->
					<div class="d-flex flex-center flex-column flex-lg-row-fluid">
						<!--begin::Wrapper-->
						<div class="w-lg-500px p-10">
							<!--begin::Form-->
							<form class="form w-100" method="POST" action="{{ route('login') }}">
                                {{ csrf_field() }}
								<!--begin::Heading-->
								<div class=" mb-11">
									<!--begin::Title-->
                                    <img alt="Logo" src="{{ url('img/logo_reangwa@5x-white@3x.png') }}" class="h-60px h-lg-75px">
                                    <br><br>
									<h1 class="text-dark fw-bolder mb-3">Hi Administrator,</h1>
									<!--end::Title-->
									<!--begin::Subtitle-->
									<div class=" text-dark fw-semibold " style="font-size:46px" >Welcome Back</div>
									<!--end::Subtitle=-->
								</div>
								<!--begin::Heading-->


								<!--begin::Input group=-->
								<div class="fv-row mb-8">
									<!--begin::Email-->
									<input type="text" placeholder="Email" name="email" value="{{ old('email') }}" autocomplete="off" class="form-control bg-transparent" />
									<!--end::Email-->
								</div>
								<!--end::Input group=-->
								<div class="fv-row mb-3">
									<!--begin::Password-->
									<input type="password" placeholder="Password" name="password" autocomplete="off" class="form-control bg-transparent" />
									<!--end::Password-->
								</div>
								<!--end::Input group=-->
								{{-- <!--begin::Wrapper-->
								<div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
									<div></div>
									<!--begin::Link-->
									<a href="../../demo1/dist/authentication/layouts/corporate/reset-password.html" class="link-primary">Forgot Password ?</a>
									<!--end::Link-->
								</div> --}}
								<!--end::Wrapper-->
								<!--begin::Submit button-->
								<div class="d-grid mb-10 mt-10">
									<button type="submit" id="kt_sign_in_submit" class="btn btn-dark">
										<!--begin::Indicator label-->
										<span class="indicator-label">Login</span>
										<!--end::Indicator label-->
										<!--begin::Indicator progress-->
										<span class="indicator-progress">Please wait...
										<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
										<!--end::Indicator progress-->
									</button>
								</div>
								<!--end::Submit button-->

								<!--end::Sign up-->
							</form>
							<!--end::Form-->
						</div>
						<!--end::Wrapper-->
					</div>
					<!--end::Form-->
                    <div class="d-flex flex-center ">
                    <p style="color: #737576">Having trouble logging in? Reach out to Superadmin support.</p>
                    </div>
					<!--begin::Footer-->
					<div class="d-flex flex-center flex-wrap px-5 " style="border-top: 1px solid #E4E6EF; margin-top: 20px !important;">


						<!--begin::Links-->
						<div class="d-flex fw-semibold text-primary fs-base mt-5" >

							<a class="px-5"  style="color: #737576">Elanco © 2025 vetlibrary.online All rights reserved.</a>
						</div>
						<!--end::Links-->
					</div>
					<!--end::Footer-->
				</div>
				<!--end::Body-->
				<!--begin::Aside-->
				<div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2" style="background-color: #2a7bc2;">
					<!--begin::Content-->
					<div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">
						<!--end::Logo-->
						<!--begin::Image-->
						<img class="d-none d-lg-block mb-10 " src="{{ url('img/Rectangle@2x.png') }}" alt="" style="width: 100%; margin-top: auto; max-width:650px" />
						<!--end::Image-->


						<!--end::Text-->
					</div>
					<!--end::Content-->
				</div>
				<!--end::Aside-->
			</div>
			<!--end::Authentication - Sign-in-->
		</div>

	</body>
	<!--end::Body-->
</html>
