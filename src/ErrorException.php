<?php
// ╭───────────────────────────────────────────────────────────┐
// │ QPF Framework [Key Studio]
// │-----------------------------------------------------------│
// │ Copyright (c) 2016-2019 quiun.com All rights reserved.
// │-----------------------------------------------------------│
// │ Author: qiun <qiun@163.com>
// ╰───────────────────────────────────────────────────────────┘
namespace qpf\error;

/**
 * 错误异常
 * 
 * 该异常会被异常处理程序作为PHP错误信息对象
 */
class ErrorException extends \ErrorException
{
    
    /**
     * 构造函数
     * @param string $message 异常消息
     * @param int $code 错误码, 自定义
     * @param int $severity PHP异常级别
     * @param string $file 出错文件
     * @param string $line 出错行号
     * @param \Exception $previous 前一个异常
     */
    public function __construct($message = '', $code = 0, $severity = E_ERROR, $file = __FILE__, $line = __LINE__, \Exception $previous = null)
    {
        parent::__construct($message, $code, $severity, $file, $line, $previous);
    }
    
    /**
     * 判断错误类型是否致命错误
     * @param int $type 错位类型, error_get_last()['type']
     * @return bool
     */
    public static function isFatal($type)
    {
        return in_array($type, [E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING]);
    }

    /**
     * 获取异常名称
     * @return string
     */
    public function getName()
    {
        static $names = [
            E_COMPILE_ERROR => 'PHP Compile Error',
            E_COMPILE_WARNING => 'PHP Compile Warning',
            E_CORE_ERROR => 'PHP Core Error',
            E_CORE_WARNING => 'PHP Core Warning',
            E_DEPRECATED => 'PHP Deprecated Warning',
            E_ERROR => 'PHP Fatal Error',
            E_NOTICE => 'PHP Notice',
            E_PARSE => 'PHP Parse Error',
            E_RECOVERABLE_ERROR => 'PHP Recoverable Error',
            E_STRICT => 'PHP Strict Warning',
            E_USER_DEPRECATED => 'PHP User Deprecated Warning',
            E_USER_ERROR => 'PHP User Error',
            E_USER_NOTICE => 'PHP User Notice',
            E_USER_WARNING => 'PHP User Warning',
            E_WARNING => 'PHP Warning'
        ];

        return isset($names[$this->getSeverity()]) ? $names[$this->getSeverity()] : 'Error';
    }
}