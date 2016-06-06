<?php
namespace Home\Controller;
use Think\Controller;
class CommentController extends CommonController {
	public function QueryData(){
    	$start = $_POST['start'];
    	$length = $_POST['length'];
    	$searchArray = session('commentSearch');
        if(isset($searchArray['where'])) $map = $searchArray['where'];
        if(isset($searchArray['order'])) $order = $searchArray['order'];
    	$comment = D('comment');
    	$result = $comment->getCommentListForPage($start,$length,$map,$order);
    	$count = $comment->getCommentListForPageCount($map,$order);
    	$jsonBack = array();
    	$jsonBack['data'] = $result;
    	$jsonBack['recordsFiltered'] = $count;
    	$jsonBack['recordsTotal'] = $count;
    	$this->ajaxReturn($jsonBack);
    }	
}