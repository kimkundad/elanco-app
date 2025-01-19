<?php

namespace App\Http\Services\Settings;

class SettingService
{
    private PageBannerService $pageBannerService;
    private HomeBannerService $homeBannerService;

    public function __construct(PageBannerService $pageBannerService, HomeBannerService $homeBannerService)
    {
        $this->pageBannerService = $pageBannerService;
        $this->homeBannerService = $homeBannerService;
    }

    public function findAll()
    {
        return [
            'pageBanners' => $this->pageBannerService->findAll(),
            'homeBanners' => $this->homeBannerService->findAll()
        ];
    }
}
