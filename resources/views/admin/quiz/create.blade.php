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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

@stop('stylesheet')

@section('content')


    <div class="page__content" style="margin-top: 50px; padding: 0">
        <div class="page__stat ">


            <div class="page__stat page__stat_pt32">




                <div class="products products_main">

                @if(session('add_success'))
                    <div class="alert alert-success">
                        {{ session('add_success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                    <form method="POST" action="{{ url('/admin/quiz') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="widget__title pb-10" style="border-bottom: 2px solid #E4E4E4;">Add New Quiz Set</div>
                        <br>
                        <div class="showFlex" style=" padding-bottom:20px">


                            <div style="width: 100%; padding: 10px">


                                <div class="showFlex ">

                                    <div style="width: 50%;" class="p-10">
                                        <div class="field__label mt-10">Quiz ID</div>
                                        <div class="field__wrap">
                                            <input class="field__input" name="quiz_id" type="text" placeholder="Q001" value="{{old('quiz_id') ? old('quiz_id') : ''}}">
                                        </div>
                                    </div>

                                    <div style="width: 50%;" class="p-10">
                                        <div class="field__label mt-10">Expire date</div>
                                        <div class="field__wrap">
                                            <input class="field__input" id="expire_date" name="expire_date" type="text" placeholder="Choose the date">
                                        </div>
                                    </div>

                                </div>

                                <div class="field__label">Questions Title</div>
                                <div class="field__wrap">
                                    <input class="field__input" name="questions_title" type="text" placeholder="Title Here…" value="{{old('questions_title') ? old('questions_title') : ''}}">
                                </div>

                                <div class="showFlex ">

                                    <div style="width: 50%;" class="p-10">
                                        <div class="field__label mt-10">Pass Percentage</div>
                                        <div class="field__wrap">
                                            <select class="field__input" name="pass_percentage">
                                                <option value="" disabled selected>Select Pass Percentage</option>
                                                <option value="50">50%</option>
                                                <option value="60">60%</option>
                                                <option value="70">70%</option>
                                                <option value="80">80%</option>
                                                <option value="90">90%</option>
                                                <option value="100">100%</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div style="width: 50%;" class="p-10">
                                        <div class="field__label mt-10">Certificate Received</div>
                                        <div class="field__wrap">
                                            <select class="field__input" name="certificate">
                                                <option value="" disabled selected>Yes or No</option>
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="field__label mt-10">CPD / CE points granted</div>
                                <div class="field__wrap">
                                    <input class="field__input" name="point_cpd" type="text" placeholder="5" value="{{old('point_cpd') ? old('point_cpd') : ''}}">
                                </div>


                                 <div class="products__more">
                            <button class=" btn btn_green" type="submit">Save</button>
                        </div>

                            </div>







                    </form>

                </div>

            </div>


        </div>
    </div>


@endsection

@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>

document.addEventListener("DOMContentLoaded", function () {
    flatpickr("#expire_date", {
        dateFormat: "d-m-Y", // กำหนดรูปแบบวัน-เดือน-ปี
        altInput: true,      // แสดงรูปแบบที่สวยขึ้น
        altFormat: "d-m-Y",  // รูปแบบในฟิลด์
        locale: {
            firstDayOfWeek: 1 // เริ่มต้นวันจันทร์
        }
    });
});


</script>

@stop('scripts')
