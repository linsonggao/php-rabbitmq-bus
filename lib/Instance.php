<?php

namespace app\lib;

use app\lib\MessageBus\base\AbstractWork;

trait Instance
{
    private static $instance;
    private static $subInstance;
    private static $abstractQuote = [AbstractWork::class];
    public static function instance(...$arguments)
    {
        $classReflection = new \ReflectionClass(__CLASS__);
        if ($classReflection->isAbstract()) {
            if($arguments && is_array($arguments) && class_exists($arguments[0]) && $subClass = $arguments[0] ) {
                $reflection = new \ReflectionClass($subClass);
                $instanceTrue = false;
                foreach (self::$abstractQuote as $abstractClass) {
                    $instanceTrue |= $reflection->isSubclassOf($abstractClass);
                }
                if($instanceTrue) {
                    if (!isset(self::$subInstance[$subClass])) {
                        self::$subInstance[$subClass] = new $subClass;
                    }
                    return self::$subInstance[$subClass];
                }
            }
        } else {
            if (!isset(self::$instance[__CLASS__])) {
                self::$instance[__CLASS__] = new self(...$arguments);
            }
            return self::$instance[__CLASS__];
        }
    }
}