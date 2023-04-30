<?php

namespace WeirdoPanelTest\Unit;

use WeirdoPanelTest\TestCase;

class HelpersTest extends TestCase
{

    /** @test * */
    public function get_route_name_works(){
        config()->set('weirdo_panel.route_prefix', 'admin');
        $this->assertEquals(getRouteName(), 'admin');
        config()->set('weirdo_panel.route_prefix', '/admin');
        $this->assertEquals(getRouteName(), 'admin');
        config()->set('weirdo_panel.route_prefix', 'admin/');
        $this->assertEquals(getRouteName(), 'admin');
        config()->set('weirdo_panel.route_prefix', '/admin/');
        $this->assertEquals(getRouteName(), 'admin');
        config()->set('weirdo_panel.route_prefix', 'admin/panel/');
        $this->assertEquals(getRouteName(), 'admin.panel');
        config()->set('weirdo_panel.route_prefix', 'admin/panel/dashboard//');
        $this->assertEquals(getRouteName(), 'admin.panel.dashboard');
    }

}
