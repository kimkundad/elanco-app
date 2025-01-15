@extends('admin.layouts.template')

@section('title')
    <title>Elanco</title>
@stop
@section('stylesheet')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet" />
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
.btn_green2{
    color: #fff;
    background: #40B430;
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
        .inbox__btn.active {
    background-color: #0056b3;
    color: #fff;
}

.inbox__btns {
    display: flex;
    gap: 10px;
}

.inbox__btn {
    padding: 10px 20px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    font-size: 16px;
    transition: background-color 0.3s, color 0.3s;
}

.btn_blue {
    background-color: #0056b3;
    color: #fff;
}

.btn_white {
    background-color: #fff;
    color: #0056b3;
    border: 1px solid #0056b3;
}

/* เนื้อหา Tabs */
.tab-content {
    margin-top: 20px;
}

.products {
    display: none;
}

.products.active {
    display: block;
}

.input-item {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    background-color: #f5f5f5;
    border-radius: 10px;
    padding: 10px;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
}

.input-item input {
    flex: 1;
    border: none;
    outline: none;
    background-color: transparent;
    font-size: 14px;
    color: #333;
    padding: 5px;
}

.input-item .remove-btn {
    background-color: #fff;
    color: #000;
    border: none;
    border-radius: 50%;
    width: 26px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    cursor: pointer;
}

.input-item .remove-btn:hover {
    background-color: #e3e3e3;
}

.add-item-btn {
    background-color: #001033;
    color: white;
    border: none;
    border-radius: 10px;
    padding: 10px 20px;
    font-size: 14px;
    cursor: pointer;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
}

.add-item-btn:hover {
    background-color: #001840;
}
.text-gray{
    color: #999
}
.btn{
        padding: 20px 30px;
        cursor: pointer;

}
.upload-container {
      text-align: center;
    }

    .upload-btn {
      background-color: #000033; /* Navy blue */
      color: white;
      font-size: 16px;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      padding: 12px 20px;
      cursor: pointer;
      transition: all 0.3s ease;
      display: inline-block;
    }

    .upload-btn:hover {
      background-color: #3333cc; /* Lighter navy */
    }

    .hidden-input {
      display: none;
    }

    .file-name {
      margin-top: 10px;
      font-size: 14px;
      color: #666;
      display: none;
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
                    <button class="inbox__btn btn btn_blue active" onclick="showTab('tab1', this)">Course Setting</button>
                    <button class="inbox__btn btn btn_white" onclick="showTab('tab2', this)">Course Detail</button>
                </div>

                <form  id="questionForm" class="form" method="POST" action="{{$url}}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{ method_field($method) }}

                <div class="tab-content">
                <div id="tab1" class="products products_main active">

                    @if (session('add_success'))
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


                        <div class="widget__title mt-20 pb-10" style="border-bottom: 2px solid #E4E4E4;">Course Setting</div>
                        <br>
                        <div class="showFlex" style=" padding-bottom:20px">
                            <div class="itemFlex">

                                <div class="field__label">Course Title</div>
                                <div class="field__wrap">
                                    <input class="field__input" name="course_title" type="text" placeholder="Title here..."
                                        value="{{ $course->course_title }}">
                                </div>

                                <div class="field__label mt-20">Course Preview</div>
                                <div class="field__wrap">
                                    <input class="field__input" name="course_preview" type="text" placeholder="Course Preview..."
                                        value="{{ $course->course_preview }}">
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
                                            <input class="field__input" name="duration" type="text" placeholder="30 Min"
                                                value="{{ $course->duration }}">
                                        </div>
                                    </div>

                                </div>

                                <div class="field__label mt-20">Link Media</div>
                                <div class="field__wrap">
                                    <input class="field__input" name="url_video" type="text" placeholder="Url Video ..."
                                        value="{{ $course->url_video }}">
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
                                        <option value="" selected>Quiz ID</option>
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

                            <div class="itemFlex">

                                <div class="field__label">Survey</div>
                                <div class="field__wrap">
                                    <select class="field__input" name="survey_id">
                                        <option value="" selected>Survey ID</option>
                                       @if($survey)
                                                    @foreach($survey as $u)
                                                        <option value="{{ $u->id }}"
                                                        @if($u->id == $course->survey_id)
                                                    selected
                                                    @endif
                                                    >{{ $u->survey_id }}</option>
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
                                                    <input class="checkbox__input" type="checkbox" name="countries[]"
                                                        value="{{ $country->id }}"
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
                                                    <input class="checkbox__input" type="checkbox" name="main_categories[]"
                                                        value="{{ $category->id }}"
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
                                                    <input class="checkbox__input" type="checkbox" name="sub_categories[]"
                                                        value="{{ $subCategory->id }}"
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
                                                    <input class="checkbox__input" type="checkbox" name="animal_types[]"
                                                        value="{{ $animalType->id }}"
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
                            {{-- <button class=" btn btn_green" type="submit">Save</button> --}}
                            <a class=" btn btn_green2" onclick="activateTab2()">Next</a>
                        </div>




                </div>

                <div id="tab2" class="products products_main">

                    <div class="widget__title mt-20 pb-10" style="border-bottom: 2px solid #E4E4E4;">Course Description</div>
                        <br>

                        <div class="showFlex" style=" padding-bottom:20px">
                            <div class="itemFlex">
                                <div id="editor-container" style="height: 320px;">{!! $course->course_description ?? '' !!}</div>
                                <textarea name="course_description" id="quill-content" style="display:none;">{!! $course->course_description ?? '' !!}</textarea>
                            </div>
                            <div class="itemFlex">
                                <p class="text-gray">What you will learn in this course</p>
                                <br>
                                <div style="text-align: center;">
                                <div id="input-list" class="input-list">

                                </div>
                                <br>
                                <a id="add-item" class="add-item-btn mt-10">Add Item</a>
                                </div>

                            </div>
                        </div>


                        <div class="widget__title mt-20 pb-10" style="border-bottom: 2px solid #E4E4E4;">Speaker background</div>
                        <br>
                        <div class="showFlex" style="padding-bottom:20px">
                            <div class="itemFlex">
                                <div class="image-upload-container">
                                    <div class="image-preview" id="imagePreview1">
                                        <img id="previewImage1" src="{{ $course->Speaker->first()->avatar ?? url('img/Mask@2x.png') }}" alt="Preview">
                                        <a class="remove-image-btn" id="removeImage1">&times;</a>
                                    </div>
                                    <label for="imageUpload1" class="upload-btn">Upload Image</label>
                                    <input type="file" id="imageUpload1" name="speaker_img" accept="image/*" style="display: none;">
                                    <p class="image-size-text">Banner Size xxx x xxx px</p>
                                </div>

                                <div class="field__label">Professor Name</div>
                                <div class="field__wrap">
                                    <input class="field__input" name="speaker_name" type="text" placeholder="Name here..."
                                        value="{{ $course->Speaker->first()->name ?? '' }}">
                                </div>

                                <div class="field__label mt-10">Job Position</div>
                                <div class="field__wrap">
                                    <input class="field__input" name="speaker_job" type="text" placeholder="Job Position here..."
                                        value="{{ $course->Speaker->first()->job_position ?? '' }}">
                                </div>

                                <div>
                                    <div class="field__label mt-10">Country</div>
                                    <div class="field__wrap">
                                        <select class="field__input" name="speaker_country">
                                            <option>Select Speaker’s Country</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}"
                                                        {{ $course->Speaker->first()->country == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="itemFlex">
                                <div id="editor-container2" style="height: 320px;">{!! $course->Speaker->first()->description ?? '' !!}</div>
                                <textarea name="speaker_background" id="quill-content2" style="display:none;">{!! $course->Speaker->first()->description ?? '' !!}</textarea>

                                <div class="mt-10">
                                    <label for="file-input" class="upload-btn">Upload File</label>
                                    <input type="file" id="file-input" name="file_speaker" class="hidden-input">

                                    @if (!empty($course->Speaker->first()->file))
                                        <p id="file-name" class="file-name" style="display: block;">
                                            <a target="blank" href="{{ $course->Speaker->first()->file }}" download>
                                                {{ basename($course->Speaker->first()->file) }}
                                            </a>
                                        </p>
                                    @else
                                        <p id="file-name" class="file-name" style="display: none;">
                                            No file chosen
                                        </p>
                                    @endif


                                </div>
                            </div>
                        </div>




                        <div class="widget__title mt-20 pb-10" style="border-bottom: 2px solid #E4E4E4;">Reference detail</div>
                        <br>
                        <div class="showFlex" style="padding-bottom:20px">
                            <div class="itemFlex">
                                <div class="image-upload-container">
                                    <div class="image-preview" id="imagePreview2">
                                        <img id="previewImage2"
                                            src="{{ $course->referances->first()->image ?? url('img/Mask@2x.png') }}"
                                            alt="Preview">
                                        <a class="remove-image-btn" id="removeImage2">&times;</a>
                                    </div>
                                    <label for="imageUpload2" class="upload-btn">Upload Image</label>
                                    <input type="file" id="imageUpload2" name="reference_img" accept="image/*" style="display: none;">
                                    <p class="image-size-text">Banner Size xxx x xxx px</p>
                                </div>

                                <div class="field__label">Product Title</div>
                                <div class="field__wrap">
                                    <input class="field__input" name="product_name" type="text" placeholder="Name here..."
                                        value="{{ $course->referances->first()->title ?? '' }}">
                                </div>
                                <br>
                                <div class="mt-10">
                                    <label for="file-input1" class="upload-btn">Upload File</label>
                                    <input type="file" id="file-input1" name="file_product" class="hidden-input">
                                    @if (!empty($course->referances->first()->file))
                                        <p id="file-name1" class="file-name" style="display: block;">
                                            <a target="blank" href="{{ $course->referances->first()->file }}" download>
                                                {{ basename($course->referances->first()->file) }}
                                            </a>
                                        </p>
                                    @else
                                        <p id="file-name1" class="file-name" style="display: none;">
                                            No file chosen
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="itemFlex">
                                <div id="editor-container3" style="height: 320px;">{!! $course->referances->first()->description ?? '' !!}</div>
                                <textarea name="reference_detail" id="quill-content3" style="display:none;">{!! $course->referances->first()->description ?? '' !!}</textarea>
                            </div>
                        </div>


                        <div class="products__more">
                            <a class=" btn btn_green submit_ans" >Save</a>
                            {{-- <a class=" btn btn_green" onclick="activateTab2()">Next</a> --}}
                        </div>
                    </div>

                </div>

            </div>

            </form>


        </div>
    </div>


@endsection

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

@section('scripts')


    <script>

    const fileInput = document.getElementById('file-input');
    const fileNameDisplay = document.getElementById('file-name');

    fileInput.addEventListener('change', function () {
      if (fileInput.files.length > 0) {
        fileNameDisplay.textContent = `Selected file: ${fileInput.files[0].name}`;
        fileNameDisplay.style.display = 'block';
      } else {
        fileNameDisplay.textContent = 'No file chosen';
        fileNameDisplay.style.display = 'none';
      }
    });

    const fileInput1 = document.getElementById('file-input1');
    const fileNameDisplay1 = document.getElementById('file-name1');

    fileInput1.addEventListener('change', function () {
      if (fileInput1.files.length > 0) {
        fileNameDisplay1.textContent = `Selected file: ${fileInput1.files[0].name}`;
        fileNameDisplay1.style.display = 'block';
      } else {
        fileNameDisplay1.textContent = 'No file chosen';
        fileNameDisplay1.style.display = 'none';
      }
    });


    document.querySelector('.submit_ans').addEventListener('click', function(e) {
            e.preventDefault(); // ป้องกันการทำงานแบบ default ของ <a>

            // ดึงข้อมูลจาก QuillJS
            const courseDescription = quill1.root.innerHTML;
            const speakerBackground = quill2.root.innerHTML;
            const reference = quill3.root.innerHTML;

            // Update hidden textareas with Quill content
            document.querySelector('#quill-content').value = courseDescription;
            document.querySelector('#quill-content2').value = speakerBackground;
            document.querySelector('#quill-content3').value = reference;

            // Submit ฟอร์ม
            document.getElementById('questionForm').submit();
        });


    function activateTab2() {
    // เปิด Tab2
    const tab2 = document.getElementById('tab2');
    const tabs = document.querySelectorAll('.products');
    tabs.forEach(tab => {
        if (tab) {
            tab.classList.remove('active'); // ซ่อนทุก Tab
        }
    });
    if (tab2) {
        tab2.classList.add('active'); // เปิด Tab2
    }

    // เปลี่ยนสถานะปุ่ม
    const allButtons = document.querySelectorAll('.inbox__btn');
    allButtons.forEach(button => {
        button.classList.remove('btn_blue', 'active'); // ลบสถานะจากปุ่มทั้งหมด
        button.classList.add('btn_white'); // ตั้งค่าเป็น White
    });

    const courseDetailButton = document.querySelector(
        '.inbox__btn[onclick="showTab(\'tab2\', this)"]'
    );
    if (courseDetailButton) {
        courseDetailButton.classList.remove('btn_white'); // ลบคลาส btn_white
        courseDetailButton.classList.add('btn_blue', 'active'); // เพิ่มคลาส btn_blue และ active
    }
}


     document.addEventListener('DOMContentLoaded', () => {
    const inputList = document.getElementById('input-list');
    const addItemBtn = document.getElementById('add-item');

    // Function to create a new input item
    function createInputItem(value = '') {
        const inputItem = document.createElement('div');
        inputItem.classList.add('input-item');

        const inputField = document.createElement('input');
        inputField.type = 'text';
        inputField.placeholder = 'Enter text here';
        inputField.classList.add('choice-input');
        inputField.name = 'item_des[]';
        inputField.value = value;

        const removeBtn = document.createElement('button');
        removeBtn.classList.add('remove-btn');
        removeBtn.innerHTML = '&times;';
        removeBtn.type = 'button';

        // Add click event listener to remove button
        removeBtn.addEventListener('click', function () {
            inputList.removeChild(inputItem); // Remove the item from the list
        });

        inputItem.appendChild(inputField);
        inputItem.appendChild(removeBtn);

        return inputItem;
    }

    // Add existing items from the server
    const existingItems = @json($course->itemDes->pluck('detail'));
    existingItems.forEach(detail => {
        const newInputItem = createInputItem(detail);
        inputList.appendChild(newInputItem);
    });

    // Add new item dynamically
    addItemBtn.addEventListener('click', () => {
        const newInputItem = createInputItem();
        inputList.appendChild(newInputItem);
    });
});




 function showTab(tabId, button) {
    // ซ่อนเนื้อหา Tabs ทั้งหมด
    const tabs = document.querySelectorAll('.products');
    tabs.forEach(tab => {
        if (tab) {
            tab.classList.remove('active');
        }
    });

    // แสดง Tab ที่เลือก
    const selectedTab = document.getElementById(tabId);
    if (selectedTab) {
        selectedTab.classList.add('active');
    }

    // รีเซ็ตสถานะปุ่ม
    const buttons = document.querySelectorAll('.inbox__btn');
    buttons.forEach(btn => {
        if (btn) {
            btn.classList.remove('btn_blue', 'active');
            btn.classList.add('btn_white');
        }
    });

    // ตั้งค่าสถานะ Active ให้ปุ่มที่คลิก
    if (button && button.classList.contains('inbox__btn')) {
        button.classList.remove('btn_white');
        button.classList.add('btn_blue', 'active');
    }
}



        document.getElementById("imageUpload").addEventListener("change", function(event) {
            const file = event.target.files[0];
            const previewImage = document.getElementById("previewImage");
            const removeButton = document.getElementById("removeImage");

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                };
                reader.readAsDataURL(file);
                removeButton.style.display = "block";
            }
        });

        document.getElementById("imageUpload1").addEventListener("change", function(event) {
            const file = event.target.files[0];
            const previewImage = document.getElementById("previewImage1");
            const removeButton = document.getElementById("removeImage1");

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                };
                reader.readAsDataURL(file);
                removeButton.style.display = "block";
            }
        });

        document.getElementById("imageUpload2").addEventListener("change", function(event) {
            const file = event.target.files[0];
            const previewImage = document.getElementById("previewImage2");
            const removeButton = document.getElementById("removeImage2");

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                };
                reader.readAsDataURL(file);
                removeButton.style.display = "block";
            }
        });

        document.getElementById("removeImage").addEventListener("click", function() {
            const previewImage = document.getElementById("previewImage");
            previewImage.src = "{{ url('img/Mask@1.5x.png') }}"; // Default image path
            this.style.display = "none";
            document.getElementById("imageUpload").value = ""; // Reset the input
        });


        document.getElementById("removeImage1").addEventListener("click", function() {
            const previewImage = document.getElementById("previewImage1");
            previewImage.src = "{{ url('img/Mask@1.5x.png') }}"; // Default image path
            this.style.display = "none";
            document.getElementById("imageUpload1").value = ""; // Reset the input
        });


        document.getElementById("removeImage2").addEventListener("click", function() {
            const previewImage = document.getElementById("previewImage2");
            previewImage.src = "{{ url('img/Mask@1.5x.png') }}"; // Default image path
            this.style.display = "none";
            document.getElementById("imageUpload2").value = ""; // Reset the input
        });


        var quill1 = new Quill('#editor-container', {
            theme: 'snow', // 'snow' เป็นธีมที่ใช้งานง่ายและสวยงาม
            placeholder: 'Write your question here...',
            modules: {
                toolbar: [
                    [{
                        header: [1, 2, false]
                    }],
                    ['bold', 'italic', 'underline'],
                    ['image', 'code-block']
                ]
            },
        });


        // Set initial content in the Quill Editor


        document.querySelector('.submit_ans').addEventListener('click', function (e) {
    e.preventDefault();
    document.querySelector('#quill-content').value = quill1.root.innerHTML;
    document.querySelector('#quill-content2').value = quill2.root.innerHTML;
    document.querySelector('#quill-content3').value = quill3.root.innerHTML;
    document.getElementById('questionForm').submit();
});

        const quill2 = new Quill('#editor-container2', {
            theme: 'snow',
            placeholder: 'Write the speaker background...',
            modules: {
            toolbar: [
                [{ header: [1, 2, false] }],
                ['bold', 'italic', 'underline'],
                ['image', 'code-block'],
            ],
            },
        });





        const quill3 = new Quill('#editor-container3', {
            theme: 'snow',
            placeholder: 'Write the speaker background...',
            modules: {
            toolbar: [
                [{ header: [1, 2, false] }],
                ['bold', 'italic', 'underline'],
                ['image', 'code-block'],
            ],
            },
        });







    // Image preview for referances
    const imageInput2 = document.getElementById('imageUpload2');
    const imagePreview2 = document.getElementById('previewImage2');
    const removeButton2 = document.getElementById('removeImage2');

    imageInput2.addEventListener('change', function () {
        const file = imageInput2.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imagePreview2.src = e.target.result;
            };
            reader.readAsDataURL(file);
            removeButton2.style.display = 'block';
        }
    });

    removeButton2.addEventListener('click', function () {
        imagePreview2.src = "{{ url('img/Mask@2x.png') }}"; // Reset to default image
        imageInput2.value = ''; // Clear file input
        this.style.display = 'none';
    });


// Image preview for speaker
    const imageInput = document.getElementById('imageUpload1');
    const imagePreview = document.getElementById('previewImage1');
    const removeButton = document.getElementById('removeImage1');

    imageInput.addEventListener('change', function () {
        const file = imageInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
            removeButton.style.display = 'block';
        }
    });

    removeButton.addEventListener('click', function () {
        imagePreview.src = "{{ url('img/Mask@2x.png') }}"; // Reset to default image
        imageInput.value = ''; // Clear file input
        this.style.display = 'none';
    });

    </script>

@stop('scripts')
