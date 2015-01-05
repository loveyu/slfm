<?php
/**
 * User: loveyu
 * Date: 2015/1/4
 * Time: 22:39
 */
/**
 * 获取客户端的IP地址
 * @return string
 */
function get_client_ip(){
	$ipaddress = '';
	if(isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP']){
		$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	} else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']){
		$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else if(isset($_SERVER['HTTP_X_FORWARDED']) && $_SERVER['HTTP_X_FORWARDED']){
		$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	} else if(isset($_SERVER['HTTP_FORWARDED_FOR']) && $_SERVER['HTTP_FORWARDED_FOR']){
		$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	} else if(isset($_SERVER['HTTP_FORWARDED']) && $_SERVER['HTTP_FORWARDED']){
		$ipaddress = $_SERVER['HTTP_FORWARDED'];
	} else if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR']){
		$ipaddress = $_SERVER['REMOTE_ADDR'];
	}
	// proxy transparente não esconde o IP local, colocando ele após o IP da rede, separado por vírgula
	if(strpos($ipaddress, ',') !== false){
		$ips = explode(',', $ipaddress);
		$ipaddress = trim($ips[0]);
	}
	if($ipaddress == '::1'){
		$ipaddress = '';
	}

	return $ipaddress;
}

/**
 * 判断当前是否为HTTPS访问
 * @return bool
 */
function is_ssl(){
	if(isset($_SERVER['HTTPS'])){
		if('on' == strtolower($_SERVER['HTTPS'])){
			return true;
		}
		if('1' == $_SERVER['HTTPS']){
			return true;
		}
	} elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])){
		return true;
	}

	return false;
}

/**
 * 转换名称为系统文件的编码
 * @param string $name
 * @return string
 */
function nameToSys($name){
	if(IS_LINUX){
		if(CHARSET_LINUX != CHARSET_FILE){
			$name = mb_convert_encoding($name, CHARSET_LINUX, CHARSET_FILE);
		}
	} else{
		if(CHARSET_WIN != CHARSET_FILE){
			$name = mb_convert_encoding($name, CHARSET_WIN, CHARSET_FILE);
		}
	}
	return trim($name);
}

/**
 * 转换名称为网页的编码
 * @param string $name
 * @return string
 */
function nameToPage($name){
	if(IS_LINUX){
		if(CHARSET_LINUX != CHARSET_FILE){
			$name = mb_convert_encoding($name, CHARSET_FILE, CHARSET_LINUX);
		}
	} else{
		if(CHARSET_WIN != CHARSET_FILE){
			$name = mb_convert_encoding($name, CHARSET_FILE, CHARSET_WIN);
		}
	}
	return trim($name);
}

/**
 * 错误消息输出
 * @param              $msg
 * @param string       $color
 * @param array|string $tag
 * @return string
 */
function msg_out($msg, $color = '#000', $tag = array()){
	if(!is_array($tag)){
		if(is_string($tag)){
			$tag = array($tag);
		} else{
			$tag = array();
		}
	}
	$rt = "<p style='color: " . $color . ";'>";
	foreach($tag as $v){
		$rt .= "<$v>";
	}
	$rt .= $msg;
	foreach($tag as $v){
		$rt .= "</$v>";
	}
	$rt .= "</p>";
	return $rt;
}

/**
 * 获取服务器地址
 * @return string
 */
function getServerURL(){
	$url = is_ssl() ? "https://" : "http://";
	$url .= $_SERVER["SERVER_NAME"]; // $_SERVER["HTTP_HOST"] is equivalent
	if($_SERVER["SERVER_PORT"] != "80"){
		$url .= ":" . $_SERVER["SERVER_PORT"];
	}

	return $url;
}

/**
 * 获取当前URL地址
 * @return string
 */
function getCompleteURL(){
	return getServerURL() . $_SERVER["REQUEST_URI"];
}

/**
 * 输出结束消息
 * @param $msg
 */
function exit_msg($msg){
	header("Content-Type: text/html; charset=utf-8");
	die($msg);
}

/**
 * 获取数据编码
 * @param $string
 * @return string
 */
function get_string_encoding($string){
	//编码转换
	$charset = 'ASCII';
	if(!empty($string)){
		foreach(array(
			'UTF-8',
			'GBK',
			'GB2312',
			'ASCII',
			'UNICODE',
			'BIG5',
			'UCS-2',
			'UCS-2LE',
			'UCS-2BE'
		) as $v){
			if($string == mb_convert_encoding(mb_convert_encoding($string, 'UTF-8', $v), $v, 'UTF-8')){
				$charset = $v;
				break;
			}
		}
	}
	return $charset;
}

/**
 * 从任意编码转为页面编码
 * @param $string
 * @return string
 */
function convert_page_encode($string){
	if(empty($string)){
		return $string;
	}
	$charset = get_string_encoding($string);
	if($charset != CHARSET_FILE){
		$string = mb_convert_encoding($string, CHARSET_FILE, $charset);
	}
	return $string;
}