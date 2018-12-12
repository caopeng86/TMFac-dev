<?php

namespace Yansongda\Pay\Tests;

<<<<<<< HEAD
<<<<<<< HEAD
use Yansongda\Pay\Contracts\GatewayInterface;
use Yansongda\Pay\Exceptions\InvalidArgumentException;
=======
use Yansongda\Pay\Contracts\GatewayApplicationInterface;
use Yansongda\Pay\Exceptions\GatewayException;
>>>>>>> dev
=======
use Yansongda\Pay\Contracts\GatewayApplicationInterface;
use Yansongda\Pay\Exceptions\GatewayException;
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
use Yansongda\Pay\Pay;

class PayTest extends TestCase
{
<<<<<<< HEAD
<<<<<<< HEAD
    public function testDriverWithoutConfig()
    {
        $this->expectException(InvalidArgumentException::class);

        $pay = new Pay([]);
        $pay->driver('foo');
    }

    public function testDriver()
    {
        $pay = new Pay(['alipay' => ['app_id' => '']]);

        $this->assertInstanceOf(Pay::class, $pay->driver('alipay'));
    }

    public function testGatewayWithoutDriver()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Driver is not defined.');

        $pay = new Pay([]);
        $pay->gateway();
    }

    public function testInvalidGateway()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Gateway [foo] is not supported.');

        $pay = new Pay(['alipay' => ['app_id' => '']]);
        $pay->driver('alipay')->gateway('foo');
    }

    public function testGateway()
    {
        $pay = new Pay(['alipay' => ['app_id' => '']]);
        $this->assertInstanceOf(GatewayInterface::class, $pay->driver('alipay')->gateway());
=======
=======
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
    public function testAlipayGateway()
    {
        $alipay = Pay::alipay(['foo' => 'bar']);

        $this->assertInstanceOf(GatewayApplicationInterface::class, $alipay);
    }

    public function testWechatGateway()
    {
        $wechat = Pay::wechat(['foo' => 'bar']);

        $this->assertInstanceOf(GatewayApplicationInterface::class, $wechat);
    }

    public function testFooGateway()
    {
        $this->expectException(GatewayException::class);
        $this->expectExceptionMessage('Gateway [foo] Not Exists');

        Pay::foo([]);
<<<<<<< HEAD
>>>>>>> dev
=======
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
    }
}
