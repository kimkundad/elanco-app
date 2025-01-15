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
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet" />
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
        .btn {
    padding: 20px 30px;
    cursor: pointer;
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

                    <form method="POST" id="questionForm" action="{{ url('/admin/survey') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="widget__title pb-10" style="border-bottom: 2px solid #E4E4E4;">Add New Survey</div>
                        <br>
                        <div class="showFlex" style=" padding-bottom:20px">


                            <div style="width: 100%; padding: 10px">


                                <div class="showFlex ">

                                    <div style="width: 50%;" class="p-10">
                                        <div class="field__label mt-10">Survey ID</div>
                                        <div class="field__wrap">
                                            <input class="field__input" name="survey_id" type="text" placeholder="Q001" value="{{old('survey_id') ? old('survey_id') : ''}}">
                                        </div>
                                    </div>

                                    <div style="width: 50%;" class="p-10">
                                        <div class="field__label mt-10">Expire date</div>
                                        <div class="field__wrap">
                                            <input class="field__input" id="expire_date" name="expire_date" type="text" placeholder="Choose the date">
                                        </div>
                                    </div>

                                </div>

                                <div class="field__label">Survey Title</div>
                                <div class="field__wrap">
                                    <input class="field__input" name="survey_title" type="text" placeholder="Title Here…" value="{{old('survey_title') ? old('survey_title') : ''}}">
                                </div>


                                <div class="field__label mt-10">Detail</div>
                                 <div class="itemFlex">

                                    <div id="editor-container" style="height: 320px;"></div>
                                    <textarea name="survey_detail" id="quill-content" style="display:none;"></textarea>
                                </div>





                                 <div class="products__more">
                            <a id="submit_ans" class=" btn btn_green " >Save</a>
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
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Initialize Quill Editor
        const quill = new Quill('#editor-container', {
            theme: 'snow',
            placeholder: 'Write your survey details here...',
            modules: {
                toolbar: [
                    [{ header: [1, 2, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link', 'image']
                ]
            },
        });

        // Populate old content if available
        const oldContent = {!! json_encode(old('survey_detail')) !!};
        if (oldContent) {
            quill.root.innerHTML = oldContent;
        }

        // Flatpickr Initialization
        flatpickr("#expire_date", {
            dateFormat: "d-m-Y",
            altInput: true,
            altFormat: "d-m-Y",
            locale: { firstDayOfWeek: 1 }
        });

        // Handle Save Button
        document.getElementById('submit_ans').addEventListener('click', function () {
            const quillContent = quill.root.innerHTML.trim();
            if (!quillContent) {
                alert('Please enter survey details.');
                return;
            }
            document.getElementById('quill-content').value = quillContent;
            document.getElementById('questionForm').submit();
        });
    });
</script>

@stop('scripts')
