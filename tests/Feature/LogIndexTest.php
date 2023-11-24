<?php

namespace Tests\Feature;

use App\Models\Log as LogModel;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * リクエストヘッダー
     *
     * @var array<string, string>
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

        Carbon::setTestNow(Carbon::parse('2024-12-23 12:34:56'));

        User::factory()->create([
            'api_token' => hash('sha256', 'test_token'),
        ]);

        $this->headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer test_token',
        ];
    }

    /**
     * ログの一覧取得 正常系テスト
     *
     * @dataProvider index200Provider
     * @param array $params
     * @param array $logs
     * @param Closure $assertFunc
     * @return void
     */
    public function test_200_index(array $params, array $logs, Closure $assertFunc): void
    {
        // Arrange
        $url = '/api/logs?' . http_build_query($params);

        LogModel::factory(count($logs))
            ->sequence(...$logs)
            ->create();

        // Act
        $response = $this->get($url, $this->headers);

        // Assert
        $response->assertStatus(200);
        $response->assertJson($assertFunc);
    }

    /**
     * ログの一覧取得 正常データ作成
     *
     * @return array
     */
    public static function index200Provider(): array
    {
        return [
            // レスポンスデータが正しく取得できていること
            [
                'params' => [],
                'logs' => [
                    [
                        'level' => 'INFO',
                        'method' => 'POST',
                        'url' => 'https://blog.hatena.ne.jp/emoriiin979/readlite.hatenablog.com/atom',
                        'key' => '1234567891',
                        'response_code' => 200,
                        'message' => '正常終了しました。',
                    ],
                ],
                'assertFunc' => function ($json) {
                    $json
                        ->where('data.0', [
                            'id' => 1,
                            'level' => 'INFO',
                            'method' => 'POST',
                            'url' => 'https://blog.hatena.ne.jp/emoriiin979/readlite.hatenablog.com/atom',
                            'key' => '1234567891',
                            'response_code' => 200,
                            'message' => '正常終了しました。',
                            'created_at' => '2024-12-23 12:34:56',
                        ]);
                },
            ],
            // 正しくソートされていること
            [
                'params' => [],
                'logs' => [
                    [
                        'id' => 1,
                    ],
                    [
                        'id' => 2,
                    ],
                ],
                'assertFunc' => function ($json) {
                    $json
                        ->where('data.0.id', 2)
                        ->where('data.1.id', 1);
                },
            ],
            // methodsで絞り込みできること
            [
                'params' => [
                    'methods' => ['GET', 'POST'],
                ],
                'logs' => [
                    [
                        'id' => 1,
                        'method' => 'PATCH',
                    ],
                    [
                        'id' => 2,
                        'method' => 'POST',
                    ],
                    [
                        'id' => 3,
                        'method' => 'GET',
                    ],
                ],
                'assertFunc' => function ($json) {
                    $json
                        ->has('data', 2)
                        ->where('data.0.id', 3)
                        ->where('data.1.id', 2);
                },
            ],
            // urlで絞り込みできること
            [
                'params' => [
                    'url' => 'hatena',
                ],
                'logs' => [
                    [
                        'id' => 1,
                        'url' => 'https://emolab.jp/articles/',
                    ],
                    [
                        'id' => 2,
                        'url' => 'https://blog.hatenablog.jp/atoms/',
                    ],
                ],
                'assertFunc' => function ($json) {
                    $json
                        ->has('data', 1)
                        ->where('data.0.id', 2);
                },
            ],
            // from,toで絞り込みできること
            [
                'params' => [
                    'from' => '2024-01-01',
                ],
                'logs' => [
                    [
                        'id' => 1,
                        'created_at' => '2023-12-31 23:59:59',
                    ],
                    [
                        'id' => 2,
                        'created_at' => '2024-01-01 00:00:00',
                    ],
                ],
                'assertFunc' => function ($json) {
                    $json
                        ->has('data', 1)
                        ->where('data.0.id', 2);
                },
            ],
            [
                'params' => [
                    'to' => '2023-12-31',
                ],
                'logs' => [
                    [
                        'id' => 1,
                        'created_at' => '2023-12-31 23:59:59',
                    ],
                    [
                        'id' => 2,
                        'created_at' => '2024-01-01 00:00:00',
                    ],
                ],
                'assertFunc' => function ($json) {
                    $json
                        ->has('data', 1)
                        ->where('data.0.id', 1);
                },
            ],
            [
                'params' => [
                    'from' => '2023-12-31',
                    'to' => '2023-12-31',
                ],
                'logs' => [
                    [
                        'id' => 1,
                        'created_at' => '2023-12-30 23:59:59',
                    ],
                    [
                        'id' => 2,
                        'created_at' => '2023-12-31 00:00:00',
                    ],
                    [
                        'id' => 3,
                        'created_at' => '2023-12-31 23:59:59',
                    ],
                    [
                        'id' => 4,
                        'created_at' => '2024-01-01 00:00:00',
                    ],
                ],
                'assertFunc' => function ($json) {
                    $json
                        ->has('data', 2)
                        ->where('data.0.id', 3)
                        ->where('data.1.id', 2);
                },
            ],
        ];
    }

    /**
     * ログの一覧取得 422異常系テスト
     *
     * @dataProvider index422Provider
     * @param array $params
     * @param Closure $assertFunc
     * @return void
     */
    public function test_422_index($params, $assertFunc)
    {
        // Arrange
        $url = '/api/logs?' . http_build_query($params);

        // Act
        $response = $this->get($url, $this->headers);

        // Assert
        $response->assertStatus(422);
        $response->assertJson($assertFunc);
    }

    /**
     * ログの一覧取得 422異常データ作成
     *
     * @return array
     */
    public static function index422Provider(): array
    {
        return [
            // 型バリデーションが有効であること
            [
                'params' => [
                    'methods' => 'methods',
                    'url' => ['url'],
                    'from' => ['from'],
                    'to' => ['to'],
                ],
                'assertFunc' => function ($json) {
                    $json
                        ->where('errors', [
                            'methods' => [
                                'methodsは配列でなくてはなりません。',
                            ],
                            'url' => [
                                'urlは文字列を指定してください。',
                            ],
                            'from' => [
                                'fromはY-m-d形式で指定してください。',
                            ],
                            'to' => [
                                'toはY-m-d形式で指定してください。',
                            ],
                        ])
                        ->etc();
                },
            ],
            // 形式バリデーションが有効であること
            [
                'params' => [
                    'from' => '2024/01/01',
                    'to' => '2024/01/01',
                ],
                'assertFunc' => function ($json) {
                    $json
                        ->where('errors', [
                            'from' => [
                                'fromはY-m-d形式で指定してください。',
                            ],
                            'to' => [
                                'toはY-m-d形式で指定してください。',
                            ],
                        ])
                        ->etc();
                },
            ],
            // methods要素バリデーションが有効であること
            [
                'params' => [
                    'methods' => [
                        ['GET'],
                        'aaaa',
                    ],
                ],
                'assertFunc' => function ($json) {
                    $json
                        ->where('errors', [
                            'methods.0' => [
                                'methodsの要素は文字列を指定してください。',
                            ],
                            'methods.1' => [
                                'methodsの要素には正しいHTTPメソッドを指定してください。(例:POST)',
                            ],
                        ])
                        ->etc();
                },
            ],
        ];
    }
}
