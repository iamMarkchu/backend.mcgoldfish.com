<?php
namespace Home\Controller;
use Think\Controller;
class PublicController extends CommonController {
    public function index(){
       $this->display();
    }
    public function login(){
    	$this->display();	
    }
}