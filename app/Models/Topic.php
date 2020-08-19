<?php

namespace App\Models;

class Topic extends Model
{
    protected $fillable = ['title', 'body', 'category_id', 'excerpt', 'slug'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ScopeWithOrder($query, $order)//Scope 调用方法: (new Model())->query->withOrder | App\Models\Topics::withOrder
    {
        // 不同的排序，使用不同的数据读取逻辑
        switch($order){
            case 'recent':
                $query->recent();
                break;
            default :
                $query->recentReplied();
                break;
        }
        // 预加载防止 N+1 问题
//        return $query->with('user', 'category');//api L03_6.x 这个分支没有用预加载,在其他地方使用了
    }

    public function ScopeRecent($query)
    {
        // 按照创建时间排序
        return $query->orderBy('created_at', 'desc');
    }

    public function ScopeRecentReplied($query)
    {
        return $query->orderBy('updated_at', 'desc');
    }

    public function link($param = [])
    {
        return route('topics.show', array_merge([$this->id, $this->slug], $param));
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function updateReplyCount()
    {
        $this->reply_count = $this->replies->count();
        $this->save();
    }

}
