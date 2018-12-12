<?php

namespace Yansongda\Pay\Gateways\Wechat;

<<<<<<< HEAD
<<<<<<< HEAD
use Yansongda\Pay\Exceptions\InvalidArgumentException;

class PosGateway extends Wechat
{
    /**
     * @var string
     */
    protected $gateway_order = 'pay/micropay';

    /**
     * get trade type config.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @return string
     */
    protected function getTradeType()
    {
        return 'MICROPAY';
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
        if (is_null($this->user_config->get('app_id'))) {
            throw new InvalidArgumentException('Missing Config -- [app_id]');
        }

        unset($this->config['trade_type']);
        unset($this->config['notify_url']);

        return $this->preOrder($config_biz);
=======
=======
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
use Yansongda\Pay\Log;
use Yansongda\Supports\Collection;

class PosGateway extends Gateway
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
        unset($payload['trade_type'], $payload['notify_url']);

        $payload['sign'] = Support::generateSign($payload);

        Log::info('Starting To Pay A Wechat Pos order', [$payload]);

        return Support::requestApi('pay/micropay', $payload);
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
        return 'MICROPAY';
<<<<<<< HEAD
>>>>>>> dev
=======
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
    }
}
