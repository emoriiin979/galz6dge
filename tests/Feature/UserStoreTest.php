<?php

namespace Tests\Feature;

use App\Models\User;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserStoreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * リクエストヘッダー
     *
     * @var array
     */
    protected $headers;

    /**
     * テスト前処理
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        User::factory()->create([
            'api_token' => hash('sha256', 'test_token'),
            'role' => 'master',
        ]);

        $this->headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer test_token',
        ];
    }

    /**
     * ユーザーの登録 正常系テスト
     *
     * @dataProvider store201Provider
     * @param array $commitData
     * @param array $expected
     * @return void
     */
    public function test_201_store(array $commitData, array $expected): void
    {
        // Arrange
        $url = '/api/users';

        // Act
        $response = $this->post($url, $commitData, $this->headers);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', $expected);
    }

    /**
     * ユーザーの登録 正常データ作成
     *
     * @return array
     */
    public static function store201Provider(): array
    {
        return [
            // role指定あり
            [
                'commitData' => [
                    'name' => 'master user',
                    'email' => 'master@gmail.com',
                    'password' => 'password',
                    'api_token' => 'master',
                    'role' => 'master',
                ],
                'expected' => [
                    'name' => 'master user',
                    'email' => 'master@gmail.com',
                    'api_token' => hash('sha256', 'master'),
                    'role' => 'master',
                ],
            ],
            // role指定なし
            [
                'commitData' => [
                    'name' => 'general user',
                    'email' => 'general@gmail.com',
                    'api_token' => 'general',
                ],
                'expected' => [
                    'name' => 'general user',
                    'email' => 'general@gmail.com',
                    'api_token' => hash('sha256', 'general'),
                    'role' => null,
                ],
            ],
        ];
    }

    /**
     * ユーザーの登録 422異常系テスト
     *
     * @dataProvider store422Provider
     * @param array $commitData
     * @param Closure $assertFunc
     * @return void
     */
    public function test_422_store(array $commitData, Closure $assertFunc): void
    {
        // Arrange
        $url = '/api/users';

        // Act
        $response = $this->post($url, $commitData, $this->headers);

        // Assert
        $response->assertStatus(422);
        $response->assertJson($assertFunc);
    }

    /**
     * ユーザーの登録 422異常データ作成
     *
     * @return array
     */
    public static function store422Provider(): array
    {
        return [
            // requiredのバリデーションが有効であること
            [
                'commitData' => [],
                'assertFunc' => function ($json) {
                    $json
                        ->where('errors', [
                            'name' => [
                                'nameは必ず指定してください。',
                            ],
                            'email' => [
                                'emailは必ず指定してください。',
                            ],
                            'api_token' => [
                                'api_tokenは必ず指定してください。',
                            ],
                        ])
                        ->etc();
                },
            ],
            // 型バリデーションが有効であること
            [
                'commitData' => [
                    'name' => ['name'],
                    'email' => ['aaaa'],
                    'password' => ['password'],
                    'api_token' => ['api_token'],
                ],
                'assertFunc' => function ($json) {
                    $json
                        ->where('errors', [
                            'name' => [
                                'nameは文字列を指定してください。',
                            ],
                            'email' => [
                                'emailは文字列を指定してください。',
                            ],
                            'password' => [
                                'passwordは文字列を指定してください。',
                            ],
                            'api_token' => [
                                'api_tokenは文字列を指定してください。',
                            ],
                        ])
                        ->etc();
                },
            ],
            // 形式バリデーションが有効であること
            [
                'commitData' => [
                    'name' => 'Test User',
                    'email' => 'aaaa',
                    'api_token' => 'cy4vuybin8jm3kxrcy5tvb67hynj',
                ],
                'assertFunc' => function ($json) {
                    $json
                        ->where('errors', [
                            'email' => [
                                'emailには、有効なメールアドレスを指定してください。',
                            ],
                        ])
                        ->etc();
                },
            ],
        ];
    }
}
