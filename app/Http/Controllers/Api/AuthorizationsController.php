<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationRequest;
use App\Http\Requests\Api\SociaAuthorizationRequest;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AuthorizationsController extends Controller
{
    public function SocialStore($type, SociaAuthorizationRequest $request)
    {
        $driver = \Socialite::driver($type);

        try {
            if ($code = $request->code) {
                $response = $driver->getAccessTokenResponse($code);
                $token = Arr::get($response, 'access_token');
            } else {
                $token = $request->access_token;
                if ($type == 'weixin') {
                    $driver->setOpenId($request->openid);
                }
            }

            $oAuthUser = $driver->userFromToken($token);
        } catch (\Exception $e) {
            throw new AuthenticationException('参数错误, 未获取用户信息');
        }

        switch ($type) {
            case 'weixin':
                $unionId = $oAuthUser->offsetExists('unionid') ? $oAuthUser->offsetGet('unionid') : null;
                if ($unionId) {
                    $user = User::where('weixin_unionid', $unionId)->get();
                } else {
                    $user = User::where('weixin_openid', $oAuthUser->getId())->first();
                }

                //如果没有此用户, 则创建
                if (!$user) {
                    $user = User::create([
                        'name' => $oAuthUser->getNickname(),
                        'avatar' => $oAuthUser->getAvatar(),
                        'weixin_openid' => $oAuthUser->getId(),
                        'weixin_unionid' => $unionId,
                    ]);
                }
            break;
        }
//        return response()->json(['token' => $user->id]);
        $token = auth('api')->login($user);//第三方登录获取 user 后，我们可以使用 login 方法为某一个用户模型生成 token。
        return $this->reponsedWithToken($token)->setStatusCode(201);
    }

    public function store(AuthorizationRequest $request)
    {
        $username = $request->username;
        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $username :
            $credentials['phone'] = $username;
        $credentials['password'] = $request->password;
        if (!$token = \Auth::guard('api')->attempt($credentials)) {
            throw new AuthenticationException('用户名或密码错误');
        }
//        return response()->json([
//            'access_token' => $token,
//            'token_type' => 'Bearer',
//            'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
//        ])->setStatusCode(201);
        return $this->reponsedWithToken($token)->setStatusCode(201);
    }

    public function reponsedWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60,
        ]);
    }

    public function update()
    {
        $token = auth('api')->refresh();
        return $this->reponsedWithToken($token);
    }

    public function destroy()
    {
        auth('api')->logout();
        return response(null, 204);
    }
}
