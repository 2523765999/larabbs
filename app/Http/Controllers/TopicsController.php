<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\handlers\ImageUploadHandlers;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

//	public function index()
	public function index(Request $request, Topic $topic, User $user)
	{
//		$topics = Topic::paginate();//默认是15条的
//		$topics = Topic::with('user', 'category')->paginate(30);
		$topics = $topic->withOrder($request->order)->paginate(30);
        $active_users = $user->getActiveUsers();
		return view('topics.index', compact('topics', 'active_users'));
	}

    public function show(Topic $topic, Request $request)
    {
        // return view('topics.show', compact('topic'));
        //url 矫正
        if (!empty($topic->slug) && ($topic->slug != $request->slug)) {
            return redirect($topic->link, 301);
        }
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
//		return redirect()->route('topics.show', $topic->id)->with('message', 'Created successfully.');
		return redirect()->to($topic->link())->with('success', 'Created successfully.');
	}

	public function edit(Topic $topic)
	{
        $categories = Category::all();
        $this->authorize('update', $topic);
		return view('topics.create_and_edit', compact('topic', 'categories'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

//		return redirect()->route('topics.show', $topic->id)->with('success', 'Updated successfully.');
		return redirect()->to($topic->link())->with('success', 'Updated successfully.');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('success', 'Deleted successfully.');
	}

	public function uploadImage(Request $request, ImageUploadHandlers $uploader)
    {
//        //初始化返回数据, 默认是失败的
//        $data = [
//            'success'   => 'false',
//            'msg'       => '上传失败!',
//            'file_path' => '',
//        ];
//        dd($request->upload_file);
//        //判断是否有上传文件, 并赋值给$file
//        if ($file = $request->upload_file) {
//            //保存图片到本地
//            $ret = $uploader->save($request->upload_file, 'topics', \Auth::id(), 1024);
//            if ($ret) {
//                $data['file_path'] = $ret['path'];
//                $data['msg'] = '上传成功!';
//                $data['success'] = true;
//            }
//        }
//        return $data;
        // 初始化返回数据，默认是失败的
        $data = [
            'success'   => false,
            'msg'       => '上传失败!',
            'file_path' => ''
        ];
        // 判断是否有上传文件，并赋值给 $file
        if ($file = $request->upload_file) {
            // 保存图片到本地
            $result = $uploader->save($request->upload_file, 'topics', \Auth::id(), 1024);
            // 图片保存成功的话
            if ($result) {
                $data['file_path'] = $result['path'];
                $data['msg']       = "上传成功!";
                $data['success']   = true;
            }
        }
        return $data;
    }


}
