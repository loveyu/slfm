<?php
/**
 * 文件相关函数
 * User: loveyu
 * Date: 2015/1/4
 * Time: 23:22
 */


/**
 * 统计文件夹或文件的大小
 * @param $arg
 * @return int
 */
function total_size($arg){
	$total = 0;
	if(file_exists($arg)){
		if(is_dir($arg)){
			$handle = opendir($arg);
			while($aux = readdir($handle)){
				if($aux != "." && $aux != ".."){
					$total += total_size($arg . "/" . $aux);
				}
			}
			@closedir($handle);
		} else{
			$total = filesize($arg);
		}
	}
	return $total;
}

/**
 * 递归删除文件或文件夹
 * @param $arg
 */
function total_delete($arg){
	if(file_exists($arg)){
		@chmod($arg, 0755);
		if(is_dir($arg)){
			$handle = opendir($arg);
			while($aux = readdir($handle)){
				if($aux != "." && $aux != ".."){
					total_delete($arg . "/" . $aux);
				}
			}
			@closedir($handle);
			rmdir($arg);
		} else{
			unlink($arg);
		}
	}
}

/**
 * 递归复制文件或文件夹
 * @param $orig
 * @param $dest
 * @return bool
 */
function total_copy($orig, $dest){
	$ok = true;
	if(file_exists($orig)){
		if(is_dir($orig)){
			mkdir($dest, 0755);
			$handle = opendir($orig);
			while(($aux = readdir($handle)) && ($ok)){
				if($aux != "." && $aux != ".."){
					$ok = total_copy($orig . "/" . $aux, $dest . "/" . $aux);
				}
			}
			@closedir($handle);
		} else{
			$ok = copy($orig, $dest);
		}
	}
	return $ok;
}

/**
 * 文件的移动或重命名
 * @param $orig
 * @param $dest
 * @return bool
 */
function total_move($orig, $dest){
	// Just why doesn't it has a MOVE alias?!
	return rename($orig, $dest);
}

/**
 * 文件的下载
 */
function download(){
	global $download_ext_filter, $current_dir, $filename;
	if(!isset($download_ext_filter) || !is_array($download_ext_filter)){
		$download_ext_filter = array();
	}
	$file = $current_dir . $filename;
	$sys_file = nameToSys($file);
	if(file_exists($sys_file)){
		$is_denied = false;
		foreach($download_ext_filter as $key => $ext){
			if(eregi($ext, $filename)){
				$is_denied = true;
				break;
			}
		}
		if(!$is_denied){
			$size = filesize($sys_file);
			header("Content-Type: application/force-download;");
			header("Content-Length: $size");
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " . $size);
			flush();
			if($_SERVER['REQUEST_METHOD'] != "HEAD"){
				$fp = fopen($sys_file, "r");
				if($fp){
					while(!feof($fp)){
						echo fread($fp, 65536);
						flush();
					}
					fclose($fp);
				} else{
					alert(et('ReadDenied') . ": " . $file);
				}
			}
		} else{
			alert(et('ReadDenied') . ": " . $file);
		}
	} else{
		alert(et('FileNotFound') . ": " . $file);
	}
}

/**
 * 执行任意命令
 */
function execute_cmd(){
	global $cmd;
	header("Content-type: text/plain; charset=" . CHARSET_FILE);
	$cmd = nameToSys($cmd);
	if(strlen($cmd)){
		echo "# " . $cmd . "\n";
		exec($cmd, $mat);
		if(count($mat)){
			$mat = convert_page_encode(implode("\r\n", $mat));
			echo trim($mat);
		} else{
			echo "exec(\"$cmd\") " . et('NoReturn') . "...";
		}
	} else{
		echo et('NoCmd');
	}
}

/**
 * 执行脚本
 */
function execute_file(){
	global $current_dir, $filename;
	header("Content-type: text/plain");
	$file = $current_dir . $filename;
	if(file_exists($file)){
		echo "# " . $file . "\n";
		exec($file, $mat);
		if(count($mat)){
			echo trim(implode("\n", $mat));
		}
	} else{
		alert(et('FileNotFound') . ": " . $file);
	}
}

/**
 * 文件上传
 * @param $temp_file
 * @param $filename
 * @param $dir_dest
 * @return int
 */
function save_upload($temp_file, $filename, $dir_dest){
	global $upload_ext_filter;
	$filename = remove_special_chars($filename);
	$file = $dir_dest . $filename;
	$filesize = filesize($temp_file);
	$is_denied = false;
	foreach($upload_ext_filter as $key => $ext){
		if(eregi($ext, $filename)){
			$is_denied = true;
			break;
		}
	}
	if(!$is_denied){
		if(!check_limit($filesize)){
			$file = nameToSys($file);
			if(file_exists($file)){
				if(unlink($file)){
					if(copy($temp_file, $file)){
						@chmod($file, 0755);
						$out = 6;
					} else{
						$out = 2;
					}
				} else{
					$out = 5;
				}
			} else{
				if(copy($temp_file, $file)){
					@chmod($file, 0755);
					$out = 1;
				} else{
					$out = 2;
				}
			}
		} else{
			$out = 3;
		}
	} else{
		$out = 4;
	}
	return $out;
}

/**
 * zip解压
 */
function zip_extract(){
	global $cmd_arg, $current_dir, $islinux;
	$zip = new ZipArchive();
	if($zip->open(nameToSys($current_dir . $cmd_arg)) !== true){
		echo msg_out("Open zip file error." . $current_dir . $cmd_arg, "#f00");
		return;
	}
	if($zip->extractTo(nameToSys($current_dir)) !== true){
		$zip->close();
		echo msg_out("Extract Zip file error.", "#f00");
	} else{
		$zip->close();
	}
}