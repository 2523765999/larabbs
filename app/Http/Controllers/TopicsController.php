<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

//	public function index()
	public function index(Request $request, Topic $topic)
	{
//		$topics = Topic::paginate();//默认是15条的
//		$topics = Topic::with('user', 'category')->paginate(30);
		$topics = $topic->withOrder($request->order)->paginate(30);
		return view('topics.index', compact('topics'));
	}

    public function show(Topic $topic)
    {
        return view('topics.show', compact('topic'));
    }

	public function create(Topic $topic)
	{
	    $categories = Category::all();
		return view('topics.create_and_edit', compact('topic', 'categories'));
	}

	public function store(TopicRequest $request, Topic $topic)//store() 方法的第二个参数，会创建一个空白的 $topic 实例；
	{
	    $topic->fill($request->all());
	    //$request->all() 获取所有用户的请求数据数组，如 ['title' => '标题', 'body' => '内容', ... ] ；
        //$topic->fill($request->all()); fill 方法会将传参的键值数组填充到模型的属性中，如以上数组， $topic-
        //>title 的值为 标题 ；
	    $topic->user_id = Auth::id();
	    $topic->save();
//		$topic = Topic::create($request->all());
		return redirect()->route('topics.show', $topic->id)->with('message', 'Created successfully.');
	}

	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);
		return view('topics.create_and_edit', compact('topic'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

		return redirect()->route('topics.show', $topic->id)->with('message', 'Updated successfully.');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('message', 'Deleted successfully.');
	}
}
