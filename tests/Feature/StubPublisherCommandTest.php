<?php

namespace WeirdoPanelTest\Feature;

use WeirdoPanelTest\TestCase;
use Illuminate\Support\Facades\File;

class StubPublisherCommandTest extends TestCase
{
    /** @test * */
    public function it_publishes_stubs()
    {
        $this->artisan('panel:publish')
            ->expectsOutput('Stubs se publicó con éxito');

        $this->assertDirectoryExists(base_path('/stubs/panel'));
    }
}
