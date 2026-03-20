<div>
    @if($type === 'desktop')
        <div class="search-box">
            <div class="input-group">
                <input type="search" wire:model="search" wire:keydown.enter="submit" class="form-control"
                       placeholder="{{ __('customer/header.search_placeholder') }}">
                <button class="btn" type="button" wire:click="submit" id="button-addon2">
                    <i data-feather="search"></i>
                </button>
            </div>
        </div>
    @else
        <div class="search-full">
            <div class="input-group">
                <span class="input-group-text" wire:click="submit" style="cursor: pointer;">
                    <i data-feather="search" class="font-light"></i>
                </span>
                <input type="text" wire:model="search" wire:keydown.enter="submit" class="form-control search-type"
                       placeholder="{{ __('customer/header.search_here_placeholder') }}">
                <span class="input-group-text close-search">
                    <i data-feather="x" class="font-light"></i>
                </span>
            </div>
        </div>
    @endif
</div>
