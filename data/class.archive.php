<?php
/**
 * User: loveyu
 * Date: 2015/1/4
 * Time: 23:43
 */

// +--------------------------------------------------
// | Open Source Contributions
// +--------------------------------------------------
/*-------------------------------------------------
 | TAR/GZIP/BZIP2/ZIP ARCHIVE CLASSES 2.0
 | By Devin Doucette
 | Copyright (c) 2004 Devin Doucette
 | Email: darksnoopy@shaw.ca
 +--------------------------------------------------
 | Email bugs/suggestions to darksnoopy@shaw.ca
 +--------------------------------------------------
 | This script has been created and released under
 | the GNU GPL and is free to use and redistribute
 | only if this copyright statement is not removed
 +--------------------------------------------------
 | Limitations:
 | - Only USTAR archives are officially supported for extraction, but others may work.
 | - Extraction of bzip2 and gzip archives is limited to compatible tar files that have
 | been compressed by either bzip2 or gzip.  For greater support, use the functions
 | bzopen and gzopen respectively for bzip2 and gzip extraction.
 | - Zip extraction is not supported due to the wide variety of algorithms that may be
 | used for compression and newer features such as encryption.
 +--------------------------------------------------
 */

class archive{
	function archive($name){
		$this->options = array(
			'basedir' => ".",
			'name' => $name,
			'prepend' => "",
			'inmemory' => 0,
			'overwrite' => 0,
			'recurse' => 1,
			'storepaths' => 1,
			'level' => 3,
			'method' => 1,
			'sfx' => "",
			'type' => "",
			'comment' => ""
		);
		$this->files = array();
		$this->exclude = array();
		$this->storeonly = array();
		$this->error = array();
	}

	function set_options($options){
		foreach($options as $key => $value){
			$this->options[$key] = $value;
		}
		if(!empty($this->options['basedir'])){
			$this->options['basedir'] = str_replace("\\", "/", $this->options['basedir']);
			$this->options['basedir'] = preg_replace("/\\/+/", "/", $this->options['basedir']);
			$this->options['basedir'] = preg_replace("/\\/$/", "", $this->options['basedir']);
		}
		if(!empty($this->options['name'])){
			$this->options['name'] = str_replace("\\", "/", $this->options['name']);
			$this->options['name'] = preg_replace("/\\/+/", "/", $this->options['name']);
		}
		if(!empty($this->options['prepend'])){
			$this->options['prepend'] = str_replace("\\", "/", $this->options['prepend']);
			$this->options['prepend'] = preg_replace("/^(\\.*\\/+)+/", "", $this->options['prepend']);
			$this->options['prepend'] = preg_replace("/\\/+/", "/", $this->options['prepend']);
			$this->options['prepend'] = preg_replace("/\\/$/", "", $this->options['prepend']) . "/";
		}
	}

	function create_archive(){
		$this->make_list();

		if($this->options['inmemory'] == 0){
			$Pwd = getcwd();
			chdir($this->options['basedir']);
			if($this->options['overwrite'] == 0 && file_exists($this->options['name'] . ($this->options['type'] == "gzip" || $this->options['type'] == "bzip" ? ".tmp" : ""))){
				$this->error[] = "File {$this->options['name']} already exists.";
				chdir($Pwd);
				return 0;
			} else if($this->archive = @fopen($this->options['name'] . ($this->options['type'] == "gzip" || $this->options['type'] == "bzip" ? ".tmp" : ""), "wb+")){
				chdir($Pwd);
			} else{
				$this->error[] = "Could not open {$this->options['name']} for writing.";
				chdir($Pwd);
				return 0;
			}
		} else{
			$this->archive = "";
		}

		switch($this->options['type']){
			case "zip":
				if(!$this->create_zip()){
					$this->error[] = "Could not create zip file.";
					return 0;
				}
				break;
			case "bzip":
				if(!$this->create_tar()){
					$this->error[] = "Could not create tar file.";
					return 0;
				}
				if(!$this->create_bzip()){
					$this->error[] = "Could not create bzip2 file.";
					return 0;
				}
				break;
			case "gzip":
				if(!$this->create_tar()){
					$this->error[] = "Could not create tar file.";
					return 0;
				}
				if(!$this->create_gzip()){
					$this->error[] = "Could not create gzip file.";
					return 0;
				}
				break;
			case "tar":
				if(!$this->create_tar()){
					$this->error[] = "Could not create tar file.";
					return 0;
				}
		}

		if($this->options['inmemory'] == 0){
			fclose($this->archive);
			@chmod($this->options['name'], 0644);
			if($this->options['type'] == "gzip" || $this->options['type'] == "bzip"){
				unlink($this->options['basedir'] . "/" . $this->options['name'] . ".tmp");
			}
		}
	}

	function add_data($data){
		if($this->options['inmemory'] == 0){
			fwrite($this->archive, $data);
		} else{
			$this->archive .= $data;
		}
	}

	function make_list(){
		if(!empty($this->exclude)){
			foreach($this->files as $key => $value){
				foreach($this->exclude as $current){
					if($value['name'] == $current['name']){
						unset($this->files[$key]);
					}
				}
			}
		}
		if(!empty($this->storeonly)){
			foreach($this->files as $key => $value){
				foreach($this->storeonly as $current){
					if($value['name'] == $current['name']){
						$this->files[$key]['method'] = 0;
					}
				}
			}
		}
		unset($this->exclude, $this->storeonly);
	}


	function add_files($list){
		$temp = $this->list_files($list);
		foreach($temp as $current){
			$this->files[] = $current;
		}
	}

	function exclude_files($list){
		$temp = $this->list_files($list);
		foreach($temp as $current){
			$this->exclude[] = $current;
		}
	}

	function store_files($list){
		$temp = $this->list_files($list);
		foreach($temp as $current){
			$this->storeonly[] = $current;
		}
	}

	function list_files($list){
		if(!is_array($list)){
			$temp = $list;
			$list = array($temp);
			unset($temp);
		}

		$files = array();

		$Pwd = getcwd();
		chdir($this->options['basedir']);

		foreach($list as $current){
			$current = str_replace("\\", "/", $current);
			$current = preg_replace("/\\/+/", "/", $current);
			$current = preg_replace("/\\/$/", "", $current);
			if(strstr($current, "*")){
				$regex = preg_replace("/([\\\\^\$\\.\\[\\]\\|\\(\\)\\?\\+\\{\\}\\/])/", "\\\\\\1", $current);
				$regex = str_replace("*", ".*", $regex);
				$dir = strstr($current, "/") ? substr($current, 0, strrpos($current, "/")) : ".";
				$temp = $this->parse_dir($dir);
				foreach($temp as $current2){
					if(preg_match("/^{$regex}$/i", $current2['name'])){
						$files[] = $current2;
					}
				}
				unset($regex, $dir, $temp, $current);
			} else if(@is_dir($current)){
				$temp = $this->parse_dir($current);
				foreach($temp as $file){
					$files[] = $file;
				}
				unset($temp, $file);
			} else if(@file_exists($current)){
				$files[] = array(
					'name' => $current,
					'name2' => $this->options['prepend'] . preg_replace("/(\\.+\\/+)+/", "", ($this->options['storepaths'] == 0 && strstr($current, "/")) ? substr($current, strrpos($current, "/") + 1) : $current),
					'type' => 0,
					'ext' => substr($current, strrpos($current, ".")),
					'stat' => stat($current)
				);
			}
		}

		chdir($Pwd);

		unset($current, $Pwd);

		usort($files, array(
			"archive",
			"sort_files"
		));

		return $files;
	}

	function parse_dir($dirname){
		if($this->options['storepaths'] == 1 && !preg_match("/^(\\.+\\/*)+$/", $dirname)){
			$files = array(
				array(
					'name' => $dirname,
					'name2' => $this->options['prepend'] . preg_replace("/(\\.+\\/+)+/", "", ($this->options['storepaths'] == 0 && strstr($dirname, "/")) ? substr($dirname, strrpos($dirname, "/") + 1) : $dirname),
					'type' => 5,
					'stat' => stat($dirname)
				)
			);
		} else{
			$files = array();
		}
		$dir = @opendir($dirname);

		while($file = @readdir($dir)){
			if($file == "." || $file == ".."){
				continue;
			} else if(@is_dir($dirname . "/" . $file)){
				if(empty($this->options['recurse'])){
					continue;
				}
				$temp = $this->parse_dir($dirname . "/" . $file);
				foreach($temp as $file2){
					$files[] = $file2;
				}
			} else if(@file_exists($dirname . "/" . $file)){
				$files[] = array(
					'name' => $dirname . "/" . $file,
					'name2' => $this->options['prepend'] . preg_replace("/(\\.+\\/+)+/", "", ($this->options['storepaths'] == 0 && strstr($dirname . "/" . $file, "/")) ? substr($dirname . "/" . $file, strrpos($dirname . "/" . $file, "/") + 1) : $dirname . "/" . $file),
					'type' => 0,
					'ext' => substr($file, strrpos($file, ".")),
					'stat' => stat($dirname . "/" . $file)
				);
			}
		}

		@closedir($dir);

		return $files;
	}

	function sort_files($a, $b){
		if($a['type'] != $b['type']){
			return $a['type'] > $b['type'] ? -1 : 1;
		} else if($a['type'] == 5){
			return strcmp(strtolower($a['name']), strtolower($b['name']));
		} else{
			if($a['ext'] != $b['ext']){
				return strcmp($a['ext'], $b['ext']);
			} else if($a['stat'][7] != $b['stat'][7]){
				return $a['stat'][7] > $b['stat'][7] ? -1 : 1;
			} else{
				return strcmp(strtolower($a['name']), strtolower($b['name']));
			}
		}
	}

	function download_file(){
		if($this->options['inmemory'] == 0){
			$this->error[] = "Can only use download_file() if archive is in memory. Redirect to file otherwise, it is faster.";
			return;
		}
		switch($this->options['type']){
			case "zip":
				header("Content-type:application/zip");
				break;
			case "bzip":
				header("Content-type:application/x-compressed");
				break;
			case "gzip":
				header("Content-type:application/x-compressed");
				break;
			case "tar":
				header("Content-type:application/x-tar");
		}
		$header = "Content-disposition: attachment; filename=\"";
		$header .= strstr($this->options['name'], "/") ? substr($this->options['name'], strrpos($this->options['name'], "/") + 1) : $this->options['name'];
		$header .= "\"";
		header($header);
		header("Content-length: " . strlen($this->archive));
		header("Content-transfer-encoding: binary");
		header("Cache-control: no-cache, must-revalidate, post-check=0, pre-check=0");
		header("Pragma: no-cache");
		header("Expires: 0");
		print($this->archive);
	}
}