<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    /**
     * ユーザーモデルオブジェクト
     *
     * @var User
     */
    protected $model;

    /**
     * 新しいユーザーサービスインスタンスを作成
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * ユーザーの登録
     *
     * @param array $commitData
     * @return void
     */
    public function create(array $commitData): void
    {
        $commitData = array_merge($commitData, [
            'api_token' => hash('sha256', $commitData['api_token']),
        ]);

        $this->model->create($commitData);
    }
}