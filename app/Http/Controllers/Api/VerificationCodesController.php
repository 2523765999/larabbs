<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use Doctrine\Common\Cache\Cache;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class VerificationCodesController extends Controller
{
    //
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        $captchaData = \Cache::get($request->captcha_key);
        if (!$captchaData) {
            abort(403, '图片验证码已失效');
        }
        if (!hash_equals($captchaData['code'], $request->captcha_code)) {
            \Cache::forget($request->captcha_key);
            throw new AuthenticationException('图片验证码不正确');
        }

//        $phone = $request->phone;
        $phone = $captchaData['phone'];

        //只有production APP_ENV为生产环境才发送短信
        if (!app()->environment('production')) {
            $code = '1234';
        } else {
            //生成4位随机数,左侧补零
//        $code = str_pad(str_random(1, 9999), 4, 0, STR_PAD_LEFT);
            $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);
            try {
                $result = $easySms->send($phone, [
                   'template' => config('easysms.gateways.aliyun.template.register'),
                    'data' => [
                        'code' => $code
                    ],
                ]);
            } catch(NoGatewayAvailableException $exception) {
                $message = $exception->getExceptions('aliyun')->getMessage();
                abort(500, $message ?: '短信发送异常');
            }
        }
        $key = 'verificationCode_'. Str::random(15);
        $expiredAt = now()->addMinutes(5);
        //缓存验证码 5min 过期
        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        return response()->json([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}
