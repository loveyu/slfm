<?php

/**
 * 配置类
 * Class config
 */
class config{
	/**
	 * @var string 对应的配置数据
	 */
	var $data;
	/**
	 * @var string 对应的文件名
	 */
	var $filename;

	/**
	 * 构造函数
	 */
	function config(){
		global $fm_self;
		$this->data = array(
			'lang' => 'zh',//语言文件
			'auth_pass' => md5(''),//默认密码为空
			'quota_mb' => 0,
			'upload_ext_filter' => array(),//允许上传的文件后缀
			'download_ext_filter' => array(),//允许下载的文件后缀
			'error_reporting' => 2,//错误提示等级
			'fm_root' => '',//文件管理根目录
			'cookie_cache_time' => 60 * 60 * 24 * 30,//登陆有效期
			'version' => '0.10.2',//程序版本
			'timezone' => 'PRC'//时区
		);
		$data = false;
		$this->filename = $fm_self;
		if(file_exists($this->filename)){
			$mat = file($this->filename);
			$objdata = trim(substr($mat[1], 2));
			if(strlen($objdata)){
				$data = unserialize($objdata);
			}
		}
		if(is_array($data) && count($data) == count($this->data)){
			$this->data = $data;
		} else{
			$this->save();
		}
	}

	//保存
	function save(){
		$objdata = "<?php" . chr(13) . chr(10) . "//" . serialize($this->data) . chr(13) . chr(10);
		if(strlen($objdata)){
			if(file_exists($this->filename)){
				$mat = file($this->filename);
				if($fh = @fopen($this->filename, "w")){
					@fputs($fh, $objdata, strlen($objdata));
					for($x = 2; $x < count($mat); $x++){
						@fputs($fh, $mat[$x], strlen($mat[$x]));
					}
					@fclose($fh);
				}
			}
		}
	}

	/**
	 * 加载
	 */
	function load(){
		foreach($this->data as $key => $val){
			$GLOBALS[$key] = $val;
		}
	}
}