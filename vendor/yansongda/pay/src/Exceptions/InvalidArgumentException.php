<?php

namespace Yansongda\Pay\Exceptions;

<<<<<<< HEAD
<<<<<<< HEAD
class InvalidArgumentException extends \InvalidArgumentException
{
=======
=======
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
class InvalidArgumentException extends Exception
{
    /**
     * Bootstrap.
     *
     * @author yansongda <me@yansonga.cn>
     *
     * @param string       $message
     * @param array|string $raw
     * @param int|string   $code
     */
    public function __construct($message, $raw = [], $code = 3)
    {
        parent::__construct($message, $raw, $code);
    }
<<<<<<< HEAD
>>>>>>> dev
=======
>>>>>>> dc81d773ef8393de8716681e5c19d1579978ea74
}
