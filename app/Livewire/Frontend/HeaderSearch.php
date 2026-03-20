<?php

namespace App\Livewire\Frontend;

use Livewire\Component;

class HeaderSearch extends Component
{
    public $search = '';

    public $type = 'desktop';

    public function mount($type = 'desktop')
    {
        $this->type = $type;
        if (request()->has('keyword')) {
            $this->search = request()->keyword;
        }
    }

    public function submit()
    {
        if (request()->routeIs('products.index') || request()->routeIs('products.category') || request()->routeIs('products.brand')) {
            $this->dispatch('searchUpdated', $this->search);
        } else {
            $this->redirectRoute('products.index', ['keyword' => $this->search]);
        }
    }

    public function render()
    {
        return view('livewire.frontend.header-search');
    }
}
