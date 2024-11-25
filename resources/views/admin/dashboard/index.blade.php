@extends('admin.layouts.template')

@section('title')
    <title>Course</title>
@stop
@section('stylesheet')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<style>
 body, html {
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
.Flag_icon_dash{
    width:35px;
    border-radius: 8px
}
.details__head .details__pic {
    max-width: 18px;
}
</style>

@stop('stylesheet')

@section('content')


    <div class="page__row" style="margin-top: 50px">
        <div class="page__col">
            <div class="details">
                <div class="details__container">
                  <div style="align-items: center;display: flex; margin-bottom :20px">
                  <img src="{{ url('img/philippines.svg') }}" class="Flag_icon_dash" />
                  <div class=" h6" style="margin-left: 8px">Overview Philippines Users</div>
                  </div>
                  <div class="details__row">
                    <div class="details__col">
                      <div class="details__top">
                        <div class="details__number h1">478</div><a class="details__line" href="#">
                          <div class="details__preview"><img class="details__pic" src="{{ url('/img/Members@1.5x.svg') }}" alt=""></div>
                          <div class="details__info caption-sm">Active Users Today</div></a>
                      </div>
                      <div class="details__bottom">
                        <div class="details__statistics">
                          <div class="details__chart details__chart_activity">
                            <div id="chart-active-users"></div>
                          </div>
                          <div class="details__status">
                            <div class="details__icon bg-blue">
                              <svg class="icon icon-arrow-down-fat">
                                <use xlink:href="#icon-arrow-down-fat"></use>
                              </svg>
                            </div>
                            <div class="details__percent caption-sm color-blue-dark">6%</div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="details__col">
                      <div class="details__box">
                        <div class="details__chart details__chart_counter">
                          <div id="chart-users-counter"></div>
                        </div>
                        <button class="details__remove">
                          <svg class="icon icon-remove">
                            <use xlink:href="#icon-remove"></use>
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="details__list details__list_four">
                    <div class="details__item">
                      <div class="details__head">
                        <div class="details__preview"><img class="details__pic" src="{{ url('/img/Members@1.5x.svg') }}" alt=""></div>
                        <div class="details__text caption-sm"> Registrations</div>
                      </div>
                      <div class="details__counter h3">5419</div>
                    </div>
                    <div class="details__item">
                      <div class="details__head">
                        <div class="details__preview"><img class="details__pic" src="{{ url('img/4.png') }}" alt=""></div>
                        <div class="details__text caption-sm">All Courses</div>
                      </div>
                      <div class="details__counter h3">302</div>
                    </div>
                    <div class="details__item">
                      <div class="details__head">
                        <div class="details__preview"><img class="details__pic" src="{{ url('img/1.png') }}" alt=""></div>
                        <div class="details__text caption-sm">Learning</div>
                      </div>
                      <div class="details__counter h3">2152</div>
                      <div class="details__indicator">
                        <div class="details__progress bg-blue" style="width: 55%;"></div>
                      </div>
                    </div>
                    <div class="details__item">
                      <div class="details__head">
                        <div class="details__preview"><img class="details__pic" src="{{ url('img/Shape.png') }}" alt=""></div>
                        <div class="details__text caption-sm">Complete</div>
                      </div>
                      <div class="details__counter h3">1570</div>
                      <div class="details__indicator">
                        <div class="details__progress bg-green" style="width: 68%;"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="details">
              <div class="page__widgets details__container" style="margin:0; justify-content: space-between;">
                <div class="widget widget_shadow" style="margin:0; padding: 0px;">
                  <div class="widget__title">Most Popular Categories</div>
                  <div class="goal">

                    <div class="goal__list">
                      <div class="goal__item">
                        <div class="goal__head">
                          <div class="goal__title title">Parasiticides</div>
                          <div class="goal__percent title">40%</div>
                        </div>
                        <div class="goal__indicator">
                          <div class="goal__progress bg-purple" style="width: 40%;"></div>
                        </div>
                      </div>
                      <div class="goal__item">
                        <div class="goal__head">
                          <div class="goal__title title">Immunology</div>
                          <div class="goal__percent title">25%</div>
                        </div>
                        <div class="goal__indicator">
                          <div class="goal__progress bg-green" style="width: 25%;"></div>
                        </div>
                      </div>
                      <div class="goal__item">
                        <div class="goal__head">
                          <div class="goal__title title">Orthopaedic</div>
                          <div class="goal__percent title">50%</div>
                        </div>
                        <div class="goal__indicator">
                          <div class="goal__progress bg-yellow" style="width: 50%;"></div>
                        </div>
                      </div>
                      <div class="goal__item">
                        <div class="goal__head">
                          <div class="goal__title title">Cardiology</div>
                          <div class="goal__percent title">80%</div>
                        </div>
                        <div class="goal__indicator">
                          <div class="goal__progress bg-pink" style="width: 80%;"></div>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
                <div class="widget widget_shadow" style="margin:0; padding: 0px;">
                  <div class="widget__title"></div>
                  <div class="goal" >

                    <div class="goal__list" style="margin-top:57px;">
                      <div class="goal__item">
                        <div class="goal__head">
                          <div class="goal__title title">Nephrology</div>
                          <div class="goal__percent title">40%</div>
                        </div>
                        <div class="goal__indicator">
                          <div class="goal__progress bg-purple" style="width: 40%;"></div>
                        </div>
                      </div>
                      <div class="goal__item">
                        <div class="goal__head">
                          <div class="goal__title title">Dentistry</div>
                          <div class="goal__percent title">25%</div>
                        </div>
                        <div class="goal__indicator">
                          <div class="goal__progress bg-green" style="width: 25%;"></div>
                        </div>
                      </div>
                      <div class="goal__item">
                        <div class="goal__head">
                          <div class="goal__title title">Neutrology</div>
                          <div class="goal__percent title">50%</div>
                        </div>
                        <div class="goal__indicator">
                          <div class="goal__progress bg-yellow" style="width: 50%;"></div>
                        </div>
                      </div>
                      <div class="goal__item">
                        <div class="goal__head">
                          <div class="goal__title title">Ophthalmology</div>
                          <div class="goal__percent title">80%</div>
                        </div>
                        <div class="goal__indicator">
                          <div class="goal__progress bg-pink" style="width: 80%;"></div>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
              </div>

        </div>
        <div class="page__col ">
            <div class="widget widget_shadow">
                <div class="widget__title">Most Popular Courses</div>
                <div class="widget__list">

                    <a class="widget__item" href="products.html">
                        <div class="widget__preview "><img class="widget__pic" src="{{ url('img/Rectangle Copy@2x.png') }}" alt="">
                        </div>
                        <div class="widget__details">
                            <div class="widget__category title">Uspendisse bibendum dolor dia m, quis luctus
                                <svg class="icon icon-arrow-right">
                                    <use xlink:href="#icon-arrow-right"></use>
                                </svg>
                            </div>
                        </div>
                    </a>
                    <a class="widget__item" href="products.html">
                        <div class="widget__preview "><img class="widget__pic" src="{{ url('img/Rectangle Copy@2x (1).png') }}" alt="">
                        </div>
                        <div class="widget__details">
                            <div class="widget__category title">Nulla fermentum bibendum nunc
                                <svg class="icon icon-arrow-right">
                                    <use xlink:href="#icon-arrow-right"></use>
                                </svg>
                            </div>
                        </div>
                    </a>
                    <a class="widget__item" href="products.html">
                        <div class="widget__preview "><img class="widget__pic" src="{{ url('img/Rectangle Copy@2x (2).png') }}" alt="">
                        </div>
                        <div class="widget__details">
                            <div class="widget__category title">Cras dapibus ex et dapibus rhoncus.
                                <svg class="icon icon-arrow-right">
                                    <use xlink:href="#icon-arrow-right"></use>
                                </svg>
                            </div>
                        </div>
                    </a>
                    <a class="widget__item" href="products.html">
                        <div class="widget__preview "><img class="widget__pic" src="{{ url('img/Rectangle Copy@2x (3).png') }}" alt="">
                        </div>
                        <div class="widget__details">
                            <div class="widget__category title">Uspendisse bibendum dolor diam, quis luctus
                                <svg class="icon icon-arrow-right">
                                    <use xlink:href="#icon-arrow-right"></use>
                                </svg>
                            </div>
                        </div>
                    </a>
                    <a class="widget__item" href="products.html">
                        <div class="widget__preview "><img class="widget__pic" src="{{ url('img/Rectangle Copy@2x.png') }}" alt="">
                        </div>
                        <div class="widget__details">
                            <div class="widget__category title">Uspendisse bibendum dolor dia m, quis luctus
                                <svg class="icon icon-arrow-right">
                                    <use xlink:href="#icon-arrow-right"></use>
                                </svg>
                            </div>
                        </div>
                    </a>
                    <a class="widget__item" href="products.html">
                        <div class="widget__preview "><img class="widget__pic" src="{{ url('img/Rectangle Copy@2x (1).png') }}" alt="">
                        </div>
                        <div class="widget__details">
                            <div class="widget__category title">Nulla fermentum bibendum nunc
                                <svg class="icon icon-arrow-right">
                                    <use xlink:href="#icon-arrow-right"></use>
                                </svg>
                            </div>
                        </div>
                    </a>
                    <a class="widget__item" href="products.html">
                        <div class="widget__preview "><img class="widget__pic" src="{{ url('img/Rectangle Copy@2x (2).png') }}" alt="">
                        </div>
                        <div class="widget__details">
                            <div class="widget__category title">Cras dapibus ex et dapibus rhoncus.
                                <svg class="icon icon-arrow-right">
                                    <use xlink:href="#icon-arrow-right"></use>
                                </svg>
                            </div>
                        </div>
                    </a>
                    <a class="widget__item" href="products.html">
                        <div class="widget__preview "><img class="widget__pic" src="{{ url('img/Rectangle Copy@2x (3).png') }}" alt="">
                        </div>
                        <div class="widget__details">
                            <div class="widget__category title">Uspendisse bibendum dolor diam, quis luctus
                                <svg class="icon icon-arrow-right">
                                    <use xlink:href="#icon-arrow-right"></use>
                                </svg>
                            </div>
                        </div>
                    </a>
                    <a class="widget__item" href="products.html">
                        <div class="widget__preview "><img class="widget__pic" src="{{ url('img/Rectangle Copy@2x.png') }}" alt="">
                        </div>
                        <div class="widget__details">
                            <div class="widget__category title">Uspendisse bibendum dolor dia m, quis luctus
                                <svg class="icon icon-arrow-right">
                                    <use xlink:href="#icon-arrow-right"></use>
                                </svg>
                            </div>
                        </div>
                    </a>
                    <a class="widget__item" href="products.html">
                        <div class="widget__preview "><img class="widget__pic" src="{{ url('img/Rectangle Copy@2x (1).png') }}" alt="">
                        </div>
                        <div class="widget__details">
                            <div class="widget__category title">Nulla fermentum bibendum nunc
                                <svg class="icon icon-arrow-right">
                                    <use xlink:href="#icon-arrow-right"></use>
                                </svg>
                            </div>
                        </div>
                    </a>
                    <a class="widget__item" href="products.html">
                        <div class="widget__preview "><img class="widget__pic" src="{{ url('img/Rectangle Copy@2x (2).png') }}" alt="">
                        </div>
                        <div class="widget__details">
                            <div class="widget__category title">Cras dapibus ex et dapibus rhoncus.
                                <svg class="icon icon-arrow-right">
                                    <use xlink:href="#icon-arrow-right"></use>
                                </svg>
                            </div>
                        </div>
                    </a>



                </div>
                <br>
                <div class="widget__btns">
                      <button class="widget__btn btn btn_black  btn_wide">All Course</button>
                </div>

            </div>



        </div>
    </div>


@endsection

@section('scripts')

@stop('scripts')
