@extends('admin.layouts.template')

@section('title')
    <title>Elanco</title>
@stop
@section('stylesheet')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

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

        .products__details {
            padding-left: 0px;
        }
.dropdown-menu{
    top: 80%;
}
.badge {
    margin-top: 10px;
    border: 1px solid #666;
    display: inline-block;
    min-width: 10px;
    padding: 6px 7px;
    font-size: 12px;
    font-weight: 400;
    line-height: 1;
    color: #2196F3;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    background-color: #ffffff;
    border-radius: 10px;
}
.btn-secondary {
    color: #000;
    background-color: #c1c1c1;
    border-color: #c1c1c1;
        border: none;
}
.list-unstyled li{
    padding: 10px 0;
    border-top: 1px solid #dee2e6;
}
.list-unstyled li strong{
    font-size: 13px
}
    </style>

@stop('stylesheet')

@section('content')


    <div class="page__content" style="margin-top: 50px; padding: 0">
        <div class="page__stat ">


            <div class="page__stat page__stat_pt32">
                <div class="products__title h6 mobile-hide">Member Directory</div>
                <div class="products__info caption mobile-hide" style="color: #B2B3BD; font-weight: 400;">Access and explore
                    all registered members easily..</div>
                <br>
                <div class="sorting">
                    <div class="sorting__row">


                        <div class="sorting__col">


                            <div class="sorting__dropdowns">
                                <form method="GET" action="{{ url('admin/members') }}" style="display: flex">
                                    <div class="sorting__search">
                                        <button class="sorting__open" type="submit">
                                            <svg class="icon icon-search">
                                                <use xlink:href="#icon-search"></use>
                                            </svg>
                                        </button>
                                        <input class="sorting__input" type="text" name="search"
                                            placeholder="Search" value="{{ request('search') }}">
                                    </div>

                                    <div class="field__wrap" style="margin-left: 10px;">
                                        <select class="field__input" name="userType">
                                            <option value="" {{ request('userType') == '' ? 'selected' : '' }}>All</option>
                                            <option value="VET" {{ request('userType') == 'VET' ? 'selected' : '' }}>VET</option>
                                            <option value="NON VET" {{ request('userType') == 'NON VET' ? 'selected' : '' }}>NON VET</option>
                                            <option value="Admin" {{ request('userType') == 'Admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </div>
                                </form>
                            </div>


                        </div>
                    </div>
                </div>
                <div class="products products_main">
                    <div class="products__table">
                        <div class="products__row products__row_head">
                            <div class="products__cell"></div>
                            <div class="products__cell">Name</div>
                            <div class="products__cell">Email</div>
                            <div class="products__cell">Type</div>
                            <div class="products__cell">Clinic / Hospital name</div>
                            <div class="products__cell"></div>
                            <div class="products__cell">Last Active</div>
                            <div class="products__cell"></div>
                        </div>

                        @foreach ($objs as $key => $user)
                            <div class="products__row">
                                <div class="products__cell">
                                    <div class="products__payment">{{ $objs->firstItem() + $key }}</div>
                                </div>
                                <div class="products__cell">
                                    <a class="products__item" href="#">
                                        <div class="products__details">
                                            <div class="products__title title">{{ $user->firstName }} {{ $user->lastName }}</div>
                                        </div>
                                    </a>
                                </div>
                                <div class="products__cell">
                                    <a class="products__item" href="#">
                                        <div class="products__details">
                                            <div class="products__title title">{{ $user->email }}</div>
                                        </div>
                                    </a>
                                </div>
                                <div class="products__cell">
                                    <!-- แสดงชื่อ Role userType -->
                                    <div class="products__status caption bg-green">
                                        {{ $user->userType }}
                                    </div>
                                </div>
                                <div class="products__cell">
                                    <div class="products__payment">{{ $user->clinic ?? 'N/A' }}</div>
                                </div>
                                <div class="products__cell">
                                    <!-- แสดงข้อมูล Country -->
                                    @if ($user->country)
                                        <img src="{{ $user->countryDetails->img }}" class="Flag_icon" alt="{{ $user->countryDetails->name }}" />
                                    @else
                                        <span>No Country</span>
                                    @endif
                                </div>
                                <div class="products__cell">
                                    <div class="products__payment">{{ $user->updated_at ?? 'N/A' }}</div>
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
                                            class="dropdown-item d-flex js-popup-open js-popup-openx"
                                                data-id="{{ $user->id }}"
                                                data-effect="mfp-zoom-in">
                                                <img src="{{ url('img/eye.svg') }}" class="eye_icon" />
                                                Preview

                                            </a>
                                            <a href="{{ url('admin/members/' . $user->id . '/edit') }}" class="dropdown-item">
                                                <svg class="icon icon-edit">
                                                    <use xlink:href="#icon-edit"></use>
                                                </svg>
                                                Edit Member
                                            </a>
                                            <a href="#" class="dropdown-item delete d-flex">
                                                <img src="{{ url('img/bin.svg') }}" class="eye_icon" />
                                                Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach



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


    <div class="popup mfp-hide" id="popup-settings">
                <form class="popup__form">
                    <div class="popup__title h6">Account Information</div>
                    <div class="tabs-container">
                        <div class="tabs">
                            <a class="tab active" data-tab="overview">Account Detail</a>
                            <a class="tab" data-tab="detail">Course History</a>
                            <a class="tab" data-tab="review">Account activity</a>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane active" id="overview">
                                <div class="">
                                    <div class="row">
                                        <!-- Left Column -->
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-4">
                                                <img src="https://kimspace2.sgp1.cdn.digitaloceanspaces.com/elanco/avatar/300-1.jpg" alt="User Avatar" class="rounded-circle me-3" style="width: 80px; height: 80px;">
                                                <div class="user-info">
                                                    <h5 class="user-info mb-0">Dr. Nattapon Choavanasilp</h5>
                                                    <p class="text-muted mb-0">golf.choavana@gmail.com</p>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mb-4">
                                                <div class="text-center stats">
                                                    <h6 class="mb-0 text-primary">51</h6>
                                                    <small class="text-muted">Learning course</small>
                                                </div>
                                                <div class="text-center stats">
                                                    <h6 class="mb-0 text-success">34</h6>
                                                    <small class="text-muted">Complete course</small>
                                                </div>
                                                <div class="text-center stats">
                                                    <h6 class="mb-0 text-warning">50</h6>
                                                    <small class="text-muted">CE Credits</small>
                                                </div>
                                            </div>
                                            <ul class="list-unstyled">
                                                <li><strong>Type:</strong> VET</li>
                                                <li><strong>VET ID:</strong> 54681544</li>
                                                <li><strong>Job Info:</strong> Veterinarian</li>
                                                <li><strong>Clinic / Hospital Name:</strong> Betagro Public Company Limited</li>
                                                <li><strong>Membership Date:</strong> 17 Aug 2024 | 05:90 AM</li>
                                                <li><strong>Last Activity Date:</strong> 17 Aug 2024 | 05:90 AM</li>
                                            </ul>
                                        </div>

                                        <!-- Right Column -->
                                        <div class="col-md-6">
                                            <h6>Category</h6>
                                            <div class="mb-3 category">
                                                <span class="badge bg-light text-dark me-1">Immunology</span>
                                                <span class="badge bg-light text-dark me-1">Orthopaedic</span>
                                                <span class="badge bg-light text-dark me-1">Neutrology</span>
                                                <span class="badge bg-light text-dark">Ophthalmology</span>
                                            </div>

                                            <h6>Sub Category</h6>
                                            <div class="mb-3">
                                                <span class="badge bg-light text-dark me-1">Diagnostic Imaging</span>
                                                <span class="badge bg-light text-dark">Emergency Critical Care</span>
                                            </div>

                                            <h6>Animal Category</h6>
                                            <div class="mb-3">
                                                <span class="badge bg-light text-dark">Cat</span>
                                            </div>
                                            <br>
                                            <div class="d-grid gap-2 mt-4">
                                                <button class="btn btn-secondary btn-block" type="button">Pause account</button>
                                                <button class="btn btn-secondary btn-block" type="button">Delete Account</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="detail">
                            <div class="tabs">
                                        <div class="tabs__row">
                                            <div class="">

                                                <div  id="detail">
                                                    <div class="popup__content">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Name</th>
                                                                    <th>Status</th>
                                                                    <th>Pass Rate</th>
                                                                    <th>Start Date</th>
                                                                    <th>Last Active</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="course-table-body">
                                                                <!-- ตารางจะถูกอัปเดตด้วย JavaScript -->
                                                            </tbody>
                                                        </table>
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


@endsection

@section('scripts')


<script>



document.addEventListener('DOMContentLoaded', () => {
    const previewButtons = document.querySelectorAll('.js-popup-openx');

    previewButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const userId = this.dataset.id; // รับ id ของผู้ใช้จาก data-id

            // Fetch user and course details
            const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
            fetch(`${baseUrl}/admin/user/${userId}/courses`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updatePopupContent(data.data); // ส่งข้อมูล User และ Courses ไปอัปเดตใน Popup
                        openPopup();
                    } else {
                        console.error('Error fetching data:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
        });
    });

    // Function to update popup content dynamically
    function updatePopupContent(data) {
        const { user, courses } = data;

        // Update user info
        document.querySelector('.user-info h5').textContent = `${user.prefix || ''} ${user.firstName} ${user.lastName}`;
        document.querySelector('.user-info p').textContent = user.email;

        // Update statistics
        document.querySelector('.stats .text-primary').textContent = user.learningCourses || '0';
        document.querySelector('.stats .text-success').textContent = user.completedCourses || '0';
        document.querySelector('.stats .text-warning').textContent = user.ceCredits || '0';

        // Update categories
        updateCategoryBadges('.category', user.mainCategories);
        updateCategoryBadges('.sub-category', user.subCategories);
        updateCategoryBadges('.animal-category', user.animalTypes);
        console.log('courses', courses)
        // Update course list in the table
        const tableBody = document.querySelector('#course-table-body');
        tableBody.innerHTML = ''; // Clear existing rows

        courses.forEach((courses, index) => {
            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${course.course.course_title}</td>
                    <td><div class="products__status caption bg-green">${course.isFinishCourse ? 'Complete' : 'Learning'}</div></td>
                    <td>${course.pass_rate.toFixed(0)}%</td>
                    <td>${new Date(course.created_at).toLocaleString()}</td>
                    <td>${new Date(course.updated_at).toLocaleString()}</td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    }

    // Utility function to update badges for categories
    function updateCategoryBadges(selector, categories) {
        const container = document.querySelector(selector);
        container.innerHTML = ''; // Clear existing badges
        categories.forEach(category => {
            const badge = `<span class="badge bg-light text-dark me-1">${category.name}</span>`;
            container.innerHTML += badge;
        });
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
