<?php
/**
 * 数据转换函数
 * User: loveyu
 * Date: 2015/1/4
 * Time: 23:33
 */

/**
 * HTML编码
 * @param $str
 * @return mixed|string
 */
function html_encode($str){
	$str = preg_replace(array(
		'/&/',
		'/</',
		'/>/',
		'/"/'
	), array(
		'&amp;',
		'&lt;',
		'&gt;',
		'&quot;'
	), $str);  // Bypass PHP to allow any charset!!
	$charset = get_string_encoding($str);
	if($charset != CHARSET_FILE){
		$str = mb_convert_encoding($str, CHARSET_FILE, $charset);
	}
	$str = htmlentities($str, ENT_QUOTES, CHARSET_FILE, false);
	return $str;
}

/**
 * 重复函数
 * @param $x
 * @param $y
 * @return string
 */
function rep($x, $y){
	if($x){
		$aux = "";
		for($a = 1; $a <= $x; $a++){
			$aux .= $y;
		}
		return $aux;
	} else{
		return "";
	}
}

/**
 * 零的输出
 * @param $arg1
 * @param $arg2
 * @return string
 */
function str_zero($arg1, $arg2){
	if(strstr($arg1, "-") == false){
		$aux = intval($arg2) - strlen($arg1);
		if($aux){
			return rep($aux, "0") . $arg1;
		} else{
			return $arg1;
		}
	} else{
		return "[$arg1]";
	}
}

/**
 * 双重替换
 * @param $sub
 * @param $str
 * @return mixed
 */
function replace_double($sub, $str){
	$out = str_replace($sub . $sub, $sub, $str);
	while(strlen($out) != strlen($str)){
		$str = $out;
		$out = str_replace($sub . $sub, $sub, $str);
	}
	return $out;
}

/**
 * 移除特殊字符
 * @param $str
 * @return mixed|string
 */
function remove_special_chars($str){
	$str = trim($str);
	$str = strtr($str, "¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ!@#%&*()[]{}+=?", "YuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy_______________");
	$str = str_replace("..", "", str_replace("/", "", str_replace("\\", "", str_replace("\$", "", $str))));
	return $str;
}

/**
 * 格式化路径
 * @param $str
 * @return mixed|string
 */
function format_path($str){
	global $islinux;
	$str = trim($str);
	$str = str_replace("..", "", str_replace("\\", "/", $str));
	$done = false;
	while(!$done){
		$str2 = str_replace("//", "/", $str);
		if(strlen($str) == strlen($str2)){
			$done = true;
		} else{
			$str = $str2;
		}
	}
	$tam = strlen($str);
	if($tam){
		$last_char = $tam - 1;
		if($str[$last_char] != "/"){
			$str .= "/";
		}
		if(!$islinux){
			$str = ucfirst($str);
		}
	}
	return $str;
}

/**
 * 数组排序
 * @return mixed
 */
function array_csort(){
	$args = func_get_args();
	$marray = array_shift($args);
	$msortline = "return(array_multisort(";
	$i = 0;
	foreach($args as $arg){
		$i++;
		if(is_string($arg)){
			foreach($marray as $row){
				$sortarr[$i][] = $row[$arg];
			}
		} else{
			$sortarr[$i] = $arg;
		}
		$msortline .= "\$sortarr[" . $i . "],";
	}
	$msortline .= "\$marray));";
	eval($msortline);
	return $marray;
}

/**
 * 权限检查
 * @param $P
 * @return string
 */
function show_perms($P){
	$sP = "<b>";
	if($P & 0x1000){
		$sP .= 'p';
	}            // FIFO pipe
	elseif($P & 0x2000){
		$sP .= 'c';
	}        // Character special
	elseif($P & 0x4000){
		$sP .= 'd';
	}        // Directory
	elseif($P & 0x6000){
		$sP .= 'b';
	}        // Block special
	elseif($P & 0x8000){
		$sP .= '&minus;';
	}  // Regular
	elseif($P & 0xA000){
		$sP .= 'l';
	}        // Symbolic Link
	elseif($P & 0xC000){
		$sP .= 's';
	}        // Socket
	else{
		$sP .= 'u';
	}                       // UNKNOWN
	$sP .= "</b>";
	// owner - group - others
	$sP .= (($P & 0x0100) ? 'r' : '&minus;') . (($P & 0x0080) ? 'w' : '&minus;') . (($P & 0x0040) ? (($P & 0x0800) ? 's' : 'x') : (($P & 0x0800) ? 'S' : '&minus;'));
	$sP .= (($P & 0x0020) ? 'r' : '&minus;') . (($P & 0x0010) ? 'w' : '&minus;') . (($P & 0x0008) ? (($P & 0x0400) ? 's' : 'x') : (($P & 0x0400) ? 'S' : '&minus;'));
	$sP .= (($P & 0x0004) ? 'r' : '&minus;') . (($P & 0x0002) ? 'w' : '&minus;') . (($P & 0x0001) ? (($P & 0x0200) ? 't' : 'x') : (($P & 0x0200) ? 'T' : '&minus;'));
	return $sP;
}

/**
 * 格式化文件大小
 * @param $arg
 * @return string
 */
function format_size($arg){
	if($arg > 0){
		$j = 0;
		$ext = array(
			" bytes",
			" KB",
			" MB",
			" GB",
			" TB"
		);
		while($arg >= pow(1024, $j)){
			++$j;
		}
		return round($arg / pow(1024, $j - 1) * 100) / 100 . $ext[$j - 1];
	} else{
		return "0 bytes";
	}
}

/**
 * 获取大小
 * @param $file
 * @return string
 */
function get_size($file){
	return format_size(filesize($file));
}

/**
 * 检查文件限制
 * @param int $new_filesize
 * @return bool
 */
function check_limit($new_filesize = 0){
	global $fm_current_root;
	global $quota_mb;
	if($quota_mb){
		$total = total_size($fm_current_root);
		if(floor(($total + $new_filesize) / (1024 * 1024)) > $quota_mb){
			return true;
		}
	}
	return false;
}

/**
 * 获取当前用户
 * @param $arg
 * @return mixed
 */
function get_user($arg){
	global $mat_passwd;
	$aux = "x:" . trim($arg) . ":";
	for($x = 0; $x < count($mat_passwd); $x++){
		if(strstr($mat_passwd[$x], $aux)){
			$mat = explode(":", $mat_passwd[$x]);
			return $mat[0];
		}
	}
	return $arg;
}

/**
 * 获取用户组
 * @param $arg
 * @return mixed
 */
function get_group($arg){
	global $mat_group;
	$aux = "x:" . trim($arg) . ":";
	for($x = 0; $x < count($mat_group); $x++){
		if(strstr($mat_group[$x], $aux)){
			$mat = explode(":", $mat_group[$x]);
			return $mat[0];
		}
	}
	return $arg;
}

/**
 * 转为大写
 * @param $str
 * @return string
 */
function uppercase($str){
	global $charset;
	return mb_strtoupper($str, $charset);
}

/**
 * 转为小写
 * @param $str
 * @return string
 */
function lowercase($str){
	global $charset;
	return mb_strtolower($str, $charset);
}

/**
 * 获取MIME类型
 * @param string $ext
 * @return string
 */
function get_mime_type($ext = ''){
	$mimes = array(
		'hqx' => 'application/mac-binhex40',
		'cpt' => 'application/mac-compactpro',
		'doc' => 'application/msword',
		'bin' => 'application/macbinary',
		'dms' => 'application/octet-stream',
		'lha' => 'application/octet-stream',
		'lzh' => 'application/octet-stream',
		'exe' => 'application/octet-stream',
		'class' => 'application/octet-stream',
		'psd' => 'application/octet-stream',
		'so' => 'application/octet-stream',
		'sea' => 'application/octet-stream',
		'dll' => 'application/octet-stream',
		'oda' => 'application/oda',
		'pdf' => 'application/pdf',
		'ai' => 'application/postscript',
		'eps' => 'application/postscript',
		'ps' => 'application/postscript',
		'smi' => 'application/smil',
		'smil' => 'application/smil',
		'mif' => 'application/vnd.mif',
		'xls' => 'application/vnd.ms-excel',
		'ppt' => 'application/vnd.ms-powerpoint',
		'pptx' => 'application/vnd.ms-powerpoint',
		'wbxml' => 'application/vnd.wap.wbxml',
		'wmlc' => 'application/vnd.wap.wmlc',
		'dcr' => 'application/x-director',
		'dir' => 'application/x-director',
		'dxr' => 'application/x-director',
		'dvi' => 'application/x-dvi',
		'gtar' => 'application/x-gtar',
		'php' => 'application/x-httpd-php',
		'php4' => 'application/x-httpd-php',
		'php3' => 'application/x-httpd-php',
		'phtml' => 'application/x-httpd-php',
		'phps' => 'application/x-httpd-php-source',
		'js' => 'application/x-javascript',
		'swf' => 'application/x-shockwave-flash',
		'sit' => 'application/x-stuffit',
		'tar' => 'application/x-tar',
		'tgz' => 'application/x-tar',
		'xhtml' => 'application/xhtml+xml',
		'xht' => 'application/xhtml+xml',
		'zip' => 'application/zip',
		'mid' => 'audio/midi',
		'midi' => 'audio/midi',
		'mpga' => 'audio/mpeg',
		'mp2' => 'audio/mpeg',
		'mp3' => 'audio/mpeg',
		'aif' => 'audio/x-aiff',
		'aiff' => 'audio/x-aiff',
		'aifc' => 'audio/x-aiff',
		'ram' => 'audio/x-pn-realaudio',
		'rm' => 'audio/x-pn-realaudio',
		'rpm' => 'audio/x-pn-realaudio-plugin',
		'ra' => 'audio/x-realaudio',
		'rv' => 'video/vnd.rn-realvideo',
		'wav' => 'audio/x-wav',
		'bmp' => 'image/bmp',
		'gif' => 'image/gif',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'jpe' => 'image/jpeg',
		'png' => 'image/png',
		'tiff' => 'image/tiff',
		'tif' => 'image/tiff',
		'css' => 'text/css',
		'html' => 'text/html',
		'htm' => 'text/html',
		'shtml' => 'text/html',
		'txt' => 'text/plain',
		'text' => 'text/plain',
		'log' => 'text/plain',
		'rtx' => 'text/richtext',
		'rtf' => 'text/rtf',
		'xml' => 'text/xml',
		'xsl' => 'text/xml',
		'mpeg' => 'video/mpeg',
		'mpg' => 'video/mpeg',
		'mpe' => 'video/mpeg',
		'qt' => 'video/quicktime',
		'mov' => 'video/quicktime',
		'avi' => 'video/x-msvideo',
		'movie' => 'video/x-sgi-movie',
		'docx' => 'application/msword',
		'word' => 'application/msword',
		'xl' => 'application/excel',
		'xlsx' => 'application/excel',
		'eml' => 'message/rfc822'
	);
	return (!isset($mimes[lowercase($ext)])) ? 'application/octet-stream' : $mimes[lowercase($ext)];
}