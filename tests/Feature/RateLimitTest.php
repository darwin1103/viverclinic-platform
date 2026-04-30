<?php
namespace Tests\Feature;
use Tests\TestCase;
class RateLimitTest extends TestCase
{
    public function test_login_rate_limiting()
    {
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/login', [
                'email' => 'fake@example.com',
                'password' => 'wrong'
            ]);
            echo "Login attempt " . ($i+1) . " status: " . $response->getStatusCode() . "\n";
        }
    }
    public function test_register_rate_limiting()
    {
        for ($i = 0; $i < 4; $i++) {
            $response = $this->post('/register', [
                'name' => 'QA',
                'email' => 'fake@example.com',
                'password' => 'wrong',
                'password_confirmation' => 'wrong',
                'branchId' => 1
            ]);
            echo "Register attempt " . ($i+1) . " status: " . $response->getStatusCode() . "\n";
        }
    }
}
