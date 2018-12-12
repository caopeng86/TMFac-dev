<?php

namespace Yansongda\Pay\Gateways\Alipay;

<<<<<<< HEAD
class AppGateway extends Alipay
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
        return 'alipay.trade.app.pay';
    }

    /**
     * get productCode method.
=======
use Symfony\Component\HttpFoundation\Response;
use Yansongda\Pay\Contracts\GatewayInterface;
use Yansongda\Pay\Log;

class AppGateway implements GatewayInterface
{
    /**
     * Pay an order.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param string $endpoint
     * @param array  $payload
     *
     * @throws \Yansongda\Pay\Exceptions\InvalidArgumentException
     * @throws \Yansongda\Pay\Exceptions\InvalidConfigException
     *
     * @return Response
     */
    public function pay($endpoint, array $payload): Response
    {
        $payload['method'] = $this->getMethod();
        $payload['biz_content'] = json_encode(array_merge(
            json_decode($payload['biz_content'], true),
            ['product_code' => $this->getProductCode()]
        ));
        $payload['sign'] = Support::generateSign($payload);

        Log::info('Starting To Pay An Alipay App Order', [$endpoint, $payload]);

        return Response::create(http_build_query($payload));
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
        return 'QUICK_MSECURITY_PAY';
    }

    /**
     * pay a order.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param array $config_biz
     *
     * @return string
     */
    public function pay(array $config_biz = [])
    {
        parent::pay($config_biz);

        return http_build_query($this->config);
=======
    protected function getMethod(): string
    {
        return 'alipay.trade.app.pay';
    }

    /**
     * Get productCode method.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @return string
     */
    protected function getProductCode(): string
    {
        return 'QUICK_MSECURITY_PAY';
>>>>>>> dev
    }
}
