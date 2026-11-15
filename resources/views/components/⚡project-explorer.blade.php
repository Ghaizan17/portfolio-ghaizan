<?php

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Technology;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $category = '';

    #[Url(except: '')]
    public string $technology = '';

    #[Url(except: 'featured')]
    public string $sort = 'featured';

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'category', 'technology', 'sort'], true)) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'category', 'technology', 'sort']);
        $this->resetPage();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, ProjectCategory>
     */
    #[Computed]
    public function categories()
    {
        return ProjectCategory::query()
            ->ordered()
            ->withCount(['projects' => fn ($query) => $query->published()])
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Technology>
     */
    #[Computed]
    public function technologies()
    {
        return Technology::query()
            ->ordered()
            ->whereHas('projects', fn ($query) => $query->published())
            ->get();
    }

    #[Computed]
    public function hasActiveFilters(): bool
    {
        return $this->search !== ''
            || $this->category !== ''
            || $this->technology !== ''
            || $this->sort !== 'featured';
    }

    #[Computed]
    public function projects()
    {
        $search = trim(mb_substr($this->search, 0, 80));
        $escaped = addcslashes($search, '%_\\');

        return Project::query()
            ->published()
            ->with(['category', 'technologies', 'media'])
            ->when($search !== '', function ($query) use ($escaped): void {
                $query->where(function ($query) use ($escaped): void {
                    $query->where('title', 'like', "%{$escaped}%")
                        ->orWhere('excerpt', 'like', "%{$escaped}%")
                        ->orWhereHas('technologies', fn ($query) => $query->where('name', 'like', "%{$escaped}%"));
                });
            })
            ->when($this->category !== '', fn ($query) => $query->whereHas('category', fn ($query) => $query->where('slug', $this->category)))
            ->when($this->technology !== '', fn ($query) => $query->whereHas('technologies', fn ($query) => $query->where('slug', $this->technology)))
            ->when($this->sort === 'newest', fn ($query) => $query->orderByDesc('year')->orderByDesc('published_at'))
            ->when($this->sort === 'oldest', fn ($query) => $query->orderBy('year')->orderBy('published_at'))
            ->when(! in_array($this->sort, ['newest', 'oldest'], true), fn ($query) => $query->ordered())
            ->paginate(config('digital-dimension.pagination.projects'));
    }
};
?>

<div>
    <div class="border-b border-line pb-8">
        <div class="grid gap-6 lg:grid-cols-[1.4fr_0.6fr] lg:items-end">
            <div>
                <label for="project-search" class="field-label">Search the archive</label>
                <div class="relative">
                    <x-ui-icon name="search" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-faint" />
                    <input
                        id="project-search"
                        type="search"
                        wire:model.live.debounce.400ms="search"
                        class="field ps-11"
                        placeholder="Project, technology, or keyword"
                        autocomplete="off"
                    >
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="project-technology" class="field-label">Technology</label>
                    <select id="project-technology" wire:model.live="technology" class="field appearance-none">
                        <option value="">All technologies</option>
                        @foreach ($this->technologies as $item)
                            <option value="{{ $item->slug }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="project-sort" class="field-label">Order</label>
                    <select id="project-sort" wire:model.live="sort" class="field appearance-none">
                        <option value="featured">Featured first</option>
                        <option value="newest">Newest first</option>
                        <option value="oldest">Oldest first</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-2">
            <button
                type="button"
                wire:click="$set('category', '')"
                class="filter-pill"
                aria-pressed="{{ $category === '' ? 'true' : 'false' }}"
            >
                All
                <span class="filter-count">{{ $this->categories->sum('projects_count') }}</span>
            </button>

            @foreach ($this->categories as $item)
                <button
                    type="button"
                    wire:click="$set('category', '{{ $item->slug }}')"
                    class="filter-pill"
                    aria-pressed="{{ $category === $item->slug ? 'true' : 'false' }}"
                >
                    {{ $item->name }}
                    <span class="filter-count">{{ $item->projects_count }}</span>
                </button>
            @endforeach

            @if ($this->hasActiveFilters)
                <button type="button" wire:click="clearFilters" class="ms-auto inline-flex items-center gap-2 text-[0.6875rem] font-medium uppercase tracking-[0.12em] text-accent transition-opacity hover:opacity-70">
                    <x-ui-icon name="x" class="h-3.5 w-3.5" />
                    Reset filters
                </button>
            @endif
        </div>
    </div>

    <div class="mt-10" wire:loading.class="opacity-40" wire:target="search,category,technology,sort,page,clearFilters">
        <div class="mb-6 flex items-center justify-between gap-4">
            <p class="text-[0.6875rem] font-medium uppercase tracking-[0.14em] text-faint" aria-live="polite">
                {{ $this->projects->total() }} {{ \Illuminate\Support\Str::plural('project', $this->projects->total()) }} found
            </p>
            <span wire:loading wire:target="search,category,technology,sort,page" class="inline-flex items-center gap-2 text-[0.6875rem] uppercase tracking-[0.14em] text-faint">
                <x-ui-icon name="loader" class="h-3.5 w-3.5 animate-spin" />
                Updating
            </span>
        </div>

        @if ($this->projects->isEmpty())
            <x-empty-state
                icon="search"
                title="No projects match those filters"
                description="Try a different keyword or clear the filters to see the full archive."
            />

            <div class="mt-6 flex justify-center">
                <button type="button" wire:click="clearFilters" class="btn-secondary">Clear all filters</button>
            </div>
        @else
            <div class="grid gap-x-8 gap-y-14 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($this->projects as $project)
                    <x-project-card :project="$project" :index="$loop->iteration + (($this->projects->currentPage() - 1) * $this->projects->perPage())" wire:key="project-{{ $project->id }}" />
                @endforeach
            </div>

            <div class="mt-14">
                {{ $this->projects->links('vendor.pagination.editorial') }}
            </div>
        @endif
    </div>
</div>
