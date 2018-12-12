<?php

namespace Yansongda\Pay;

<<<<<<< HEAD
use Yansongda\Pay\Exceptions\InvalidArgumentException;
use Yansongda\Pay\Support\Config;

class Pay
{
    /**
     * @var \Yansongda\Pay\Support\Config
     */
    private $config;

    /**
     * @var string
     */
    private $drivers;

    /**
     * @var \Yansongda\Pay\Contracts\GatewayInterface
     */
    private $gateways;

    /**
     * construct method.
     *
     * @author JasonYan <me@yansongda.cn>
     *
     * @param array $config
     */
    public function __construct(array $config = [])
=======
use Yansongda\Pay\Contracts\GatewayApplicationInterface;
use Yansongda\Pay\Exceptions\InvalidGatewayException;
use Yansongda\Pay\Gateways\Alipay;
use Yansongda\Pay\Gateways\Wechat;
use Yansongda\Supports\Config;
use Yansongda\Supports\Log;
use Yansongda\Supports\Str;

/**
 * @method static Alipay alipay(array $config) 支付宝
 * @method static Wechat wechat(array $config) 微信
 */
class Pay
{
    /**
     * Config.
     *
     * @var Config
     */
    protected $config;

    /**
     * Bootstrap.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param array $config
     */
    public function __construct(array $config)
>>>>>>> dev
    {
        $this->config = new Config($config);
    }

    /**
<<<<<<< HEAD
     * set pay's driver.
     *
     * @author JasonYan <me@yansongda.cn>
     *
     * @param string $driver
     *
     * @return Pay
     */
    public function driver($driver)
    {
        if (is_null($this->config->get($driver))) {
            throw new InvalidArgumentException("Driver [$driver]'s Config is not defined.");
        }

        $this->drivers = $driver;

        return $this;
    }

    /**
     * set pay's gateway.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param string $gateway
     *
     * @return \Yansongda\Pay\Contracts\GatewayInterface
     */
    public function gateway($gateway = 'web')
    {
        if (!isset($this->drivers)) {
            throw new InvalidArgumentException('Driver is not defined.');
        }

        $this->gateways = $this->createGateway($gateway);

        return $this->gateways;
    }

    /**
     * create pay's gateway.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param string $gateway
     *
     * @return \Yansongda\Pay\Contracts\GatewayInterface
     */
    protected function createGateway($gateway)
    {
        if (!file_exists(__DIR__.'/Gateways/'.ucfirst($this->drivers).'/'.ucfirst($gateway).'Gateway.php')) {
            throw new InvalidArgumentException("Gateway [$gateway] is not supported.");
        }

        $gateway = __NAMESPACE__.'\\Gateways\\'.ucfirst($this->drivers).'\\'.ucfirst($gateway).'Gateway';

        return $this->build($gateway);
    }

    /**
     * build pay's gateway.
     *
     * @author JasonYan <me@yansongda.cn>
     *
     * @param string $gateway
     *
     * @return \Yansongda\Pay\Contracts\GatewayInterface
     */
    protected function build($gateway)
    {
        return new $gateway($this->config->get($this->drivers));
=======
     * Magic static call.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param string $method
     * @param array  $params
     *
     * @throws InvalidGatewayException
     *
     * @return GatewayApplicationInterface
     */
    public static function __callStatic($method, $params): GatewayApplicationInterface
    {
        $app = new self(...$params);

        return $app->create($method);
    }

    /**
     * Create a instance.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param string $method
     *
     * @throws InvalidGatewayException
     * @throws \Exception
     *
     * @return GatewayApplicationInterface
     */
    protected function create($method): GatewayApplicationInterface
    {
        !$this->config->has('log.file') ?: $this->registerLog();

        $gateway = __NAMESPACE__.'\\Gateways\\'.Str::studly($method);

        if (class_exists($gateway)) {
            return self::make($gateway);
        }

        throw new InvalidGatewayException("Gateway [{$method}] Not Exists");
    }

    /**
     * Make a gateway.
     *
     * @author yansongda <me@yansonga.cn>
     *
     * @param string $gateway
     *
     * @throws InvalidGatewayException
     *
     * @return GatewayApplicationInterface
     */
    protected function make($gateway): GatewayApplicationInterface
    {
        $app = new $gateway($this->config);

        if ($app instanceof GatewayApplicationInterface) {
            return $app;
        }

        throw new InvalidGatewayException("Gateway [{$gateway}] Must Be An Instance Of GatewayApplicationInterface");
    }

    /**
     * Register log service.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @throws \Exception
     */
    protected function registerLog()
    {
        $logger = Log::createLogger(
            $this->config->get('log.file'),
            'yansongda.pay',
            $this->config->get('log.level', 'warning'),
            $this->config->get('log.type', 'daily'),
            $this->config->get('log.max_file', 30)
        );

        Log::setLogger($logger);
>>>>>>> dev
    }
}
