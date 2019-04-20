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
 * HTTP请求异常
 */
class HttpException extends \RuntimeException
{
    /**
     * HTTP状态代码，如403，404，500，等。
     * @var integer
     */
    public $statusCode;
    /**
     * 报头
     * @var array
     */
    public $headers;
    
    /**
     * 构造函数
     *
     * @param integer $status HTTP状态码
     * @param string $message 错误信息
     * @param \Exception $previous
     * @param array $headers
     * @param integer $code 错误码
     */
    public function __construct($status, $message = null, \Exception $previous = null, array $headers = [], $code = 0)
    {
        $this->statusCode = $status;
        $this->headers = $headers;
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * 返回状态码
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
    
    /**
     * 返回报头
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
    
    /**
     * 获取异常名称
     * @return string 返回字符串描述的该异常名称
     */
    public function getName()
    {
        if (isset(self::$httpStatus[$this->statusCode])) {
            return self::$httpStatus[$this->statusCode];
        } else {
            return 'Error';
        }
    }
    
    /**
     * HTTP状态码和状态文本
     * @var array
     */
    public static $httpStatus = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        118 => 'Connection timed out',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        210 => 'Content Different',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        310 => 'Too many Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested range unsatisfiable',
        417 => 'Expectation failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable entity',
        423 => 'Locked',
        424 => 'Method failure',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway or Proxy Error',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        507 => 'Insufficient storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];
}