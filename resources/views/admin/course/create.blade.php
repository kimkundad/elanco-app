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
        .products__cell:first-child {
     width: 180px;

}
.products__details {
    padding-left: 10px;
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
                            <div class="products__title h6 mobile-hide">Add New Course</div>
                            <div class="products__info caption mobile-hide" style="color: #B2B3BD; font-weight: 400;">Create an engaging quiz course to enhance learning and assessment.</div>
                        </div>
                    </div>
                </div>
                <div class="inbox__btns">
                    <button class="inbox__btn btn btn_blue">Course Setting</button>
                    <button class="inbox__btn btn btn_white">Course Detail</button>
                  </div>
                <div class="products products_main">

                    <div class="widget__title mt-20 pb-10" style="border-bottom: 2px solid #E4E4E4;">Course Setting</div>
                    <br>
                    <div class="showFlex">
                        <div class="itemFlex">

                            <div class="field__label">Course Title</div>
                            <div class="field__wrap">
                                <input class="field__input" type="text" placeholder="Title here...">
                            </div>

                        </div>
                        <div class="itemFlex">

                        </div>
                    </div>

                </div>
            </div>


        </div>
    </div>


@endsection

@section('scripts')

@stop('scripts')
