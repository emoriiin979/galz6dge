<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Http\Response;

class ArticleService
{
    /**
     * 記事モデルオブジェクト
     *
     * @var  \App\Models\Article
     */
    protected $model;

    /**
     * 新しい記事サービスインスタンスを作成
     *
     * @param  \App\Models\Article  $model
     * @return void
     */
    public function __construct(Article $model)
    {
        $this->model = $model;
    }

    /**
     * 記事の一覧取得
     *
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get($params)
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
     * @param  array  $commitData
     * @return void
     */
    public function bulkUpsert($commitData)
    {
        $this->model->upsert($commitData, ['id']);
    }
}
