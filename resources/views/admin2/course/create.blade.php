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


                    <div class="card mb-5 mb-xl-10">
                        <div class="card-header border-0 cursor-pointer">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold text-gray-800">Add New Course</span>
                                    <span class="text-gray-400 mt-1 fw-semibold fs-7">Create an engaging quiz course to
                                        enhance learning and assessment.</span>
                                </h3>
                            </div>
                            <!--end::Card title-->
                        </div>

                        <div id="kt_account_settings_profile_details" class="collapse show">
                            <!--begin::Form-->
                            <form id="kt_account_profile_details_form" class="form">
                                <!--begin::Card body-->
                                <div class="card-body border-top p-9">
                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Avatar</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8">
                                            <!--begin::Image input-->
                                            <div class="image-input image-input-outline" data-kt-image-input="true"
                                                style="background-image: url('{{ url('img/Mask@1.5x.png') }}')">
                                                <!--begin::Preview existing avatar-->
                                                <div class="image-input-wrapper w-220px h-140px"
                                                    style="background-image: url({{ url('img/Mask@1.5x.png') }})"></div>
                                                <!--end::Preview existing avatar-->
                                                <!--begin::Label-->
                                                <label
                                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                    data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                                    title="Change avatar">
                                                    <i class="bi bi-pencil-fill fs-7"></i>
                                                    <!--begin::Inputs-->
                                                    <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                                                    <input type="hidden" name="avatar_remove" />
                                                    <!--end::Inputs-->
                                                </label>
                                                <!--end::Label-->
                                                <!--begin::Cancel-->
                                                <span
                                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                    data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                                    title="Cancel avatar">
                                                    <i class="bi bi-x fs-2"></i>
                                                </span>
                                                <!--end::Cancel-->
                                                <!--begin::Remove-->
                                                <span
                                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                    data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                                    title="Remove avatar">
                                                    <i class="bi bi-x fs-2"></i>
                                                </span>
                                                <!--end::Remove-->
                                            </div>
                                            <!--end::Image input-->
                                            <!--begin::Hint-->
                                            <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
                                            <!--end::Hint-->
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->

                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Course
                                            Title</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8 fv-row">
                                            <input type="text" name="course_title"
                                                class="form-control form-control-lg form-control-solid"
                                                placeholder="Title here..."
                                                value="{{ old('course_title') ? old('course_title') : '' }}" />
                                        </div>
                                        <!--end::Col-->
                                    </div>

                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Public
                                            Preview</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8 fv-row">
                                            <textarea name="course_preview" placeholder="Course Preview..." class="form-control" data-kt-autosize="true">{{ old('course_preview') ? old('course_preview') : '' }}</textarea>
                                        </div>
                                        <!--end::Col-->
                                    </div>


                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Public
                                            Status</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8">
                                            <!--begin::Row-->
                                            <div class="row">
                                                <!--begin::Col-->
                                                <div class="col-lg-6 fv-row">
                                                    <select class="form-select" name="status" aria-label="Select example">
                                                        <option value="0">Hide</option>
                                                        <option value="1">Show</option>
                                                    </select>
                                                </div>
                                                <!--end::Col-->
                                                <!--begin::Col-->
                                                <div class="col-lg-6 fv-row">
                                                    <input type="text" name="duration"
                                                        class="form-control form-control-lg form-control-solid"
                                                        placeholder="Duration (Minutes) 30 Min"
                                                        value="{{ old('duration') ? old('duration') : '' }}" />
                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Row-->
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->

                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                            <span class="required">Link Media</span>
                                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                title="vimeo url"></i>
                                        </label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8 fv-row">
                                            <input type="text" name="url_video"
                                                class="form-control form-control-lg form-control-solid"
                                                placeholder="Url Video ..."
                                                value="{{ old('url_video') ? old('url_video') : '' }}" />
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->



                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Course View</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8 fv-row">
                                            <!--begin::Options-->
                                            <div class="d-flex align-items-center mt-3">
                                                <!--begin::Option-->
                                                <label
                                                    class="form-check form-check-custom form-check-inline form-check-solid me-5">
                                                    <input class="form-check-input" name="Highlight" type="checkbox"
                                                        value="1" />
                                                    <span class="fw-semibold ps-2 fs-6">Highlight</span>
                                                </label>
                                                <!--end::Option-->
                                                <!--begin::Option-->
                                                <label
                                                    class="form-check form-check-custom form-check-inline form-check-solid">
                                                    <input class="form-check-input" name="Featured" type="checkbox"
                                                        value="2" />
                                                    <span class="fw-semibold ps-2 fs-6">Featured</span>
                                                </label>
                                                <!--end::Option-->
                                            </div>
                                            <!--end::Options-->
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->
                                    <br><br>
                                    <div class="card-title m-0">
                                        <h3 class="fw-bold fs-5 m-0">Course Settings Link</h3>
                                    </div>
                                    <div class="separator separator-dashed my-6"></div>


                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label  fw-semibold fs-6"></label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8">
                                            <!--begin::Row-->
                                            <div class="row">
                                                <!--begin::Col-->
                                                <div class="col-lg-6 fv-row">
                                                    <label class="col-form-label  fw-semibold fs-6">Quiz</label>
                                                    <select class="form-select" name="status"
                                                        aria-label="Select example">
                                                        <option value="" selected>Quiz ID</option>
                                                        @if ($quiz)
                                                            @foreach ($quiz as $u)
                                                                <option value="{{ $u->id }}">{{ $u->quiz_id }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <!--end::Col-->
                                                <!--begin::Col-->
                                                <div class="col-lg-6 fv-row">
                                                    <label class="col-form-label  fw-semibold fs-6">Survey</label>
                                                    <select class="form-select" name="status"
                                                        aria-label="Select example">
                                                        <option value="" selected>Survey ID</option>
                                                        @if ($survey)
                                                            @foreach ($survey as $u)
                                                                <option value="{{ $u->id }}">{{ $u->survey_id }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Row-->
                                        </div>
                                        <!--end::Col-->
                                    </div>


                                    <br>
                                    <div class="card-title m-0">
                                        <h3 class="fw-bold fs-5 m-0">Preview Setting</h3>
                                    </div>
                                    <div class="separator separator-dashed my-6"></div>

                                    <div class="d-flex justify-content-between">

                                        <div>
                                            <label class="col-form-label  fw-semibold fs-6">County</label>

                                            @if($countries)
                                                @foreach($countries as $country)

                                                    <label class="form-check form-check-custom form-check-solid align-items-start">
														<!--begin::Input-->
														<input class="form-check-input me-3" type="checkbox" name="countries[]" value="{{ $country->id }}" />
														<!--end::Input-->
														<!--begin::Label-->
														<span class="form-check-label d-flex flex-column align-items-start">
															<span class="fw-bold fs-5 mb-0">{{ $country->name }}</span>
														</span>
														<!--end::Label-->
													</label>
                                                    <div class="separator separator-dashed my-6"></div>
                                                @endforeach
                                            @endif

                                        </div>

                                        <div></div>

                                        <div></div>

                                        <div></div>

                                    </div>



                                </div>
                                <!--end::Card body-->
                                <!--begin::Actions-->
                                <div class="card-footer d-flex justify-content-end py-6 px-9">
                                    <button type="reset"
                                        class="btn btn-light btn-active-light-primary me-2">Discard</button>
                                    <button type="submit" class="btn btn-primary"
                                        id="kt_account_profile_details_submit">Save Changes</button>
                                </div>
                                <!--end::Actions-->
                            </form>
                            <!--end::Form-->
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
