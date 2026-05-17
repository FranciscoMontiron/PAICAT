<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test que la aplicación responde correctamente.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // La ruta raíz redirige según autenticación (302)
        $response->assertStatus(302);
    }
}
