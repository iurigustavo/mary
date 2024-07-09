<?php

namespace Mary\View\Components;

use ArrayAccess;
use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Pagination extends Component
{
    public string $uuid;

    public function __construct(
        public ArrayAccess|array $rows,
        public ?array $perPageValues = [10, 20, 50, 100],
    ) {
        $this->uuid = "mary".md5(serialize($this));
    }

    public function modelName(): ?string
    {
        return $this->attributes->whereStartsWith('wire:model.live')->first();
    }

    public function isShowable(): bool
    {
        return $this->modelName() !== null && $this->rows instanceof LengthAwarePaginator;
    }

    public function render(): View|Closure|string
    {
        return <<<'HTML'
            <div class="mary-table-pagination">
                <div {{ $attributes->class(["border border-x-0 border-t-0 border-b-1 border-b-base-300 mb-5"]) }}></div>
                <div class="justify-between md:flex md:flex-row w-auto md:w-full items-center py-3 overflow-y-auto pl-2 pr-2 relative">
                    @if($isShowable())
                    <div class="flex flex-row justify-center md:justify-start mb-2 md:mb-0">
                        <select id="{{ $uuid }}" @if(!empty($modelName())) wire:model.live="{{ $modelName() }}" @endif
                                class="select select-primary select-sm flex sm:text-sm sm:leading-6 w-auto md:mr-5">
                            @foreach ($perPageValues as $option)
                            <option value="{{ $option }}" @selected($rows->perPage() === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="w-full">
                        {{ $rows->onEachSide(1)->links(data: ['scrollTo' => false]) }}
                    </div>
                </div>
            </div>
            HTML;
    }
}
