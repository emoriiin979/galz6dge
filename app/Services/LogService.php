<?php

namespace App\Services;

use App\Models\Log as LogModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class LogService
{
    /**
     * ログモデルオブジェクト
     *
     * @var LogModel
     */
    protected $model;

    /**
     * 新しいログサービスインスタンスを作成
     *
     * @param LogModel $model
     */
    public function __construct(LogModel $model)
    {
        $this->model = $model;
    }

    /**
     * ログの一覧取得
     *
     * @param array $params
     * @return Collection
     */
    public function get(array $params): Collection
    {
        $query = $this->model->query();

        if (isset($params['methods'])) {
            $query->whereIn('method', $params['methods']);
        }

        if (isset($params['url'])) {
            $query->where('url', 'LIKE', '%' . $params['url'] . '%');
        }

        if (isset($params['from'])) {
            $query->where('created_at', '>=', $params['from']);
        }

        if (isset($params['to'])) {
            $query->where('created_at', '<=', $params['to']);
        }

        $query->orderBy('id', 'desc');
        $query->limit(config('app.max_count', 1000));

        return $query->get();
    }

    /**
     * ログの登録
     *
     * @param array $commitData
     * @return void
     */
    public function create(array $commitData): void
    {
        $method = static::isSuccess(Arr::get($commitData, 'response_code', 0)) ? 'info' : 'error';

        Log::channel('db')->$method(Arr::get($commitData, 'message'), [
            'method' => Arr::get($commitData, 'method'),
            'url' => Arr::get($commitData, 'url'),
            'key' => Arr::get($commitData, 'key'),
            'response_code' => Arr::get($commitData, 'response_code'),
        ]);
    }

    /**
     * 成功レスポンスかどうか判定
     *
     * @param int $code
     * @return bool
     */
    protected static function isSuccess(int $code): bool
    {
        return in_array($code, [
            Response::HTTP_OK,
            Response::HTTP_CREATED,
            Response::HTTP_NO_CONTENT,
        ]);
    }
}
