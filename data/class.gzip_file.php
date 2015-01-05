<?php

/**
 * User: loveyu
 * Date: 2015/1/4
 * Time: 23:45
 */
class gzip_file extends tar_file{
	function gzip_file($name){
		$this->tar_file($name);
		$this->options['type'] = "gzip";
	}

	function create_gzip(){
		if($this->options['inmemory'] == 0){
			$Pwd = getcwd();
			chdir($this->options['basedir']);
			if($fp = gzopen($this->options['name'], "wb{$this->options['level']}")){
				fseek($this->archive, 0);
				while($temp = fread($this->archive, 1048576)){
					gzwrite($fp, $temp);
				}
				gzclose($fp);
				chdir($Pwd);
			} else{
				$this->error[] = "Could not open {$this->options['name']} for writing.";
				chdir($Pwd);
				return 0;
			}
		} else{
			$this->archive = gzencode($this->archive, $this->options['level']);
		}

		return 1;
	}

	function open_archive(){
		return @gzopen($this->options['name'], "rb");
	}
}