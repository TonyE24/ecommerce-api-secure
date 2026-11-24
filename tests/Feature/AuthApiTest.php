<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_and_use_the_returned_jwt(): void
    {
        config(['jwt.secret' => 'feature-test-secret']);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Ana Pérez',
            'email' => 'ana@example.com',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => ['user' => ['id', 'email'], 'access_token', 'token_type'],
            ]);

        $token = $response->json('data.access_token');

        $this->getJson('/api/auth/me', [
            'Authorization' => "Bearer {$token}",
        ])->assertOk()
            ->assertJsonPath('data.email', 'ana@example.com');
    }

    public function test_duplicate_email_is_rejected_with_json_validation_errors(): void
    {
        config(['jwt.secret' => 'feature-test-secret']);
        $this->postJson('/api/auth/register', [
            'name' => 'Ana Pérez',
            'email' => 'ana@example.com',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ])->assertCreated();

        $this->postJson('/api/auth/register', [
            'name' => 'Otra Ana',
            'email' => 'ana@example.com',
            'password' => 'another-password',
            'password_confirmation' => 'another-password',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }
}
