<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->assertGreaterThan(0, count(app('router')->getRoutes()), 'Aucune route chargée dans le test.');
        $hasUp = false;
        foreach (app('router')->getRoutes() as $route) {
            if ($route->uri() === 'up') {
                $hasUp = true;
                break;
            }
        }
        $this->assertTrue($hasUp, 'La route /up n\'est pas présente dans le router.');
    }
}
