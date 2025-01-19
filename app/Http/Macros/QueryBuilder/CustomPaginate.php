<?php

namespace App\Http\Macros\QueryBuilder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CustomPaginate
{
    private $perPage;

    public function loadPagination()
    {
        $this->perPage = env('PAGINATION_PER_PAGE', 10);
        $this->pagination();
    }

    public function pagination()
    {
        $perPage = $this->perPage;
        $self = new static;

        Builder::macro('customPaginate', function ($formatCallback, $queryParams = []) use ($perPage, $self) {
            $paginate = $this->paginate($perPage)->appends($queryParams);

            return [
                'status' => ['status' => 'success', 'message' => null],
                'data' => $formatCallback($paginate->items()), // ใช้ callback format
                'links' => $self->getLinks($paginate),
            ];
        });
    }

    public function getLinks($paginator)
    {
        return (new Collection($paginator->toArray()))->only($this->linksKeys());
    }

    public function linksKeys(): array
    {
        return [
            "current_page",
            "first_page_url",
            "from",
            "last_page",
            "last_page_url",
            "next_page_url",
            "path",
            "per_page",
            "prev_page_url",
            "to",
            "total",
        ];
    }
}
