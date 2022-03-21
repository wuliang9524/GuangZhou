<?php

namespace Logan\Guangzhou;

use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client as HttpClient;
use Logan\Guangzhou\exceptions\InitRuntimeException;

class Client
{
    /**
     * 接口域名(带端口号)
     *
     * @var string
     */
    protected $domain;

    /**
     * api key
     *
     * @var string
     */
    protected $key;

    /**
     * api secret
     *
     * @var string
     */
    protected $secret;

    /**
     * 请求的 reqParams
     *
     * @var array
     */
    protected $reqParams;

    /**
     * sign
     *
     * @var string
     */
    protected $sign;

    /**
     * GuzzleHttp 实例
     *
     * @var GuzzleHttp\Client
     */
    protected $httpClient = null;

    /**
     * 接口返回值
     *
     * @var string
     */
    protected $response;

    /**
     * 构造方法
     *
     * @param string $domain    接口地址
     * @param string $appId     appid
     * @author LONG <1121116451@qq.com>
     * @version version
     * @date 2022-02-21
     */
    public function __construct(string $domain, string $key, string $secret)
    {
        $domain = rtrim($domain, '/');

        if (empty($domain)) {
            throw new InitRuntimeException("domain is not null", 0);
        }
        if (empty($key)) {
            throw new InitRuntimeException("key is not null", 0);
        }
        if (empty($secret)) {
            throw new InitRuntimeException("secret is not null", 0);
        }

        $this->domain     = $domain;
        $this->key        = $key;
        $this->secret     = $secret;
        $this->httpClient = new HttpClient();
    }

    public function setSign(array $signParams)
    {
        ksort($signParams);
        $string = http_build_query($signParams);
        $string .= $this->secret;

        $this->sign = md5($string);
        return $this;
    }

    public function getSign()
    {
        return $this->sign;
    }

    /**
     * 添加考勤记录
     *
     * @param array $params
     * @return void
     * @author LONG <1121116451@qq.com>
     * @version version
     * @date 2022-03-18
     */
    public function addAttendance(array $params)
    {
        $signParams = $this->reqParams = array_filter(
            $params,
            function ($val) {
                return ($val !== null && $val !== '');
            }
        );
        unset($signParams['atteImage']);
        $this->setSign($signParams);

        $this->reqParams = array_merge(
            $this->reqParams,
            ['sign' => $this->sign]
        );

        $url = $this->domain . '/attendance/atteApi/save';
        $res = $this->httpClient->request('POST', $url, [
            RequestOptions::FORM_PARAMS => $this->reqParams
        ])
            ->getBody()
            ->getContents();
        return $res;
    }

    /**
     * 获取接口请求的时间 YYYYmmddHHiissSSS
     * 精准到毫秒
     *
     * @return void
     * @author LONG <1121116451@qq.com>
     * @version version
     * @date 2022-03-18
     */
    public function getReqTimestamp()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msec = floor($msec * 1000);
        $time = date('YmdHis', $sec) . $msec;
        return $time;
    }
}
