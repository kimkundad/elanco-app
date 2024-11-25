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

        .products__details {
            padding-left: 0px;
        }

        .pl-5 {
            padding-left: 8px
        }

        .banner-section {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        /* Individual Banner */
        .banner {
            display: flex;
            gap: 20px;
            background: #fff;
            border-bottom: 1px solid #ddd;
            margin-top: 15px;
            border-radius: 8px;
            padding: 0px;
        }

        /* Banner Image Section */
        .banner-image {
            position: relative;
            max-width: 300px;
            border-radius: 8px;
            overflow: hidden;
        }

        .banner-image img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }



        /* Banner Content Section */
        .banner-content {
            flex: 1;
        }

        .banner-content h3 {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .banner-content p {
            font-size: 14px;
            line-height: 1.6;
            color: #666;
            margin-bottom: 20px;
        }

        /* Settings Button */
        .settings-button {
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 14px;
        }

        .settings-button:hover {
            background-color: #e0e0e0;
        }
        .box-btn{
            display: flex;
            align-items: center;
        }
    </style>

@stop('stylesheet')

@section('content')


    <div class="page__content" style="margin-top: 50px; padding: 0">
        <div class="page__stat ">


            <div class="products__title h6 mobile-hide">Setting</div>
            <div class="products__info caption mobile-hide" style="color: #B2B3BD; font-weight: 400;">Engage with our
                eye-catching banners featuring key promotions and discover popular courses to enhance your skils.</div>

            <div class="banner-section" style="margin-top: 50px">

                <!-- Home Banner -->
                <div>
                <div class="page__hello h5">Main Page Banner</div>
                <div class="banner">
                    <div class="banner-image">
                        <img src="{{ url('img/Screenshot 2567-10-25 at 23.27.03@1.5x.png') }}" alt="Home Banner">
                    </div>
                    <div class="banner-content">
                        <p>
                            The Main Banner is a prominent feature on the
                            homepage, designed to capture immediate
                            attention and communicate the most important
                            messages, offers, or announcements. This banner
                            acts as a visual anchor, ensuring users quickly
                            understand the primary focus or value of the site or
                            app.
                        </p>
                    </div>
                    <div class="box-btn">
                        <button class="notification__btn btn btn_gray">Go to setting</button>
                    </div>
                </div>
                </div>

                <!-- Home Banner -->
                <div>
                <div class="page__hello h5">Home Banner</div>
                <div class="banner">
                    <div class="banner-image">
                        <img src="{{ url('img/Screenshot 2567-10-25 at 23.27.03.png') }}" alt="Home Banner">
                    </div>
                    <div class="banner-content">
                        <p>
                           The Home Banner is a key visual space that grabs
                            users' attention right as they arrive, highlighting
                            essential promotions, updates, or featured content.
                            A well-designed Home Banner enhances user
                            engagement and directs traffic to priority areas
                            within the site
                        </p>
                    </div>
                    <div class="box-btn">
                        <button class="notification__btn btn btn_gray">Go to setting</button>
                    </div>
                </div>
                </div>


                <!-- Home Banner -->
                <div>
                <div class="page__hello h5">Featured Course Manager</div>
                <div class="banner">
                    <div class="banner-image">
                        <img src="{{ url('img/Rectangle@1.5x.png') }}" alt="Home Banner">
                    </div>
                    <div class="banner-content">
                        <p>
                            This section showcases key courses, offering a
                            quick overview of popular or recommended
                            learning options. It's an essential feature to
                            promote valuable content, helping users discover
                            educational opportunities at a glance.
                        </p>
                    </div>
                    <div class="box-btn">
                        <button class="notification__btn btn btn_gray">Go to setting</button>
                    </div>
                </div>
                </div>

            </div>


        </div>
    </div>


@endsection

@section('scripts')

@stop('scripts')
