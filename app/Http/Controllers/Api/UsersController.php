<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\Image;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function store(UserRequest $request)
    {
        $vevifyData = \Cache::get($request->verification_key);
        if (empty($vevifyData)) {
            abort(403, '验证码已失效');
        }

        if (!hash_equals($vevifyData['code'], $request->verification_code)) {
            //返回401
            throw new AuthenticationException('验证码错误');
        }

        $user = User::create([
            'name' => $request->name,
            'password' => $request->password,
            'phone' => $vevifyData['phone'],
        ]);
        //清除验证码缓存
        \Cache::forget($request->verification_key);
        return new UserResource($user);
    }

    public function show(User $user, Request $request)
    {
        return new UserResource($user);
    }

    public function me(Request $request)
    {
//        return new UserResource($request->user())->showSensitiveFields();
        return (new UserResource($request->user()))->showSensitiveFields();//开关设计的很巧妙
    }

    public function update(UserRequest $request)
    {

        $user = $request->user();

        $attributes = $request->only(['name', 'email', 'introduction', 'registration_id']);

        if ($request->avatar_image_id) {
            $image = Image::find($request->avatar_image_id);

            $attributes['avatar'] = $image->path;
        }
//        dd($attributes);
        $user->update($attributes);

        return (new UserResource($user))->showSensitiveFields();
    }
}
