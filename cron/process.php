<?php 
/**
 * 爬取文章作为测试数据
 */
include_once(dirname(dirname(__FILE__))."/etc/const.php");
include_once(INCLUDE_ROOT."lib/Class.MyException.php");
include_once(INCLUDE_ROOT."lib/Class.Mysql.php");
include_once(INCLUDE_ROOT."func/function.php");
define("MARK_DEBUG", false);
$db = new Mysql();
$sql = "select * from robot where `status` = 'active' and `from` LIKE 'http://loveteemo.com/%'";
$result = $db->getRows($sql);
foreach ($result as $k => $v) {
	$html = str_replace("\n","", $v['content']);
	$data = array();
	//匹配articlesource ,title
	$pregA = "/<h5>(.*)<\/h5>/";
	preg_match($pregA,$html,$matchA);
	if(isset($matchA[1])){
		$pregArticleSource = '/<span class=".*">【(.*)】<\/span>/';
		preg_match($pregArticleSource, $matchA[1],$articleSourceMatch);
		$data['articlesource'] = isset($articleSourceMatch[1])?$articleSourceMatch[1]:'';
		$pregTitle = '/<a href="\/article-(\d+).html">(.*)<\/a>/';
		preg_match($pregTitle, $matchA[1],$titleMatch);
		$data['title'] = isset($titleMatch[2])?$titleMatch[2]:'';
	}
	//匹配addeditor,clickcount,addtime
	$pregB = '/<div class="a\-write">(.*)<div class="a\-content">/';
	preg_match($pregB,$html,$matchB);
	if(isset($matchB[1])){
		$pregCategory = '/栏目：(.*)(?:&nbsp;&nbsp;)作者/';
		preg_match($pregCategory, $matchB[1],$categoryMatch);
		$data['category'] = isset($categoryMatch[1])?$categoryMatch[1]:'';
		$pregAddEditor = '/作者：(.*)&nbsp;&nbsp;阅读/';
		preg_match($pregAddEditor, $matchB[1],$addEditorMatch);
		$data['addeditor'] = isset($addEditorMatch[1])?$addEditorMatch[1]:'';
		$pregClickCount = '/阅读：\（(.*)\）/';
		preg_match($pregClickCount, $matchB[1],$clickcountMatch);
		$data['clickcount'] = isset($clickcountMatch[1])?$clickcountMatch[1]:'';
		$pregAddTime = '/更新于：(.*)<\/span>/';
		preg_match($pregAddTime, $matchB[1],$addtimeMatch);
		$data['addtime'] = isset($addtimeMatch[1])?$addtimeMatch[1]:'';
	}
	//匹配文章content
	$pregC = '/<div class="a\-content">(.*)<div class="article\-copy hidden\-xs">/';
	preg_match($pregC,$html,$matchC);
	$data['content'] = isset($matchC[1])?addslashes(preg_replace("/<\/div>$/","",rtrim($matchC[1]))):'';

	//存入文章
	$sql = "insert into article (`title`,`content`,`addtime`,`articlesource`,`addeditor`,`clickcount`) VALUES 
	('{$data['title']}','{$data['content']}','{$data['addtime']}','{$data['articlesource']}','{$data['addeditor']}','{$data['clickcount']}')";
	$articleid = $db->query($sql);
	if($articleid){
		//url信息
		// $requestpath = "/article/{$articleid}.html";
		// $modeltype = 'article';
		// $optdataid = $articleid;
		// $isjump = 'NO';
		// $status = 'yes';
		// $sql = "insert into rewrite_url (`requestpath`,`modeltype`,`optdataid`,`isjump`,`status`) values ('{$requestpath}','{$modeltype}','{$optdataid}','{$isjump}','{$status}')";
		// $urlid = $db->query($sql);
		//判断category是否存在,不存在则创建
		if(!empty($data['category'])){
			$sql = "select * from category where displayname = '{$data['category']}'";
			$categoryInfo = $db->getFirstRow($sql);
			if(!empty($categoryInfo)){
				$sql = "insert into category_mapping (`optdataid`,`categoryid`,`addtime`) VALUES ('{$articleid}','{$categoryInfo['id']}','".date('Y-m-d H:i:s')."')";
				$db->query($sql);
			}else{
				$sql = "insert into category (`displayname`,`parentcategoryid`,`addtime`) VALUES ('{$date['category']}','0','".date('Y-m-d H:i:s')."')";
				$categoryid = $db->query($sql);
				if($categoryid){
					$requestpath = "/category/{$categoryid}.html";
					$modeltype = 'category';
					$optdataid = $articleid;
					$isjump = 'NO';
					$status = 'yes';
					$urlid = $db->query($sql);
					$sql = "insert into category_mapping (`optdataid`,`categoryid`,`addtime`) VALUES ('{$articleid}','{$categoryid}','".date('Y-m-d H:i:s')."')";
					$db->query($sql);
				}
			}
		}
	}
}