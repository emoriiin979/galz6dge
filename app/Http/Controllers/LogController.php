<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogIndexRequest;
use App\Http\Requests\LogStoreRequest;
use App\Http\Resources\LogCollection;
use App\Services\LogService;
use Illuminate\Http\Response;

class LogController extends Controller
{
    /**
     * ログサービスオブジェクト
     *
     * @var LogService
     */
    protected $service;

    /**
     * 新しいログコントローラインスタンスを作成
     *
     * @param LogService $service
     */
    public function __construct(LogService $service)
    {
        $this->service = $service;
    }

    /**
     * ログの一覧取得
     *
     * @param LogIndexRequest $request
     * @return LogCollection
     */
    public function index(LogIndexRequest $request): LogCollection
    {
        $params = $request->only([
            'methods',
            'url',
            'from',
            'to',
        ]);

        $logs = $this->service->get($params);

        return app()->make(LogCollection::class, [
            'resource' => $logs,
        ]);
    }

    /**
     * ログの登録
     *
     * @param LogStoreRequest $request
     * @return Response
     */
    public function store(LogStoreRequest $request): Response
    {
        $commitData = $request->only([
            'method',
            'url',
            'key',
            'response_code',
            'message',
        ]);

        $this->service->create($commitData);

        return response()->noContent(Response::HTTP_CREATED);
    }
}
