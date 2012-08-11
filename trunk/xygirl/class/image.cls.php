<?php
class image
{
	var $urlPath;
	var $galleryPath;
	var $toFile = true;


	function run($sourFile,$width = '',$height = '',$fix='',$isw='')
	{
		$imageInfo = $this->getInfo($sourFile);
		$this->galleryPath = substr($sourFile,0,strrpos($sourFile, "/"));
		$this->urlPath = substr($sourFile,0,strrpos($sourFile, "/"));
		$newName = substr($imageInfo["name"], 0, strrpos($imageInfo["name"], ".")) ."_".$fix.".";
		switch ($imageInfo["type"])
		{
			case 1: //gif
				$img = imagecreatefromgif($sourFile);
				$newName .= 'gif';
				break;
			case 2: //jpg
				$img = imagecreatefromjpeg($sourFile);
				$newName .= 'jpg';
				break;
			case 3: //png
				$img = imagecreatefrompng($sourFile);
				$newName .= 'png';
				break;
			default:
				return 0;
				break;
		}
		if (!$img)
			return 0;

		$srcW = $imageInfo["width"];
		$srcH = $imageInfo["height"];
		if($width && $height){
			$width = ($width > $srcW) ? $srcW : $width;
			$height = ($height > $srcH) ? $srcH : $height;
			if ($isw || $srcW * $height > $srcH * $width)
				$height = round($srcH * $width / $srcW);
			else
				$width = round($srcW * $height / $srcH);
		} elseif($width){
			$height = round($srcH * $width / $srcW);
		} else {
			$width = round($srcW * $height / $srcH);
		}

		if (function_exists("imagecreatetruecolor")) //GD2.0.1
		{
			$new = imagecreatetruecolor($width, $height);
			ImageCopyResampled($new, $img, 0, 0, 0, 0, $width, $height, $imageInfo["width"], $imageInfo["height"]);
		}
		else
		{
			$new = imagecreate($width, $height);
			ImageCopyResized($new, $img, 0, 0, 0, 0, $width, $height, $imageInfo["width"], $imageInfo["height"]);
		}

		if ($this->toFile)
		{
			ImageJPEG($new, $this->galleryPath .'/'. $newName);
			return $this->urlPath .'/'. $newName;
		}
		else
		{
			ImageJPEG($new);
		}
		ImageDestroy($new);
		ImageDestroy($img);
	}

	function getInfo($file)
	{
		$data = getimagesize($file);
		$imageInfo["width"] = $data[0];
		$imageInfo["height"]= $data[1];
		$imageInfo["type"] = $data[2];
		$imageInfo["name"] = basename($file);
		return $imageInfo;
	}

}

?>