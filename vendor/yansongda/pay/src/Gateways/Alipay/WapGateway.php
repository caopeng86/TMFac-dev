<?php

namespace Yansongda\Pay\Gateways\Alipay;

<<<<<<< HEAD
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
=======
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
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
<<<<<<< HEAD
>>>>>>> dev
=======
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
    {
        return 'alipay.trade.wap.pay';
    }

    /**
<<<<<<< HEAD
<<<<<<< HEAD
     * get productCode config.
=======
     * Get productCode config.
>>>>>>> dev
=======
     * Get productCode config.
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
=======
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
    protected function getProductCode(): string
    {
        return 'QUICK_WAP_WAY';
    }
<<<<<<< HEAD
>>>>>>> dev
=======
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
}
