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

        .products__cell:first-child {
            width: 20px;
        }

        .products__cell {
            display: table-cell;
            vertical-align: middle;
            padding-left: 20px;
            padding-top: 18px;
            padding-bottom: 15px;
            border-bottom: 1px solid #E4E4E4;
        }

        .btn {
            min-width: 166px;
            height: 56px;
            padding: 10px 20px;
            border-radius: 16px / 16px;
            font-family: "Inter", sans-serif;
            font-size: 14px;
            line-height: 1.4;
            font-weight: 700;
            transition: all 0.25s;
        }
        .checkbox{
                margin-bottom: 15px;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

@stop('stylesheet')

@section('content')


    <div class="page__content" style="margin-top: 50px; padding: 0">
        <div class="page__stat ">


            <div class="page__stat page__stat_pt32">




                <div class="products products_main">

                    @if (session('edit_success'))
                        <div class="alert alert-success">
                            {{ session('edit_success') }}
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

                    <form method="POST" action="{{ $url }}" enctype="multipart/form-data">
                        {{ method_field($method) }}
                        {{ csrf_field() }}
                        <div class="widget__title pb-10" style="border-bottom: 2px solid #E4E4E4;"> Edit Quiz</div>
                        <br>
                        <div class="showFlex" style=" padding-bottom:20px">
                            <div style="width: 100%; padding: 10px">
                                <div class="showFlex ">

                                    <div style="width: 50%;" class="p-10">
                                        <div class="field__label mt-10">Quiz ID</div>
                                        <div class="field__wrap">
                                            <input class="field__input" name="quiz_id" type="text" placeholder="Q001"
                                                value="{{ $objs->quiz_id }}">
                                        </div>
                                    </div>

                                    <div style="width: 50%;" class="p-10">
                                        <div class="field__label mt-10">Expire date</div>
                                        <div class="field__wrap">
                                            <input class="field__input" id="expire_date" name="expire_date" type="text"
                                                placeholder="Choose the date" value="{{ $objs->expire_date }}">
                                        </div>
                                    </div>

                                </div>

                                <div class="field__label">Questions Title</div>
                                <div class="field__wrap">
                                    <input class="field__input" name="questions_title" type="text"
                                        placeholder="Title Here…" value="{{ $objs->questions_title }}">
                                </div>

                                <div class="showFlex ">

                                    <div style="width: 50%;" class="p-10">
                                        <div class="field__label mt-10">Pass Percentage</div>
                                        <div class="field__wrap">
                                            <select class="field__input" name="pass_percentage">
                                                <option value="" disabled
                                                    {{ empty($objs->pass_percentage) ? 'selected' : '' }}>Select Pass
                                                    Percentage</option>
                                                <option value="50" {{ $objs->pass_percentage == 50 ? 'selected' : '' }}>
                                                    50%</option>
                                                <option value="60"
                                                    {{ $objs->pass_percentage == 60 ? 'selected' : '' }}>60%</option>
                                                <option value="70"
                                                    {{ $objs->pass_percentage == 70 ? 'selected' : '' }}>70%</option>
                                                <option value="80"
                                                    {{ $objs->pass_percentage == 80 ? 'selected' : '' }}>80%</option>
                                                <option value="90"
                                                    {{ $objs->pass_percentage == 90 ? 'selected' : '' }}>90%</option>
                                                <option value="100"
                                                    {{ $objs->pass_percentage == 100 ? 'selected' : '' }}>100%</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div style="width: 50%;" class="p-10">
                                        <div class="field__label mt-10">Certificate Received</div>
                                        <div class="field__wrap">
                                            <select class="field__input" name="certificate">
                                                <option value="" disabled
                                                    {{ empty($objs->certificate) ? 'selected' : '' }}>Yes or No</option>
                                                <option value="1" {{ $objs->certificate == 1 ? 'selected' : '' }}>Yes
                                                </option>
                                                <option value="0" {{ $objs->certificate == 0 ? 'selected' : '' }}>No
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="field__label mt-10">CPD / CE points granted</div>
                                <div class="field__wrap">
                                    <input class="field__input" name="point_cpd" type="text" placeholder="5"
                                        value="{{ $objs->point_cpd }}">
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


    <div class="page__content" style="margin-top: 50px; padding: 0">
        <div class="page__stat ">
            <div class="page__stat page__stat_pt32">
                <div class="products products_main">

                    <form id="questionForm" method="POST" action="{{ url('admin/PostQuestion/' . $objs->id) }}"
                        enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="widget__title pb-10" style="border-bottom: 2px solid #E4E4E4;"> Question Set</div>

                        <div class="showFlex" style=" padding-bottom:20px">
                            <div style="width: 100%; padding: 10px">
                                <div class="showFlex ">

                                    <div style="width: 50%;" class="p-10">
                                        <div id="editor-container" style="height: 320px;"></div>
                                        <textarea name="detail" id="quill-content" style="display:none;"></textarea>
                                    </div>

                                    <div style="width: 50%;" class="p-10">


                                        <div class="products__table" id="productsTable">


                                            <div class="products__row">
                                                <div class="products__cell">
                                                    <label class="checkbox">
                                                        <!-- Hidden input ซ่อนเพื่อส่งค่า 0 ถ้า checkbox ไม่ถูกติ๊ก -->
                                                        <input type="hidden" class="hidden_status"
                                                            name="answers_status[]" value="0">
                                                        <!-- Checkbox สำหรับส่งค่า 1 -->
                                                        <input class="checkbox__input" type="checkbox" value="1">
                                                        <span class="checkbox__in"><span
                                                                class="checkbox__tick"></span></span>
                                                    </label>
                                                </div>
                                                <div class="products__cell color-gray">
                                                    <!-- ช่องกรอกคำตอบ -->
                                                    <input class="field__input" name="answer[]" type="text"
                                                        placeholder="answer">
                                                </div>
                                                <div class="products__cell text-right">
                                                    <a class="sorting__action remove_element">
                                                        <svg class="icon icon-box">
                                                            <use xlink:href="#icon-box"></use>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>


                                            <div class="products__row">
                                                <div class="products__cell">
                                                    <label class="checkbox">
                                                        <!-- Hidden input ซ่อนเพื่อส่งค่า 0 ถ้า checkbox ไม่ถูกติ๊ก -->
                                                        <input type="hidden" class="hidden_status"
                                                            name="answers_status[]" value="0">
                                                        <!-- Checkbox สำหรับส่งค่า 1 -->
                                                        <input class="checkbox__input" type="checkbox" value="1">
                                                        <span class="checkbox__in"><span
                                                                class="checkbox__tick"></span></span>
                                                    </label>
                                                </div>
                                                <div class="products__cell color-gray">
                                                    <!-- ช่องกรอกคำตอบ -->
                                                    <input class="field__input" name="answer[]" type="text"
                                                        placeholder="answer">
                                                </div>
                                                <div class="products__cell text-right">
                                                    <a class="sorting__action remove_element">
                                                        <svg class="icon icon-box">
                                                            <use xlink:href="#icon-box"></use>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>


                                            <div class="products__row">
                                                <div class="products__cell">
                                                    <label class="checkbox">
                                                        <!-- Hidden input ซ่อนเพื่อส่งค่า 0 ถ้า checkbox ไม่ถูกติ๊ก -->
                                                        <input type="hidden" class="hidden_status"
                                                            name="answers_status[]" value="0">
                                                        <!-- Checkbox สำหรับส่งค่า 1 -->
                                                        <input class="checkbox__input" type="checkbox" value="1">
                                                        <span class="checkbox__in"><span
                                                                class="checkbox__tick"></span></span>
                                                    </label>
                                                </div>
                                                <div class="products__cell color-gray">
                                                    <!-- ช่องกรอกคำตอบ -->
                                                    <input class="field__input" name="answer[]" type="text"
                                                        placeholder="answer">
                                                </div>
                                                <div class="products__cell text-right">
                                                    <a class="sorting__action remove_element">
                                                        <svg class="icon icon-box">
                                                            <use xlink:href="#icon-box"></use>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>


                                            <div class="products__row">
                                                <div class="products__cell">
                                                    <label class="checkbox">
                                                        <!-- Hidden input ซ่อนเพื่อส่งค่า 0 ถ้า checkbox ไม่ถูกติ๊ก -->
                                                        <input type="hidden" class="hidden_status"
                                                            name="answers_status[]" value="0">
                                                        <!-- Checkbox สำหรับส่งค่า 1 -->
                                                        <input class="checkbox__input" type="checkbox" value="1">
                                                        <span class="checkbox__in"><span
                                                                class="checkbox__tick"></span></span>
                                                    </label>
                                                </div>
                                                <div class="products__cell color-gray">
                                                    <!-- ช่องกรอกคำตอบ -->
                                                    <input class="field__input" name="answer[]" type="text"
                                                        placeholder="answer">
                                                </div>
                                                <div class="products__cell text-right">
                                                    <a class="sorting__action remove_element">
                                                        <svg class="icon icon-box">
                                                            <use xlink:href="#icon-box"></use>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>






                                        </div>

                                        <div class="products__more">
                                            <a class="products__btn btn btn_green" id="addAnswerBtn">Add Answer</a>
                                        </div>

                                    </div>

                                </div>

                                <hr>

                                <div class="products__more">
                                    <a class="products__btn btn btn_green submit_ans">Updaate Question</a>
                                </div>

                            </div>
                        </div>

                        <br>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <div class="page__content" style="margin-top: 50px; padding: 0">
        <div class="page__stat ">
            <div class="page__stat page__stat_pt32">
                <div class="products products_main">
                    <div class="widget__title pb-10" style="border-bottom: 2px solid #E4E4E4;"> Question list</div>

                        <div style="width: 100%; padding: 10px">
                            <div class="showFlex ">

                                @if($quiz)
                                    @foreach($quiz as $u)
                                        <div  class="p-10" style="border-right: 0.5px solid #f1f1f1;">
                                            <h3 style="margin-bottom: 15px; font-weight: 400; font-size: 14px; display: flex"> {{ $loop->iteration }}. {!! $u->detail !!}</h3>
                                            <div class="filters__variants" >

                                                @foreach($u->answers as $k)
                                                <label class="checkbox">
                                                    <input class="checkbox__input" type="checkbox" disabled
                                                    @if($k->answers_status == 1)
                                                    checked
                                                    @endif
                                                    >
                                                        <span class="checkbox__in">
                                                        <span class="checkbox__tick"></span>
                                                        <span class="checkbox__text">{{ $k->answers }}</span>
                                                    </span>
                                                </label>
                                                @endforeach

                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                            </div>
                        </div>

                </div>
            </div>
        </div>
    </div>



@endsection

@section('scripts')

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const checkboxes = document.querySelectorAll('.checkbox__input');

            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const hiddenInput = this.closest('.products__cell')
                        .querySelector('.hidden_status');

                    if (this.checked) {
                        hiddenInput.value = "1"; // เปลี่ยนค่า Hidden Input เป็น 1
                    } else {
                        hiddenInput.value = "0"; // เปลี่ยนกลับเป็น 0 ถ้าไม่ได้ติ๊ก
                    }
                });
            });
        });


        document.addEventListener("DOMContentLoaded", function() {
            flatpickr("#expire_date", {
                dateFormat: "d-m-Y", // กำหนดรูปแบบวัน-เดือน-ปี
                altInput: true, // แสดงรูปแบบที่สวยขึ้น
                altFormat: "d-m-Y", // รูปแบบในฟิลด์
                locale: {
                    firstDayOfWeek: 1 // เริ่มต้นวันจันทร์
                }
            });
        });


        // Initialize QuillJS Editor
        var quill = new Quill('#editor-container', {
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






        document.addEventListener("DOMContentLoaded", function () {
    const addAnswerBtn = document.getElementById("addAnswerBtn");
    const productsTable = document.getElementById("productsTable");

    // Function to create a new row
    function createNewRow() {
        const newRow = document.createElement("div");
        newRow.classList.add("products__row");
        newRow.innerHTML = `
            <div class="products__cell">
                <label class="checkbox">
                    <!-- Hidden input ซ่อนเพื่อส่งค่า 0 ถ้า checkbox ไม่ถูกติ๊ก -->
                    <input type="hidden" class="hidden_status" name="answers_status[]" value="0">
                    <!-- Checkbox สำหรับส่งค่า 1 -->
                    <input class="checkbox__input" type="checkbox" value="1">
                    <span class="checkbox__in"><span class="checkbox__tick"></span></span>
                </label>
            </div>
            <div class="products__cell color-gray">
                <!-- ช่องกรอกคำตอบ -->
                <input class="field__input" name="answer[]" type="text" placeholder="answer">
            </div>
            <div class="products__cell text-right">
                <a class="sorting__action remove_element">
                    <svg class="icon icon-box">
                        <use xlink:href="#icon-box"></use>
                    </svg>
                </a>
            </div>
        `;

        return newRow;
    }

    // Add a new row when "Add Answer" is clicked
    addAnswerBtn.addEventListener("click", function () {
        const newRow = createNewRow();
        productsTable.appendChild(newRow);
    });

    // ใช้ Event Delegation สำหรับ checkbox
    productsTable.addEventListener("change", function (event) {
        if (event.target.classList.contains("checkbox__input")) {
            const hiddenInput = event.target.closest(".products__cell")
                .querySelector(".hidden_status");

            hiddenInput.value = event.target.checked ? "1" : "0"; // เปลี่ยนค่า Hidden Input
        }
    });

    // ใช้ Event Delegation สำหรับปุ่มลบ
    productsTable.addEventListener("click", function (event) {
        if (event.target.closest(".remove_element")) {
            event.target.closest(".products__row").remove();
        }
    });
});




        document.querySelector('.submit_ans').addEventListener('click', function(e) {
            e.preventDefault(); // ป้องกันการทำงานแบบ default ของ <a>

            // ดึงข้อมูลจาก QuillJS
            var quillContent = quill.root.innerHTML;
            document.getElementById('quill-content').value = quillContent; // ใส่ค่าใน textarea

            // Submit ฟอร์ม
            document.getElementById('questionForm').submit();
        });
    </script>

@stop('scripts')
