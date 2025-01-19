<?php

namespace App\Http\Services\Settings;

class SettingService
{
    private PageBannerService $pageBannerService;
    private HomeBannerService $homeBannerService;
    private FeaturedCourseService $featuredCourseService;

    public function __construct(PageBannerService $pageBannerService, HomeBannerService $homeBannerService, FeaturedCourseService $featuredCourseService)
    {
        $this->pageBannerService = $pageBannerService;
        $this->homeBannerService = $homeBannerService;
        $this->featuredCourseService = $featuredCourseService;
    }

    public function findAll()
    {
        return [
            'pageBanners' => $this->pageBannerService->findAll(),
            'homeBanners' => $this->homeBannerService->findAll(),
            'featuredCourses' => $this->featuredCourseService->findAll(),
        ];
    }
}
