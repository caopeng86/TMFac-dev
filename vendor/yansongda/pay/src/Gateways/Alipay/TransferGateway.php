<?php

namespace Yansongda\Pay\Gateways\Alipay;

<<<<<<< HEAD
<<<<<<< HEAD
class TransferGateway extends Alipay
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
        return 'alipay.fund.trans.toaccount.transfer';
    }

    /**
     * get productCode config.
=======
=======
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
use Yansongda\Pay\Contracts\GatewayInterface;
use Yansongda\Pay\Log;
use Yansongda\Supports\Collection;

class TransferGateway implements GatewayInterface
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

        Log::info('Starting To Pay An Alipay Transfer Order', [$endpoint, $payload]);

        return Support::requestApi($payload);
    }

    /**
     * Get method config.
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
    protected function getProductCode()
    {
        return '';
    }

    /**
     * transfer amount to account.
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
=======
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
    protected function getMethod(): string
    {
        return 'alipay.fund.trans.toaccount.transfer';
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
<<<<<<< HEAD
>>>>>>> dev
=======
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
    }
}
