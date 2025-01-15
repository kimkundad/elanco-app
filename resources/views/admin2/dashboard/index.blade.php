@extends('admin2.layouts.template')

@section('title')
    <title>วงษ์พาณิชย์รีไซเคิล ระยอง จำกัด</title>
    <meta name="description" content=" รายละเอียด วงษ์พาณิชย์รีไซเคิล ระยอง จำกัด">
@stop
@section('stylesheet')

@stop('stylesheet')

@section('content')

    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">

                    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                        <div class="col-xl-8">
                            <div class="card card-flush ">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="card-title">Most Popular Courses</h3>
                                </div>
                                <div class="card-body pt-6"></div>
                            </div>
                            <div class="card card-flush  mt-10">
                                <div class="card-body pt-6"></div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card card-flush h-md-100">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="card-title align-items-start flex-column">Most Popular Courses</h3>
                                    <div class="card-toolbar">
										<a href="#" class="btn btn-sm btn-light">All Courses</a>
									</div>
                                </div>
                                <div class="card-body pt-6">

                                    @foreach($objs as $u)
                                    <div class="d-flex justify-content-between">
														<!--begin::Wrapper-->
														<div class="d-flex align-items-center me-3">
															<!--begin::Logo-->
															<img src="{{ $u->course_img }}" class="me-4 w-75px" alt="">
															<!--end::Logo-->
															<!--begin::Section-->
															<div class="flex-grow-1">
																<!--begin::Text-->
																<a href="#" class="text-gray-800 text-hover-primary fs-8 fw-bold lh-0">{{ $u->course_title }}</a>
																<!--end::Text-->
															</div>
															<!--end::Section-->
														</div>
														<!--end::Wrapper-->
														<!--begin::Statistics-->
														<div class="">
															<a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
																<!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
																<span class="svg-icon svg-icon-2">
																	<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																		<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor"></rect>
																		<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor"></path>
																	</svg>
																</span>
																<!--end::Svg Icon-->
															</a>
														</div>
														<!--end::Statistics-->
													</div>
                                    <div class="separator separator-dashed my-4"></div>
                                    @endforeach


                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->

    </div>

@endsection

@section('scripts')


@stop('scripts')
