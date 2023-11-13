<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 記事の一覧取得 正常系テスト
     *
     * @dataProvider index200Provider
     * @param array $params
     * @param array $articles
     * @param \Closure $assertFunc
     */
    public function test_200_index($params, $articles, $assertFunc)
    {
        // Arrange
        $url = '/api/articles' . (empty($params) ? '' : '?' . http_build_query($params));

        Article::factory(count($articles))
            ->sequence(...$articles)
            ->create();

        // Act
        $response = $this->get($url, [
            'Accept' => 'application/json',
        ]);

        // Assert
        $response->assertStatus(200);
        $response->assertJson($assertFunc);
    }

    public static function index200Provider()
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
     * @param \Closure $assertFunc
     */
    public function test_422_index($params, $assertFunc)
    {
        // Arrange
        $url = '/api/articles' . (empty($params) ? '' : '?' . http_build_query($params));

        // Act
        $response = $this->get($url, [
            'Accept' => 'application/json',
        ]);

        // Assert
        $response->assertStatus(422);
        $response->assertJson($assertFunc);
    }

    public static function index422Provider()
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
