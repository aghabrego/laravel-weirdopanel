<?php

namespace WeirdoPanelTest;

use Faker\Factory;
use WeirdoPanel\Parsers\StubParser;
use Illuminate\Support\Facades\Hash;
use Livewire\LivewireServiceProvider;
use WeirdoPanelTest\Dependencies\User;
use WeirdoPanelTest\Dependencies\Article;
use WeirdoPanel\WeirdoPanelServiceProvider;
use Orchestra\Testbench\TestCase as _TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DynamicAcl\Providers\DynamicAclServiceProvider;
use Javoscript\MacroableModels\MacroableModelsServiceProvider;

abstract class TestCase extends _TestCase
{
    use RefreshDatabase;
    
    /**
     * @var \WeirdoPanelTest\Dependencies\User
     */
    protected $user;

    /**
     * @var StubParser
     */
    protected $parser;

    /**
     * Set up the test
     * 
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Configurar la base de datos en memoria
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        $this->loadMigrationsFrom(__DIR__.'/Dependencies/database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/../vendor/weirdo/dynamic-acl/database/migrations');

        $this->refreshDatabase();

        $this->setUser();
        $this->setParser();
        
        config()->set('weirdo_panel.user_model', User::class);
    }

    protected function getPackageProviders($app)
    {
        return [
            WeirdoPanelServiceProvider::class,
            LivewireServiceProvider::class,
            MacroableModelsServiceProvider::class,
            DynamicAclServiceProvider::class,
        ];
    }

    protected function setUser()
    {
        $faker = Factory::create();
        $user = User::create(['name' => $faker->name, 'password' => Hash::make('password')]);
        $this->user = $user;
    }

    public function getAdmin()
    {
        $this->user->panelAdmin()->create([
            'is_superuser' => true
        ]);

        return $this->user->refresh();
    }

    private function setParser()
    {
        $this->parser = new StubParser('article', Article::class);
    }
}
