<?php
namespace App\Domain;

use App\Model\Home as Model_Home;
use App\Domain\Skill as Domain_Skill;

class Home {

    /* 广告 */
	public function getSilide($id='1') {

        $key='getSilide_'.$id;
		$list=\App\getcaches($key);
		if(!$list){
            $model = new Model_Home();
			$list= $model->getSilide($id);
            if($list){
                \App\setcaches($key,$list);
            }
			
		}
        foreach($list as $k=>$v){
            $v['image']=\App\get_upload_path($v['image']);
            $list[$k]=$v;
        }

		return $list;
	}

    /* 用户列表 */
	public function getUsers($uid='0',$p='1') {

        $model = new Model_Home();
        $list= $model->getUsers($uid,$p);
        
        $Domain_skill=new Domain_Skill();
        $order='orders desc';

        foreach($list as $k=>$v){
            $v=\App\handleUser($v);
            $where=[];
            $where['uid']=$v['id'];
            $where['switch']='1';
            $where['status']='1';
            
            $skills=$Domain_skill->getSkillAuth($where,$order);
            $skills=array_slice($skills,0,3);
            $list2=[];
            foreach($skills as $k1=>$v1){
                $info=$Domain_skill->getSkill($v1['skillid']);
                $list2[]=[
                    'name'=>$info['name'],
                    'colour_font'=>$info['colour_font'],
                    'colour_bg'=>$info['colour_bg'],
                ];
            }
            
            $v['list']=$list2;
            
            unset($v['birthday']);
            
            $list[$k]=$v;
        }

		return $list;
	}
	
	public function getActive($limit){
		$model = new Model_Home();
		$list= $model->getActive($limit);
		return $list;
	}

    /* 搜索用户 */
	public function searchUser($keyword,$p='1') {

        $model = new Model_Home();
        $list= $model->searchUser($keyword,$p);

        foreach($list as $k=>$v){
            $v=\App\handleUser($v);
            unset($v['birthday']);
            $list[$k]=$v;
        }

		return $list;
	}

	
}
