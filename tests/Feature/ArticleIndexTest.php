<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleIndexTest extends TestCase
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

        User::factory()->create([
            'api_token' => hash('sha256', 'test_token'),
        ]);

        $this->headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer test_token',
        ];
    }

    /**
     * 記事の一覧取得 正常系テスト
     *
     * @dataProvider index200Provider
     * @param array $params
     * @param array $articles
     * @param Closure $assertFunc
     * @return void
     */
    public function test_200_index(array $params, array $articles, Closure $assertFunc): void
    {
        // Arrange
        $url = '/api/articles?' . http_build_query($params);

        Article::factory(count($articles))
            ->sequence(...$articles)
            ->create();

        // Act
        $response = $this->get($url, $this->headers);

        // Assert
        $response->assertStatus(200);
        $response->assertJson($assertFunc);
    }

    /**
     * 記事の一覧取得 正常データ作成
     *
     * @return array
     */
    public static function index200Provider(): array
    {
        return [
            // レスポンスデータが正しく取得できていること
            [
                'params' => [],
                'articles' => [
                    [
                        'id' => '1',
                        'title' => 'title',
                        'edited_at' => '2024-01-01 00:00:00',
                        'is_modified' => true,
                        'body' => 'body',
                    ],
                ],
                'assertFunc' => function ($json) {
                    $json
                        ->where('data.0', [
                            'id' => '1',
                            'title' => 'title',
                            'edited_at' => '2024-01-01 00:00:00',
                            'is_modified' => true,
                            'body' => 'body',
                        ]);
                },
            ],
            // 正しくソートされていること
            [
                'params' => [],
                'articles' => [
                    [
                        'id' => '1',
                        'edited_at' => '2024-01-01 00:00:00',
                    ],
                    [
                        'id' => '2',
                        'edited_at' => '2024-01-02 00:00:00',
                    ],
                ],
                'assertFunc' => function ($json) {
                    $json
                        ->where('data.0.id', '2')
                        ->where('data.1.id', '1');
                },
            ],
            // entry_idsで絞り込みできること
            [
                'params' => [
                    'entry_ids' => ['2', '3'],
                ],
                'articles' => [
                    [
                        'id' => '1',
                    ],
                    [
                        'id' => '2',
                    ],
                    [
                        'id' => '3',
                    ],
                ],
                'assertFunc' => function ($json) {
                    $json
                        ->has('data', 2)
                        ->where('data.0.id', '2')
                        ->where('data.1.id', '3');
                },
            ],
        ];
    }

    /**
     * 記事の一覧取得 422異常系テスト
     *
     * @dataProvider index422Provider
     * @param array $params
     * @param Closure $assertFunc
     * @return void
     */
    public function test_422_index(array $params, Closure $assertFunc): void
    {
        // Arrange
        $url = '/api/articles?' . http_build_query($params);

        // Act
        $response = $this->get($url, $this->headers);

        // Assert
        $response->assertStatus(422);
        $response->assertJson($assertFunc);
    }

    /**
     * 記事の一覧取得 422異常データ作成
     *
     * @return array
     */
    public static function index422Provider(): array
    {
        return [
            // entry_idsのバリデーションが有効であること
            [
                'params' => [
                    'entry_ids' => 'aaaa',
                ],
                'assertFunc' => function ($json) {
                    $json
                        ->where('message', 'entry_idsは配列でなくてはなりません。')
                        ->etc();
                },
            ],
        ];
    }
}
