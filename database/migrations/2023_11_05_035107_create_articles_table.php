<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->string('id', 32)->comment('記事ID');
            $table->string('title', 128)->comment('タイトル');
            $table->dateTime('edited_at')->comment('編集日時');
            $table->boolean('is_modified')->default(false)->comment('整形済みフラグ');
            $table->longText('body')->nullable()->comment('ボディ');
            $table->dateTime('created_at')->nullable()->comment('登録日時');
            $table->dateTime('updated_at')->nullable()->comment('更新日時');
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
