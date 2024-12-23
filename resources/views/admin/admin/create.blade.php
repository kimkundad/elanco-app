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
                            <div class="products__title h6 mobile-hide">Add Your Teammates</div>
                            <div class="products__info caption mobile-hide" style="color: #B2B3BD; font-weight: 400;">Create an engaging banner to welcome and inform your users.</div>
                        </div>
                    </div>
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
                        <br>
                    <form method="POST" action="{{ url('/admin/adminUser') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="showFlex" style=" padding-bottom:20px">
                            <div class="itemFlex">


                            <div class="showFlex">
                                <div class="itemFlex">
                                    <div class="field__label">Name</div>
                                    <div class="field__wrap">
                                        <input class="field__input" name="name" type="text" placeholder="Name">
                                    </div>
                                </div>
                                <div class="itemFlex">
                                    <div class="field__label">Surname</div>
                                    <div class="field__wrap">
                                        <input class="field__input" name="surname" type="text" placeholder="Surname">
                                    </div>
                                </div>
                            </div>

                                <div class="field__label mt-20">Username</div>
                                <div class="field__wrap">
                                    <input class="field__input" name="email" type="email" placeholder="mail@mail.com">
                                </div>

                                <div class="mt-20">
                                <div class="showFlex ">
                                    <div class="itemFlex">
                                        <div class="field__label">Password</div>
                                        <div class="field__wrap">
                                            <input class="field__input" id="password" name="password" type="password" placeholder="Password">
                                        </div>
                                    </div>
                                    <div class="itemFlex">
                                        <div class="field__label">Retype Password</div>
                                        <div class="field__wrap">
                                            <input class="field__input" name="retype_password" type="password" placeholder="Retype Password">
                                        </div>
                                    </div>
                                </div>
                                </div>

                                <div class="mt-20">
                                    <div class="showFlex">
                                        <div class="itemFlex">
                                            <div class="field__label">Role</div>
                                            <div class="field__wrap">
                                                <select class="field__input" name="role">
                                                    <option> -- กำหนด Role User -- </option>
                                                    @isset($Role)
                                                        @foreach ($Role as $item)
                                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                        @endforeach
                                                    @endisset
                                                </select>
                                            </div>
                                        </div>
                                        <div class="itemFlex">
                                            <div class="field__label">Country</div>
                                            <div class="field__wrap">
                                                <select class="field__input" name="country">
                                                    @isset($countries)
                                                        @foreach ($countries as $item)
                                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                        @endforeach
                                                    @endisset
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="itemFlex">
                                <div class="image-upload-container">
                                    <div class="image-preview" id="imagePreview" style=" height: 300px;   border-radius: 100%;">
                                        <img id="previewImage" src="{{ url('img/300-1.jpg') }}" alt="Preview" />
                                        <a class="remove-image-btn" id="removeImage">&times;</a>
                                    </div>
                                    <label for="imageUpload" class="upload-btn">Upload Image</label>
                                    <input type="file" id="imageUpload" name="avatar_img" accept="image/*" style="display: none;" />
                                    <p class="image-size-text">Avatar help your teammates recognize you in System.</p>
                                </div>
                            </div>

                        </div>


                        <div class="products__more">
                            <button class=" btn btn_green" type="submit">Add User</button>
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
