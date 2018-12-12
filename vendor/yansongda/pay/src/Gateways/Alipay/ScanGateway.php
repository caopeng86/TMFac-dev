<?php

namespace Yansongda\Pay\Gateways\Alipay;

<<<<<<< HEAD
class ScanGateway extends Alipay
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
        return 'alipay.trade.precreate';
    }

    /**
     * get productCode config.
=======
use Yansongda\Pay\Contracts\GatewayInterface;
use Yansongda\Pay\Log;
use Yansongda\Supports\Collection;

class ScanGateway implements GatewayInterface
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
            ['product_code' => $this->getProductCode()]
        ));
        $payload['sign'] = Support::generateSign($payload);

        Log::info('Starting To Pay An Alipay Scan Order', [$endpoint, $payload]);

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
        return '';
    }

    /**
     * pay a order.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param array $config_biz
     *
     * @return array|bool
     */
    public function pay(array $config_biz = [])
    {
        return $this->getResult($config_biz, $this->getMethod());
=======
    protected function getMethod(): string
    {
        return 'alipay.trade.precreate';
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
        return '';
>>>>>>> dev
    }
}
