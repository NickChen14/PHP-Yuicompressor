<?php
include_once 'class/yuicompress.class.php';

$path = '/Volumes/Mac HD/Web/about_me/css/';
$excludepath = array(

);

$cls = new Yuicompressor;
$cls->setCompressPath($path);
$cls->setExcludeFilePath($excludepath);
$cls->arrangeCompressFiles();
$cls->compressFiles();