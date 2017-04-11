<?php 

// 创建Redis缓存
$cache = new \Redis();
if (!$cache->connect('127.0.0.1',6379,300)) {
	header('context-Type:html/text;charset:utf-8');
	echo "<script>alert('缓存连接失败')</script>";
	$cache = null;
	die;
}