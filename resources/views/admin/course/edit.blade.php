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
                            <div class="products__title h6 mobile-hide">Edit Course</div>
                            <div class="products__info caption mobile-hide" style="color: #B2B3BD; font-weight: 400;">Create
                                an engaging quiz course to enhance learning and assessment.</div>
                        </div>
                    </div>
                </div>
                <div class="inbox__btns">
                    <button class="inbox__btn btn btn_blue">Course Setting</button>
                    <button class="inbox__btn btn btn_white">Course Detail</button>
                </div>
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

                    <form id="kt_account_profile_details_form" class="form" method="POST" action="{{$url}}" enctype="multipart/form-data">
                        {{ method_field($method) }}
                        {{ csrf_field() }}
                        <div class="widget__title mt-20 pb-10" style="border-bottom: 2px solid #E4E4E4;">Course Setting</div>
                        <br>
                        <div class="showFlex" style=" padding-bottom:20px">
                            <div class="itemFlex">

                                <div class="field__label">Course Title</div>
                                <div class="field__wrap">
                                    <input class="field__input" name="course_title" type="text" placeholder="Title here..." value="{{ $course->course_title }}">
                                </div>

                                <div class="field__label mt-20">Course Preview</div>
                                <div class="field__wrap">
                                    <input class="field__input" name="course_preview" type="text" placeholder="Course Preview..." value="{{ $course->course_preview }}" >
                                </div>

                                <div class="showFlex p-10">

                                    <div>
                                        <div class="field__label mt-10">Public Status</div>
                                        <div class="field__wrap">
                                            <select class="field__input" name="status">
                                                <option value="0"
                                                        @if($course->status == 0)
                                                    selected
                                                    @endif
                                                    >Hide</option>
                                                    <option value="1"
                                                        @if($course->status == 1)
                                                    selected
                                                    @endif
                                                    >Show</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="field__label mt-10">Duration (Minutes)</div>
                                        <div class="field__wrap">
                                            <input class="field__input" name="duration" type="text" placeholder="30 Min" value="{{ $course->duration }}">
                                        </div>
                                    </div>

                                </div>

                                <div class="field__label mt-20">Link Media</div>
                                <div class="field__wrap">
                                    <input class="field__input" name="url_video" type="text" placeholder="Url Video ..." value="{{ $course->url_video }}">
                                </div>

                            </div>

                            <div class="itemFlex">
                                <div class="image-upload-container">
                                    <div class="image-preview" id="imagePreview">
                                        <img id="previewImage" src="{{ $course->course_img }}" alt="Preview" />
                                        <a class="remove-image-btn" id="removeImage">&times;</a>
                                    </div>
                                    <label for="imageUpload" class="upload-btn">Upload Image</label>
                                    <input type="file" id="imageUpload" name="course_img" accept="image/*" style="display: none;" />
                                    <p class="image-size-text">Banner Size xxx x xxx px</p>
                                </div>
                            </div>

                        </div>


                        <div class="widget__title mt-20 pb-10" style="border-bottom: 2px solid #E4E4E4;">Course Settings Link</div>
                        <br>
                        <div class="showFlex" style=" padding-bottom:20px">
                            <div class="itemFlex">

                                <div class="field__label">Quiz</div>
                                <div class="field__wrap">
                                            <select class="field__input" name="id_quiz">
                                                <option value=""  selected>Quiz ID</option>
                                                @if($quiz)
                                                    @foreach($quiz as $u)
                                                        <option value="{{ $u->id }}"
                                                        @if($u->id == $course->id_quiz)
                                                    selected
                                                    @endif
                                                    >{{ $u->quiz_id }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                            </div>
                        </div>


                        <div class="widget__title mt-20 pb-10" style="border-bottom: 2px solid #E4E4E4;">Preview Setting</div>
                        <br>
                        <div class="showFlex" style=" padding-bottom:20px">


                            {{-- Start County Group --}}
<div class="">
    <div>
        <div class="field__label">County</div>
        @if($countries)
            @foreach($countries as $country)
            <div class="checkbox-set">
                <label class="checkbox">
                    <input class="checkbox__input" type="checkbox" name="countries[]" value="{{ $country->id }}"
   {{ in_array($country->id, $course->countries->pluck('id')->toArray()) ? 'checked' : '' }}>
                    <span class="checkbox__in"><span class="checkbox__tick"></span></span>
                </label>
                <div class="field__label ml-10">{{ $country->name }}</div>
            </div>
            @endforeach
        @endif
    </div>
</div>
{{-- End County Group --}}

{{-- Start Main Category Group --}}
<div class="">
    <div>
        <div class="field__label">Main Category</div>
        @if($mainCategories)
            @foreach($mainCategories as $category)
            <div class="checkbox-set">
                <label class="checkbox">
                    <input class="checkbox__input" type="checkbox" name="main_categories[]" value="{{ $category->id }}"
   {{ in_array($category->id, $course->mainCategories->pluck('id')->toArray()) ? 'checked' : '' }}>
                    <span class="checkbox__in"><span class="checkbox__tick"></span></span>
                </label>
                <div class="field__label ml-10">{{ $category->name }}</div>
            </div>
            @endforeach
        @endif
    </div>
</div>
{{-- End Main Category Group --}}

{{-- Start Sub Category Group --}}
<div class="">
    <div>
        <div class="field__label">Sub Category</div>
        @if($subCategories)
            @foreach($subCategories as $subCategory)
            <div class="checkbox-set">
                <label class="checkbox">
                    <input class="checkbox__input" type="checkbox" name="sub_categories[]" value="{{ $subCategory->id }}"
   {{ in_array($subCategory->id, $course->subCategories->pluck('id')->toArray()) ? 'checked' : '' }}>
                    <span class="checkbox__in"><span class="checkbox__tick"></span></span>
                </label>
                <div class="field__label ml-10">{{ $subCategory->name }}</div>
            </div>
            @endforeach
        @endif
    </div>
</div>
{{-- End Sub Category Group --}}

{{-- Start Animal Type Group --}}
<div class="">
    <div>
        <div class="field__label">Type of Animal</div>
        @if($animalTypes)
            @foreach($animalTypes as $animalType)
            <div class="checkbox-set">
                <label class="checkbox">
                    <input class="checkbox__input" type="checkbox" name="animal_types[]" value="{{ $animalType->id }}"
   {{ in_array($animalType->id, $course->animalTypes->pluck('id')->toArray()) ? 'checked' : '' }}>
                    <span class="checkbox__in"><span class="checkbox__tick"></span></span>
                </label>
                <div class="field__label ml-10">{{ $animalType->name }}</div>
            </div>
            @endforeach
        @endif
    </div>
</div>
{{-- End Animal Type Group --}}



                        </div>


                        <div class="products__more">
                            <button class=" btn btn_green" type="submit">Save</button>
                        </div>

                    </form>

                </div>

            </div>


        </div>
    </div>


@endsection

@section('scripts')


<script>

document.getElementById("imageUpload").addEventListener("change", function (event) {
    const file = event.target.files[0];
    const previewImage = document.getElementById("previewImage");
    const removeButton = document.getElementById("removeImage");

    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImage.src = e.target.result;
        };
        reader.readAsDataURL(file);
        removeButton.style.display = "block";
    }
});

document.getElementById("removeImage").addEventListener("click", function () {
    const previewImage = document.getElementById("previewImage");
    previewImage.src = "{{ url('img/Mask@1.5x.png') }}"; // Default image path
    this.style.display = "none";
    document.getElementById("imageUpload").value = ""; // Reset the input
});


</script>

@stop('scripts')
