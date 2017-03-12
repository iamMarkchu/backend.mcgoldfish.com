<?php 
namespace Home\Model;
use Think\Model;
class NodeModel extends Model {
	public function getAllNode($auth = [1,2,3,4,12,34], $default=true){
	    $default_auth = $auth;
		$sql = "select * from node where level = 1 and status = 'active'";
		$levelOneNode = $this->query($sql);
		foreach ($levelOneNode as $k => $v) {
			$sql = "select * from node where level = 2 and pid = {$v['id']} and name != 'Public' and status = 'active'";
			$levelTwoNode = $this->query($sql);
			foreach ($levelTwoNode as $kk => $vv) {
				$sql = "select * from node where level = 3 and pid = {$vv['id']} and status = 'active'";
				$levelThreeNode = $this->query($sql);
                $sql = "select * from node where pid = 9 and level = 3 and status = 'active'";
                $publicNode = $this->query($sql);
                if(in_array($vv['id'], $default_auth))
                {
                    $levelTwoNode[$kk]['default'] = true;
                    foreach ($publicNode as $kkk => $vvv)
                    {
                        $publicNode[$kkk]['default'] = true;
                    }
                }
                $levelThreeNode = array_merge($publicNode, $levelThreeNode);

                foreach ($levelThreeNode as $kkk => $vvv)
                {
                    if(in_array($vvv['id'], $default_auth))
                    {
                        $levelThreeNode[$kkk]['default'] = true;
                    }
                }
                $levelTwoNode[$kk]['child'] = $levelThreeNode;

			}
			$levelOneNode[$k]['child'] = $levelTwoNode;
			if(in_array($v['id'], $default_auth))
            {
                $levelOneNode[$k]['default'] = true;
            }
		}
		return $levelOneNode;
	}
}