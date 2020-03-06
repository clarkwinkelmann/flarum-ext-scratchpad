<?php

use Flarum\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

return Migration::createTable('scratchpads', function (Blueprint $table) {
    $table->increments('id');
    $table->boolean('enabled')->default(true);
    $table->string('title')->default('');
    $table->mediumText('admin_js')->default('');
    $table->mediumText('forum_js')->default('');
    $table->mediumText('admin_js_compiled')->nullable();
    $table->mediumText('forum_js_compiled')->nullable();
    $table->mediumText('admin_less')->default('');
    $table->mediumText('forum_less')->default('');
    $table->mediumText('php')->default('');
    $table->timestamps();
});
