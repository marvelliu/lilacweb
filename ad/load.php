<?php

error_reporting(E_ALL);
class ad {
	public $path = "/var/www/lilacbbs/ad/";
	public $bannerPath = 'banner';
	public $bannerSourceFile = 'source.list';
	public function getFileList($subDir) {
		$dir = @opendir($this->path. $subDir);
		$fileList = array();
		$i =0.0001 ;
		while(($file = readdir($dir)) !== false) {
			if($file == '.' or $file == '..') 
				continue;
			$fileList[filectime($thiis->path. $subDir . '/'.$file) . $i] = $file;
			$i += 0.0001;
		}
		return $fileList;
	}
	public function getBannerSource() {
		$fileName = $this->path . $this->bannerPath . '/' . $this->bannerSourceFile;

		if(is_file($fileName)) {
			$source = file_get_contents($fileName);
			if(empty($source))
				return false;
			return $source;
		}
		return false;
	}
	public function getBannerFile() {
		$fileList = $this->getFileList($this->bannerPath);
		ksort($fileList);
		return array_pop($fileList);
	}
	public function fileIsType($fileName) {
		$extend = pathinfo($fileName);
		$img = @getimagesize($fileName);
		if(is_array($img) and $img['mime'] != 'application/x-shockwave-flash')
			return 'image';
			
		return strtolower($extend['extension']);
	}
	public function bannerFileIsType($fileName) {
		return $this->fileIsType($this->path . $this->bannerPath . '/' . $fileName);
	}
}
$ad = new ad();
$banner = $ad->getBannerSource();

if($banner == false) {
	$file= $ad->getBannerFile();
	$type = $ad->bannerFileIsType($file) ;
	switch($type) {
		case 'image':
		$banner = "<img src=\"ad/{$ad->bannerPath}/$file\" class=\"a_d_image\" />";
		break;
		case 'swf':
		$banner = "<embed src=\"ad/{$ad->bannerPath}/$file\" allowFullScreen=\"true\" quality=\"high\" width=\"720\" height=\"60\" align=\"middle\" allowScriptAccess=\"always\" type=\"application/x-shockwave-flash\"></embed>";
		break;
	}
}
return array(
	'banner'=>$banner
);
