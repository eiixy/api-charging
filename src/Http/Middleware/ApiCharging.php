<?php


namespace Eiixy\ApiCharging\Http\Middleware;

use Sczts\Skeleton\Exceptions\HttpException;
use Sczts\Skeleton\Http\StatusCode;
use Eiixy\ApiCharging\Models\Api;
use Eiixy\ApiCharging\Models\UsageRecord;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiCharging
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws HttpException
     */
    public function handle(Request $request, Closure $next)
    {
        $apis = Api::getAllFromCache();

        $route = $request->route();
        $method = $request->method();
        $uri = $route->uri;
        $ip = $request->getClientIp();
        $options = [
            'params' => $route->parameters,
            'data' => $request->all()
        ];
        $api = $apis->where('method', $method)->where('uri', $uri)->first();
        $access = \auth('tenant')->user();
        if (!$access){
            throw new HttpException(StatusCode::AUTHENTICATION_FAILED);
        }

        // 判断余额
        if ($api->price != 0 && $api->price > $access->balance) {
            throw new HttpException(StatusCode::INSUFFICIENT_BALANCE);
        }
        $response = $next($request);

        if ($response->getStatusCode() == 200){
            $res = $response->original;
            if ($res['code'] == 0 && !empty($res['data'])){
                $access->charging($api->price);
            }
            $this->log([
                'access' => $access->id,
                'api' => $api->id,
                'method' => $method,
                'uri' => $uri,
                'options' => $options,
                'ip' => $ip
            ]);
            // 判断结果 是否扣费,记录
        }


        return $response;
    }

    /**
     * 保存使用记录
     * @param $data
     */
    private function log($data)
    {
        UsageRecord::create($data);
    }
}
