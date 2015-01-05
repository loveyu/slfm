<?php
/**
 * User: loveyu
 * Date: 2015/1/5
 * Time: 0:11
 */

/**
 * 显示文件目录中的列表
 */
function frame3(){
	global $islinux, $cmd_arg, $chmod_arg, $zip_dir, $fm_current_root, $cookie_cache_time;
	global $dir_dest, $current_dir, $dir_before;
	global $selected_file_list, $selected_dir_list, $old_name, $new_name;
	global $action, $or_by, $order_dir_list_by;
	if(!isset($order_dir_list_by)){
		$order_dir_list_by = "1A";
		setcookie("order_dir_list_by", $order_dir_list_by, time() + $cookie_cache_time, "/");
	} elseif(strlen($or_by)){
		$order_dir_list_by = $or_by;
		setcookie("order_dir_list_by", $or_by, time() + $cookie_cache_time, "/");
	}
	html_header();
	echo "<body>\n";
	if($action){
		switch($action){
			case 1: // create dir
				if(strlen($cmd_arg)){
					$cmd_arg = format_path($current_dir . $cmd_arg);
					if(!file_exists(nameToSys($cmd_arg))){
						@mkdir(nameToSys($cmd_arg), 0755);
						@chmod(nameToSys($cmd_arg), 0755);
						reloadframe("parent", 2, "&ec_dir=" . $cmd_arg);
					} else{
						alert(et('FileDirExists') . ".");
					}
				}
				break;
			case 2: // create arq
				if(strlen($cmd_arg)){
					$cmd_arg = nameToSys($current_dir . $cmd_arg);
					if(!file_exists($cmd_arg)){
						if($fh = @fopen($cmd_arg, "w")){
							@fclose($fh);
						}
						@chmod($cmd_arg, 0644);
					} else{
						alert(et('FileDirExists') . ".");
					}
				}
				break;
			case 3: // rename arq ou dir
				if((strlen($old_name)) && (strlen($new_name))){
					rename(nameToSys($current_dir . $old_name), nameToSys($current_dir . $new_name));
					if(is_dir(nameToSys($current_dir . $new_name))){
						reloadframe("parent", 2);
					}
				}
				break;
			case 4: // delete sel
				if(strstr($current_dir, $fm_current_root)){
					if(strlen($selected_file_list)){
						$selected_file_list = explode("<|*|>", $selected_file_list);
						if(count($selected_file_list)){
							for($x = 0; $x < count($selected_file_list); $x++){
								$selected_file_list[$x] = trim($selected_file_list[$x]);
								if(strlen($selected_file_list[$x])){
									total_delete(nameToSys($current_dir . $selected_file_list[$x]), nameToSys($dir_dest . $selected_file_list[$x]));
								}
							}
						}
					}
					if(strlen($selected_dir_list)){
						$selected_dir_list = explode("<|*|>", $selected_dir_list);
						if(count($selected_dir_list)){
							for($x = 0; $x < count($selected_dir_list); $x++){
								$selected_dir_list[$x] = trim($selected_dir_list[$x]);
								if(strlen($selected_dir_list[$x])){
									total_delete(nameToSys($current_dir . $selected_dir_list[$x]), nameToSys($dir_dest . $selected_dir_list[$x]));
								}
							}
							reloadframe("parent", 2);
						}
					}
				}else{
					alert("必须切换到对应目录才运行删除！");
				}
				break;
			case 5: // copy sel
				if(strlen($dir_dest)){
					if(uppercase($dir_dest) != uppercase($current_dir)){
						if(strlen($selected_file_list)){
							$selected_file_list = explode("<|*|>", $selected_file_list);
							if(count($selected_file_list)){
								for($x = 0; $x < count($selected_file_list); $x++){
									$selected_file_list[$x] = trim($selected_file_list[$x]);
									if(strlen($selected_file_list[$x])){
										total_copy(nameToSys($current_dir . $selected_file_list[$x]), nameToSys($dir_dest . $selected_file_list[$x]));
									}
								}
							}
						}
						if(strlen($selected_dir_list)){
							$selected_dir_list = explode("<|*|>", $selected_dir_list);
							if(count($selected_dir_list)){
								for($x = 0; $x < count($selected_dir_list); $x++){
									$selected_dir_list[$x] = trim($selected_dir_list[$x]);
									if(strlen($selected_dir_list[$x])){
										total_copy(nameToSys($current_dir . $selected_dir_list[$x]), nameToSys($dir_dest . $selected_dir_list[$x]));
									}
								}
								reloadframe("parent", 2);
							}
						}
						$current_dir = $dir_dest;
					}
				}
				break;
			case 6: // move sel
				if(strlen($dir_dest)){
					if(uppercase($dir_dest) != uppercase($current_dir)){
						if(strlen($selected_file_list)){
							$selected_file_list = explode("<|*|>", $selected_file_list);
							if(count($selected_file_list)){
								for($x = 0; $x < count($selected_file_list); $x++){
									$selected_file_list[$x] = trim($selected_file_list[$x]);
									if(strlen($selected_file_list[$x])){
										total_move(nameToSys($current_dir . $selected_file_list[$x]), nameToSys($dir_dest . $selected_file_list[$x]));
									}
								}
							}
						}
						if(strlen($selected_dir_list)){
							$selected_dir_list = explode("<|*|>", $selected_dir_list);
							if(count($selected_dir_list)){
								for($x = 0; $x < count($selected_dir_list); $x++){
									$selected_dir_list[$x] = trim($selected_dir_list[$x]);
									if(strlen($selected_dir_list[$x])){
										total_move(nameToSys($current_dir . $selected_dir_list[$x]), nameToSys($dir_dest . $selected_dir_list[$x]));
									}
								}
								reloadframe("parent", 2);
							}
						}
						$current_dir = $dir_dest;
					}
				}
				break;
			case 71: // compress sel
				if(strlen($cmd_arg)){
					ignore_user_abort(true);
					ini_set("display_errors", 0);
					ini_set("max_execution_time", 0);
					$zipfile = false;
					$cmd_arg = nameToSys($cmd_arg);
					if(strstr($cmd_arg, ".tar")){
						$zipfile = new tar_file($cmd_arg);
					} elseif(strstr($cmd_arg, ".zip")){
						$zipfile = new zip_file($cmd_arg);
					} elseif(strstr($cmd_arg, ".bzip")){
						$zipfile = new bzip_file($cmd_arg);
					} elseif(strstr($cmd_arg, ".gzip")){
						$zipfile = new gzip_file($cmd_arg);
					}
					if($zipfile){
						$zipfile->set_options(array(
							'basedir' => nameToSys($current_dir),
							'overwrite' => 1,
							'level' => 3
						));
						if(strlen($selected_file_list)){
							$selected_file_list = explode("<|*|>", $selected_file_list);
							if(count($selected_file_list)){
								for($x = 0; $x < count($selected_file_list); $x++){
									$selected_file_list[$x] = trim($selected_file_list[$x]);
									if(strlen($selected_file_list[$x])){
										$zipfile->add_files(nameToSys($selected_file_list[$x]));
									}
								}
							}
						}
						if(strlen($selected_dir_list)){
							$selected_dir_list = explode("<|*|>", $selected_dir_list);
							if(count($selected_dir_list)){
								for($x = 0; $x < count($selected_dir_list); $x++){
									$selected_dir_list[$x] = trim($selected_dir_list[$x]);
									if(strlen($selected_dir_list[$x])){
										$zipfile->add_files(nameToSys($selected_dir_list[$x]));
									}
								}
							}
						}
						$zipfile->create_archive();
					}
					unset($zipfile);
				}
				break;
			case 72: // decompress arq
				if(strlen($cmd_arg)){
					if(file_exists($current_dir . $cmd_arg)){
						$zipfile = false;
						if(strstr($cmd_arg, ".zip")){
							zip_extract();
						} elseif(strstr($cmd_arg, ".bzip") || strstr($cmd_arg, ".bz2") || strstr($cmd_arg, ".tbz2") || strstr($cmd_arg, ".bz") || strstr($cmd_arg, ".tbz")){
							$zipfile = new bzip_file($cmd_arg);
						} elseif(strstr($cmd_arg, ".gzip") || strstr($cmd_arg, ".gz") || strstr($cmd_arg, ".tgz")){
							$zipfile = new gzip_file($cmd_arg);
						} elseif(strstr($cmd_arg, ".tar")){
							$zipfile = new tar_file($cmd_arg);
						}
						if($zipfile){
							$zipfile->set_options(array(
								'basedir' => $current_dir,
								'overwrite' => 1
							));
							$zipfile->extract_files();
						}
						unset($zipfile);
						reloadframe("parent", 2);
					}
				}
				break;
			case 8: // delete arq/dir
				if(strlen($cmd_arg)){
					if(file_exists(nameToSys($current_dir . $cmd_arg))){
						total_delete(nameToSys($current_dir . $cmd_arg));
					}
					if(is_dir(nameToSys($current_dir . $cmd_arg))){
						reloadframe("parent", 2);
					}
				}
				break;
			case 9: // CHMOD
				if((strlen($chmod_arg) == 4) && (strlen($current_dir))){
					if($chmod_arg[0] == "1"){
						$chmod_arg = "0" . $chmod_arg;
					} else{
						$chmod_arg = "0" . substr($chmod_arg, strlen($chmod_arg) - 3);
					}
					$new_mod = octdec($chmod_arg);
					if(strlen($selected_file_list)){
						$selected_file_list = explode("<|*|>", $selected_file_list);
						if(count($selected_file_list)){
							for($x = 0; $x < count($selected_file_list); $x++){
								$selected_file_list[$x] = trim($selected_file_list[$x]);
								if(strlen($selected_file_list[$x])){
									@chmod(nameToSys($current_dir . $selected_file_list[$x]), $new_mod);
								}
							}
						}
					}
					if(strlen($selected_dir_list)){
						$selected_dir_list = explode("<|*|>", $selected_dir_list);
						if(count($selected_dir_list)){
							for($x = 0; $x < count($selected_dir_list); $x++){
								$selected_dir_list[$x] = trim($selected_dir_list[$x]);
								if(strlen($selected_dir_list[$x])){
									@chmod(nameToSys($current_dir . $selected_dir_list[$x]), $new_mod);
								}
							}
						}
					}
				}
				break;
		}
		if($action != 10){
			dir_list_form();
		}
	} else{
		dir_list_form();
	}
	echo "</body>\n</html>";
}

/**
 *
 */
function frame2(){
	global $expanded_dir_list, $ec_dir;
	if(!isset($expanded_dir_list)){
		$expanded_dir_list = "";
	}
	if(strlen($ec_dir)){
		if(strstr($expanded_dir_list, ":" . $ec_dir)){
			$expanded_dir_list = str_replace(":" . $ec_dir, "", $expanded_dir_list);
		} else{
			$expanded_dir_list .= ":" . $ec_dir;
		}
		setcookie("expanded_dir_list", $expanded_dir_list, 0, "/");
	}
	show_tree();
}

/**
 *
 */
function frameset(){
	global $path_info, $leftFrameWidth;
	if(!isset($leftFrameWidth)){
		$leftFrameWidth = 300;
	}
	html_header();
	echo "
    <frameset cols=\"" . $leftFrameWidth . ",*\" framespacing=\"0\">
        <frameset rows=\"0,*\" framespacing=\"0\" frameborder=\"0\">
            <frame src=\"" . $path_info["basename"] . "?frame=1\" name=frame1 border=\"0\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"no\">
            <frame src=\"" . $path_info["basename"] . "?frame=2\" name=frame2 border=\"0\" marginwidth=\"0\" marginheight=\"0\">
        </frameset>
        <frame src=\"" . $path_info["basename"] . "?frame=3\" name=frame3 border=\"0\" marginwidth=\"0\" marginheight=\"0\">
    </frameset>
    </html>";
}