<?php

class Bzip_file extends Tar_file{
	function __construct($name){
		parent::__construct($name);
		$this->options['type'] = "bzip";
	}

	function test(){
		$flag = parent::test();
		if($flag!==true){
			return $flag;
		}
		if(function_exists('bzopen')){
			return true;
		}else{
			return "Bzip compress is not support.";
		}
	}

	function create_bzip(){
		if($this->options['inmemory'] == 0){
			$pwd = getcwd();
			chdir($this->options['basedir']);
			$fp = bzopen($this->options['name'], "w");
			if($fp){
				fseek($this->archive, 0);
				while($temp = fread($this->archive, 1048576)){
					bzwrite($fp, $temp);
				}
				bzclose($fp);
				chdir($pwd);
			} else{
				$this->error[] = "Could not open {$this->options['name']} for writing.";
				chdir($pwd);
				return 0;
			}
		} else{
			$this->archive = bzcompress($this->archive, $this->options['level']);
		}

		return 1;
	}

	function open_archive(){
		return @bzopen($this->options['name'], "rb");
	}
}