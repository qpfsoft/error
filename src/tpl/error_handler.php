<?php
use qpf\error\Error;

if(!function_exists('parse_tarce')) {
    /**
     * 解析tarce[]中一条数据为字符串
     *
     * - 拼接出类操作的方法和方法参数.
     * @param array $tarce
     */
    function parse_tarce($tarce)
    {
        $str = '';
        if (isset($tarce['class'])) {
            $str .= $tarce['class'] . '()';
        }
        if (isset($tarce['type'])) {
            $str .= $tarce['type'];
        }
        if (!empty($tarce['function'])) {
            $str .= $tarce['function'] . '(';
            
            if (! empty($tarce['args'])) {
                $str .= parse_tarce_args($tarce['args']) . ')';
            } else {
                $str .= ')';
            }
        }
        
        return $str;
    }
}
if(!function_exists('parse_tarce_args')) {
    /**
     * 解析tarce['args']数据为字符串
     *
     * @param array $args
     */
    function parse_tarce_args($args)
    {
        $str = '';
        foreach ($args as $val) {
            if (is_object($args)) {
                $str .= '对象';
            } elseif (is_string($val) && ! empty($val)) {
                $str .= "'$val' ";
            }
        }
        return $str;
    }
}
if(!function_exists('parse_tarce_line')) {
    /**
     * 解析tarce['line']
     *
     * @param integer $line
     */
    function parse_tarce_line($line)
    {
        return (empty($line) ? '' : $line);
    }
}

if(!function_exists('parse_file')) {
    /**
     * 缩短路径长度
     * @param string $file 完整路径
     * @param int $type 文件路径转换类型，默认值`0`,
     * - 1 : 去除根路径的信息
     * - 0 : 只保留文件名
     * @return string
     */
    function parse_file($file, $type = 0)
    {
        if (empty($file)) return '';
        
        // 统一目录分隔符为`/`;
        if (strpos($file, '\\') !== false) {
            $file = str_replace('\\', '/', $file);
        }
        
        // 去除根路径
        if ($type) {
            $file = str_replace(rtrim(dirname($_SERVER['DOCUMENT_ROOT']), '/'), '@root', $file);
            // 获得文件名
        } else {
            $file = './'. basename($file);
        }
        
        return $file;
    }
}
if (! function_exists('html_encode')) {
    
    /**
     * 特殊字符转换为HTML实体, 例如 & > &amp;
     * - 安全提示: 若是用户提供的内容, 应该考虑html实体编码, 来防止XSS攻击.
     * @param string $string
     * @param bool $doubleEncode 是否重复转换HTML实体, 默认`true`
     * @return string
     */
    function html_encode($string, $doubleEncode = true)
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }
}

if(!function_exists('highlight_row')) {
    /**
     * 高亮解析一行PHP代码
     * @param string $text
     * @return mixed
     */
    function highlight_row($code)
    {
        // 转义\t 即4个空格
        $escape_tabs = function ($str) {
            return str_replace('    ', '&nbsp;&nbsp;&nbsp;&nbsp;', $str);
        };
        
        // 匹配多行注解行
        if (strpos(trim($code, ' '), '*')  !== false) {
            return '<span style="color: #3F55BF; ">' . $escape_tabs($code) . '</span>';
        }
        
        // 完整匹配 - 单行注解
        if (strpos(trim($code, ' '), '//') === 0) {
            return '<span style="color: #3F55BF; ">' . $escape_tabs($code) . '</span>';
        }
        
        // 匹配单双引号值
        $code = preg_replace(
            '/["|\'](.*?)["|\']/U',
            '"<span style="color: #0000C0">$1</span>"', $code
            );
        
        // 匹配单行注解
        $code = preg_replace(
            '/(\/\/)(.+)\s/',
            '<span style="color: #557F5F; "> $0 </span>',
            $code
            );
        
        // 特殊关键字 - 红色
        $code = preg_replace(
            '/\b(print|echo|new|function|null|static|self|true|false|if|else|throw|return)\b/',
            '<span style="color: #7F0055;font-weight: 900">$1</span>', $code
            );
        // 特殊关键字 - 绿色
        $code = preg_replace(
            '/\b(public)\b/',
            '<span style="color: #22804C;font-weight: 900">$1</span>', $code
            );
        // 将四个连续空格转换为4个转义符
        return $escape_tabs($code);
    }
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="renderer" content="webkit|ie-comp|ie-stand">
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="robots" content="noindex,nofollow" />
		<title>
		<?php
		  $isDebug = Error::isDebug();
		  $pageTitle = ['Not found', 'System error', '系统发生错误'];
		  echo $pageTitle[$isDebug];
		?>
		</title>
		<link href="/favicon.ico" type="image/x-icon" rel="icon">
		<link href="/favicon.ico" type="image/x-icon" rel="shortcut icon">
		<!-- /static/qpf-ui/qpf-ui.css -->
		<link rel="stylesheet" href="//qpf-ui.com/api.php">
	</head>
<style>
code {
	padding: 0;
    border-radius: 0;
    background-color: transparent;
    color: #c7254e;
    white-space: nowrap;
    font-size: 100%;
}
table td {
    padding: 0 6px;
    vertical-align: top;
    word-break: break-all;
}
.phpCode {
	font-size: 14px;
	line-height: 1.2;
	background-color: #F9F9F9; /* 源码背景色 */
	border: 1px solid #C1C1C1;
	position: relative;
	margin-top: 10px;
	word-wrap: break-word;
	word-break: break-all;
	height: 300px;
	overflow-y: scroll;
	opacity: 1;
}
.phpCode .code-column{
	background-color: #B9C9D1;
	width: 43px;
	position: absolute; 
	height: 100%; 
}

.phpCode .line {
	position: relative;
	color: #00112F;
	font-family: Consolas;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	margin-left: 43px;
	padding: 0;
	border-bottom: 1px dashed #B9C9D1;
}

.phpCode .line .line-code {
	position: absolute;
	top: 0;
	bottom: 0;
	display: inline-block;
	width: 43px;
	margin-left: -43px;
	padding: 0px;
	color: #676767;
	background-color: #E6E6E6;/* 行号背景色 */
	text-align: center;
	vertical-align: middle;
}

.phpCode .line,
.phpCode .line .line-code{
	font-size: 1.4rem;
	height: 1.8rem;
	line-height: 1.8rem;
}

.line.line-error {
	background-color: #FFA5A5;
}

.line.line-error .line-code {
	color: #FD0004;
	font-size: 14px;
	font-weight: bold;
}

.plate {
	border: 1px solid #C1C1C1;
	font-size: 1.4rem;
}
.title {
	font-size: 2rem;
	font-weight: 400;
	padding: 5px 20px;
	border-bottom: 1px solid #DDDDDD;
}
.plate ol {
	padding: 15px 15px;
}
.plate ol li {
	padding-bottom: 5px;
}
</style>
<body>
	<div class="g-box-full">
		<div>
    		<?php  echo isset($echo) ? $echo : ''; ?>
    	</div>
		<!-- [异常名称 |error](#错误代码号) -->
		<h1 class="h7 mt-3">
			<?php echo isset($name) ? html_encode($name) : ':-('; ?>
		</h1>
		<!-- 错误消息 -->
		<h2 class="txt-code txt-error h4 txt-br">
			<?php echo isset($message) ? html_encode($message): 'Internal Server Error'; ?>
		</h2>
		<!-- 处理过的文件路径. 和错误行 -->
		<h3 class="h5">
			<?php if(isset($file)) { ?>
			File : (<span class="txt-me txt-br"> <?php echo parse_file($file, Error::isDebug()); ?> </span> ) 
			- Line (<span class="txt-warning"> <?php echo $line; ?> </span> )
			<?php } ?>
		</h3>
		<div class="">
		<?php 
		// 循环生成发送错误位置的代码预览
		if (isset($source) && isset($source['code'])) {
		    $html = '<div class="phpCode txt-code mb-3">
				            <div class="code-column"></div>
				            <div style="opacity: 1; position: relative;">';
		    // 遍历源代码文件数组, 显示指定范围行, 会正确的选择报错行(上移一行). 而不是在错误行的下一行.
		    foreach ($source['code'] as $key => $val) {
		        $html .= '<p onselectstart="return false;" ';
		        $html .= 'date-line="' . $key . '" ';
		        $html .= 'class="line txt-br-none';
		        $html .=  ($key == ($line - 1 > 0 ? $line - 1 : $line) ? ' line-error' : '');
		        $html .= '"';
		        $html .= '>';
		        $html .= '<span class="line-code">' . ++$key . '</span>' . highlight_row($val) . '</p>';
		    }
		    $html .= '</div>';
		    
		    echo $html;
		}
		?>
		</div>
		<!-- PHP代码执行跟踪打印  -->
		<?php
		     if (!empty($trace)) {
		         $html = '<div class="plate mb-3">';
		         $html .= '<div class="title txt-info mb-2">Call Stack</div>';
		         // 循环生成回溯
		         $html .= '<ol>';
		         //$trace = array_reverse($trace); // 执行顺序
		         foreach ($trace as $tarce_line) {
		             $html .= '<li class="txt-br"><span class="txt-me">' . parse_tarce($tarce_line) . '</span> <span class="txt-hei">in</span> <span class="txt-hui">' . parse_file(isset($tarce_line['file']) ? $tarce_line['file'] : '', Error::isDebug()) . ':' . parse_tarce_line(isset($tarce_line['line']) ? $tarce_line['line'] : '') . '</span>';
		         }
		         $html .= '</ol>';
		         $html .= '</div>';
		         echo $html;
		     }
		?>

		<?php 
		  if(Error::isDebug() == 2) { 
		 ?>
		<p class="txt-zh" style="letter-spacing: .1rem">
			处理您的请求发生上述Web服务器错误. <br>
			对此造成的不便，我们表示歉意！ 请联系我们. 谢谢.<br>
		</p>
		<?php } else { ?>
		<p class="txt-code">
		    Web server error occurred  above while processing your request.<br>
		    We are sorry for the inconvenience. Please contact us. Thank you.<br>
		</p>
		<?php } ?>
		

		<div class="txt-hui txt-code">
	        Date : <?php echo date("Y-m-d H:i:s"); ?>  |  Powered by QPF
	    </div>
	
	<!--/g-box-->
	</div>
	<script type="text/javascript" src="//qpf-ui.com/lib/jquery/jquery-1.11.3.js"></script>
	<script type="text/javascript" src="//qpf-ui.com/js/qpf.js"></script>
</body>
</html>