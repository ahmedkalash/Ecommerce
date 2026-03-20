<div class="row">
    <!-- Sidebar Start -->
    <div class="col-custom-3 wow fadeInUp">
        <div class="left-box wow fadeInUp">
            <div class="shop-left-sidebar">
                <div class="back-button">
                    <h3><i class="fa-solid fa-arrow-left"></i> {{ __('customer/catalog.back') }}</h3>
                </div>

                <div class="form-floating theme-form-floating-2 search-box">
                    <input type="search" class="form-control" id="search"
                           placeholder="{{ __('customer/catalog.search_placeholder') }}"
                           wire:model.live.debounce.500ms="search">
                    <label for="search">{{ __('customer/catalog.search') }}</label>
                </div>

                <div class="filter-category">
                    <div class="filter-title">
                        {{--                        <h2>{{ __('customer/catalog.filters') }}</h2>--}}
                        <a href="javascript:void(0)"
                           wire:click="clearFilters">{{ __('customer/catalog.clear_all_filters') }}</a>
                    </div>
                </div>

                <div class="accordion custom-accordion" id="accordionExample">
                    <!-- Categories -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne">
                                <span>{{ __('customer/catalog.categories') }}</span>
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                <ul class="category-list custom-padding custom-height" x-data="{ open: {} }">
                                    @foreach($categories as $cat)
                                        @php $hasChildren = $cat->childrenCategories->isNotEmpty(); @endphp
                                        <li>
                                            <div class="form-check ps-0 m-0 category-list-box d-flex align-items-center">
                                                <input class="checkbox_animated" type="checkbox"
                                                       wire:model.live="selectedCategories"
                                                       value="{{ $cat->slug }}"
                                                       id="cat-{{ $cat->id }}">
                                                <label class="form-check-label flex-grow-1 d-flex align-items-center justify-content-between ms-2"
                                                       for="cat-{{ $cat->id }}"
                                                       style="cursor: pointer; margin-inline-end: 25px;">
                                                    <span class="name text-truncate"
                                                          style="max-width: 80%;">{{ $cat->name }}</span>
                                                    <span class="d-flex align-items-center gap-2">
                                                        <span class="number">({{ $cat->products_count ?? 0 }})</span>
                                                        @if ($hasChildren)
                                                            <i class="fa-solid fa-angle-down"
                                                               @click.prevent="open[{{ $cat->id }}] = !open[{{ $cat->id }}]"
                                                               style="transition: transform 0.2s;"
                                                               :style="open[{{ $cat->id }}] ? 'transform: rotate(180deg)' : ''"
                                                            ></i>
                                                        @endif
                                                    </span>
                                                </label>
                                            </div>

                                            @if ($hasChildren)
                                                <ul class="category-list custom-padding"
                                                    x-show="open[{{ $cat->id }}]"
                                                    x-cloak
                                                    style="padding-left: 15px;">
                                                    @foreach ($cat->childrenCategories as $child)
                                                        <li>
                                                            <div class="form-check ps-0 m-0 category-list-box d-flex align-items-center">
                                                                <input class="checkbox_animated" type="checkbox"
                                                                       wire:model.live="selectedCategories"
                                                                       value="{{ $child->slug }}"
                                                                       id="cat-{{ $child->id }}">
                                                                <label class="form-check-label flex-grow-1 d-flex align-items-center justify-content-between ms-2"
                                                                       for="cat-{{ $child->id }}"
                                                                       style="cursor: pointer; margin-inline-end: 25px;">
                                                                    <span class="name text-truncate"
                                                                          style="max-width: 80%;">{{ $child->name }}</span>
                                                                    <span class="number">({{ $child->products_count ?? 0 }})</span>
                                                                </label>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Brands -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseTwo">
                                <span>{{ __('customer/catalog.brands') }}</span>
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                <ul class="category-list custom-padding custom-height">
                                    @foreach($brands as $b)
                                        <li>
                                            <div class="form-check ps-0 m-0 category-list-box d-flex align-items-center">
                                                <input class="checkbox_animated" type="checkbox"
                                                       wire:model.live="selectedBrands"
                                                       value="{{ $b->slug }}"
                                                       id="brand-{{ $b->id }}">
                                                <label class="form-check-label flex-grow-1 d-flex align-items-center justify-content-between ms-2"
                                                       for="brand-{{ $b->id }}"
                                                       style="cursor: pointer; margin-inline-end: 25px;">
                                                    <span class="name text-truncate"
                                                          style="max-width: 80%;">{{ $b->name }}</span>
                                                    <span class="number">({{ $b->products_count ?? 0 }})</span>
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseThree">
                                <span>{{ __('customer/catalog.price') }}</span>
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                <div class="d-flex align-items-center mb-2">
                                    <input type="number" class="form-control" wire:model.lazy="min_price"
                                           placeholder="{{ __('customer/catalog.min') }}" min="0">
                                    <span class="mx-2">-</span>
                                    <input type="number" class="form-control" wire:model.lazy="max_price"
                                           placeholder="{{ __('customer/catalog.max') }}" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Sidebar End -->

    <!-- Main Content Start -->
    <div class="col-custom-">
        <div class="show-button">
            <div class="filter-button-group mt-0">
                <div class="filter-button d-inline-block d-lg-none">
                    <a><i class="fa-solid fa-filter"></i> Filter Menu</a>
                </div>
            </div>

            <div class="top-filter-menu">
                <div class="category-dropdown">
                    <h5 class="text-content">{{ __('customer/catalog.sort_by') }}</h5>
                    <div class="dropdown">
                        <select class="form-select" wire:model.live="sort_by">
                            <option value="newest">{{ __('customer/catalog.newest') }}</option>
                            <option value="oldest">{{ __('customer/catalog.oldest') }}</option>
                            <option value="price-asc">{{ __('customer/catalog.price_asc') }}</option>
                            <option value="price-desc">{{ __('customer/catalog.price_desc') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-sm-4 g-3 row-cols-xxl-4 row-cols-xl-3 row-cols-lg-2 row-cols-md-3 row-cols-2 product-list-section"
             wire:loading.class="opacity-50">
            @forelse($products as $product)
                <div>
                    <div class="product-box-3 h-100 wow fadeInUp">
                        <div class="product-header">
                            <div class="product-image">
                                <a href="{{ route('product', $product->slug) }}">
                                    <img src="{{ $product->getFirstMediaUrl('thumbnail') }}"
                                         class="img-fluid blur-up lazyload" alt="{{ $product->name }}">
                                </a>

                                <ul class="product-option">
                                    <li data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="{{ __('customer/catalog.view') }}">
                                        <a href="javascript:void(0)" data-bs-toggle="modal"
                                           data-bs-target="#quick-view-{{ $product->id }}">
                                            <i data-feather="eye"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="product-footer">
                            <div class="product-detail">
                                <span class="span-name">{{ optional($product->categories->first())->name }}</span>
                                <a href="{{ route('product', $product->slug) }}">
                                    <h5 class="name">{{ $product->name }}</h5>
                                </a>
                                <div class="product-rating mt-2">
                                    <ul class="rating">
                                        @for($i=1; $i<=5; $i++)
                                            <li>
                                                <i data-feather="star"
                                                   class="{{ $i <= $product->rating ? 'fill' : '' }}"></i>
                                            </li>
                                        @endfor
                                    </ul>
                                    <span>({{ $product->rating }})</span>
                                </div>
                                <h5 class="price"><span
                                            class="theme-color">{{ single_price(home_discounted_base_price($product)) }}</span>
                                    <del>{{ single_price(home_price($product)) }}</del>
                                </h5>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick View Modal --}}
                <div class="modal fade" id="quick-view-{{ $product->id }}" tabindex="-1" wire:ignore.self>
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            <div class="modal-body p-4">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <img src="{{ $product->getFirstMediaUrl('thumbnail') }}" class="img-fluid"
                                             alt="">
                                    </div>
                                    <div class="col-md-6">
                                        <h2 class="mb-3">{{ $product->name }}</h2>
                                        <h4 class="mb-3">{{ single_price(home_discounted_base_price($product)) }}</h4>
                                        <p>{{ strip_tags($product->description) }}</p>
                                        <a href="{{ route('product', $product->slug) }}"
                                           class="btn btn-primary mt-3">{{ __('customer/catalog.view_details') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <h4>{{ __('customer/catalog.no_products_found') }}</h4>
                    <button class="btn btn-primary mt-3"
                            wire:click="clearFilters">
                        {{ __('customer/catalog.clear_filters') }}
                    </button>
                </div>
            @endforelse
        </div>

        @if($products->hasPages())
            <nav class="custom-pagination mt-4">
                {{ $products->links() }}
            </nav>
        @endif
    </div>
    <!-- Main Content End -->

</div>
