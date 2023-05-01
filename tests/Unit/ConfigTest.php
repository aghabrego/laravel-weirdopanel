<?php

namespace WeirdoPanelTest\Unit;

use WeirdoPanelTest\TestCase;

class ConfigTest extends TestCase
{
    /** @test * */
    public function config_is_defined()
    {
        $this->assertNotNull(config('weirdo_panel'));
    }
}
