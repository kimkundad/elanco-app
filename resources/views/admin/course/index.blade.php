@extends('admin.layouts.template')

@section('title')
    <title>Elanco</title>
@stop
@section('stylesheet')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
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
                            <div class="products__info caption mobile-hide" style="color: #B2B3BD; font-weight: 400;">Explore and manage all
                                courses featuring interactive quizzes for effective learning.</div>
                        </div>
                        <div class="sorting__col">
                            <div class="sorting__line">
                                <div class="sorting__search">
                                    <button class="sorting__open">
                                        <svg class="icon icon-search">
                                            <use xlink:href="#icon-search"></use>
                                        </svg>
                                    </button>
                                    <input class="sorting__input" type="text" placeholder="Search">
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
                        <div class="products__row products__row_head">
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

                        @if($objs)
                            @foreach($objs as $u)
                                <div class="products__row">
                                    <div class="products__cell">
                                        <div class="products__payment">1</div>
                                    </div>
                                    <div class="products__cell"><a class="products__item" href="#">
                                            <div class="products__preview"><img class="products__pic"
                                                    src="{{ $u->course_img }}" alt=""></div>
                                            <div class="products__details" style="max-width: 250px;">
                                                <div class="products__title title">C001</div>
                                                <div class="products__info caption color-gray">{{ $u->course_title }}</div>
                                            </div>
                                        </a></div>
                                    <div class="products__cell">
                                        <div class="products__payment">0.0</div>
                                    </div>
                                    <div class="products__cell">
                                        <div class="products__payment">0</div>
                                    </div>
                                    <div class="products__cell">
                                        <img src="{{ url('img/philippines.svg') }}" class="Flag_icon" />
                                    </div>
                                    <div class="products__cell">
                                        <div class="products__status caption bg-green">null</div>
                                    </div>

                                    <div class="products__cell">
                                        <div class="products__payment">0</div>
                                    </div>
                                    <div class="products__cell">
                                        <div class="products__payment">17 Aug 2024</div>
                                    </div>
                                    <div class="products__cell">
                                        <div style="display: flex">
                                            <button class="actions__btn">
                                                <svg class="icon icon-comment">
                                                    <use xlink:href="#icon-comment"></use>
                                                </svg>
                                            </button>
                                            <button class="actions__btn">
                                                <svg class="icon icon-star">
                                                    <use xlink:href="#icon-star"></use>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="products__cell">
                                        <label class="switch switch_theme ">
                                            <input class="switch__input" type="checkbox"><span class="switch__in"
                                                style="    padding-left: 0px;"><span class="switch__box"
                                                    style="width: 44px;"></span><span class="switch__icon">
                                                </span></span>
                                        </label>
                                    </div>
                                    <div class="products__cell">
                                        <div class="dropdown actions__btn">
                                            <button class="dropdown-toggle">
                                                <svg class="icon icon-more">
                                                <use xlink:href="#icon-more"></use>
                                            </svg>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="#popup-settings" class="dropdown-item js-popup-open" data-effect="mfp-zoom-in">
                                                    <img src="{{ url('img/eye.svg') }}" class="eye_icon" />
                                                    Preview
                                                </a>
                                                <a href="{{url('admin/course/'.$u->id.'/edit')}}" class="dropdown-item">
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
                    <div class="products__more">
                        <button class="products__btn btn btn_black">Load More</button>
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
                                <div class="h6 mt-10">CO01 - Integrated Nephrology & Orthopaedic Care</div>
                                <p class="text-blue">Explore the connection between kidney health and musculoskeletal conditions in veterinary patients.</p>

                                <div style=" display: flex; ">
                                    <div class="quality__chartx text-center">
                                        <div id="chart-circle-yellow"></div>
                                        <div class="quality__percent caption-sm">35%</div>
                                        <div class="quality__info caption-sm ">Complete Course</div>
                                        <div class="caption-sm1">550 of 1000 Enrolled</div>
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
                <div class="tab-pane" id="detail">
                    <div class="tabs">
                        <div class="tabs__row">
                            <div class="tabs__col">
                                <div class="h6 mt-10">CO01 - Integrated Nephrology & Orthopaedic Care</div>
                                <p class="text-blue">Explore the connection between kidney health and musculoskeletal conditions in veterinary patients.</p>

                                <div class="getheader">
                                    <div class="header-item">
                                        <img src="{{ url('/img/book-icon.png') }}" alt="Enrolled Icon" class="icon"> <p>51 Enrolled</p>
                                    </div>
                                    <div class="header-item">
                                        <img src="{{ url('/img/star-icon.png') }}" alt="Complete Icon" class="icon"> 34 Complete course
                                    </div>

                                </div>
                                <div class="getheader">
                                    <div class="header-item">
                                        <img src="{{ url('/img/trophy-icon.png') }}" alt="Rating Icon" class="icon"> 4 CE Credits
                                    </div>
                                    <div class="header-item">
                                        <img src="{{ url('/img/heart-icon.png') }}" alt="Rating Icon" class="icon"> 4.3 Rating / 40 Reviews
                                    </div>
                                </div>


                                <div class="details">
                                    <div class="detail-row">
                                        <span class="label">Status</span>
                                        <span class="value public">Public</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">Topic of interest</span>
                                        <span class="value">Ophthalmology</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">Sub</span>
                                        <span class="value">Diagnostic Imaging</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">Species category</span>
                                        <span class="value">Cat</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">Language</span>
                                        <span class="value">English</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">Speaker</span>
                                        <span class="value">Parsley Montana</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">Expire Date</span>
                                        <span class="value">17 Aug 2024</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">Created By</span>
                                        <span class="value">Nattapon Choavanasilp</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">Date Created</span>
                                        <span class="value">17 Aug 2024 | 05:90 AM</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">Last Modified Date</span>
                                        <span class="value">17 Aug 2024 | 05:90 AM</span>
                                    </div>
                                </div>



                            </div>
                            <div class="tabs__col" style="padding: 15px">
                                <div class="right__title">Banner Thumbnail</div>
                                <img src="{{ url('img/Rectangle.png') }}" class="editor__pic"/>
                                <br>
                                <div class="right__title">Media</div>
                                <img src="{{ url('img/Rectangle2.png') }}" class="editor__pic"/>
                                <br>
                                <div class="right__title">Course Settings Link</div>
                                <div class="details">
                                    <div class="detail-row">
                                        <span class="label">Quiz</span>
                                        <span class="value ">Q001 -  Common early symptom of kidney disease </span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="label">Survey</span>
                                        <span class="value">S001 - Diet modification is typically recommended </span>
                                    </div>
                                </div>
                           </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="review">

                    <div class="page__stat " style="padding: 10px;">
                <div class="sorting">
                    <div class="sorting__row">


                        <div class="sorting__col">
                            <div class="sorting__dropdowns">
                                <div class="sorting__search">
                                    <button class="sorting__open">
                                        <svg class="icon icon-search">
                                            <use xlink:href="#icon-search"></use>
                                        </svg>
                                    </button>
                                    <input class="sorting__input" type="text" placeholder="Search">
                                </div>
                                <div class="dropdown js-dropdown">
                                    <div class="dropdown__head js-dropdown-head">Select Type</div>
                                    <div class="dropdown__body js-dropdown-body"><a class="dropdown__item" href="#">
                                            <div class="dropdown__title title">VET </div>
                                        </a><a class="dropdown__item" href="#">
                                            <div class="dropdown__title title">NON VET </div>
                                        </a><a class="dropdown__item" href="#">
                                            <div class="dropdown__title title">Admin </div>
                                        </a>
                                    </div>
                                </div>

                            </div>



                        </div>
                    </div>
                </div>
                <div class="products products_main">
                    <div class="products__table">
                        <div class="products__row products__row_head">
                            <div class="products__cell"></div>
                            <div class="products__cell">Name</div>
                            <div class="products__cell">Type</div>
                            <div class="products__cell">Email</div>
                            <div class="products__cell">Ratting</div>
                            <div class="products__cell">time Stamp</div>
                        </div>

                        <div class="products__row">
                            <div class="products__cell">
                                <div class="products__payment">1</div>
                            </div>
                            <div class="products__cell">
                                <a class="products__item" href="#">
                                <img src="{{ url('img/philippines.svg') }}" class="Flag_icon" />
                                    <div class="products__details">
                                        <div class="products__title title">Jacqueline Asong</div>
                                    </div>
                                </a>
                            </div>
                            <div class="products__cell">
                                <div class="products__status caption bg-gray">VET</div>
                            </div>
                            <div class="products__cell">
                                <a class="products__item" href="#">
                                    <div class="products__details">
                                        <div class="products__title title">mail@mail.com</div>
                                    </div>
                                </a>
                            </div>

                            <div class="products__cell">
                                <div class="rating">
                                    <span class="star">★</span>
                                    <span class="star">★</span>
                                    <span class="star">★</span>
                                    <span class="star">★</span>
                                    <span class="star">★</span>
                                </div>
                            </div>

                            <div class="products__cell">
                                <div class="products__payment">17 Aug 2024 | 05:60 AM</div>
                            </div>

                        </div>

                    </div>
                    <div class="products__more">
                        <button class="products__btn btn btn_black">Load More</button>
                    </div>
                </div>
            </div>

                </div>
                </div>
            </div>



      </form>
    </div>

@section('scripts')

@stop('scripts')
