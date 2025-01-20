<?php

namespace App\Http\Repositories\Settings;

use App\Models\Settings\PageBanner;
use Illuminate\Support\Facades\DB;

class PageBannerRepositoryImpl implements PageBannerRepository
{
    public function findAll(array $queryParams)
    {
        $query = PageBanner::with(['createdByUser.countryDetails', 'updatedByUser.countryDetails', 'country']);

        foreach ($queryParams as $key => $value) {
            $query->where($key, 'LIKE', '%' . $value . '%');
        }

        return $query->get();
    }

    public function findById($id)
    {
        return PageBanner::find($id);
    }

    public function findMaxOrder()
    {
        return PageBanner::max('order');
    }

    public function save(array $data)
    {
        $banner = new PageBanner();
        $banner->fill($data);
        $banner->save();
        return $banner;
    }

    public function update($id, array $data)
    {
        $banner = PageBanner::findOrFail($id);
        $banner->fill($data);
        $banner->save();
        return $banner;
    }

    public function shiftOrderRange($start, $end, $increment)
    {
        $query = PageBanner::where('order', '>=', $start);

        if (!is_null($end)) {
            $query->where('order', '<=', $end);
        }

        $query->update([
            'order' => DB::raw("`order` + $increment")
        ]);
    }

    public function delete($id)
    {
        $banner = PageBanner::findOrFail($id);
        $banner->delete();
    }
}
