<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ReplyRequest;
use App\Http\Resources\ReplyResource;
use App\Models\Reply;
use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RepliesController extends Controller
{
    /**
     * @desc 发布回复
     * @param Topic $topic
     * @param Reply $reply
     * @param ReplyRequest $request
     * @return ReplyResource
     */
    public function store(Topic $topic, Reply $reply, ReplyRequest $request)
    {
        $reply->content = $request->content;
        $reply->topic()->associate($topic);//当更新 belongsTo 关联时，可以使用 associate 方法。此方法将会在子模型中设置外键：
        $reply->user()->associate($request->user());//user 可以这么获取
        $reply->save();
        return new ReplyResource($reply);
    }
}
