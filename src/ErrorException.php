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
 * PHP错误异常
 */
class ErrorException extends \Exception
{
    /**
     * 错误类型
     * @var int
     */
    protected $type;
    
    /**
     * 构造函数
     * @param int $type 错误类型
     * @param string $message 消息
     * @param string $file 出错文件
     * @param string $line 出错行号
     */
    public function __construct($type, $message, $file, $line)
    {
        $this->type = $type;
        $this->message = $message;
        $this->file = $file;
        $this->line = $line;
        $this->code = 0;
    }
    
    /**
     * 判断错误类型是否致命错误
     * @param int $type
     * @return bool
     */
    public static function isFatal($type)
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }
    
    /**
     * 返回错误类型
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * 获取异常名称
     * @return string
     */
    public function getName()
    {
        $en = [
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
        $zh = [
            E_COMPILE_ERROR => 'PHP编译错误',
            E_COMPILE_WARNING => 'PHP编译器警告',
            E_CORE_ERROR => 'PHP核心错误',
            E_CORE_WARNING => 'PHP核心警告',
            E_DEPRECATED => 'PHP弃用警告',
            E_ERROR => 'PHP致命错误',
            E_NOTICE => 'PHP注意',
            E_PARSE => 'PHP解析错误',
            E_RECOVERABLE_ERROR => 'PHP可恢复错误',
            E_STRICT => 'PHP严格模式警告',
            E_USER_DEPRECATED => 'PHP用户弃用的警告',
            E_USER_ERROR => 'PHP用户错误',
            E_USER_NOTICE => 'PHP用户注意事项',
            E_USER_WARNING => 'PHP用户警告',
            E_WARNING => 'PHP警告'
        ];
        
        if (Error::isDebug() == 2) {
            $names = &$zh;
        } else {
            $names = &$en;
        }
        
        return isset($names[$this->getType()]) ? $names[$this->getType()] : 'Error';
    }
}