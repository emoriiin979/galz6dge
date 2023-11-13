<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleBulkUpsertTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 記事の一括登録＆更新 正常系テスト
     *
     * @dataProvider bulkUpsert204Provider
     * @param array $storeArticles
     * @param array $commitArticles
     * @param array $assertData
     */
    public function test_204_bulkUpsert($storeArticles, $commitArticles, $assertData)
    {
        // Arrange
        $url = '/api/articles';

        if (!empty($storeArticles)) {
            Article::factory(count($storeArticles))
                ->sequence(...$storeArticles)
                ->create();
        }

        // Act
        $response = $this->post($url, $commitArticles, [
            'Accept' => 'application/json',
        ]);

        // Assert
        $response->assertStatus(204);
        $this->assertDatabaseCount('articles', count($assertData));
        foreach ($assertData as $expected) {
            $this->assertDatabaseHas('articles', $expected);
        }
    }

    public static function bulkUpsert204Provider()
    {
        return [
            [
                // 記事の一括登録が正常終了すること
                'storeArticles' => [],
                'commitArticles' => [
                    [
                        'entry_id' => '1',
                        'title' => 'title1',
                        'edited_at' => '2024-01-01 00:00:00',
                        'is_modified' => false,
                        'body' => 'body1',
                    ],
                    [
                        'entry_id' => '2',
                        'title' => 'title2',
                        'edited_at' => '2024-02-01 00:00:00',
                        'is_modified' => true,
                        'body' => 'body2',
                    ],
                ],
                'assertData' => [
                    [
                        'id' => '1',
                        'title' => 'title1',
                        'edited_at' => '2024-01-01 00:00:00',
                        'is_modified' => false,
                        'body' => 'body1',
                    ],
                    [
                        'id' => '2',
                        'title' => 'title2',
                        'edited_at' => '2024-02-01 00:00:00',
                        'is_modified' => true,
                        'body' => 'body2',
                    ],
                ],
            ],
            [
                // 記事の一括更新が正常終了すること
                'storeArticles' => [
                    [
                        'id' => '1',
                        'title' => 'title1更新前',
                        'edited_at' => '2024-01-01 00:00:00',
                        'is_modified' => false,
                        'body' => 'body1更新前',
                    ],
                    [
                        'id' => '2',
                        'title' => 'title2更新前',
                        'edited_at' => '2024-02-01 00:00:00',
                        'is_modified' => false,
                        'body' => 'body2更新前',
                    ],
                ],
                'commitArticles' => [
                    [
                        'entry_id' => '1',
                        'title' => 'title1更新後',
                        'edited_at' => '2024-01-02 00:00:00',
                        'is_modified' => true,
                        'body' => 'body1更新後',
                    ],
                    [
                        'entry_id' => '2',
                        'title' => 'title2更新後',
                        'edited_at' => '2024-02-02 00:00:00',
                        'is_modified' => true,
                        'body' => 'body2更新後',
                    ],
                ],
                'assertData' => [
                    [
                        'id' => '1',
                        'title' => 'title1更新後',
                        'edited_at' => '2024-01-02 00:00:00',
                        'is_modified' => true,
                        'body' => 'body1更新後',
                    ],
                    [
                        'id' => '2',
                        'title' => 'title2更新後',
                        'edited_at' => '2024-02-02 00:00:00',
                        'is_modified' => true,
                        'body' => 'body2更新後',
                    ],
                ],
            ],
        ];
    }

    /**
     * 記事の一括登録＆更新 422異常系テスト
     *
     * @dataProvider bulkUpsert422Provider
     * @param array $commitData
     * @param \Closure $assertFunc
     */
    public function test_422_bulkUpsert($commitData, $assertFunc)
    {
        // Arrange
        $url = '/api/articles';

        // Act
        $response = $this->post($url, $commitData, [
            'Accept' => 'application/json',
        ]);

        // Assert
        $response->assertStatus(422);
        $response->assertJson($assertFunc);
    }

    public static function bulkUpsert422Provider()
    {
        return [
            // requiredのバリデーションが有効であること
            [
                'commitData' => [
                    [],
                ],
                'assertFunc' => function ($json) {
                    $json
                        ->where('errors', [
                            '0.entry_id' => [
                                'entry_idは必ず指定してください。',
                            ],
                            '0.title' => [
                                'titleは必ず指定してください。'
                            ],
                            '0.edited_at' => [
                                'edited_atは必ず指定してください。'
                            ],
                        ])
                        ->etc();
                },
            ],
            // 型バリデーションが有効であること
            [
                'commitData' => [
                    [
                        'entry_id' => '1',
                        'title' => 'title',
                        'edited_at' => '2024/12/23 12:34:56',
                        'is_modified' => 'aaaa',
                    ],
                ],
                'assertFunc' => function ($json) {
                    $json
                        ->where('errors', [
                            '0.edited_at' => [
                                'edited_atはY-m-d H:i:s形式で指定してください。',
                            ],
                            '0.is_modified' => [
                                'is_modifiedは、trueかfalseを指定してください。',
                            ],
                        ])
                        ->etc();
                },
            ],
        ];
    }
}
