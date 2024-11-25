  <div class="sidebar">
      <div class="sidebar__top">
          <button class="sidebar__close">
              <svg class="icon icon-close">
                  <use xlink:href="#icon-close"></use>
              </svg>
          </button>
          <a class="sidebar__logo" href="index.html"><img class="sidebar__pic sidebar__pic_black"
                  src="{{ url('img/1732202759709.jpg') }}" alt="" />
              <img class="sidebar__pic sidebar__pic_white" src="{{ url('img/1732202759709.jpg') }}" alt="" />
          </a>
          <button class="sidebar__burger"></button>
      </div>
      <div class="sidebar__wrapper">
          <div class="sidebar__inner"><a class="sidebar__logo" href="{{ url('/') }}"><img class="sidebar__pic"
                      src="img/logo-sm.png" alt="" /></a>
              <div class="sidebar__list">
                  <div class="sidebar__group">
                      <div class="sidebar__caption caption-sm">Admin<span> tools</span></div>
                      <div class="sidebar__menu">
                            <a class="sidebar__item {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="{{ url('/admin/dashboard') }}">
                              <div class="sidebar__icon">
                                  @if (request()->is('admin/dashboard'))
                                  <img class="details__pic" src="{{ url('/img/wicon/Overview.png') }}" alt="">
                                  @else
                                   <img class="details__pic" src="{{ url('/img/icon/Overview@2x.png') }}" alt="">
                                  @endif
                              </div>
                              <div class="sidebar__text">Overview</div>
                          </a><a class="sidebar__item {{ request()->is('admin/course') ? 'active' : '' }}" href="{{ url('/admin/course') }}">
                              <div class="sidebar__icon">

                                  @if (request()->is('admin/course'))
                                  <img class="details__pic" src="{{ url('/img/wicon/🍎Icon@2x.png') }}" alt="">
                                  @else
                                   <img class="details__pic" src="{{ url('/img/icon/🍎Icon@2x.png') }}" alt="">
                                  @endif
                              </div>
                              <div class="sidebar__text">Course</div>
                          </a><a class="sidebar__item {{ request()->is('admin/quiz') ? 'active' : '' }}" href="{{ url('/admin/quiz') }}">
                              <div class="sidebar__icon">
                                  @if (request()->is('admin/quiz'))
                                  <img class="details__pic" src="{{ url('/img/wicon/🍎Icon@2x (1).png') }}" alt="">
                                  @else
                                   <img class="details__pic" src="{{ url('/img/icon/🍎Icon@2x (1).png') }}" alt="">
                                  @endif
                              </div>
                              <div class="sidebar__text">Quiz</div>
                          </a><a class="sidebar__item {{ request()->is('admin/survey') ? 'active' : '' }}" href="{{ url('/admin/survey') }}">
                              <div class="sidebar__icon">

                                  @if (request()->is('admin/survey'))
                                  <img class="details__pic" src="{{ url('/img/wicon/🍎Icon@2x (2).png') }}" alt="">
                                  @else
                                   <img class="details__pic" src="{{ url('/img/icon/🍎Icon@2x (2).png') }}" alt="">
                                  @endif
                              </div>
                              <div class="sidebar__text">Survey</div>
                          </a><a class="sidebar__item {{ request()->is('admin/members') ? 'active' : '' }}" href="{{ url('/admin/members') }}">
                              <div class="sidebar__icon">

                                  @if (request()->is('admin/members'))
                                  <img class="details__pic" src="{{ url('/img/wicon/🍎Icon@2x (6).png') }}" alt="">
                                  @else
                                   <img class="details__pic" src="{{ url('/img/icon/🍎Icon@2x (6).png') }}" alt="">
                                  @endif
                              </div>
                              <div class="sidebar__text">Members</div>
                          </a><a class="sidebar__item {{ request()->is('admin/adminUser') ? 'active' : '' }}" href="{{ url('/admin/adminUser') }}">
                              <div class="sidebar__icon">

                                  @if (request()->is('admin/adminUser'))
                                  <img class="details__pic" src="{{ url('/img/wicon/🍎Icon@2x (3).png') }}" alt="">
                                  @else
                                   <img class="details__pic" src="{{ url('/img/icon/🍎Icon@2x (3).png') }}" alt="">
                                  @endif
                              </div>
                              <div class="sidebar__text">Admin User</div>
                          </a>
                          <a class="sidebar__item {{ request()->is('admin/setting') ? 'active' : '' }}" href="{{ url('/admin/setting') }}">
                              <div class="sidebar__icon">

                                  @if (request()->is('admin/setting'))
                                  <img class="details__pic" src="{{ url('/img/wicon/🍎Icon@2x (4).png') }}" alt="">
                                  @else
                                   <img class="details__pic" src="{{ url('/img/icon/🍎Icon@2x (4).png') }}" alt="">
                                  @endif
                              </div>
                              <div class="sidebar__text">Settings</div>
                          </a>


                      </div>
                  </div>
                  <div class="sidebar__group">
                      <div class="sidebar__caption caption-sm">Insights</div>
                        <div class="sidebar__menu">

                            <a class="sidebar__item {{ request()->is('admin/systemlogs') ? 'active' : '' }}" href="{{ url('/admin/systemlogs') }}">
                              <div class="sidebar__icon">

                                  @if (request()->is('admin/systemlogs'))
                                  <img class="details__pic" src="{{ url('/img/wicon/🍎Icon@2x (5).png') }}" alt="">
                                  @else
                                   <img class="details__pic" src="{{ url('/img/icon/🍎Icon@2x (5).png') }}" alt="">
                                  @endif
                              </div>
                              <div class="sidebar__text">Systemlogs</div>
                              <div class="sidebar__counter">18</div>
                          </a>

                        </div>
                  </div>
              </div>
              <div class="sidebar__profile">
                  <div class="sidebar__details"><a class="sidebar__link" href="#">
                          <div class="sidebar__icon">
                              <svg class="icon icon-profile">
                                  <use xlink:href="#icon-profile"></use>
                              </svg>
                          </div>
                          <div class="sidebar__text">Profile</div>
                      </a><a class="sidebar__link" href="#">
                          <div class="sidebar__icon">
                              <svg class="icon icon-logout">
                                  <use xlink:href="#icon-logout"></use>
                              </svg>
                          </div>
                          <div class="sidebar__text">Log out</div>
                      </a></div><a class="sidebar__user" href="#">
                      <div class="sidebar__ava"><img class="sidebar__pic" src="img/ava.png" alt="" /></div>
                      <div class="sidebar__desc">
                          <div class="sidebar__man">Tam Tran</div>
                          <div class="sidebar__status caption">Free account</div>
                      </div>
                      <div class="sidebar__arrow">
                          <svg class="icon icon-arrows">
                              <use xlink:href="#icon-arrows"></use>
                          </svg>
                      </div>
                  </a>
              </div>
          </div>
      </div>
      <div class="sidebar__bottom">
          <label class="switch switch_theme js-switch-theme">
              <input class="switch__input" type="checkbox" /><span class="switch__in"><span
                      class="switch__box"></span><span class="switch__icon">
                      <svg class="icon icon-moon">
                          <use xlink:href="#icon-moon"></use>
                      </svg>
                      <svg class="icon icon-sun">
                          <use xlink:href="#icon-sun"></use>
                      </svg></span></span>
          </label>
          <button class="sidebar__download">
              <svg class="icon icon-download">
                  <use xlink:href="#icon-download"></use>
              </svg>
          </button>
      </div>
  </div>
