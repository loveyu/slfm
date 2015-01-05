<?php
/**
 * HTML接口
 * User: loveyu
 * Date: 2015/1/4
 * Time: 23:40
 */
/**
 * @param string $header
 */
function html_header($header = ""){
	global $fm_color;
	echo "
	<!DOCTYPE HTML PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
	<html xmlns=\"http://www.w3.org/1999/xhtml\">
    <head>
    <meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />
	<title>...:::: " . et('FileMan') . "</title>
    <script language=\"Javascript\" type=\"text/javascript\">
        function Is(){
            this.appname = navigator.appName;
            this.appversion = navigator.appVersion;
            this.platform = navigator.platform;
            this.useragent = navigator.userAgent.toLowerCase();
            this.ie = ( this.appname == 'Microsoft Internet Explorer' );
            if (( this.useragent.indexOf( 'mac' ) != -1 ) || ( this.platform.indexOf( 'mac' ) != -1 )){
                this.sisop = 'mac';
            } else if (( this.useragent.indexOf( 'windows' ) != -1 ) || ( this.platform.indexOf( 'win32' ) != -1 )){
                this.sisop = 'windows';
            } else if (( this.useragent.indexOf( 'inux' ) != -1 ) || ( this.platform.indexOf( 'linux' ) != -1 )){
                this.sisop = 'linux';
            }
        }
        var is = new Is();
        function enterSubmit(keypressEvent,submitFunc){
            var kCode = (is.ie) ? keypressEvent.keyCode : keypressEvent.which;
            if( kCode == 13) eval(submitFunc);
        }
        function getCookieVal (offset) {
            var endstr = document.cookie.indexOf (';', offset);
            if (endstr == -1) endstr = document.cookie.length;
            return unescape(document.cookie.substring(offset, endstr));
        }
        function getCookie (name) {
            var arg = name + '=';
            var alen = arg.length;
            var clen = document.cookie.length;
            var i = 0;
            while (i < clen) {
                var j = i + alen;
                if (document.cookie.substring(i, j) == arg) return getCookieVal (j);
                i = document.cookie.indexOf(' ', i) + 1;
                if (i == 0) break;
            }
            return null;
        }
        function setCookie (name, value, expires) {
            var argv = setCookie.arguments;
            var argc = setCookie.arguments.length;
            var expires = (argc > 2) ? argv[2] : null;
            var path = (argc > 3) ? argv[3] : null;
            var domain = (argc > 4) ? argv[4] : null;
            var secure = (argc > 5) ? argv[5] : false;
            document.cookie = name + '=' + escape (value) +
            ((expires == null) ? '' : ('; expires=' + expires.toGMTString())) +
            ((path == null) ? '' : ('; path=' + path)) +
            ((domain == null) ? '' : ('; domain=' + domain)) +
            ((secure == true) ? '; secure' : '');
        }
        function delCookie (name) {
            var exp = new Date();
            exp.setTime (exp.getTime() - 1);
            var cval = getCookie (name);
            document.cookie = name + '=' + cval + '; expires=' + exp.toGMTString();
        }
        var frameWidth, frameHeight;
        function getFrameSize(){
            if (self.innerWidth){
                frameWidth = self.innerWidth;
                frameHeight = self.innerHeight;
            }else if (document.documentElement && document.documentElement.clientWidth){
                frameWidth = document.documentElement.clientWidth;
                frameHeight = document.documentElement.clientHeight;
            }else if (document.body){
                frameWidth = document.body.clientWidth;
                frameHeight = document.body.clientHeight;
            }else return false;
            return true;
        }
        getFrameSize();
    </script>
    $header
    </head>
    <script language=\"Javascript\" type=\"text/javascript\">
        var W = screen.width;
        var H = screen.height;
        var FONTSIZE = 0;
        switch (W){
            case 640:
                FONTSIZE = 8;
            break;
            case 800:
                FONTSIZE = 10;
            break;
            case 1024:
                FONTSIZE = 12;
            break;
            default:
                FONTSIZE = 14;
            break;
        }
    ";
	echo replace_double(" ", str_replace(chr(13), "", str_replace(chr(10), "", "
        document.writeln('
        <style type=\"text/css\">
        body {
            font-size: '+FONTSIZE+'px;
            font-weight : normal;
            color: #" . $fm_color['Text'] . ";
            background-color: #" . $fm_color['Bg'] . ";
        }
        table {
            font-size: '+FONTSIZE+'px;
            font-weight : normal;
            color: #" . $fm_color['Text'] . ";
            cursor: default;
        }
        input {
            font-size: '+FONTSIZE+'px;
            font-weight : normal;
            color: #" . $fm_color['Text'] . ";
        }
        textarea {
            font-size: 12px;
            font-weight : normal;
            color: #" . $fm_color['Text'] . ";
        }
        a {
            font-size : '+FONTSIZE+'px;
            font-weight : bold;
            text-decoration: none;
            color: #" . $fm_color['Text'] . ";
        }
        a:link {
            color: #" . $fm_color['Text'] . ";
        }
        a:visited {
            color: #" . $fm_color['Text'] . ";
        }
        a:hover {
            color: #" . $fm_color['Link'] . ";
        }
        a:active {
            color: #" . $fm_color['Text'] . ";
        }
        tr.entryUnselected {
            background-color: #" . $fm_color['Entry'] . ";
        }
        tr.entryUnselected:hover {
            background-color: #" . $fm_color['Over'] . ";
        }
        tr.entrySelected {
            background-color: #" . $fm_color['Mark'] . ";
        }
        </style>
        ');
    ")));
	echo "
    </script>
    ";
}

/**
 * @param        $ref
 * @param        $frame_number
 * @param string $Plus
 */
function reloadframe($ref, $frame_number, $Plus = ""){
	global $current_dir, $path_info;
	echo "
    <script language=\"Javascript\" type=\"text/javascript\">
        " . $ref . ".frame" . $frame_number . ".location.href='" . $path_info["basename"] . "?frame=" . $frame_number . "&current_dir=" . $current_dir . $Plus . "';
    </script>
    ";
}

/**
 * @param $arg
 */
function alert($arg){
	echo "
    <script language=\"Javascript\" type=\"text/javascript\">
        alert('$arg');
    </script>
    ";
}

/**
 * @param $dir_before
 * @param $dir_current
 * @param $indice
 */
function tree($dir_before, $dir_current, $indice){
	global $fm_current_root, $current_dir, $islinux;
	global $expanded_dir_list;
	$indice++;
	$num_dir = 0;
	$dir_name = str_replace($dir_before, "", $dir_current);
	$dir_before = str_replace("//", "/", $dir_before);
	$dir_current = str_replace("//", "/", $dir_current);
	$is_denied = false;
	if($islinux){
		$denied_list = "/proc#/dev";
		$mat = explode("#", $denied_list);
		foreach($mat as $key => $val){
			if($dir_current == $val){
				$is_denied = true;
				break;
			}
		}
		unset($mat);
	}
	$sys_dir_current = nameToSys($dir_current);
	if(!$is_denied){
		if($handle = @opendir($sys_dir_current)){
			// Permitido
			$mat_dir = array();
			while($file = readdir($handle)){
				if($file != "." && $file != ".." && is_dir(str_replace("//", "/", "$sys_dir_current/$file"))){
					$mat_dir[] = nameToPage($file);
				}
			}
			@closedir($handle);
			if(count($mat_dir)){
				sort($mat_dir, SORT_STRING);
				// with Sub-dir
				if($indice != 0){
					for($aux = 1; $aux < $indice; $aux++){
						echo "&nbsp;&nbsp;&nbsp;&nbsp;";
					}
				}
				if($dir_before != $dir_current){
					if(strstr($expanded_dir_list, ":$dir_current/$dir_name")){
						$op_str = "[-]";
					} else{
						$op_str = "[+]";
					}
					echo "<nobr><a href=\"JavaScript:go_dir('$dir_current/$dir_name')\">$op_str</a> <a href=\"JavaScript:go('$dir_current')\">$dir_name</a></nobr><br>\n";
				} else{
					echo "<nobr><a href=\"JavaScript:go('$dir_current')\">$fm_current_root</a></nobr><br>\n";
				}
				for($x = 0; $x < count($mat_dir); $x++){
					if(($dir_before == $dir_current) || (strstr($expanded_dir_list, ":$dir_current/$dir_name"))){
						tree($dir_current . "/", $dir_current . "/" . $mat_dir[$x], $indice);
					} else{
						flush();
					}
				}
			} else{
				// no Sub-dir
				if($dir_before != $dir_current){
					for($aux = 1; $aux < $indice; $aux++){
						echo "&nbsp;&nbsp;&nbsp;&nbsp;";
					}
					echo "<b>[&nbsp;]</b>";
					echo "<nobr><a href=\"JavaScript:go('$dir_current')\"> $dir_name</a></nobr><br>\n";
				} else{
					echo "<nobr><a href=\"JavaScript:go('$dir_current')\"> $fm_current_root</a></nobr><br>\n";
				}
			}
		} else{
			// denied
			if($dir_before != $dir_current){
				for($aux = 1; $aux < $indice; $aux++){
					echo "&nbsp;&nbsp;&nbsp;&nbsp;";
				}
				echo "<b>[&nbsp;]</b>";
				echo "<nobr><a href=\"JavaScript:go('$dir_current')\"><font color=red> $dir_name</font></a></nobr><br>\n";
			} else{
				echo "<nobr><a href=\"JavaScript:go('$dir_current')\"><font color=red> $fm_current_root</font></a></nobr><br>\n";
			}

		}
	} else{
		// denied
		if($dir_before != $dir_current){
			for($aux = 1; $aux < $indice; $aux++){
				echo "&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			echo "<b>[&nbsp;&nbsp;]</b>";
			echo "<nobr><a href=\"JavaScript:go('$dir_current')\"><font color=red> $dir_name</font></a></nobr><br>\n";
		} else{
			echo "<nobr><a href=\"JavaScript:go('$dir_current')\"><font color=red> $fm_current_root</font></a></nobr><br>\n";
		}
	}
}

/**
 * 显示树状目录
 */
function show_tree(){
	global $fm_current_root, $path_info, $setflag, $islinux, $cookie_cache_time, $current_dir;
	html_header("
    <script language=\"Javascript\" type=\"text/javascript\">
    <!--
        function saveFrameSize(){
            if (getFrameSize()){
                var exp = new Date();
                exp.setTime(exp.getTime()+$cookie_cache_time);
                setCookie('leftFrameWidth',frameWidth,exp);
            }
        }
        window.onresize = saveFrameSize;
    //-->
    </script>");
	echo "<body marginwidth=\"0\" marginheight=\"0\">\n";
	echo "
    <script language=\"Javascript\" type=\"text/javascript\">
    <!--
        // Disable text selection, binding the onmousedown, but not for some elements, it must work.
        function disableTextSelection(e){
			var type = String(e.target.type);
			return (type.indexOf('select') != -1 || type.indexOf('button') != -1 || type.indexOf('input') != -1 || type.indexOf('radio') != -1);
		}
        function enableTextSelection(){return true}
        if (is.ie) document.onselectstart=new Function('return false');
        else {
            document.body.onmousedown=disableTextSelection;
            document.body.onclick=enableTextSelection;
        }
        var flag = " . (($setflag) ? "true" : "false") . ";
        function set_flag(arg) {
            flag = arg;
        }
        function go_dir(arg) {
            var setflag;
            setflag = (flag)?1:0;
            document.location.href='" . addslashes($path_info["basename"]) . "?frame=2&setflag='+setflag+'&current_dir=" . addslashes($current_dir) . "&ec_dir='+arg;
        }
        function go(arg) {
            if (flag) {
                parent.frame3.set_dir_dest(arg+'/');
                flag = false;
            } else {
                parent.frame3.location.href='" . addslashes($path_info["basename"]) . "?frame=3&current_dir='+arg+'/';
            }
        }
        function set_fm_current_root(arg){
            document.location.href='" . addslashes($path_info["basename"]) . "?frame=2&set_fm_current_root='+escape(arg);
        }
        function atualizar(){
            document.location.href='" . addslashes($path_info["basename"]) . "?frame=2';
        }
    //-->
    </script>
    ";
	echo "<table width=\"100%\" height=\"100%\" border=0 cellspacing=0 cellpadding=5>\n";
	echo "<form><tr valign=top height=10><td>";
	if(!$islinux){
		echo "<select name=drive onchange=\"set_fm_current_root(this.value)\">";
		$aux = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		for($x = 0; $x < strlen($aux); $x++){
			if(!is_readable($aux[$x] . ":/")){
				continue;
			}
			if($handle = opendir($aux[$x] . ":/")){
				@closedir($handle);
				if(strstr(uppercase($fm_current_root), $aux[$x] . ":/")){
					$is_sel = "selected";
				} else{
					$is_sel = "";
				}
				echo "<option $is_sel value=\"" . $aux[$x] . ":/\">" . $aux[$x] . ":/";
			}
		}
		echo "</select> ";
	}
	echo "<input type=button value=" . et('Refresh') . " onclick=\"atualizar()\"></tr></form>";
	echo "<tr valign=top><td>";
	clearstatcache();
	tree($fm_current_root, $fm_current_root, -1, 0);
	echo "</td></tr>";
	echo "
        <form name=\"login_form\" action=\"" . $path_info["basename"] . "\" method=\"post\" target=\"_parent\">
        <input type=hidden name=action value=1>
        <tr>
        <td height=10 colspan=2><input type=submit value=\"" . et('Leave') . "\">
        </tr>
        </form>
    ";
	echo "</table>\n";
	echo "</body>\n</html>";
}

/**
 * 获取运行当前时间的微秒
 * @return float
 */
function getmicrotime(){
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

/**
 * 查看某一文件
 */
function view(){
	global $doc_root, $path_info, $url_info, $current_dir, $islinux, $filename, $passthru, $download_ext_filter;
	if(!isset($download_ext_filter)){
		$download_ext_filter = array();
	}
	if(intval($passthru)){
		$file = $current_dir . $filename;
		$sys_file = nameToSys($file);
		if(!is_file($sys_file)){
			exit_msg("只允许文件查看");
		}
		if(file_exists($sys_file)){
			$is_denied = false;
			foreach($download_ext_filter as $key => $ext){
				if(eregi($ext, $filename)){
					$is_denied = true;
					break;
				}
			}
			if(!$is_denied){
				if($fh = fopen($sys_file, "rb")){
					fclose($fh);
					$ext = pathinfo($file, PATHINFO_EXTENSION);
					$ctype = get_mime_type($ext);
					if($ctype != "application/octet-stream"){
						$ctype = "text/plain; charset=" . get_string_encoding(file_get_contents($sys_file));
					}
					header("Pragma: public");
					header("Expires: 0");
					header("Connection: close");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Cache-Control: public");
					header("Content-Description: File Transfer");
					header("Content-Type: " . $ctype);
					header("Content-Disposition: inline; filename=\"" . pathinfo($file, PATHINFO_BASENAME) . "\";");
					header("Content-Transfer-Encoding: binary");
					header("Content-Length: " . filesize($sys_file));
					@readfile($sys_file);
					exit();
				} else{
					alert(et('ReadDenied') . ": " . $file);
				}
			} else{
				alert(et('ReadDenied') . ": " . $file);
			}
		} else{
			alert(et('FileNotFound') . ": " . $file);
		}
		echo "
	    <script language=\"Javascript\" type=\"text/javascript\">
	    <!--
	        window.close();
	    //-->
	    </script>";
	} else{
		html_header();
		echo "<body marginwidth=\"0\" marginheight=\"0\">";
		$is_reachable_thru_webserver = (stristr($current_dir, $doc_root) !== false);
		if($is_reachable_thru_webserver){
			$url = $url_info["scheme"] . "://" . $url_info["host"];
			if(isset($url_info["port"]) && strlen($url_info["port"])){
				$url .= ":" . $url_info["port"];
			}
			// Malditas variaveis de sistema!! No windows doc_root é sempre em lowercase... cadê o str_ireplace() ??
			$url .= str_replace($doc_root, "", "/" . $current_dir) . $filename;
			$url = str_replace(":/", "://", preg_replace("/[\\/]{2,}/", "/", $url));
		} else{
			$url = addslashes($path_info["basename"]) . "?action=4&current_dir=" . addslashes($current_dir) . "&filename=" . addslashes($filename) . "&passthru=1";
		}
		echo "
	    <script language=\"Javascript\" type=\"text/javascript\">
	    <!--
        	window.moveTo((window.screen.width-800)/2,((window.screen.height-600)/2)-20);
	        document.location.href='$url';
	    //-->
	    </script>
    	</body>\n</html>";
	}
}


/**
 * 输出服务器信息
 */
function server_info(){
	if(!@phpinfo()){
		echo et('NoPhpinfo') . "...";
	}
	echo "<br><br>";
	$a = ini_get_all();
	$output = "<table border=1 cellspacing=0 cellpadding=4 align=center>";
	$output .= "<tr><th colspan=2>ini_get_all()</td></tr>";
	while(list($key, $value) = each($a)){
		list($k, $v) = each($a[$key]);
		$output .= "<tr><td align=right>$key</td><td>$v</td></tr>";
	}
	$output .= "</table>";
	echo $output;
	echo "<br><br>";
	$output = "<table border=1 cellspacing=0 cellpadding=4 align=center>";
	$output .= "<tr><th colspan=2>\$_SERVER</td></tr>";
	foreach($_SERVER as $k => $v){
		$output .= "<tr><td align=right>$k</td><td>$v</td></tr>";
	}
	$output .= "</table>";
	echo $output;
	echo "<br><br>";
	echo "<table border=1 cellspacing=0 cellpadding=4 align=center>";
	$safe_mode = trim(ini_get("safe_mode"));
	if((strlen($safe_mode) == 0) || ($safe_mode == 0)){
		$safe_mode = false;
	} else{
		$safe_mode = true;
	}
	$is_windows_server = (uppercase(substr(PHP_OS, 0, 3)) === 'WIN');
	echo "<tr><td colspan=2>" . php_uname();
	echo "<tr><td>safe_mode<td>" . ($safe_mode ? "on" : "off");
	if($is_windows_server){
		echo "<tr><td>sisop<td>Windows<br>";
	} else{
		echo "<tr><td>sisop<td>Linux<br>";
	}
	echo "</table><br><br><table border=1 cellspacing=0 cellpadding=4 align=center>";
	$display_errors = ini_get("display_errors");
	$ignore_user_abort = ignore_user_abort();
	$max_execution_time = ini_get("max_execution_time");
	$upload_max_filesize = ini_get("upload_max_filesize");
	$memory_limit = ini_get("memory_limit");
	$output_buffering = ini_get("output_buffering");
	$default_socket_timeout = ini_get("default_socket_timeout");
	$allow_url_fopen = ini_get("allow_url_fopen");
	$magic_quotes_gpc = ini_get("magic_quotes_gpc");
	ignore_user_abort(true);
	ini_set("display_errors", 0);
	ini_set("max_execution_time", 0);
	ini_set("upload_max_filesize", "10M");
	ini_set("memory_limit", "20M");
	ini_set("output_buffering", 0);
	ini_set("default_socket_timeout", 30);
	ini_set("allow_url_fopen", 1);
	ini_set("magic_quotes_gpc", 0);
	echo "<tr><td> <td>Get<td>Set<td>Get";
	echo "<tr><td>display_errors<td>$display_errors<td>0<td>" . ini_get("display_errors");
	echo "<tr><td>ignore_user_abort<td>" . ($ignore_user_abort ? "on" : "off") . "<td>on<td>" . (ignore_user_abort() ? "on" : "off");
	echo "<tr><td>max_execution_time<td>$max_execution_time<td>0<td>" . ini_get("max_execution_time");
	echo "<tr><td>upload_max_filesize<td>$upload_max_filesize<td>10M<td>" . ini_get("upload_max_filesize");
	echo "<tr><td>memory_limit<td>$memory_limit<td>20M<td>" . ini_get("memory_limit");
	echo "<tr><td>output_buffering<td>$output_buffering<td>0<td>" . ini_get("output_buffering");
	echo "<tr><td>default_socket_timeout<td>$default_socket_timeout<td>30<td>" . ini_get("default_socket_timeout");
	echo "<tr><td>allow_url_fopen<td>$allow_url_fopen<td>1<td>" . ini_get("allow_url_fopen");
	echo "<tr><td>magic_quotes_gpc<td>$magic_quotes_gpc<td>0<td>" . ini_get("magic_quotes_gpc");
	echo "</table><br><br>";
	echo "
    <script language=\"Javascript\" type=\"text/javascript\">
    <!--
        window.moveTo((window.screen.width-800)/2,((window.screen.height-600)/2)-20);
        window.focus();
    //-->
    </script>";
	echo "</body>\n</html>";
}

function copyright_info(){
	global $version;
	return "<p style='margin: 0;padding: 0;'>原程序为<a href='http://phpfm.sourceforge.net/' target='_blank'>PHPFM(0.9.8)</a>,进行了部分错误和逻辑修改,转为<a href='http://www.loveyu.net/slfm' target='_blank'>SLFM({$version})</a>,由<a href='http://www.loveyu.org' target='_blank'>Loveyu</a>修改.</p>";
}