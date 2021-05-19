<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class LivewireCustomCrudCommand extends Command
{
    /**
     * Class property to store the livewire class name.
     *
     * @var string
     */
    protected $livewireClassName;

    /**
     * Class property to store the model class name.
     *
     * @var string
     */
    protected $modelClassName;

    /**
     * Laravel file system instance.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:livewire-crud
	{livewireClassName? : The name assigned to the livewire class.},
	{modelClassName? : The name assigned to the model class.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create a new livewire component initialized with a model CRUD';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->filesystem = new Filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Gather the parameters value.
        $this->gatherParametersValues();

        // Generate the livewire class file.
        $this->generateCrudLivewireClassFile();

        // Generate the livewire view file.
        $this->generateCrudLivewireViewBlade();
    }

    /**
     * Retrieve the user input values.
     *
     * @return void
     */
    private function gatherParametersValues(): void
    {
        // Ask user for inputs if not inserted.
        $this->livewireClassName = Str::studly($this->argument('livewireClassName')
            ?? $this->ask('Enter livewire class name'));

        $this->modelClassName = Str::studly($this->argument('modelClassName')
            ?? $this->ask('Enter model class name'));
    }

    /**
     * Create CRUD livewire component class.
     *
     * @return void
     */
    private function generateCrudLivewireClassFile(): void
    {
        // Set the original and distention location for the livewire class.
        $stubFilePath      = base_path('\\stubs\\crud.livewire.class.stub');
        $livewireClassPath = app_path("\\Http\\Livewire\\{$this->livewireClassName}.php");

        // Check if file already exists.
        if ($this->doesNotFileExist(filePath: $livewireClassPath, fileTitle: 'class')) {

            // Copy the the stub content inside the file distention.
            $stubContent = $this->filesystem->get($stubFilePath);

            // Replace content variable with the actual values.
            $livewireClassContent = Str::replaceArray(
                '{{}}',
                [
                    $this->modelClassName,                              // model namespace.
                    $this->livewireClassName,                           // livewire class name.
                    $this->modelClassName,                              // parseModelDataIntoState method model invocation.
                    Str::lower($this->modelClassName),                  // delete confirmation alert.
                    $this->modelClassName,                              // alert confirmed method model invocation.
                    Str::ucfirst($this->modelClassName),                // alert confirmed method message.
                    $this->modelClassName,                              // update method model invocation.
                    $this->modelClassName,                              // store method model invocation.
                    Str::kebab($this->livewireClassName),               // render view name.
                    Str::camel(Str::plural($this->modelClassName)),     // render view paginator variable.
                    $this->modelClassName,                              // render view model invocation.
                ],
                $stubContent
            );

            // Paste the the stub content inside the file distention.
            $this->filesystem->put($livewireClassPath, $livewireClassContent);

            $this->info("Livewire class created in App\\Http\\Livewire\\{$this->livewireClassName}.php 🤝");
        }
    }

    /**
     * Create CRUD livewire component blade.
     *
     * @return void
     */
    private function generateCrudLivewireViewBlade(): void
    {
        // Set the original location for the livewire blade.
        $stubFilePath      = base_path('\\stubs\\crud.livewire.blade.stub');

        // Set the distention location for the livewire blade.
        $viewName          = Str::kebab($this->livewireClassName);
        $livewireBladePath = resource_path("\\views\\livewire\\{$viewName}.blade.php");

        // Check if file already exists.
        if ($this->doesNotFileExist(filePath: $livewireBladePath, fileTitle: 'blade')) {

            // Copy the the stub content inside the file distention.
            $stubContent = $this->filesystem->get($stubFilePath);

            // Replace content variable with the actual values.
            $livewireBladeContent = Str::replaceArray(
                '{{}}',
                [
                    Str::title($this->modelClassName),                  // create button name.
                    Str::camel(Str::plural($this->modelClassName)),     // view model instances variable.
                    Str::camel(Str::plural($this->modelClassName)),     // view model instances check count.
                    Str::camel(Str::plural($this->modelClassName)),     // view model instances pagination links.
                    Str::title($this->modelClassName),                  // create modal title.
                    Str::title($this->modelClassName),                  // update modal title.
                ],
                $stubContent
            );

            // Paste the the stub content inside the file distention.
            $this->filesystem->put($livewireBladePath, $livewireBladeContent);

            $this->info("Livewire blade created in resources\\views\\livewire\\{$viewName}.blade.php 🤝");
        }
    }

    /**
     * Checks whether the file exists withing the given path.
     *
     * @param  string $filePath
     * @param  string $fileTitle
     * @return bool
     */
    private function doesNotFileExist(string $filePath, string $fileTitle): bool
    {
        if ($this->filesystem->exists($filePath)) {
            $path = Str::after($filePath, base_path().'\\');
            $this->error("This {$fileTitle} already exists in: {$path} 😎");
            return false;
        }
        return true;
    }
}
