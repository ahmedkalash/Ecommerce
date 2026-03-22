<?php

namespace App\Livewire\Frontend\Catalog;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Database\Eloquent\Builder as eloquentBuilder;
use Laravel\Scout\Builder as scoutBuilder;
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

    public function render()
    {
        $scoutProductIds = $this->getResultApproximateCount();

        $categories = $this->categoriesForFiltersQuery($scoutProductIds)->get();

        $brands = $this->brandsForFiltersQuery($scoutProductIds)->get();

        $query = $this->applySearchAndFiltersQuery();

        $products = $query->paginate(12);

        return view('livewire.frontend.catalog.catalog-search', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }

    private function categoriesForFiltersQuery(?array $scoutProductIds = null): eloquentBuilder
    {
        $countClosure = function (eloquentBuilder $q) use ($scoutProductIds) {
            $q->isApprovedPublished();

            if ($scoutProductIds !== null) {
                $q->whereIn('products.id', $scoutProductIds);
            }

            if (! empty($this->selectedBrands)) {
                $q->whereHas('brand', function (eloquentBuilder $qb) {
                    $qb->whereIn('slug', $this->selectedBrands);
                });
            }

            if ($this->min_price !== null) {
                $q->whereHas('stocks', function (eloquentBuilder $qs) {
                    $qs->where('price', '>=', $this->min_price);
                });
            }

            if ($this->max_price !== null) {
                $q->whereHas('stocks', function (eloquentBuilder $qs) {
                    $qs->where('price', '<=', $this->max_price);
                });
            }
        };

        return Category::query()->withCount(['products' => $countClosure])
            ->with(['childrenCategories' => fn ($q) => $q->withCount(['products' => $countClosure])])
            ->whereNull('parent_id')
            ->orderBy('name');
    }

    private function brandsForFiltersQuery(?array $scoutProductIds = null): eloquentBuilder
    {
        $countClosure = function (eloquentBuilder $q) use ($scoutProductIds) {
            $q->isApprovedPublished();

            if ($scoutProductIds !== null) {
                $q->whereIn('products.id', $scoutProductIds);
            }

            if (! empty($this->selectedCategories)) {
                $q->whereHas('categories', function (eloquentBuilder $qc) {
                    $qc->whereIn('slug', $this->selectedCategories);
                });
            }

            if ($this->min_price !== null) {
                $q->whereHas('stocks', function (eloquentBuilder $qs) {
                    $qs->where('price', '>=', $this->min_price);
                });
            }

            if ($this->max_price !== null) {
                $q->whereHas('stocks', function (eloquentBuilder $qs) {
                    $qs->where('price', '<=', $this->max_price);
                });
            }
        };

        return Brand::query()->withCount(['products' => $countClosure])
            ->orderBy('name');
    }

    private function applySearchAndFiltersQuery(): scoutBuilder
    {
        return Product::search($this->search)->query(function (eloquentBuilder $query) {
            $query->isApprovedPublished()->with(['taxes', 'media', 'stocks', 'categories', 'brand']);

            if (! empty($this->selectedCategories)) {
                $query->whereHas('categories', function (eloquentBuilder $q) {
                    $q->whereIn('slug', $this->selectedCategories);
                });
            }

            if (! empty($this->selectedBrands)) {
                $query->whereHas('brand', function (eloquentBuilder $q) {
                    $q->whereIn('slug', $this->selectedBrands);
                });
            }

            if ($this->min_price !== null) {
                $query->whereHas('stocks', function (eloquentBuilder $q) {
                    $q->where('price', '>=', $this->min_price);
                });
            }

            if ($this->max_price !== null) {
                $query->whereHas('stocks', function (eloquentBuilder $q) {
                    $q->where('price', '<=', $this->max_price);
                });
            }

            match ($this->sort_by) {
                'newest' => $query->orderBy('created_at', 'desc'),
                'oldest' => $query->orderBy('created_at'),
                'price-asc' => $query->orderBy(
                    ProductStock::select('price')
                        ->whereColumn('product_id', 'products.id')
                        ->orderBy('price', 'asc')
                        ->limit(1),
                    'asc'
                ),
                'price-desc' => $query->orderBy(
                    ProductStock::select('price')
                        ->whereColumn('product_id', 'products.id')
                        ->orderBy('price', 'asc')
                        ->limit(1),
                    'desc'
                ),
                default => $query->orderBy('id', 'desc'),
            };
        });
    }

    private function getResultApproximateCount(): ?array
    {
        $scoutProductIds = null;
        if (! empty($this->search)) {
            $scoutProductIds = Product::search($this->search)->take(10000)->keys()->toArray();
        }

        return $scoutProductIds;
    }
}
