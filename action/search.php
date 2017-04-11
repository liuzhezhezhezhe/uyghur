<?php 
/**
 * 功能：完成搜索功能，主要从百度抓取第一页的相关数据
 * 参数：前端传过来的ajax请求，即关键字
 */

header("Content-type: text/html; charset=utf-8");

// 引入缓存库
require_once '../include/cache.php';

// 查看缓存是否存在
$strcache = $cache->get($_GET['content']);
// 存在则直接返回缓存中内容
if ($strcache) {
	echo $strcache;
	die;
}

// 引入html页面解析库
require_once '../lib/simple_html_dom.php';

// 搜索函数，通过爬虫技术（curl）搜索相关内容
// 将获取到的内容通过simplehtmldom库进行解析，获取其中的关键字
function chineseSearch($keyWord) {

	// 内容爬取部分
	$ch = curl_init('http://www.baidu.com/s?wd='.$keyWord);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$response = curl_exec($ch);
	curl_close();


	$content = array(array());

	// 开始解析整个页面
	$smd = str_get_html($response);
	foreach ($smd->find('#content_left[tpl=se_com_default]') as $element) {
		$title = htmlspecialchars_decode($element->find('h3[class=t] a',0)->innertext());
		$article = htmlspecialchars_decode($element->find('div[class=c-abstract]',0)->innertext());
		$content[] = [ 'title' => $title, 'content' => $article];
	}
	// 删除第一条内容（因为第一条是无效内容）
	array_shift($content);

	$content = json_encode($content);
	return $content;
}

// 添加缓存
$content = chineseSearch($_GET['content']);
$cache->set($_GET['content'],$content);

// 返回解析后的json数据
echo $content;