<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 認証 401異常系テスト
     *
     * @return void
     */
    public function test_401_authenticate(): void
    {
        // Arrange
        $url = '/api/articles';

        User::factory()->create([
            'api_token' => hash('sha256', 'test_token'),
            'role' => 'master',
        ]);

        // Act
        $response = $this->get($url, [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer other_user',
        ]);

        // Assert
        $response->assertStatus(401);
    }

    /**
     * 認可 403異常系テスト
     *
     * @return void
     */
    public function test_403_authorize(): void
    {
        // Arrange
        $url = '/api/users';

        User::factory()->create([
            'api_token' => hash('sha256', 'test_token'),
            'role' => null,
        ]);

        // Act
        $response = $this->post($url, [], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer test_token',
        ]);

        // Assert
        $response->assertStatus(403);
    }
}
