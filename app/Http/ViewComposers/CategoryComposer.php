<?php

namespace App\Http\ViewComposers;

use App\Models\Category;
use Illuminate\View\View;

class CategoryComposer
{
    /**
     * Bind data to the view.
     *
     * @return void
     */
    public function compose(View $view)
    {
        $categories_query = Category::query()->with('coverImage');
        $categories = $categories_query->whereNull('parent_id')->orderBy('name', 'asc')->get();

        $view->with(['categories' => $categories]);
    }
}
