<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * ユーザーサービスオブジェクト
     *
     * @var  \App\Services\UserService
     */
    protected $service;

    /**
     * 新しいユーザーコントローラインスタンスを作成
     *
     * @param  \App\Services\UserService  $service
     * @return void
     */
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * ユーザーの登録
     *
     * @param  \App\Http\Requests\UserStoreRequest
     * @param  \App\Models\User
     * @return \Illuminate\Http\Response
     */
    public function store(UserStoreRequest $request)
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
