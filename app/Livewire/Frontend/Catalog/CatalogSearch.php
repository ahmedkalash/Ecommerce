<?php

namespace App\Livewire\Frontend\Catalog;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CatalogSearch extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url]
    public string $search = '';

    /** @var array<int, string> Selected category slugs (multi-select). */
    #[Url]
    public array $selectedCategories = [];

    /** @var array<int, string> Selected brand slugs (multi-select). */
    #[Url]
    public array $selectedBrands = [];

    #[Url]
    public ?int $min_price = null;

    #[Url]
    public ?int $max_price = null;

    #[Url]
    public string $sort_by = 'newest';

    /**
     * Reset pagination whenever a filterable property changes.
     */
    public function updating(string $property): void
    {
        if (in_array($property, ['search', 'selectedCategories', 'selectedBrands', 'min_price', 'max_price', 'sort_by'],
            true)) {
            $this->resetPage();
        }
    }

    /**
     * Reset all active filters and return the user to page 1.
     */
    public function clearFilters(): void
    {
        $this->selectedCategories = [];
        $this->selectedBrands = [];
        $this->min_price = null;
        $this->max_price = null;
        $this->search = '';
        $this->sort_by = 'newest';
        $this->resetPage();
    }

    /**
     * Initialise from URL slugs passed by the controller (e.g. /category/{slug}).
     */
    public function mount(?string $category = null, ?string $brand = null): void
    {
        if ($category && ! in_array($category, $this->selectedCategories, true)) {
            $this->selectedCategories[] = $category;
        }

        if ($brand && ! in_array($brand, $this->selectedBrands, true)) {
            $this->selectedBrands[] = $brand;
        }

        if (request()->has('keyword')) {
            $this->search = (string) request()->keyword;
        }
    }

    #[On('searchUpdated')]
    public function updateSearch(string $keyword): void
    {
        $this->search = $keyword;
        $this->resetPage();
    }

    public function render(): \Illuminate\View\View
    {
        $categories = Category::withCount('products')
            ->with(['childrenCategories' => fn ($q) => $q->withCount('products')])
            ->whereNull('parent_id')
            ->orderBy('name', 'asc')
            ->get();

        $brands = Brand::orderBy('name', 'asc')->get();

        $query = Product::search($this->search)->query(function ($query) {
            $query->isApprovedPublished()->with(['taxes', 'media', 'stocks', 'categories', 'brand']);

            if (! empty($this->selectedCategories)) {
                $query->whereHas('categories', function ($q) {
                    $q->whereIn('slug', $this->selectedCategories);
                });
            }

            if (! empty($this->selectedBrands)) {
                $query->whereHas('brand', function ($q) {
                    $q->whereIn('slug', $this->selectedBrands);
                });
            }

            if ($this->min_price !== null && $this->max_price !== null) {
                $query->whereHas('stocks', function ($q) {
                    $q->whereBetween('price', [$this->min_price, $this->max_price]);
                });
            }

            match ($this->sort_by) {
                'newest' => $query->orderBy('created_at', 'desc'),
                'oldest' => $query->orderBy('created_at', 'asc'),
                'price-asc' => $query->orderBy(
                    \App\Models\ProductStock::select('price')
                        ->whereColumn('product_id', 'products.id')
                        ->orderBy('price', 'asc')
                        ->limit(1),
                    'asc'
                ),
                'price-desc' => $query->orderBy(
                    \App\Models\ProductStock::select('price')
                        ->whereColumn('product_id', 'products.id')
                        ->orderBy('price', 'asc')
                        ->limit(1),
                    'desc'
                ),
                default => $query->orderBy('id', 'desc'),
            };
        });

        $products = $query->paginate(12);

        return view('livewire.frontend.catalog.catalog-search', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }
}
