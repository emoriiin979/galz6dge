<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;

class ArticleService
{
    /**
     * 記事モデルオブジェクト
     *
     * @var Article
     */
    protected $model;

    /**
     * 新しい記事サービスインスタンスを作成
     *
     * @param Article $model
     */
    public function __construct(Article $model)
    {
        $this->model = $model;
    }

    /**
     * 記事の一覧取得
     *
     * @param array $params
     * @return Collection
     */
    public function get(array $params): Collection
    {
        $query = $this->model->query();

        if (isset($params['entry_ids'])) {
            $query->whereIn('id', $params['entry_ids']);
        }

        $query->orderBy('edited_at', 'desc');
        $query->limit(config('app.max_count', 1000));

        return $query->get();
    }

    /**
     * 記事の一括登録＆更新
     *
     * @param array $commitData
     * @return void
     */
    public function bulkUpsert(array $commitData): void
    {
        $this->model->upsert($commitData, ['id']);
    }
}
