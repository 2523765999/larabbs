<?php

namespace App\Http\Controllers\Api;

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
        return response()->json(['token' => $user->id]);
    }
}
