<?php

namespace App\Http\Repositories\Settings;

use App\Models\Settings\FeaturedCourse;
use Illuminate\Support\Facades\DB;

class FeaturedCourseRepositoryImpl implements FeaturedCourseRepository
{
    public function findAll()
    {
        return FeaturedCourse::with(['createdByUser.countryDetails', 'updatedByUser.countryDetails', 'country'])->get();
    }

    public function findById($id)
    {
        return FeaturedCourse::find($id);
    }

    public function findMaxOrder()
    {
        return FeaturedCourse::max('order');
    }

    public function save(array $data)
    {
        $banner = new FeaturedCourse();
        $banner->fill($data);
        $banner->save();
        return $banner;
    }

    public function update($id, array $data)
    {
        $banner = FeaturedCourse::findOrFail($id);
        $banner->fill($data);
        $banner->save();
        return $banner;
    }

    public function shiftOrderRange($start, $end, $increment)
    {
        FeaturedCourse::whereBetween('order', [$start, $end])
            ->update([
                'order' => DB::raw("`order` + $increment")
            ]);
    }

    public function delete($id)
    {
        $banner = FeaturedCourse::findOrFail($id);
        $banner->delete();
    }
}
