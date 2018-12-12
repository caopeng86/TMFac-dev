<?php

namespace Yansongda\Pay\Exceptions;

class GatewayException extends Exception
{
    /**
<<<<<<< HEAD
     * error raw data.
     *
     * @var array
     */
    public $raw = [];

    /**
     * [__construct description].
     *
     * @author JasonYan <me@yansongda.cn>
     *
     * @param string     $message
     * @param string|int $code
     */
    public function __construct($message, $code, $raw = [])
    {
        parent::__construct($message, intval($code));

        $this->raw = $raw;
=======
     * Bootstrap.
     *
     * @author yansongda <me@yansonga.cn>
     *
     * @param string       $message
     * @param array|string $raw
     * @param int|string   $code
     */
    public function __construct($message, $raw = [], $code = 4)
    {
        parent::__construct($message, $raw, $code);
>>>>>>> dev
    }
}
