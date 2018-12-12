<?php

namespace Yansongda\Pay\Gateways\Wechat;

<<<<<<< HEAD
<<<<<<< HEAD
use Yansongda\Pay\Exceptions\InvalidArgumentException;

class AppGateway extends Wechat
{
    /**
     * get trade type config.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @return string
     */
    protected function getTradeType()
    {
        return 'APP';
    }

    /**
     * pay a order.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param array $config_biz
     *
     * @return array
     */
    public function pay(array $config_biz = [])
    {
        if (is_null($this->user_config->get('appid'))) {
            throw new InvalidArgumentException('Missing Config -- [appid]');
        }

        $this->config['appid'] = $this->user_config->get('appid');

        $payRequest = [
            'appid'     => $this->user_config->get('appid'),
            'partnerid' => $this->user_config->get('mch_id'),
            'prepayid'  => $this->preOrder($config_biz)['prepay_id'],
            'timestamp' => strval(time()),
            'noncestr'  => $this->createNonceStr(),
            'package'   => 'Sign=WXPay',
        ];
        $payRequest['sign'] = $this->getSign($payRequest);

        return $payRequest;
=======
=======
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Yansongda\Pay\Gateways\Wechat;
use Yansongda\Pay\Log;
use Yansongda\Supports\Str;

class AppGateway extends Gateway
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
     * @throws \Exception
     *
     * @return Response
     */
    public function pay($endpoint, array $payload): Response
    {
        $payload['appid'] = Support::getInstance()->appid;
        $payload['trade_type'] = $this->getTradeType();

        if ($this->mode !== Wechat::MODE_SERVICE) {
            $payload['sub_appid'] = Support::getInstance()->sub_appid;
        }

        $pay_request = [
            'appid'     => $this->mode === Wechat::MODE_SERVICE ? $payload['sub_appid'] : $payload['appid'],
            'partnerid' => $this->mode === Wechat::MODE_SERVICE ? $payload['sub_mch_id'] : $payload['mch_id'],
            'prepayid'  => $this->preOrder($payload)->prepay_id,
            'timestamp' => strval(time()),
            'noncestr'  => Str::random(),
            'package'   => 'Sign=WXPay',
        ];
        $pay_request['sign'] = Support::generateSign($pay_request);

        Log::info('Starting To Pay A Wechat App Order', [$endpoint, $pay_request]);

        return JsonResponse::create($pay_request);
    }

    /**
     * Get trade type config.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @return string
     */
    protected function getTradeType(): string
    {
        return 'APP';
<<<<<<< HEAD
>>>>>>> dev
=======
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
    }
}
