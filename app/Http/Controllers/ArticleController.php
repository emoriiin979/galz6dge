<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleBulkUpsertRequest;
use App\Http\Requests\ArticleIndexRequest;
use App\Http\Resources\ArticleCollection;
use App\Services\ArticleService;

class ArticleController extends Controller
{
    /**
     * 記事サービスオブジェクト
     *
     * @var \App\Services\ArticleService
     */
    protected $service;

    /**
     * 新しい記事コントローラインスタンスを作成
     *
     * @param  \App\Services\ArticleService  $service
     * @return void
     */
    public function __construct(ArticleService $service)
    {
        $this->service = $service;
    }

    /**
     * 記事の一覧取得
     *
     * @param \App\Http\Requests\ArticleIndexRequest
     * @return \App\Http\Resources\ArticleCollection
     */
    public function index(ArticleIndexRequest $request)
    {
        $params = $request->only([
            'entry_ids',
        ]);

        $articles = $this->service->get($params);

        return app()->make(ArticleCollection::class, [
            'resource' => $articles,
        ]);
    }

    /**
     * 記事の一括登録＆更新
     *
     * @param  \App\Http\Requests\ArticleBulkUpsertRequest
     * @return \Illuminate\Http\Response
     */
    public function bulkUpsert(ArticleBulkUpsertRequest $request)
    {
        $commitData = array_map(
            fn ($value) => [
                'id' => $value['entry_id'],
                'title' => $value['title'],
                'edited_at' => $value['edited_at'],
                'is_modified' => $value['is_modified'],
                'body' => $value['body'],
            ],
            $request->input()
        );

        $this->service->bulkUpsert($commitData);

        return response()->noContent();
    }
}
