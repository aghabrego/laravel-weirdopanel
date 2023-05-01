<?php

namespace WeirdoPanelTest\Integration;


use WeirdoPanel\Models\PanelAdmin;
use WeirdoPanelTest\Dependencies\User;

class PanelAdminModelTest extends \WeirdoPanelTest\TestCase
{
    /** @test * */
    public function user_relation_is_an_instance_of_user_model()
    {
        config()->set('weirdo_panel.user_model', User::class);

        $panelAdmin = PanelAdmin::query()->create([
            'user_id' => $this->user->id,
            'is_superuser' => false
        ]);

        $this->assertInstanceOf(User::class, $panelAdmin->user);
    }
}
