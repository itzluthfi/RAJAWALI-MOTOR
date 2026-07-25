<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Support\Facades\File;
use Illuminate\View\Component;

class Icon extends Component
{
    private static array $cache = [];

    public string $svg;

    public function __construct(public string $name, public string $class = 'w-4 h-4')
    {
        $this->svg = static::$cache[$name] ??= $this->muat($name);
    }

    private function muat(string $name): string
    {
        $path = base_path("node_modules/lucide-static/icons/{$name}.svg");

        if (! File::exists($path)) {
            return '';
        }

        $svg = File::get($path);

        return preg_replace(
            ['/width="24"/', '/height="24"/'],
            ['width="100%"', 'height="100%"'],
            $svg,
            1
        );
    }

    public function render()
    {
        return <<<'BLADE'
            <span aria-hidden="true" {{ $attributes->merge(['class' => 'inline-flex items-center justify-center shrink-0 ' . $class]) }}>{!! $svg !!}</span>
        BLADE;
    }
}
