<?php

namespace Winavin\LivewireResource\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Str;

class MakeLivewireResource extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'livewire:make-resource {name : The resource name (plural)}
                {--folder=pages : Only generate specific actions (comma-separated)}
                {--only= : Only generate specific actions (comma-separated)}
                {--except= : Exclude specific actions (comma-separated)}
                {--inline : Generate inline components}
                {--force : Overwrite existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate index/create/show/edit Livewire components for a resource..';

    protected array $availableActions = [ 'index', 'create', 'show', 'edit'];

    /**
     * Execute the console command.
     */
    public function handle() : void
    {
        $dotName = trim($this->argument('name'));
        $folder = $this->option('folder');
        $segments = explode('.', $dotName);

        $slug = array_pop($segments);
        $folders = $segments;
        $singular = Str::singular($slug);

        $namespacePrefix = implode('.', [...$folders, $slug]); // pages.organizations

        $only = $this->option('only') ? explode(',', $this->option('only')) : null;
        $except = $this->option('except') ? explode(',', $this->option('except')) : [];

        $actions = $only ?? $this->availableActions;
        $actions = array_diff($actions, $except);

        foreach ($actions as $action) {
            $componentName = "{$folder}.{$namespacePrefix}.{$action}-{$singular}";
            $command = ['livewire:make', ['name' => $componentName]];

            if ($this->option('inline')) {
                $command[] = '--inline';
            }

            if ($this->option('force')) {
                $command[] = '--force';
            }

            $this->call(...$command);
        }
    }
}
