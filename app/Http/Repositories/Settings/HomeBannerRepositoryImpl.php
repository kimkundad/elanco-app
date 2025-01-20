<?php

namespace App\Http\Repositories\Settings;

use App\Models\Settings\HomeBanner;
use Illuminate\Support\Facades\DB;

class HomeBannerRepositoryImpl implements HomeBannerRepository
{
    public function findAll(array $queryParams)
    {
        $query = HomeBanner::with(['createdByUser.countryDetails', 'updatedByUser.countryDetails', 'country']);

        foreach ($queryParams as $key => $value) {
            $query->where($key, 'LIKE', '%' . $value . '%');
        }

        return $query->get();
    }

    public function findById($id)
    {
        return HomeBanner::find($id);
    }

    public function findMaxOrder()
    {
        return HomeBanner::max('order');
    }

    public function save(array $data)
    {
        $banner = new HomeBanner();
        $banner->fill($data);
        $banner->save();
        return $banner;
    }

    public function update($id, array $data)
    {
        $banner = HomeBanner::findOrFail($id);
        $banner->fill($data);
        $banner->save();
        return $banner;
    }

    public function shiftOrderRange($start, $end, $increment)
    {
        HomeBanner::whereBetween('order', [$start, $end])
            ->update([
                'order' => DB::raw("`order` + $increment")
            ]);
    }

    public function delete($id)
    {
        $banner = HomeBanner::findOrFail($id);
        $banner->delete();
    }
}
