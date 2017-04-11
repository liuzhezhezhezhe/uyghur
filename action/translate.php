<?php 
ini_set('memory_limit', '1024M');

// 防止维文乱码
header("Content-type: text/html; charset=utf-8");

// 引入缓存库
require_once '../include/cache.php';

// 查看缓存是否存在，存在则直接返回缓存内容
$strcache = $cache->get($_REQUEST['content']."uyghur");
if ($strcache) {
	echo $strcache;
	die;
}

// 引入html页面解析库
require_once '../lib/simple_html_dom.php';

// 引入jieba分词库
require_once "../lib/jieba-php/src/vendor/multi-array/MultiArray.php";
require_once "../lib/jieba-php/src/vendor/multi-array/Factory/MultiArrayFactory.php";
require_once "../lib/jieba-php/src/class/Jieba.php";
require_once "../lib/jieba-php/src/class/Finalseg.php";

// 初始化jieba分词
use Fukuball\Jieba\Jieba;
use Fukuball\Jieba\Finalseg;
Jieba::init();
Finalseg::init();

// 进行汉维翻译
function translator($target) {
	// 将汉语句子分词
	$seg_list = Jieba::cut($target);
	$result = "";

	// 将分好的词进行逐个翻译
	foreach ($seg_list as $trans) {
		// 使用curl将词发送到远端查询工具进行翻译
		$ch = curl_init('http://cn.dict.izda.com/?a=search&type=cn_ug&q='.$trans.'&field=dict');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$response = curl_exec($ch);
		$smd = str_get_html($response);

		// 过滤翻译结果，读取有关内容
		$mezmuni = trim(explode("<br>",$smd->find('.mezmuni',0)->innertext())[0]);
		$mezmuni2 = trim(explode("<br>",$smd->find('.mezmuni2',0)->innertext())[0]);

		// 如果mezmuni和mezmuni2内容相同，则说明该词无法翻译，顾返回原词
		if ($mezmuni == $mezmuni2) {
			$result .= $trans;
			continue;
		}
		$str;

		// 对于一词多义问题，取第一条常用词作为翻译结果
		if (preg_match('/1\).*\(2/i', $mezmuni, $str) != 0) {
			$str = str_replace(['1)','(2'], "", $str)[0];
		} else {
			$str = $mezmuni;
		}
		$result .= $str;
	}
	return $result;
}

$res = array(array());

// 循环遍历所有结果
foreach ($_POST['result'] as $value) {
	// 去除掉多余html标签
	$value = preg_replace('/<[^>]*>/i', "", $value);
	$title = translator($value['title']);
	$content = translator($value['content']);
	$res[] = ['title' => $title, 'content' => $content];
}

$res = json_encode($res);
// 添加缓存
$cache->set($_REQUEST['content']."uyghur",$res);
echo $res;