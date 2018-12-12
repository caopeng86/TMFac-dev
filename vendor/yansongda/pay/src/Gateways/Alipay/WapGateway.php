<?php

namespace Yansongda\Pay\Gateways\Alipay;

<<<<<<< HEAD
class WapGateway extends Alipay
{
    /**
     * get method config.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @version 2017-08-10
     *
     * @return string [description]
     */
    protected function getMethod()
=======
class WapGateway extends WebGateway
{
    /**
     * Get method config.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @return string
     */
    protected function getMethod(): string
>>>>>>> dev
    {
        return 'alipay.trade.wap.pay';
    }

    /**
<<<<<<< HEAD
     * get productCode config.
=======
     * Get productCode config.
>>>>>>> dev
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @return string
     */
<<<<<<< HEAD
    protected function getProductCode()
    {
        return 'QUICK_WAP_WAY';
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

        return $this->buildPayHtml();
    }
=======
    protected function getProductCode(): string
    {
        return 'QUICK_WAP_WAY';
    }
>>>>>>> dev
}
