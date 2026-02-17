<?php

namespace App\Utility;

use App\Models\Category;

class CategoryUtility
{
    /* when with trashed is true id will get even the deleted items */
    public static function get_immediate_children($id, $with_trashed = false, $as_array = false)
    {
        $children = $with_trashed ? Category::where('parent_id', $id)->orderBy('name', 'asc')->get() : Category::where('parent_id', $id)->orderBy('name', 'asc')->get();
        $children = $as_array && ! is_null($children) ? $children->toArray() : $children;

        return $children;
    }

    public static function get_immediate_children_ids($id, $with_trashed = false)
    {

        $children = CategoryUtility::get_immediate_children($id, $with_trashed, true);

        return ! empty($children) ? array_column($children, 'id') : [];
    }

    public static function get_immediate_children_count($id, $with_trashed = false)
    {
        return $with_trashed ? Category::where('parent_id', $id)->count() : Category::where('parent_id', $id)->count();
    }

    /* when with trashed is true id will get even the deleted items */
    public static function flat_children($id, $with_trashed = false, $container = [])
    {
        $children = CategoryUtility::get_immediate_children($id, $with_trashed, true);

        if (! empty($children)) {
            foreach ($children as $child) {

                $container[] = $child;
                $container = CategoryUtility::flat_children($child['id'], $with_trashed, $container);
            }
        }

        return $container;
    }

    /* when with trashed is true id will get even the deleted items */
    public static function children_ids($id, $with_trashed = false)
    {
        $children = CategoryUtility::flat_children($id, $with_trashed = false);

        return ! empty($children) ? array_column($children, 'id') : [];
    }

    public static function category_tree_ids($category, $category_ids)
    {
        foreach ($category->childrenCategories as $category) {
            $category_ids[] = $category->id;

            if (count($category->childrenCategories) > 0) {
                $category_ids = static::category_tree_ids($category, $category_ids);
            }
        }

        return $category_ids;
    }

    public static function move_children_to_parent($id)
    {
        $children_ids = CategoryUtility::get_immediate_children_ids($id, true);

        $category = Category::where('id', $id)->first();

        // Level logic removed

        Category::whereIn('id', $children_ids)->update(['parent_id' => $category->parent_id]);
    }

    public static function create_initial_category($key)
    {
        return true;
    }

    /**
     * @deprecated Level logic removed
     */
    public static function move_level_up($id) {}

    /**
     * @deprecated Level logic removed
     */
    public static function move_level_down($id) {}

    /**
     * @deprecated Level logic removed
     */
    public static function update_child_level($id) {}

    public static function delete_category($id)
    {
        $category = Category::where('id', $id)->first();
        if (! is_null($category)) {
            CategoryUtility::move_children_to_parent($category->id);
            $category->delete();
        }
    }
}
