<?php

<<<<<<< HEAD
<<<<<<< HEAD
/**
 * 发放普通红包
 * Class RedPackGateway
 * Date: 2017/12/21
 * Time: 19:23
 * Com:萌点云科技（深圳）有限公司.
 *
 * Author:陈老司机
 *
 * Email:690712575@qq.com
 */

namespace Yansongda\Pay\Gateways\Wechat;

use Yansongda\Pay\Exceptions\GatewayException;
use Yansongda\Pay\Exceptions\InvalidArgumentException;

class RedpackGateway extends Wechat
{
    /**
     * @var string
     */
    protected $gateway_transfer = 'mmpaymkttransfers/sendredpack';

    /**
     * pay a order.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param array $config_biz
     *
     * @return mixed
     */
    public function pay(array $config_biz = [])
    {
        if (is_null($this->user_config->get('app_id'))) {
            throw new InvalidArgumentException('Missing Config -- [app_id]');
        }
        unset($this->config['sign_type']);
        unset($this->config['trade_type']);
        unset($this->config['notify_url']);
        unset($this->config['app_id']);
        unset($this->config['appid']);

        $this->config = array_merge($this->config, $config_biz);

        $this->config['sign'] = $this->getSign($this->config);

        $data = $this->fromXml($this->post(
            $this->endpoint.$this->gateway_transfer,
            $this->toXml($this->config),
            [
                'cert'    => $this->user_config->get('cert_client', ''),
                'ssl_key' => $this->user_config->get('cert_key', ''),
            ]
        ));

        if (!isset($data['return_code']) || $data['return_code'] !== 'SUCCESS' || $data['result_code'] !== 'SUCCESS') {
            $error = 'getResult error:'.$data['return_msg'];
            $error .= isset($data['err_code_des']) ? ' - '.$data['err_code_des'] : '';
        }

        if (isset($error)) {
            throw new GatewayException(
                $error,
                20000,
                $data);
        }

        return $data;
    }

    /**
     * get trade type config.
=======
=======
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
namespace Yansongda\Pay\Gateways\Wechat;

use Symfony\Component\HttpFoundation\Request;
use Yansongda\Pay\Gateways\Wechat;
use Yansongda\Pay\Log;
use Yansongda\Supports\Collection;

class RedpackGateway extends Gateway
{
    /**
     * Pay an order.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param string $endpoint
     * @param array  $payload
     *
     * @throws \Yansongda\Pay\Exceptions\GatewayException
     * @throws \Yansongda\Pay\Exceptions\InvalidArgumentException
     * @throws \Yansongda\Pay\Exceptions\InvalidSignException
     *
     * @return Collection
     */
    public function pay($endpoint, array $payload): Collection
    {
        $payload['wxappid'] = $payload['appid'];

        if (php_sapi_name() !== 'cli') {
            $payload['client_ip'] = Request::createFromGlobals()->server->get('SERVER_ADDR');
        }

        if ($this->mode !== Wechat::MODE_SERVICE) {
            $payload['msgappid'] = $payload['appid'];
        }

        unset($payload['appid'], $payload['trade_type'],
              $payload['notify_url'], $payload['spbill_create_ip']);

        $payload['sign'] = Support::generateSign($payload);

        Log::info('Starting To Pay A Wechat Redpack Order', [$endpoint, $payload]);

        return Support::requestApi(
            'mmpaymkttransfers/sendredpack',
            $payload,
            true
        );
    }

    /**
     * Get trade type config.
<<<<<<< HEAD
>>>>>>> dev
=======
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @return string
     */
<<<<<<< HEAD
<<<<<<< HEAD
    protected function getTradeType()
=======
    protected function getTradeType(): string
>>>>>>> dev
=======
    protected function getTradeType(): string
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
    {
        return '';
    }
}
