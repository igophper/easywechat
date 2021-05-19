<?php

declare(strict_types=1);

namespace EasyWeChat\Pay\Fundflow;

use EasyWeChat\Kernel\Http\StreamResponse;
use EasyWeChat\Pay\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * Download fundflow history as a table file.
     *
     * @param string $date
     * @param string $type
     * @param array  $options
     *
     * @return array|\EasyWeChat\Kernel\Http\Response|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $date, string $type = 'Basic', $options = [])
    {
        $params = [
            'appid' => $this->app['config']->app_id,
            'bill_date' => $date,
            'account_type' => $type,
            'sign_type' => 'HMAC-SHA256',
            'nonce_str' => uniqid('micro'),
        ];
        $options = array_merge(
            [
                'cert' => $this->app['config']->get('cert_path'),
                'ssl_key' => $this->app['config']->get('key_path'),
            ],
            $options
        );
        $response = $this->requestRaw('pay/downloadfundflow', $params, 'post', $options);

        if (0 === strpos($response->getBody()->getContents(), '<xml>')) {
            return $this->castResponseToType($response, $this->app['config']->get('response_type'));
        }

        return StreamResponse::buildFromPsrResponse($response);
    }
}