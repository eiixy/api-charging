<?php

namespace Eiixy\ApiCharging\Models;

use App\Exceptions\HttpException;
use App\Http\StatusCode;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Contracts\JWTSubject;

class AccessUser extends Authenticatable implements JWTSubject
{
    use Notifiable;
    // 账号状态
    const STATUS_NORMAL = 0; // 正常
    const STATUS_FROZEN = 1; // 冻结
    const STATUS_INSUFFICIENT_FUNDS = 2; // 余额不足


    protected $table = 'access_users';

    protected $fillable = ['name', 'uri', 'options'];

    protected $hidden = ['secret_key'];


    /**
     * 扣费
     * @param $price
     */
    public function charging($price)
    {
        $this->balance = $this->balance - $price;
        $this->save();
    }


    /**
     * 充值
     * @param $amount
     */
    public function recharge($amount)
    {
        $this->balance = $this->balance + $amount;
        $this->save();
    }


    /**
     * 生成token
     * @param $request
     * @return string
     * @throws HttpException
     */
    public static function makeToken($request)
    {
        $options = static::validator($request);
        $access = AccessUser::where(['access_key' => $options['access_key'], 'secret_key' => $options['secret_key']])->first();
        if (empty($access)) {
            throw new HttpException(StatusCode::AUTHENTICATION_FAILED);
        }
        $options['validity'] = time() + $options['expires'];
        $token = encrypt(json_encode($options));
        return $token;
    }


    /**
     * 检查token
     * @param $token
     * @return mixed
     * @throws HttpException
     */
    public static function checkToken($token)
    {
        // 解析 token 内容
        $token = explode(' ', $token);
        $token = count($token) == 1 ? $token[0] : $token[1];
        try {
            $config = decrypt($token);
            $config = json_decode($config, true);
        } catch (DecryptException $exception) {
            throw new HttpException(StatusCode::AUTHENTICATION_FAILED);
        }

        // 验证是否过期
        if (time() > $config['validity']) {
            throw new HttpException(StatusCode::TOKEN_EXPIRATION);
        }

        // 验证有效性
        $access = AccessUser::where(['secret_key' => $config['secret_key'], 'access_key' => $config['access_key']])->first();
        if (empty($access)) {
            throw new HttpException(StatusCode::AUTHENTICATION_FAILED);
        }
        return $access;
    }

    /**
     * 验证参数
     * @param $options
     * @return mixed
     * @throws \Exception
     */
    public static function validator($options)
    {
        $rules = [
            'secret_key' => 'required',
            'access_key' => 'required',
            'expires' => 'required|numeric'
        ];
        $result = Validator::make($options, $rules);
        if ($result->fails()) {
            throw new HttpException([
                'code' => -1,
                'msg' => 'Token 参数验证失败',
                'error' => $result->errors()
            ]);
        }

        return $result->validate();
    }


    /**
     * 获取会储存到 jwt 声明中的标识
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * 返回包含要添加到 jwt 声明中的自定义键值对数组
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return ['role' => 'tenant'];
    }
}
