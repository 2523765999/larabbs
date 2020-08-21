<?php

namespace App\Http\Controllers\Api;

use App\Http\Queries\ReplyQuery;
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

    /**
     * @desc 回复删除
     * @param Topic $topic
     * @param Reply $reply
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Topic $topic, Reply $reply)
    {
        if ($topic->id != $reply->topic_id) {
            abort(404);
        }

        $this->authorize('destroy', $reply);//授权策略

        $reply->delete();
        return response(null, 204);
    }

//    public function index(Topic $topic)
    public function index($topicId, ReplyQuery $query)
    {
//        return new ReplyResource($topic->replies()->paginate());//"message": "Undefined property: Illuminate\\Pagination\\LengthAwarePaginator::$id",
//        "exception": "ErrorException",
//        return ReplyResource::collection($topic->replies()->paginate());
        $replies = $query->where('topic_id', $topicId)->paginate();
        return ReplyResource::collection($replies);
    }
}
