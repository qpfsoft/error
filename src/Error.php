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
 * 错误处理程序
 * 
 * 提示:
 * 错误处理程序不能捕捉`PHP语法错误`, 解决办法:
 * ```
 * try{}catch(Exception $e){}
 * ```
 */
class Error
{
    /**
     * 异常处理程序
     * @var Handle
     */
    protected static $handle;
    /**
     * 调试模式
     * @var int
     */
    protected static $debug = true;
    
    /**
     * 日志处理程序
     * @var object
     */
    protected static $log;
    
    /**
     * 应用程序异常
     * @var integer
     */
    const APP_EXCETPION = 0;
    /**
     * 应用程序错误
     * @var integer
     */
    const APP_ERROR = 1;
    /**
     * 应用程序最后错误
     * @var integer
     */
    const APP_ERROR_LAST = 2;

    /**
     * 是否启用调试模式
     * @return bool
     */
    public static function isDebug()
    {
        return self::$debug;
    }
    
    /**
     * 是否调试模式1
     * @return boolean
     */
    public static function isDebug1()
    {
        return self::$debug === true || self::$debug == 1;
    }
    
    /**
     * 是否调试模式2
     * @return boolean
     */
    public static function isDebug2()
    {
        return 2 === (int) self::$debug;
    }
    
    /**
     * 设置调试模式
     * @param mixed $debug
     * @return void
     */
    public static function setDebug($debug)
    {
        self::$debug = $debug;
    }
    
    /**
     * 设置日志处理程序
     * @param object $log
     */
    public function setLog($log)
    {
        self::$log = $log;
    }
    
    /**
     * 记录日志
     * @param mixed $message
     * @throws \Exception
     */
    public static function log(... $message)
    {
        if (self::$log === null) {
            return;
        }
        
        if (self::$log instanceof \Closure) {
            self::$log($message);
        } elseif (is_callable(self::$log)) {
            call_user_func_array(self::$log, $message);
        } elseif (is_object(self::$log) && method_exists(self::$log, 'log')) {
            call_user_func_array(self::$log, $message);
        }
        
        throw new \Exception('Missing log handler');
    }
    
    /**
     * 注册错误处理程序
     * @return void
     */
    public static function register()
    {
        // 设置PHP报告什么级别的错误
        error_reporting(E_ALL); // E_ALL^E_NOTICE , E_ALL | -1 , 0
        set_exception_handler([ __CLASS__, 'appException']);
        set_error_handler([__CLASS__, 'appError']);
        register_shutdown_function([__CLASS__, 'appFatalError']);
    }
    
    /**
     * 注销错误处理程序
     */
    public static function unregister()
    {
        restore_error_handler();
        restore_exception_handler();
    }
    
    /**
     * 异常处理程序
     * @param \Throwable $e
     * @return void
     */
    public static function appException(\Throwable $e)
    {
        // 获得异常处理程序
        $handle = self::getHandle();
        // 保存当前异常对象
        $handle->set($e);
        // 注销错误捕捉，以避免在处理异常的递归错误
        self::unregister();
        
        // 设置响应的 HTTP 状态码为 500 预防headers头部被发送
        if (PHP_SAPI !== 'cli') {
            http_response_code(500);
        }

        try{
            // 日志记录异常
            $handle->renderLog($e);
            // 呈现异常
            if(PHP_SAPI == 'cli') {
                $handle->renderCli($e);
            } else {
                $handle->renderException($e);
            }
            
        } catch (\Exception $exception) {
            $title = "App exception handle, Error!". PHP_EOL;
            $msg = '[After Exception]' . PHP_EOL;
            $msg .= (string) $exception;
            $msg .= PHP_EOL . '[Before Exception]:' . PHP_EOL;
            $msg .= (string) $e;
            if (self::isDebug()) {
                if(PHP_SAPI === 'cli') {
                    echo $title . $msg . PHP_EOL;
                } else {
                    echo '<h1 style="color:red;font-size: 2rem;">'.$title.'</h1>';
                    echo '<pre>' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</pre>';
                }
            } else {
                echo 'Server error.';
            }
            $msg .= "\n\$_SERVER = " . print_r($_SERVER, true);
            // TODO 记录到系统日志
            $file = __DIR__ . '/QPF-Error.log';
            if (!is_file($file)) {
                file_put_contents($file, $msg);
            } else {
                error_log($msg, 3, $file);
            }
            exit(1);
        }
        
        $handle->set(null);
    }
    
    /**
     * 处理错误程序
     * @param int $type 错误类型
     * @param string $message 错误描述
     * @param string $file 出错文件
     * @param int $line 出错行号
     * @return void
     */
    public static function appError($type, $message, $file, $line)
    {
        if (error_reporting() & $type) {
            throw new ErrorException($message, self::APP_ERROR, $type, $file, $line);
        }
    }
    
    /**
     * 捕捉最后错误处理程序
     */
    public static function appFatalError()
    {
        $error = error_get_last();
        if(ErrorException::isFatal($error['type'])) {
            $e = new ErrorException($error['message'], self::APP_ERROR_LAST,$error['type'], $error['file'], $error['line']);
            self::appException($e);
        }
    }
    
    /**
     * 设置异常处理程序
     * @param mixed $handle
     * @return void
     */
    public static function setHandle($handle)
    {
        self::$handle = $handle;
    }
    
    /**
     * 返回异常处理程序
     * @return \qpf\error\Handle
     */
    public static function getHandle()
    {
        if (self::$handle === null) {
            self::$handle = new Handle();
        } elseif (is_string(self::$handle)) {
            self::$handle = new self::$handle;
        }
        
        return self::$handle;
    }
}