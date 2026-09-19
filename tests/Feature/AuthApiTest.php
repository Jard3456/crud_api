<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $client = app(ClientRepository::class)->createPasswordGrantClient(
            'Testing Password Client',
            null,
            true,
        );

        config([
            'passport.password_client_id' => $client->id,
            'passport.password_client_secret' => $client->plainSecret,
        ]);
    }

    public function test_user_can_login_and_receive_a_token(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
        ]);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertOk()
            ->assertJsonStructure([
                'token_type',
                'expires_in',
                'access_token',
                'refresh_token',
            ])
            ->assertJsonPath('token_type', 'Bearer');
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
        ]);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertBadRequest()
            ->assertJsonPath('error', 'invalid_grant');
    }

    public function test_login_assigns_requested_scopes(): void
    {
        $user = User::factory()->create([
            'email' => 'scoped@example.com',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
            'scopes' => ['products:read', 'profile:read'],
        ])->assertOk();

        $payload = explode('.', $response->json('access_token'))[1];
        $payload .= str_repeat('=', (4 - strlen($payload) % 4) % 4);
        $claims = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

        $this->assertSame(['products:read', 'profile:read'], $claims['scopes']);
    }

    public function test_login_rejects_undefined_scopes(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
            'scopes' => ['unknown:scope'],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['scopes.0']);
    }

    public function test_authenticated_user_can_logout(): void
    {
        Passport::actingAs(User::factory()->create());

        $this->postJson('/api/logout')
            ->assertOk()
            ->assertJson(['message' => 'Sesión cerrada correctamente.']);
    }

    public function test_authenticated_user_can_view_their_profile(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user, ['profile:read']);

        $this->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.email', $user->email);
    }
}
