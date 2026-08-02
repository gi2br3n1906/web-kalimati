<?php

declare(strict_types=1);

namespace App\Livewire\Public\Umkm;

use App\Enums\UmkmCategory;
use App\Models\UmkmBusiness;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class DirectoryIndex extends Component
{
    public string $search = '';

    public string $category = '';

    public function render(): View
    {
        $businesses = UmkmBusiness::query()
            ->with('owner')
            ->when($this->category !== '', fn ($query) => $query->where('category', $this->category))
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($businessQuery): void {
                    $businessQuery
                        ->where('business_name', 'like', '%'.$this->search.'%')
                        ->orWhere('address', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('business_name')
            ->get();

        return view('livewire.public.umkm.directory-index', [
            'businesses' => $businesses,
            'categories' => UmkmCategory::options(),
        ])->layout('components.layouts.app', [
            'title' => 'Direktori UMKM Kalimati',
        ]);
    }
}
