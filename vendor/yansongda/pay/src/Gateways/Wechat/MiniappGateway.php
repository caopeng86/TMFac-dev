<?php

namespace Yansongda\Pay\Gateways\Wechat;

<<<<<<< HEAD
use Yansongda\Pay\Exceptions\InvalidArgumentException;

class MiniappGateway extends Wechat
{
    /**
     * get trade type config.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @return string [description]
     */
    protected function getTradeType()
    {
        return 'JSAPI';
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
        if (is_null($this->user_config->get('miniapp_id'))) {
            throw new InvalidArgumentException('Missing Config -- [miniapp_id]');
        }

        $this->config['appid'] = $this->user_config->get('miniapp_id');

        $payRequest = [
            'appId'     => $this->user_config->get('miniapp_id'),
            'timeStamp' => strval(time()),
            'nonceStr'  => $this->createNonceStr(),
            'package'   => 'prepay_id='.$this->preOrder($config_biz)['prepay_id'],
            'signType'  => 'MD5',
        ];
        $payRequest['paySign'] = $this->getSign($payRequest);

        return $payRequest;
=======
use Yansongda\Pay\Gateways\Wechat;
use Yansongda\Supports\Collection;

class MiniappGateway extends MpGateway
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
        $payload['appid'] = Support::getInstance()->miniapp_id;

        if ($this->mode !== Wechat::MODE_SERVICE) {
            $payload['sub_appid'] = Support::getInstance()->sub_miniapp_id;
        }

        return parent::pay($endpoint, $payload);
>>>>>>> dev
    }
}
