<?php
	header('Content-Type: text/html; charset=utf-8');
	require_once '../../lib/simple_html_dom.php';
	$ch = curl_init('http://cn.dict.izda.com/?a=search&type=cn_ug&q=今天你真帅&field=dict');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$response = curl_exec($ch);
	curl_close($ch);
	$smd = str_get_html($response);
	$mezmuni = trim(explode("<br>",$smd->find('.mezmuni',0)->innertext())[0]);
	$mezmuni2 = trim(explode("<br>",$smd->find('.mezmuni2',0)->innertext())[0]);
	var_dump($mezmuni);
	var_dump($mezmuni2);
	var_dump($mezmuni2 == $mezmuni);

	ini_set('memory_limit', '1024M');
	require_once "./jieba-php/src/vendor/multi-array/MultiArray.php";
	require_once "./jieba-php/src/vendor/multi-array/Factory/MultiArrayFactory.php";
	require_once "./jieba-php/src/class/Jieba.php";
	require_once "./jieba-php/src/class/Finalseg.php";
	
	use Fukuball\Jieba\Jieba;
	use Fukuball\Jieba\Finalseg;
	
	Jieba::init();
	Finalseg::init();
	
	$seg_list = Jieba::cut("怜香惜玉也得要看对象啊!");
	var_dump($seg_list);
	
	$seg_list = Jieba::cut("我来到北京清华大学", true);
	var_dump($seg_list); #全模式
 ?>