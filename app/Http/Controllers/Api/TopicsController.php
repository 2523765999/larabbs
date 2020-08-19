<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\TopicRequest;
use App\Http\Resources\TopicResource;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TopicsController extends Controller
{
    public function store(TopicRequest $request, Topic $topic)
    {
        $topic->fill($request->all());
//        $topic->user_id = $request->user()->id();//错误用法
        $topic->user_id = $request->user()->id;
        $topic->save();

        return new TopicResource($topic);
    }

    public function update(TopicRequest $request, Topic $topic)
    {
        $this->authorize('update', $topic);
        $topic->update($request->all());
        return new TopicResource($topic);
    }

    public function destroy(Topic $topic)
    {
        $this->authorize('destroy', $topic);
        $topic->delete();

        return response(null, 204);
    }

    public function index(Request $request, Topic $topic)
    {
//        dd($topic);
        $query = $topic->query();
//        dd($query);

        if ($categoryId = $request->category_id) {
            $query->where('category_id', $categoryId);
        }
//        $topics = $query
//            ->with('user', 'category')
//            ->withOrder($request->order)
//            ->paginate();
        $topics = QueryBuilder::for(Topic::class)
            ->allowedIncludes('user', 'category')//allowedIncludes 方法传入可以被 include 的参数
            ->allowedFilters([//控制可用的搜索条件
                'title',//模糊搜索
                AllowedFilter::exact('category_id'),//category_id 是精确搜索
                AllowedFilter::scope('withOrder')->default('recentReplied'),
            ])//使用 filter 参数可以进行搜索，该参数是个数组
            ->paginate();
//        dd($topics);
//        $topics = Topic::withOrder($query, $request->order)->paginate();
        return  TopicResource::collection($topics);
    }

    public function userIndex(Request $request, User $user)
    {
        $query = $user->topics()->getQuery();
        $topics = QueryBuilder::for($query)
            ->allowedIncludes('user', 'category')
            ->allowedFilters([
                'title',
                AllowedFilter::exact('category_id'),
                AllowedFilter::scope('withOrder')->default('recentReplied'),
            ])
            ->paginate();
        return TopicResource::collection($topics);
    }
}
