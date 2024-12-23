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
    </style>

@stop('stylesheet')

@section('content')


    <div class="page__content" style="margin-top: 50px; padding: 0">
        <div class="page__stat ">


            <div class="page__stat page__stat_pt32">
                <div class="sorting">
                    <div class="sorting__row">
                        <div class="sorting__col">
                            <div class="products__title h6 mobile-hide">Quiz Lists</div>
                            <div class="products__info caption mobile-hide" style="color: #B2B3BD; font-weight: 400;">Browse and manage courses focused on survey assignments to deepen learning.</div>
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
                                <a href="{{ url('admin/quiz/create') }}">
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
                            <div class="products__cell">Quiz ID</div>
                            <div class="products__cell">Name</div>
                            <div class="products__cell">Course Link</div>
                            <div class="products__cell">Submitted</div>
                            <div class="products__cell"></div>
                            <div class="products__cell">Create Date</div>
                            <div class="products__cell"></div>
                        </div>

                        @if($objs)
                            @foreach($objs as $u)
                                <div class="products__row">
                                    <div class="products__cell">
                                        <div class="products__payment">1</div>
                                    </div>
                                    <div class="products__cell"><a class="products__item" href="#">

                                            <div class="products__details">
                                                <div class="products__title title">{{ $u->quiz_id }}</div>

                                            </div>
                                        </a></div>
                                    <div class="products__cell">
                                        <div class="products__details" style="max-width: 250px;">
                                                <div class="products__info caption color-gray">{{ $u->questions_title }}</div>
                                            </div>
                                    </div>
                                    <div class="products__cell">
                                        <div class="products__payment">0</div>
                                    </div>
                                    <div class="products__cell">
                                        <div class="products__payment">0</div>
                                    </div>
                                    <div class="products__cell">
                                        <img src="{{ url('img/philippines.svg') }}" class="Flag_icon" />
                                    </div>

                                    <div class="products__cell">
                                        <div class="products__payment">{{ \Carbon\Carbon::parse($u->created_at)->format('d M Y') }}</div>
                                    </div>


                                    <div class="products__cell">
                                        <div class="dropdown actions__btn">
                                            <button class="dropdown-toggle">
                                                <svg class="icon icon-more">
                                                <use xlink:href="#icon-more"></use>
                                            </svg>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="#" class="dropdown-item">
                                                    <img src="{{ url('img/eye.svg') }}" class="eye_icon" />
                                                    Preview
                                                </a>
                                                <a href="{{url('admin/quiz/'.$u->id.'/edit')}}" class="dropdown-item">
                                                    <svg class="icon icon-edit">
                                                        <use xlink:href="#icon-edit"></use>
                                                    </svg>
                                                    Edit Quiz
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

@section('scripts')

@stop('scripts')
