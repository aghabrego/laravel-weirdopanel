<?php

namespace WeirdoPanelTest\Integration;

use WeirdoPanelTest\TestCase;
use WeirdoPanel\Models\PanelAdmin;

class DynamicRelationTest extends TestCase
{
    /** @test * */
    public function it_adds_panel_admin_relation_to_user_model()
    {
        $user = $this->getAdmin();

        $this->assertInstanceOf(PanelAdmin::class, $user->panelAdmin);
    }
}
