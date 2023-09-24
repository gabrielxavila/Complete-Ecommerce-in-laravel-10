<?php

namespace Mary\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class MenuItem extends Component
{
    public string $uuid;

    public function __construct(
        public ?string $title = null,
        public ?string $icon = null,
        public ?string $link = null,
        public ?bool $active = false,
        public ?bool $separator = false
    ) {
        $this->uuid = md5(serialize($this));
    }

    public function routeMatches(): bool
    {
        if ($this->link == null) {
            return false;
        }

        $link = url($this->link ?? '');
        $route = url(Route::current()->uri());

        return Str::startsWith($route, $link);
    }

    public function render(): View|Closure|string
    {
        return <<<'HTML'
                @aware(['activateByRoute' => false, 'activeBgColor' => 'bg-base-300'])

                <li>
                    <a 
                        {{ 
                            $attributes->class([
                                "my-0.5 hover:text-inherit rounded-md", 
                                "mary-active-menu $activeBgColor" => ($active || ($activateByRoute && $routeMatches())) 
                            ]) 
                        }}

                        @if($link) 
                            href="{{ $link }}" wire:navigate 
                        @endif  
                    >
                        @if($icon) 
                            <x-icon :name="$icon" /> 
                        @endif

                            <span class="mary-hideable">{{ $title ?? $slot }}</span>
                    </a>
                </li>
            HTML;
    }
}