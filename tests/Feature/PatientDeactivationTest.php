<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PatientDeactivationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_deactivated_patient_cannot_login()
    {
        // 1. Create a patient user
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'active' => false,
        ]);
        $user->assignRole('PATIENT');

        // 2. Attempt login
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        // 3. Assert redirected back with error
        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        
        $errors = session('errors')->get('email');
        $this->assertContains('Su cuenta está desactivada. Por favor, póngase en contacto con el administrador para más información.', $errors);
        
        // 4. Assert user is not authenticated
        $this->assertGuest();
    }

    public function test_active_patient_can_login()
    {
        // 1. Create an active patient user
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'active' => true,
        ]);
        $user->assignRole('PATIENT');

        // 2. Attempt login
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        // 3. Assert redirected to dashboard or authenticated
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }
}
