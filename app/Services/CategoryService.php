<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Str;

/**
 * Handles core category business logic: create, update, delete.
 *
 * This service contains pure business logic only.
 * DB transactions, error handling, and logging are the caller's responsibility.
 */
class CategoryService
{
    /**
     * Store a newly created category.
     *
     * @param  array{
     *     name: string,
     *     slug?: string,
     *     parent_id?: int|string|null,
     *     commision_rate?: float|string,
     *     featured?: bool,
     *     top?: bool,
     *     digital?: bool,
     *     meta_title?: string,
     *     meta_description?: string,
     *     refund_request_time?: int
     * }  $data
     */
    public function store(array $data): Category
    {
        $data = $this->prepareData($data);

        return Category::create($data);
    }

    /**
     * Update an existing category.
     *
     * @param  array{
     *     name?: string,
     *     slug?: string,
     *     parent_id?: int|string|null,
     *     commision_rate?: float|string,
     *     featured?: bool,
     *     top?: bool,
     *     digital?: bool,
     *     meta_title?: string,
     *     meta_description?: string,
     *     refund_request_time?: int
     * }  $data
     */
    public function update(array $data, Category $category): Category
    {
        $data = $this->prepareData($data);
        $category->update($data);

        return $category;
    }

    /**
     * Delete a category.
     */
    public function delete(Category $category): ?bool
    {
        return $category->delete();
    }

    /**
     * Delete multiple categories.
     */
    public function bulkDelete(\Illuminate\Support\Collection $records): void
    {
        $records->each(fn (Category $record) => $this->delete($record));
    }

    /**
     * Prepare data for storage or update.
     *
     * @param  array{
     *     name?: string,
     *     slug?: string,
     *     parent_id?: int|string|null,
     *     commision_rate?: float|string,
     *     featured?: bool,
     *     top?: bool,
     *     digital?: bool,
     *     meta_title?: string,
     *     meta_description?: string,
     *     refund_request_time?: int
     * }  $data
     */
    protected function prepareData(array $data): array
    {
        if (empty($data['slug']) && ! empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (empty($data['meta_title']) && ! empty($data['name'])) {
            $data['meta_title'] = $data['name'];
        }

        if (isset($data['parent_id']) && ($data['parent_id'] == 0 || empty($data['parent_id']))) {
            $data['parent_id'] = null;
        }

        return $data;
    }
}
