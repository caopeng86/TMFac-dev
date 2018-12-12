<?php

namespace Yansongda\Pay\Gateways\Alipay;

<<<<<<< HEAD
class PosGateway extends Alipay
{
    /**
     * get method config.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @return string
     */
    protected function getMethod()
    {
        return 'alipay.trade.pay';
    }

    /**
     * get productCode config.
=======
use Yansongda\Pay\Contracts\GatewayInterface;
use Yansongda\Pay\Log;
use Yansongda\Supports\Collection;

class PosGateway implements GatewayInterface
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
     * @throws \Yansongda\Pay\Exceptions\InvalidConfigException
     * @throws \Yansongda\Pay\Exceptions\InvalidSignException
     *
     * @return Collection
     */
    public function pay($endpoint, array $payload): Collection
    {
        $payload['method'] = $this->getMethod();
        $payload['biz_content'] = json_encode(array_merge(
            json_decode($payload['biz_content'], true),
            [
                'product_code' => $this->getProductCode(),
                'scene'        => 'bar_code',
            ]
        ));
        $payload['sign'] = Support::generateSign($payload);

        Log::info('Starting To Pay An Alipay Pos Order', [$endpoint, $payload]);

        return Support::requestApi($payload);
    }

    /**
     * Get method config.
>>>>>>> dev
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @return string
     */
<<<<<<< HEAD
    protected function getProductCode()
    {
        return 'FACE_TO_FACE_PAYMENT';
    }

    /**
     * pay a order.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param array  $config_biz
     * @param string $scene
     *
     * @return array|bool
     */
    public function pay(array $config_biz = [], $scene = 'bar_code')
    {
        $config_biz['scene'] = $scene;

        return $this->getResult($config_biz, $this->getMethod());
=======
    protected function getMethod(): string
    {
        return 'alipay.trade.pay';
    }

    /**
     * Get productCode config.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @return string
     */
    protected function getProductCode(): string
    {
        return 'FACE_TO_FACE_PAYMENT';
>>>>>>> dev
    }
}
