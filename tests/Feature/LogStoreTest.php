<?php

namespace Tests\Feature;

use App\Models\User;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogStoreTest extends TestCase
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
        ]);

        $this->headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer test_token',
        ];
    }
    /**
     * ログの登録 正常系テスト
     *
     * @dataProvider store201Provider
     * @param array $commitData
     * @param array $expected
     * @return void
     */
    public function test_201_store(array $commitData, array $expected): void
    {
        // Arrange
        $url = '/api/logs';

        // Act
        $response = $this->post($url, $commitData, $this->headers);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('logs', $expected);
    }

    /**
     * ログの登録 正常データ作成
     *
     * @return array
     */
    public static function store201Provider(): array
    {
        return [
            // 通常ログ登録
            [
                'commitData' => [
                    'method' => 'PATCH',
                    'url' => 'https://blog.hatena.ne.jp/emoriiin979/readlite.hatenablog.com/atom',
                    'key' => '1234567891',
                    'response_code' => 200,
                    'message' => '正常終了しました。',
                ],
                'expected' => [
                    'level' => 'INFO',
                    'method' => 'PATCH',
                    'url' => 'https://blog.hatena.ne.jp/emoriiin979/readlite.hatenablog.com/atom',
                    'key' => '1234567891',
                    'response_code' => 200,
                    'message' => '正常終了しました。',
                ],
            ],
            // エラーログ登録
            [
                'commitData' => [
                    'method' => 'PATCH',
                    'url' => 'https://blog.hatena.ne.jp/emoriiin979/readlite.hatenablog.com/atom',
                    'key' => '1234567892',
                    'response_code' => 404,
                    'message' => 'データが存在しません。',
                ],
                'expected' => [
                    'level' => 'ERROR',
                    'method' => 'PATCH',
                    'url' => 'https://blog.hatena.ne.jp/emoriiin979/readlite.hatenablog.com/atom',
                    'key' => '1234567892',
                    'response_code' => 404,
                    'message' => 'データが存在しません。',
                ],
            ],
        ];
    }

    /**
     * ログの登録 422異常系テスト
     *
     * @dataProvider store422Provider
     * @param array $commitData
     * @param Closure $assertFunc
     * @return void
     */
    public function test_422_store(array $commitData, Closure $assertFunc): void
    {
        // Arrange
        $url = '/api/logs';

        // Act
        $response = $this->post($url, $commitData, $this->headers);

        // Assert
        $response->assertStatus(422);
        $response->assertJson($assertFunc);
    }

    /**
     * ログの登録 422異常データ作成
     *
     * @return array
     */
    public static function store422Provider(): array
    {
        return [
            // 必須バリデーションが有効であること
            [
                'commitData' => [],
                'assertFunc' => function ($json) {
                    $json
                        ->where('errors', [
                            'method' => [
                                'methodは必ず指定してください。',
                            ],
                            'url' => [
                                'urlは必ず指定してください。',
                            ],
                            'key' => [
                                'keyは必ず指定してください。',
                            ],
                            'response_code' => [
                                'response_codeは必ず指定してください。',
                            ],
                            'message' => [
                                'messageは必ず指定してください。',
                            ],
                        ])
                        ->etc();
                },
            ],
            // 型バリデーションが有効であること
            [
                'commitData' => [
                    'method' => ['method'],
                    'url' => ['url'],
                    'key' => ['key'],
                    'response_code' => 'aaaa',
                    'message' => ['message'],
                ],
                'assertFunc' => function ($json) {
                    $json
                        ->where('errors', [
                            'method' => [
                                'methodは文字列を指定してください。',
                            ],
                            'url' => [
                                'urlは文字列を指定してください。',
                            ],
                            'key' => [
                                'keyは文字列を指定してください。',
                            ],
                            'response_code' => [
                                'response_codeは整数で指定してください。',
                            ],
                            'message' => [
                                'messageは文字列を指定してください。',
                            ],
                        ])
                        ->etc();
                },
            ],
            // 絞込バリデーションが有効であること
            [
                'commitData' => [
                    'method' => 'METHOD',
                    'url' => 'aaaa',
                    'key' => '1234567891',
                    'response_code' => 200,
                    'message' => '正常終了しました。',
                ],
                'assertFunc' => function ($json) {
                    $json
                        ->where('errors', [
                            'method' => [
                                'methodには正しいHTTPメソッドを指定してください。(例:POST)',
                            ],
                            'url' => [
                                'urlに正しい形式を指定してください。',
                            ],
                        ])
                        ->etc();
                },
            ],
        ];
    }
}
