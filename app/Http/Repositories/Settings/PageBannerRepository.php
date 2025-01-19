<?php

namespace App\Http\Repositories\Settings;

interface PageBannerRepository
{
    public function findAll();

    public function findById($id);

    public function findMaxOrder();

    public function save(array $data);

    public function update($id, array $data);

    public function shiftOrderRange($start, $end, $increment);

    public function delete($id);
}
