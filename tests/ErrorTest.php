<?php
use qpf\error\Error;

include 'boot.php';

Error::register();

class ErrorTest
{
    public function base1()
    {
        // 触发一个异常错误
        throw new \Exception('test');
    }
    
    public function base2()
    {
        try {
            $a = $b;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    public function base3()
    {
        
        $arr = [];
        
        echo $arr;
    }
}

$test = new ErrorTest();
$test->base1();
