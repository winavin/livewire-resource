<?php

namespace Winavin\LivewireResource\Routing;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class LivewireResourceRoute
{
    protected string $name;
    protected ?string $componentBase;
    protected array $options;

    public function __construct(string $name, ?string $componentBase = null, array $options = [])
    {
        $this->name = $name;
        $this->componentBase = $componentBase;
        $this->options = $options;

        $this->register();
    }

    public function only(array $methods): static
    {
        $this->options['only'] = $methods;
        return $this->register();
    }

    public function except(array $methods): static
    {
        $this->options['except'] = $methods;
        return $this->register();
    }

    public function register(): static
    {
        $slug = $this->options['slug'] ?? Str::kebab($this->name);
        $singularSlug = Str::singular($slug);

        $title = Str::studly($this->name);
        $singularTitle = Str::singular($title);

        $this->options['folder'] = $this->options['folder'] ?? config('livewire-resource.folder');

        $folder = $this->options['folder'] == "" ? "" : Str::studly( $this->options['folder']);

        $namespace = $this->options['namespace'] ?? "App\\Livewire\\$folder\\$title\\";
        $component = $this->componentBase ?? $singularTitle;

        $routes = [
            'index' => [
                'uri' => "$slug",
                'component' => "{$namespace}Index$component",
                'name' => "$slug.index",
            ],
            'create' => [
                'uri' => "$slug/create",
                'component' => "{$namespace}Create{$component}",
                'name' => "$slug.create",
            ],
            'show' => [
                'uri' => "$slug/{{$singularSlug}}",
                'component' => "{$namespace}Show{$component}",
                'name' => "$slug.show",
            ],
            'edit' => [
                'uri' => "$slug/{{$singularSlug}}/edit",
                'component' => "{$namespace}Edit{$component}",
                'name' => "$slug.edit",
            ],
        ];

        if (isset($this->options['only'])) {
            $routes = array_intersect_key($routes, array_flip($this->options['only']));
        }

        if (isset($this->options['except'])) {
            $routes = array_diff_key($routes, array_flip($this->options['except']));
        }

        foreach ($routes as $route) {
            Route::get($route['uri'], $route['component'])->name($route['name']);
        }

        return $this;
    }
}
