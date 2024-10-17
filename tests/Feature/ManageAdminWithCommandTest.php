<?php

namespace WeirdoPanelTest\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use WeirdoPanelTest\TestCase;
use WeirdoPanel\Support\Contract\UserProviderFacade;

class ManageAdminWithCommandTest extends TestCase
{
    /** @test * */
    public function create_admin_with_command()
    {
        Artisan::call('panel:add', [
            'user' => $this->user->id
        ]);

        $this->assertTrue( (bool) $this->user->panelAdmin()->exists());
    }

    /** @test * */
    public function message_will_be_written_from_user_provider()
    {
        UserProviderFacade::shouldReceive('makeAdmin')
            ->with($this->user->id, true)
            ->once()
            ->andReturn([
                'type' => 'success',
                'message' => "Done",
            ]);

        $this->artisan('panel:add', [
            'user' => $this->user->id,
            '--super' => true
        ])->expectsOutput('Done');
    }

    /** @test * */
    public function it_shows_error_when_an_exception_is_thrown()
    {
        UserProviderFacade::shouldReceive('makeAdmin')
            ->with($this->user->id, true)
            ->once()
            ->andThrow(\Exception::class, 'Error');

        $this->artisan('panel:add', [
            'user' => $this->user->id,
            '--super' => true
        ])->expectsOutput("¡Algo salió mal!\nError: Error");
    }

    /** @test * */
    public function remove_admin_with_command()
    {
        Artisan::call('panel:add', [
            'user' => $this->user->id
        ]);

        Artisan::call('panel:remove', [
            'user' => $this->user->id,
            '--force' => true
        ]);

        $this->assertFalse( (bool) $this->user->panelAdmin()->exists());
    }

    /** @test * */
    public function it_asks_to_remove_an_admin()
    {
        $this->artisan('panel:remove', [
            'user' => 1
        ])->expectsConfirmation("¿Quieres eliminar {$this->user->id} de la administración");
    }

    /** @test * */
    public function all_admins_is_listed()
    {
        Artisan::call('panel:add', [
            'user' => $this->user->id
        ]);

        $this->artisan('panel:admins')
            ->expectsOutput("• {$this->user->name}: {$this->user->email}");
    }

    /** @test * */
    public function all_super_users_are_returned()
    {
        Artisan::call('panel:add', [
            'user' => $this->user->id,
            '--super' => true
        ]);

        $this->artisan('panel:admins')
            ->expectsOutput("• {$this->user->name}: {$this->user->email} ( Super Admin ✅ )");
    }
}
