<?php
/**
 * User: loveyu
 * Date: 2015/1/4
 * Time: 22:40
 */

$ip = get_client_ip();
$islinux = !(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
define('IS_LINUX', $islinux);
$url = getCompleteURL();
$url_info = parse_url($url);
if(!isset($_SERVER['DOCUMENT_ROOT'])){
	if(isset($_SERVER['SCRIPT_FILENAME'])){
		$path = $_SERVER['SCRIPT_FILENAME'];
	} elseif(isset($_SERVER['PATH_TRANSLATED'])){
		$path = str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']);
	} else{
		exit_msg("服务器信息解析失败");
		$path = "";
	}
	$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr($path, 0, 0 - strlen($_SERVER['PHP_SELF'])));
}
$doc_root = str_replace('//', '/', str_replace(DIRECTORY_SEPARATOR, '/', $_SERVER["DOCUMENT_ROOT"]));
$fm_self = $doc_root . $_SERVER["PHP_SELF"];
$path_info = pathinfo($fm_self);
// 注册全局变量
$blockKeys = array(
	'_SERVER',
	'_SESSION',
	'_GET',
	'_POST',
	'_COOKIE',
	'charset',
	'ip',
	'islinux',
	'url',
	'url_info',
	'doc_root',
	'fm_self',
	'path_info'
);
foreach($_GET as $key => $val){
	if(array_search($key, $blockKeys) === false){
		$$key = $val;
	}
}
foreach($_POST as $key => $val){
	if(array_search($key, $blockKeys) === false){
		$$key = $val;
	}
}
foreach($_COOKIE as $key => $val){
	if(array_search($key, $blockKeys) === false){
		$$key = $val;
	}
}

//加载配置文件
$cfg = new config();
$cfg->load();
/**
 * 加载的配置变量
 * @var string $lang                语言选项
 * @var string $auth_pass           密码
 * @var string $quota_mb
 * @var string $upload_ext_filter   支持上传的文件后缀
 * @var string $download_ext_filter 支持的下载文件后缀
 * @var int    $error_reporting     错误等级
 * @var string $fm_root             文件根目录
 * @var int    $cookie_cache_time   COOKIE有效时间
 * @var string $version             程序版本
 * @var string $timezone            程序版本
 */
if(!isset($timezone) || empty($timezone)){
	$timezone = "PRC";
}
date_default_timezone_set($timezone);//时区设置
switch($error_reporting){
	case 0:
		error_reporting(0);
		@ini_set("display_errors", 0);
		@ini_set('display_errors', 'off');
		break;
	case 1:
		error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);
		@ini_set("display_errors", 1);
		@ini_set('display_errors', 'on');
		break;
	case 2:
		error_reporting(E_ALL | E_STRICT);
		@ini_set("display_errors", 1);
		@ini_set('display_errors', 'on');
		break;
}
if(!isset($current_dir)){
	$current_dir = $path_info["dirname"] . "/";
	if(!$islinux){
		$current_dir = ucfirst($current_dir);
	}
	//@chmod($current_dir,0755);
} else{
	$current_dir = format_path($current_dir);
}
// Auto Expand Local Path
if(!isset($expanded_dir_list)){
	$expanded_dir_list = "";
	$mat = explode("/", $path_info["dirname"]);
	for($x = 0; $x < count($mat); $x++){
		$expanded_dir_list .= ":" . $mat[$x];
	}
	setcookie("expanded_dir_list", $expanded_dir_list, 0, "/");
}
if(!isset($fm_current_root)){
	if(strlen($fm_root)){
		$fm_current_root = $fm_root;
	} else{
		if(!$islinux){
			$fm_current_root = ucfirst($path_info["dirname"] . "/");
		} else{
			$fm_current_root = $doc_root . "/";
		}
	}
	setcookie("fm_current_root", $fm_current_root, 0, "/");
} elseif(isset($set_fm_current_root)){
	if(!$islinux){
		$fm_current_root = ucfirst($set_fm_current_root);
	}
	setcookie("fm_current_root", $fm_current_root, 0, "/");
}
if(!isset($resolveIDs)){
	setcookie("resolveIDs", 0, time() + $cookie_cache_time, "/");
} elseif(isset($set_resolveIDs)){
	$resolveIDs = ($resolveIDs) ? 0 : 1;
	setcookie("resolveIDs", $resolveIDs, time() + $cookie_cache_time, "/");
}
if(isset($resolveIDs) && $resolveIDs){
	exec("cat /etc/passwd", $mat_passwd);
	exec("cat /etc/group", $mat_group);
}
$fm_color['Bg'] = "EEEEEE";
$fm_color['Text'] = "000000";
$fm_color['Link'] = "0A77F7";
$fm_color['Entry'] = "FFFFFF";
$fm_color['Over'] = "C0EBFD";
$fm_color['Mark'] = "A7D2E4";
foreach($fm_color as $tag => $color){
	$fm_color[$tag] = strtolower($color);
}

/**
 * 文件管理器操作
 * @var string $loggedon 通过COOKIE设置的密码文件
 * @var string $frame    当前指定的框架
 * @var string $action   当前激活的操作
 */
foreach(array(
	'loggedon',
	'frame',
	'action'
) as $_v){
	if(!isset($$_v)){
		$$_v = NULL;
	}
}
unset($_v);
if($loggedon == $auth_pass){
	switch($frame){
		case 1:
			break; // Empty Frame
		case 2:
			frame2();
			break;
		case 3:
			frame3();
			break;
		default:
			switch($action){
				case 1:
					logout();
					break;
				case 2:
					config_form();
					break;
				case 3:
					download();
					break;
				case 4:
					view();
					break;
				case 5:
					server_info();
					break;
				case 6:
					execute_cmd();
					break;
				case 7:
					edit_file_form();
					break;
				case 8:
					chmod_form();
					break;
				case 9:
					shell_form();
					break;
				case 10:
					upload_form();
					break;
				case 11:
					execute_file();
					break;
				default:
					frameset();
			}
	}
} else{
	if(isset($pass)){
		login();
	} else{
		login_form();
	}
}