<?php
// +----------------------------------------------------------------------
// | This project is based on ThinkPHP 3.2, created by Choicky ZHOU (zhoucaiqi@gmail.com).
// +----------------------------------------------------------------------
// | Choicky ZHOU is a lawyer in China, specialized in IP matters such as patent, trademark and copyright.
// +----------------------------------------------------------------------
// | "Think\Model" is for normal Model, "Think\Model\RelationModel" for relation Model, "Think\Model\ViewModel" for view Model.
// +----------------------------------------------------------------------
// | This file is required by: CaseGroupController,
// +----------------------------------------------------------------------

namespace Home\Model;

use Think\Model\RelationModel;
//因为不需要数据表关联，注释RelationModel
//use Think\Model\RelationModel;

class CaseGroupModel extends RelationModel {
	
	//定义本数据表的数据关联
	protected $_link = array(
		
		'CaseType'	=>	array(							//本数据关联的名称
			'mapping_name'		=>	'CaseType',			//重新定义本数据关联的名称
			'class_name'		=>	'CaseType',			//被关联的数据表
			'mapping_type'		=>	self::HAS_MANY,	//属于关系一对多关联			
			'foreign_key'		=>	'case_group_id',		//外键，
			'mapping_fields'	=>	'case_type_id,case_type_name',	//关联字段
			//'as_fields'			=>	'case_type_name'	//字段别名
		),
		
	);
	
	//返回本数据表中与法律事务有关的数据
	public function listAllLaws() {
		$map['case_group_name']	=	array('like','%法律事务%');
		$order['convert(case_group_name using gb2312)']	=	'asc';
		$data	=	$this->where($map)->order($order)->select();
		return $data;
	}
	
	//返回本数据表中与法律事务有关的数据
	public function listAllIPinfo() {
		$map['case_group_name']	=	array('notlike','%法律事务%');
		$order['convert(case_group_name using gb2312)']	=	'asc';
		$data	=	$this->where($map)->order($order)->select();
		return $data;
	}
		
	//返回本数据表中与商标有关的数据
	public function listAllTrademark() {
		$map['case_group_name']	=	array('like','%商标%');
		$order['convert(case_group_name using gb2312)']	=	'asc';
		$data	=	$this->field(true)->where($map)->order($order)->select();
		return $data;
	}
    
    //返回本数据表中与版权有关的数据
	public function listAllCopyright() {
		$map['case_group_name']	=	array('like','%版权%');
		$order['convert(case_group_name using gb2312)']	=	'asc';
		$data	=	$this->field(true)->where($map)->order($order)->select();
		return $data;
	}
	
			
	//返回本数据表的基本数据
	public function listBasic() {
		$order['convert(case_group_name using gb2312)']	=	'asc';
		$list	=	$this->order($order)->select();
		return $list;
	}

	//返回本数据表的所有数据
	public function listAll() {
		$order['convert(case_group_name using gb2312)']	=	'asc';
		$list	=	$this->relation(true)->order($order)->select();
		return $list;
	}
	
	//分页返回本数据表的所有数据，$current_page 为当前页数，$recodes_per_page 为每页显示的记录条数
	public function listPage($current_page,$recodes_per_page) {
		$order['convert(case_group_name using gb2312)']	=	'asc';
		$data	= $this->relation(true)->order($order)->page($current_page.','.$recodes_per_page)->select();
		
		$count	= $this->count();
		$Page	= new \Think\Page($count,$recodes_per_page);
		$show	= $Page->show();
		
		return array("data"=>$data,"page"=>$show,"count"=>$count);
	}
	
	//更新本数据表中主键为$case_group_id的记录，$data是数组
	public function update($case_group_id,$data){
		$map['case_group_id']	=	$case_group_id;
		$result	=	$this->where($map)->save($data);
		return $result;
	}

}