<?php
/**
 * User: loveyu
 * Date: 2015/1/4
 * Time: 23:46
 */
class bzip_file extends tar_file{
	function bzip_file($name){
		$this->tar_file($name);
		$this->options['type'] = "bzip";
	}

	function create_bzip(){
		if($this->options['inmemory'] == 0){
			$Pwd = getcwd();
			chdir($this->options['basedir']);
			if($fp = bzopen($this->options['name'], "wb")){
				fseek($this->archive, 0);
				while($temp = fread($this->archive, 1048576)){
					bzwrite($fp, $temp);
				}
				bzclose($fp);
				chdir($Pwd);
			} else{
				$this->error[] = "Could not open {$this->options['name']} for writing.";
				chdir($Pwd);
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