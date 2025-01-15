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


                    <div class="card">
                        <div class="card-header border-0 pt-6">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold text-gray-800">Course List</span>
                                    <span class="text-gray-400 mt-1 fw-semibold fs-7">Explore and manage all courses
                                        featuring interactive quizzes for effective learning.</span>
                                </h3>
                            </div>
                            <!--begin::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar">
                                <!--begin::Search-->
                                <div class="d-flex align-items-center position-relative my-1">
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                    <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2"
                                                rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor">
                                            </rect>
                                            <path
                                                d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                fill="currentColor"></path>
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                    <input type="text" data-kt-customer-table-filter="search"
                                        class="form-control form-control-solid w-250px ps-15"
                                        placeholder="Search Customers">
                                </div>
                                <!--end::Search-->
                                <a href="{{ url('admin/course/create') }}">
                                    <img src={{ url('img/add.svg') }} style="width: 65px; margin-top:10px" />
                                </a>
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <div class="card-body pt-0">

                            <div class="table-responsive">
                                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_customers_table">
                                    <!--begin::Table head-->
                                    <thead>
                                        <!--begin::Table row-->
                                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                            <th class="w-10px pe-2"></th>
                                            <th class="min-w-125px">Course</th>
                                            <th class="">Ratting</th>
                                            <th class="">Enrolled</th>
                                            <th class=""></th>
                                            <th class="">Category</th>
                                            <th class="">CE</th>
                                            <th class="">Expire Date</th>
                                            <th class="">Quiz/Survey</th>
                                            <th class=""></th>
                                            <th class=" min-w-70px">Actions</th>
                                        </tr>
                                        <!--end::Table row-->
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody class="fw-semibold text-gray-600">
                                        @if ($objs)
                                            @foreach ($objs as $key => $u)
                                                <tr>
                                                    <!--begin::Checkbox-->
                                                    <td>
                                                        {{ $objs->firstItem() + $key }}
                                                    </td>
                                                    <!--end::Checkbox-->
                                                    <!--begin::Name=-->
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="symbol symbol-50px me-3">
                                                                <img src="{{ $u->course_img }}" class=""
                                                                    alt="">
                                                            </div>
                                                            <div class="d-flex justify-content-start flex-column"
                                                                style="width: 250px">
                                                                <a href="{{ url('/') }}"
                                                                    class="text-gray-800 fw-bold text-hover-primary mb-1 fs-7">{{ $u->course_title }}</a>

                                                            </div>
                                                        </div>

                                                    </td>
                                                    <!--end::Name=-->
                                                    <!--begin::Email=-->
                                                    <td>
                                                        {{ number_format($u->ratting, 1) }}
                                                    </td>
                                                    <!--end::Email=-->
                                                    <!--begin::Company=-->
                                                    <td>{{ $u->enrolled_count }}</td>
                                                    <!--end::Company=-->
                                                    <!--begin::Payment method=-->
                                                    <td>
                                                        <div class="symbol-group symbol-hover flex-nowrap">
                                                            @foreach ($u->countries as $country)
                                                                <div class="symbol symbol-25px symbol-circle"
                                                                    data-bs-toggle="tooltip" aria-label="Melody Macy"
                                                                    data-kt-initialized="1">
                                                                    <img alt="Pic" src="{{ $country->img }}">
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                    <!--end::Payment method=-->
                                                    <!--begin::Date=-->
                                                    <td><span
                                                            class="badge badge-light-success fw-bold px-4 py-3">{{ $u->mainCategories[0]->name }}</span>
                                                    </td>
                                                    <td>{{ $u->ce_count }}</td>
                                                    <td>{{ $u->quiz->expire_date ?? 'No Expiry' }}</td>
                                                    <td>
                                                        <div style="display: flex">
                                                            @if ($u->id_quiz)
                                                                <div class="symbol symbol-20px symbol-circle">
                                                                    <img src="{{ url('img/quiz.png') }}"
                                                                        class="symbol-30px" />
                                                                </div>
                                                            @endif
                                                            @if ($u->survey_id)
                                                                <div class="symbol symbol-20px symbol-circle">
                                                                    <img src="{{ url('img/Edit@2x.png') }}"
                                                                        class="symbol-30px" />
                                                                </div>
                                                            @endif

                                                            {{-- <img src="{{ url('img/Edit@2x.png') }}" class="img-quiz"  /> --}}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div
                                                            class="form-check form-switch form-check-custom form-check-solid">
                                                            <input class="form-check-input" type="checkbox" value=""
                                                                id="allowchanges" {{ $u->status ? 'checked' : '' }} />
                                                        </div>
                                                    </td>
                                                    <!--end::Date=-->
                                                    <!--begin::Action=-->
                                                    <td class="text-center">
                                                        <div class="m-0">
                                                            <!--begin::Menu toggle-->
                                                            <button
                                                                class="btn btn-icon btn-color-gray-400 btn-active-color-primary me-n4"
                                                                data-kt-menu-trigger="click"
                                                                data-kt-menu-placement="bottom-end"
                                                                data-kt-menu-overflow="true">
                                                                <!--begin::Svg Icon | path: icons/duotune/general/gen023.svg-->
                                                                <span class="svg-icon svg-icon-1">
                                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <rect opacity="0.3" x="2" y="2" width="20"
                                                                            height="20" rx="4"
                                                                            fill="currentColor" />
                                                                        <rect x="11" y="11" width="2.6" height="2.6"
                                                                            rx="1.3" fill="currentColor" />
                                                                        <rect x="15" y="11" width="2.6" height="2.6"
                                                                            rx="1.3" fill="currentColor" />
                                                                        <rect x="7" y="11" width="2.6" height="2.6"
                                                                            rx="1.3" fill="currentColor" />
                                                                    </svg>
                                                                </span>
                                                                <!--end::Svg Icon-->
                                                            </button>
                                                            <!--end::Menu toggle-->
                                                            <!--begin::Menu 2-->
                                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px"
                                                                data-kt-menu="true">

                                                                <!--end::Menu separator-->
                                                                <!--begin::Menu item-->
                                                                <div class="menu-item px-3">
                                                                    <a href="#" class="menu-link px-3">Preview</a>
                                                                </div>
                                                                <!--end::Menu item-->
                                                                <!--begin::Menu item-->
                                                                <div class="menu-item px-3">
                                                                    <a href="#" class="menu-link px-3">Edit
                                                                        Course</a>
                                                                </div>
                                                                <!--end::Menu item-->

                                                                <!--end::Menu item-->
                                                                <!--begin::Menu item-->
                                                                <div class="menu-item px-3">
                                                                    <a href="#" class="menu-link px-3">Delete</a>
                                                                </div>
                                                                <!--end::Menu item-->

                                                            </div>
                                                            <!--end::Menu 2-->
                                                        </div>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
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
