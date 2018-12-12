<?php

namespace Yansongda\Pay\Contracts;

interface GatewayInterface
{
    /**
<<<<<<< HEAD
<<<<<<< HEAD
     * pay a order.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param array $config_biz
     *
     * @return mixed
     */
    public function pay(array $config_biz);

    /**
     * refund a order.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param array|string $config_biz
     *
     * @return array|bool
     */
    public function refund($config_biz);

    /**
     * close a order.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param array|string $config_biz
     *
     * @return array|bool
     */
    public function close($config_biz);

    /**
     * find a order.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param string $out_trade_no
     *
     * @return array|bool
     */
    public function find($out_trade_no);

    /**
     * verify notify.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param mixed  $data
     * @param string $sign
     * @param bool   $sync
     *
     * @return array|bool
     */
    public function verify($data, $sign = null, $sync = false);
=======
=======
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
     * Pay an order.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param string $endpoint
     * @param array  $payload
     *
     * @return \Yansongda\Supports\Collection|\Symfony\Component\HttpFoundation\Response
     */
    public function pay($endpoint, array $payload);
<<<<<<< HEAD
>>>>>>> dev
=======
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
}
