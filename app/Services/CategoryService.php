<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryService
{
    /**
     * Store a newly created category.
     *
     * @param array{
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
     * } $data
     */
    public function store(array $data): Category
    {
        return DB::transaction(function () use ($data) {
            $data = $this->prepareData($data);

            return Category::create($data);
        });
    }

    /**
     * Update an existing category.
     *
     * @param array{
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
     * } $data
     * @param Category $category
     */
    public function update(array $data, Category $category): Category
    {
        return DB::transaction(function () use ($data, $category) {
            $data = $this->prepareData($data);
            $category->update($data);

            return $category;
        });
    }

    /**
     * Delete a category.
     */
    public function delete(Category $category): ?bool
    {
        return DB::transaction(function () use ($category) {
            return $category->delete();
        });
    }

    /**
     * Delete multiple categories.
     */
    public function bulkDelete(\Illuminate\Support\Collection $records): void
    {
        DB::transaction(function () use ($records) {
            $records->each(fn(Category $record) => $this->delete($record));
        });
    }

    /**
     * Prepare data for storage or update.
     *
     * @param array{
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
     * } $data
     */
    protected function prepareData(array $data): array
    {
        // Fallback for slug if not provided
        if (empty($data['slug']) && ! empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Fallback for SEO title
        if (empty($data['meta_title']) && ! empty($data['name'])) {
            $data['meta_title'] = $data['name'];
        }

        // Ensure parent_id is null if it's 0 or empty for adjacency list compatibility
        if (isset($data['parent_id']) && ($data['parent_id'] == 0 || empty($data['parent_id']))) {
            $data['parent_id'] = null;
        }

        return $data;
    }
}
