<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Services\UserService;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * ユーザーサービスオブジェクト
     *
     * @var UserService
     */
    protected $service;

    /**
     * 新しいユーザーコントローラインスタンスを作成
     *
     * @param UserService $service
     */
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * ユーザーの登録
     *
     * @param UserStoreRequest $request
     * @return Response
     */
    public function store(UserStoreRequest $request): Response
    {
        $commitData = $request->only([
            'name',
            'email',
            'password',
            'api_token',
            'role',
        ]);

        $this->service->create($commitData);

        return response()->noContent(Response::HTTP_CREATED);
    }
}
