<?php

namespace WeirdoPanel\Commands\Actions;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MakeCRUDConfig extends GeneratorCommand
{

    protected $name = 'panel:config';
    protected $type = 'Create a config';
    protected $description = 'Hacer una configuración crud';

    protected function getStub()
    {
        return $this->resolveStubPath('crud.stub');
    }

    public function handle()
    {
        $name = $this->getNameInput();

        $path = base_path("app/CRUD/{$name}Component.php");

        if($this->files->exists($path) and !$this->option('force')){
            $this->warn("'{$name}Component.php' ya existe en el directorio CRUD");
            return;
        }

        $this->makeDirectory($path);

        $stub = $this->files->get($this->getStub());
        $newStub = $this->parseStub($stub);

        $this->files->put($path, $newStub);
        $this->info("{$name} archivo de configuración fue creado para {$this->getNameInput()} model\nPuedes gestionarlo en : app/CRUD/{$name}Component.php");
    }

    private function parseStub($stub)
    {
        $array = [
            '{{ modelNamespace }}' => $this->parseModel(),
            '{{ modelName }}' => $this->getNameInput(),
            '{{ withAuth }}' => $this->withAuth(),
            '{{ fields }}' => $this->parseSearchFields(),
        ];

        return str_replace(array_keys($array), array_values($array), $stub);
    }

    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'force mode'],
        ];
    }

    private function parseModel()
    {
        $model = $this->qualifyModel($this->getNameInput());

        if(!class_exists($model)){
            $this->warn("La opción del modelo debe ser válida y el modelo debe existir");
            die();
        }

        return $model;
    }

    private function withAuth()
    {
        $fillableList = $this->getFillableList();
        if(!in_array('user_id', $fillableList)){
            return 'true';
        }

        return 'false';
    }

    private function getFillableList()
    {
        $modelNamespace = $this->qualifyModel($this->getNameInput());
        $modelInstance = new $modelNamespace;
        return $modelInstance->getFillable();
    }

    private function parseSearchFields()
    {
        $fillableList = $this->getFillableList();
        $array = [];
        foreach ($fillableList as $fillable){
            if(!Str::contains($fillable, 'id')){
                $array[] = "'$fillable'";
            }
        }

        return implode(', ', $array);
    }

    private function resolveStubPath($stub)
    {
        return file_exists($customPath = base_path(trim("stubs/panel/".$stub, '/')))
            ? $customPath
            : __DIR__.'/../stub/'.$stub;
    }

    protected function qualifyModel($model)
    {
        $model = ltrim($model, '\\/');

        $model = str_replace('/', '\\', $model);

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($model, $rootNamespace)) {
            return $model;
        }

        return is_dir(app_path('Models'))
            ? $rootNamespace.'Models\\'.$model
            : $rootNamespace.$model;
    }

}
