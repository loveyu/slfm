<?php
/**
 * User: loveyu
 * Date: 2015/1/4
 * Time: 22:35
 */
/**
 * 定义文件编码，勿修改，否者导致文件不可用
 */
define('CHARSET_FILE', 'UTF-8');
//文件系统编码，如有误请手动更正
/**
 * Windows 文件系统编码
 */
define('CHARSET_WIN', 'GBK');
/**
 * Linux文件系统编码
 */
define('CHARSET_LINUX', 'UTF-8');

$charset = CHARSET_FILE;//文件编码方式，请勿改动
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=" . $charset);
if(@get_magic_quotes_gpc()){
	/**
	 * 转义字符函数
	 * @param $value
	 * @return array
	 */
	function stripslashes_deep($value){
		return is_array($value) ? array_map('stripslashes_deep', $value) : $value;
	}

	$_POST = array_map('stripslashes_deep', $_POST);
	$_GET = array_map('stripslashes_deep', $_GET);
	$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
}