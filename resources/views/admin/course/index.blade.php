@extends('admin.layouts.template')

@section('title')
    <title>Elanco</title>
@stop
@section('stylesheet')
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="base-url" content="{{ url('/') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
        <link
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
        rel="stylesheet">
    <style>
        body,
        html {
            font-family: 'Prompt', sans-serif !important;
        }

        .title {
            font-size: 14px;
            line-height: 1.2;
            font-weight: 600;
        }

        .page__col {
            padding: 0 24px 44px;
        }

        .widget__preview {
            width: 86px;
            height: 54px;
        }

        .widget {
            padding: 28px;
        }

        .widget__item:not(:last-child) {
            margin-bottom: 15px;
        }

        .products__cell:first-child {
            /* width: 20px; */
            padding: 0;
            font-size: 14px;
        }

        .title {
            font-size: 14px;
            line-height: 1.2;
            font-weight: 400;
            margin-bottom: 0px !important;
        }

        .products__preview:before {
            background: #e7faff00;
        }

        .products__preview {
            height: auto;
        }

        .products__pic {
            border-radius: 8px;
        }


        .getheader {
            display: flex;
            justify-content: space-between;
            padding-bottom: 10px;
            margin-top: 10px
        }

        .header-item {
            font-size: 14px;
            color: #333;
            display: flex;
            align-items: center;
        }

        .header-item .icon {
            width: 16px;
            height: 16px;
            margin-right: 8px;
        }

        .details {
            display: flex;
            flex-direction: column;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .label {
            font-weight: bold;
            color: #555;
            font-size: 13px;
        }

        .value {
            color: #333;
            font-size: 13px;
            text-align: right
        }

        .value.public {
            color: green;
            font-weight: bold;
        }

        .img-quiz {
            width: 20px;
            height: 20px;
            margin-right: 5px
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 25px;
        }

        /* Hidden Input */
        .switch__input {
            display: none;
        }

        /* Slider */
        .switch__label {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            border-radius: 25px;
            transition: background-color 0.3s;
        }

        /* Circle */
        .switch__label:before {
            content: '';
            position: absolute;
            height: 21px;
            width: 21px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            border-radius: 50%;
            transition: transform 0.3s;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* Active State */
        .switch__input:checked+.switch__label {
            background-color: #4caf50;
        }

        .switch__input:checked+.switch__label:before {
            transform: translateX(25px);
        }
    </style>

@stop('stylesheet')

@section('content')


    <div class="page__content" style="margin-top: 50px; padding: 0">
        <div class="page__stat ">


            <div class="page__stat page__stat_pt32">
                <div class="sorting">
                    <div class="sorting__row">
                        <div class="sorting__col">
                            <div class="products__title h6 mobile-hide">Course List</div>
                            <div class="products__info caption mobile-hide" style="color: #B2B3BD; font-weight: 400;">
                                Explore and manage all
                                courses featuring interactive quizzes for effective learning.</div>
                        </div>
                        <div class="sorting__col">
                            <div class="sorting__line">
                                <div class="sorting__search">
                                    <form method="GET" action="{{ url('admin/course') }}">
                                        <button type="submit" class="sorting__open">
                                            <svg class="icon icon-search">
                                                <use xlink:href="#icon-search"></use>
                                            </svg>
                                        </button>
                                        <input class="sorting__input" type="text" name="search" placeholder="Search"
                                            value="{{ request('search') }}">
                                    </form>
                                </div>
                                <div class="sorting__actions">
                                    <a href="{{ url('admin/course/create') }}">
                                        <img src={{ url('img/add.svg') }} style="width: 65px" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="products products_main">
                    <div class="products__table">
                        <div class="products__row products__row_head productsRowx">
                            <div class="products__cell">
                            </div>
                            <div class="products__cell">Course</div>
                            <div class="products__cell">Ratting</div>
                            <div class="products__cell">Enrolled</div>
                            <div class="products__cell"></div>
                            <div class="products__cell">Category</div>
                            <div class="products__cell">CE</div>
                            <div class="products__cell">Expire Date</div>
                            <div class="products__cell">Quiz/Survey</div>
                            <div class="products__cell"></div>
                            <div class="products__cell"></div>
                        </div>

                        @if ($objs)
                            @foreach ($objs as $key => $u)
                                <div class="products__row productsRow">
                                    <div class="products__cell">
                                        <div class="products__payment">{{ $objs->firstItem() + $key }}</div>
                                    </div>
                                    <div class="products__cell">
                                        <a class="products__item" href="#">
                                            <div class="products__preview"><img class="products__pic"
                                                    src="{{ $u->course_img }}" alt=""></div>
                                            <div class="products__details" style="max-width: 250px;">
                                                <div class="products__title title">{{ $u->quiz ? $u->quiz->quiz_id : 'No Quiz Available' }}</div>
                                                <div class="products__info caption color-gray">{{ $u->course_title }}</div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="products__cell">
                                        <div class="products__payment">{{ number_format($u->ratting, 1) }} </div>
                                    </div>
                                    <div class="products__cell">
                                        <div class="products__payment">{{ $u->enrolled_count }}</div>
                                    </div>
                                    <div class="products__cell">
                                        <div style="display:flex">
                                            @foreach ($u->countries as $country)
                                                <img src="{{ $country->img }}" class="Flag_icon"
                                                    alt="{{ $country->name }}" />
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="products__cell">
                                        <div class="products__status caption bg-green">{{ $u->mainCategories[0]->name }}
                                        </div>
                                    </div>

                                    <div class="products__cell">
                                        <div class="products__payment">{{ $u->ce_count }}</div>
                                    </div>
                                    <div class="products__cell">
                                        <div class="products__payment">{{ $u->quiz->expire_date ?? 'No Expiry' }}</div>
                                    </div>
                                    <div class="products__cell">
                                        <div style="display: flex">
                                            @if($u->id_quiz)
                                            <img src="{{ url('img/quiz.png') }}" class="img-quiz" />
                                            @endif
                                            @if($u->survey_id)
                                            <img src="{{ url('img/Edit@2x.png') }}" class="img-quiz"  />
                                            @endif

                                            {{-- <img src="{{ url('img/Edit@2x.png') }}" class="img-quiz"  /> --}}
                                        </div>
                                    </div>
                                    <div class="products__cell">
                                        <div class="switch">
                                            <input type="checkbox" id="toggleSwitch{{ $u->id }}"
                                                class="switch__input" {{ $u->status ? 'checked' : '' }}
                                                data-id="{{ $u->id }}">
                                            <label for="toggleSwitch{{ $u->id }}" class="switch__label"></label>
                                        </div>
                                    </div>
                                    <div class="products__cell">
                                        <div class="dropdown actions__btn">
                                            <button class="dropdown-toggle">
                                                <svg class="icon icon-more">
                                                    <use xlink:href="#icon-more"></use>
                                                </svg>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="#popup-settings"
                                                class="dropdown-item js-popup-open js-popup-openx"
                                                data-id="{{ $u->id }}"
                                                data-effect="mfp-zoom-in">
                                                    <img src="{{ url('img/eye.svg') }}" class="eye_icon" />
                                                    Preview
                                                </a>
                                                <a href="{{ url('admin/course/' . $u->id . '/edit') }}" class="dropdown-item">
                                                    <svg class="icon icon-edit">
                                                        <use xlink:href="#icon-edit"></use>
                                                    </svg>
                                                    Edit Course
                                                </a>
                                                <a href="#" class="dropdown-item delete">
                                                    <img src="{{ url('img/bin.svg') }}" class="eye_icon" />
                                                    Delete
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif



                    </div>
                    <br>
                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            {{ $objs->appends(['search' => request('search')])->links('admin.pagination.custom') }}
                        </div>
                    </div>

                </div>
            </div>


        </div>
    </div>


@endsection


            <div class="popup mfp-hide" id="popup-settings">
                <form class="popup__form">
                    <div class="popup__title h6">Course Information</div>
                    <div class="tabs-container">
                        <div class="tabs">
                            <a class="tab active" data-tab="overview">Overview</a>
                            <a class="tab" data-tab="detail">Detail</a>
                            <a class="tab" data-tab="review">Review</a>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane active" id="overview">

                                    <div class="tabs">
                                        <div class="tabs__row">
                                            <div class="tabs__col">
                                                <div class="h6 popup__titlex mt-10"></div>
                                                <p class="course_description text-blue">Explore the connection between kidney health and musculoskeletal
                                                    conditions in veterinary patients.</p>

                                                <div style=" display: flex; ">
                                                    <div class="quality__chartx text-center">
                                                        <div id="chart-circle-yellow"></div>
                                                        <div class="percent_CompleteCourse quality__percent caption-sm">0%</div>
                                                        <div class="quality__info caption-sm ">Complete Course</div>
                                                        <div class="CompleteCourse caption-sm1"></div>
                                                    </div>
                                                    <div class="quality__chartx text-center">
                                                        <div id="chart-circle-purple"></div>
                                                        <div class="quality__percent caption-sm">40%</div>
                                                        <div class="quality__info caption-sm">Rating</div>
                                                        <div class="caption-sm1">For 1000 Reviews</div>
                                                    </div>
                                                </div>
                                                <div style=" display: flex; ">
                                                    <div class="quality__chartx text-center">
                                                        <div id="chart-circle-green"></div>
                                                        <div class="quality__percent caption-sm">35%</div>
                                                        <div class="quality__info caption-sm">Passed %</div>
                                                        <div class="caption-sm1">800 out of 1000 Passed</div>
                                                    </div>
                                                    <div class="quality__chartx text-center">
                                                        <div id="chart-circle-red"></div>
                                                        <div class="quality__percent caption-sm">40%</div>
                                                        <div class="quality__info caption-sm">Survey Summit</div>
                                                        <div class="caption-sm1">800 out of 1000 Enrolled</div>
                                                    </div>
                                                </div>


                                            </div>
                                            <div class="tabs__col">
                                                <div class=" widget_white" style="padding: 10px">
                                                    <div class="widget__title color-black">Enrolled Report</div>
                                                    <div class="widget__wrap">
                                                        <div class="widget__chart widget__chart_earning">
                                                            <div id="chart-income"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            <!-- Add tabs for "Detail" and "Review" here -->
                        </div>
                    </div>
                </form>
            </div>


@section('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
toastr.options = {
  "closeButton": true,
  "newestOnTop": false,
  "progressBar": true,
  "positionClass": "toast-top-center",
  "preventDuplicates": false,
  "onclick": null,
  "showDuration": "300",
  "hideDuration": "1000",
  "timeOut": "5000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
}

$(document).ready(function onDocumentReady() {
  setInterval(function doThisEveryTwoSeconds() {
    toastr.success("update success!");
  }, 15000);   // 2000 is 2 seconds
});

        document.addEventListener('DOMContentLoaded', () => {
            // ใช้ document สำหรับจับ Event ทั่วหน้า
            document.addEventListener('change', function (event) {
                const target = event.target;

                // ตรวจสอบว่า element ที่เปลี่ยนคือ switch__input
                if (target && target.classList.contains('switch__input')) {
                    const courseId = target.getAttribute('data-id'); // ดึง course ID
                    const isChecked = target.checked;

                    if (!courseId) {
                        console.error("data-id ไม่พบใน switch__input");
                        return;
                    }

                    // ใช้ fetch สำหรับอัปเดตสถานะ course
                    fetch("{{ url('/admin/course/toggle-status/') }}/" + courseId, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({
                            status: isChecked,
                        }),
                    })
                        .then(response => response.json())
                        .then(data => {
                            console.log(`สถานะของคอร์ส ${courseId} ถูกอัปเดต:`, data);
                        })
                        .catch(error => {
                            console.log('เกิดข้อผิดพลาดในการอัปเดตสถานะคอร์ส:', error);
                        });
                }
            });
        });




        document.addEventListener('DOMContentLoaded', () => {
    // Add event listener to "Preview" buttons
    const previewButtons = document.querySelectorAll('.js-popup-openx');

    previewButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const courseId = this.dataset.id; // รับ id ของ course จาก data-id

            // Fetch course details
            const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
            fetch(`${baseUrl}/admin/course/${courseId}/details`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updatePopupContent(
                            data.course,
                            data.enrolledStats,
                            data.totalCompleted,
                            data.totalEnrolled,
                            data.completionPercentage
                            );
                        openPopup();
                    } else {
                        console.error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error fetching course details:', error);
                });
        });
    });

    // Function to update popup content dynamically
    function updatePopupContent(course, enrolledStats, totalCompleted, totalEnrolled, completionPercentage) {
        console.log('-->', course.quiz.quiz_id)
        document.querySelector('.course_description').textContent = `${course.course_preview}`;
        document.querySelector('.h6.mt-10').textContent = `${course.quiz.quiz_id} - ${course.course_title}`;
        document.querySelector('.CompleteCourse').textContent = `${totalCompleted} of ${totalEnrolled} Enrolled `;
        document.querySelector('.percent_CompleteCourse').textContent = `${completionPercentage}`;

        if (chartYellow) {
            chartYellow.updateOptions({
                series: [completionPercentage], // เปลี่ยนเปอร์เซ็นต์เป็น 75%
            });
            }

        // Update graphs or charts (use your chart library logic here)
        console.log('Enrolled Stats:', enrolledStats);
    }

    // Function to open popup
    function openPopup() {
        const popup = document.getElementById('popup-settings');
        popup.classList.remove('mfp-hide');
        popup.classList.add('open-popup');
    }
});




    </script>

@stop('scripts')
