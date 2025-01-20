<?php

namespace App\Http\Repositories\Settings;

interface FeaturedCourseRepository
{
    public function findAll(array $queryParams);

    public function findById($id);

    public function findMaxOrder();

    public function save(array $data);

    public function update($id, array $data);

    public function shiftOrderRange($start, $end, $increment);

    public function delete($id);
}
