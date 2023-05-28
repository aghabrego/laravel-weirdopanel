<?php


namespace WeirdoPanelTest\Feature;

use WeirdoPanel\Http\Middleware\isAdmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use WeirdoPanel\Http\Middleware\LangChanger;
use WeirdoPanelTest\TestCase;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->addRouteWithAdminMiddleware();
    }

    /** @test * */
    public function user_is_unauthorized()
    {
        $this->actingAs($this->user);

        $this->get('/test')
            ->assertRedirect('/');

        config()->set('weirdo_panel.redirect_unauthorized', '/redirect-page');

        $this->get('/test')
            ->assertRedirect('/redirect-page');
    }

    /** @test * */
    public function a_default_guard_will_be_used_when_custom_guard_is_null()
    {
        config()->set('weirdo_panel.redirect_unauthorized', null);

        $this->actingAs($this->user);

        $this->get('/test')
            ->assertRedirect('/');
    }

    /** @test * */
    public function user_is_valid()
    {
        $this->withoutExceptionHandling();

        $this->addRouteWithAdminMiddleware();

        $this->actingAs($this->getAdmin())
            ->get('/test')
            ->assertOk();
    }

    /** @test * */
    public function language_will_be_set()
    {
        $this->addRouteWithAdminMiddleware();

        $this->actingAs($this->getAdmin())->get('/test');

        $this->assertEquals('en_panel', App::getLocale());
    }

    /** @test * */
    public function a_guest_user_will_be_redirected()
    {
        $this->get('/test')
            ->assertRedirect();

        config()->set('weirdo_panel.redirect_unauthorized', '/redirect-page');

        $this->get('/test')
            ->assertRedirect('/redirect-page');
    }

    /** @test * */
    public function a_custom_language_is_applied()
    {
        config()->set('weirdo_panel.lang', 'fa');

        $this->actingAs($this->getAdmin())->get('/test');

        $this->assertEquals('fa_panel', App::getLocale());
    }

    /** @test * */
    public function a_default_language_is_applied_when_its_null()
    {
        config()->set('weirdo_panel.lang', null);

        $this->actingAs($this->getAdmin())->get('/test');

        $this->assertEquals('en_panel', App::getLocale());
    }

    /** @test * */
    public function it_will_read_the_session_for_changing_lang()
    {
        session()->put('weirdopanel_lang', 'fa_panel');

        $this->actingAs($this->getAdmin())->get('/test');

        $this->assertEquals('fa_panel', App::getLocale());
    }

    /** @test * */
    public function auth_guard_is_read_from_config()
    {
        config()->set('weirdo_panel.auth_guard', '::test_guard::');

        $this->actingAs($this->getAdmin())->get('/test');

        $this->assertEquals('::test_guard::', Auth::getDefaultDriver());
    }

    /** @test * */
    public function auth_guard_is_set_when_its_null()
    {
        config()->set('weirdo_panel.auth_guard', null);

        $this->actingAs($this->getAdmin())->get('/test');

        $this->assertEquals(config('auth.defaults.guard'), Auth::getDefaultDriver());
    }

    private function addRouteWithAdminMiddleware()
    {
        \Illuminate\Support\Facades\Route::get('/test', function () {
            //
        })->middleware([isAdmin::class, LangChanger::class]);
    }
}
