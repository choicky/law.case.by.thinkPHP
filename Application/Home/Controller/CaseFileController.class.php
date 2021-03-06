<?php
namespace Home\Controller;
use Think\Controller;

class CaseFileController extends Controller {
    
	//新增
	public function add(){
	
		$case_id	=	trim(I('post.case_id'));
		$map_case['case_id']	=	$case_id;
		$condition_case	=	M('Case')->where($map_case)->find();
		if(!is_array($condition_case)){
			$this->error('案件编号不正确');
		}
		
		$Model	=	D('CaseFile');
		if (!$Model->create()){ // 创建数据对象
			 // 如果创建失败 表示验证没有通过 输出错误提示信息
			 $this->error($Model->getError());
			 //exit($Model->getError());
		}else{
			 // 验证通过 写入新增数据
			 $result	=	$Model->add();		 
		}
		if(false !== $result){
			$this->success('新增成功', U('CaseFile/view','case_id='.$case_id));
		}else{
			$this->error('增加失败');
		}
	}
	
  
  	//批量增加商标续展提醒	
	public function addRenewalNotice(){
		
		//接收参数
		$case_id	=	trim(I('post.case_id'));
		$case_type_name	=	trim(I('post.case_type_name'));
		$application_number	=	trim(I('post.application_number'));
		$issue_date	=	trim(I('post.issue_date'));
        $annual_number	=	trim(I('post.annual_number'));
        //dump($annual_number);
    
		//检查合法性
		if(!$issue_date){
			$this->error('未填写发证日');
		}
    
    if(!$application_number){
			$this->error('未填写申请号');
		}
    
    if(!(false	!==	strpos($case_type_name,'商标'))){
      $this->error('案件类型未含“商标”二字，不能增加续展提醒任务');
		}

		//计算出下一次续展的期限日
        $renewal_date	=	strtotime ("+".$annual_number." year", $issue_date);
        //dump(date( "Y-m-d", $renewal_date ));
    
        
    //找出提前01个月提醒续展的文件任务的 file_type_id
    $map_file_type[1]['file_type_name'] =array('like',array('%商标%','%01%','%月%','%续展%'),'AND');
    $file_type_list[1]	=	M('FileType')->where($map_file_type[1])->find();
    $file_type_id[1]  = $file_type_list[1]['file_type_id'];
    //dump($file_type_id[1]);
    
    //找出提前01个月提醒续展的 due_date
    $due_date[1]  = strtotime('-1 month',$renewal_date);
    //dump(date( "Y-m-d", $due_date[1] ));
    
    //找出提前06个月提醒续展的文件任务的 file_type_id
    $map_file_type[2]['file_type_name'] =array('like',array('%商标%','%06%','%月%','%续展%'),'AND');
    $file_type_list[2]	=	M('FileType')->where($map_file_type[2])->find();
    $file_type_id[2]  = $file_type_list[2]['file_type_id'];
    //dump($file_type_id[2]);
    
    //找出提前06个月提醒续展的 due_date
    $due_date[2]  = strtotime('-6 month',$renewal_date);
    //dump(date( "Y-m-d", $due_date[2] ));
    
    //找出提前09个月提醒续展的文件任务的 file_type_id
    $map_file_type[3]['file_type_name'] =array('like',array('%商标%','%09%','%月%','%续展%'),'AND');
    $file_type_list[3]	=	M('FileType')->where($map_file_type[3])->find();
    $file_type_id[3]  = $file_type_list[3]['file_type_id'];
    //dump($file_type_id[3]);
    
    //找出提前09个月提醒续展的 due_date
    $due_date[3]  = strtotime('-9 month',$renewal_date);
    //dump(date( "Y-m-d", $due_date[3] ));
    
    //找出提前12个月提醒续展的文件任务的 file_type_id
    $map_file_type[4]['file_type_name'] =array('like',array('%商标%','%12%','%月%','%续展%'),'AND');
    $file_type_list[4]	=	M('FileType')->where($map_file_type[4])->find();
    $file_type_id[4]  = $file_type_list[4]['file_type_id'];
    //dump($file_type_id[4]);
    
    //找出提前12个月提醒续展的 due_date
    $due_date[4]  = strtotime('-12 month',$renewal_date);
    //dump(date( "Y-m-d", $due_date[4] ));
    
    //找出 提交商标续展文件的 file_type_id
    $map_file_type[5]['file_type_name'] =array('like',array('%商标%','%提交%','%续展%'),'AND');
    $file_type_list[5]	=	M('FileType')->where($map_file_type[5])->find();
    $file_type_id[5]  = $file_type_list[5]['file_type_id'];
    //dump($file_type_id[5]);
    
    //找出提前12个月提醒续展的 due_date
    $due_date[5]  = strtotime('-1 day',$renewal_date);
    //dump(date( "Y-m-d", $due_date[5] ));
    
    //构造数组 & 写入
		for($i=1;$i<6;$i++){
      $data_case_file['case_id']	=	$case_id;
      $data_case_file['file_type_id']	=	$file_type_id[$i];
      $data_case_file['due_date']	=	$due_date[$i];
      
      //写入对应的提醒任务
      $result_case_file	=	M('CaseFile')->add($data_case_file);
      if(!$result_case_file){			
        $this->error('增加失败');			
      }
    }
    
    $this->success('商标续展提醒任务增加完成，请检查是否正确', U('Case/view','case_id='.$case_id));

		
	}
  
    	//批量增加PCT提醒	
	public function addPctNotice(){
		
		//接收参数
		$case_id	=	trim(I('post.case_id'));
		$our_ref	=	trim(I('post.our_ref'));
		$application_number	=	trim(I('post.application_number'));
		$application_date	=	trim(I('post.application_date'));
    
		//检查合法性
		if(!(false	!==	strpos($our_ref,'PCT'))){
      $this->error('案号未含“PCT”，不能增加PCT的提醒任务');
		}
    
    //定义查询并取出对应的优先权信息，从而确定最早的优先权日
		$map['case_id']	=	$case_id;
		$case_priority_list	=	D('CasePriorityView')->where($map)->listAll();
    if(is_array($case_priority_list)){
      $priority_date = $case_priority_list[0]['priority_date'];
    }else{
      $priority_date = $application_date;
    }
    if(!$priority_date){
			$this->error('本案未填写优先权日、也未填写PCT的申请日，无法建立进入国家阶段的提醒任务');
		}

  
    //找出提前18个月提醒续展的文件任务的 file_type_id
    $map_file_type[1]['file_type_name'] =array('like',array('%PCT%','%18%','%月%','%提醒%'),'AND');
    $file_type_list[1]	=	M('FileType')->where($map_file_type[1])->find();
    $file_type_id[1]  = $file_type_list[1]['file_type_id'];
    
    //找出提前18个月提醒续展的 due_date
    $due_date[1]  = strtotime('+18 month',$priority_date);
    
    //找出提前24个月提醒续展的文件任务的 file_type_id
    $map_file_type[2]['file_type_name'] =array('like',array('%PCT%','%24%','%月%','%提醒%'),'AND');
    $file_type_list[2]	=	M('FileType')->where($map_file_type[2])->find();
    $file_type_id[2]  = $file_type_list[2]['file_type_id'];
    
    //找出提前24个月提醒续展的 due_date
    $due_date[2]  = strtotime('+24 month',$priority_date);
    
    //找出提前28个月提醒续展的文件任务的 file_type_id
    $map_file_type[3]['file_type_name'] =array('like',array('%PCT%','%28%','%月%','%提醒%'),'AND');
    $file_type_list[3]	=	M('FileType')->where($map_file_type[3])->find();
    $file_type_id[3]  = $file_type_list[3]['file_type_id'];
    
    //找出提前28个月提醒续展的 due_date
    $due_date[3]  = strtotime('+28 month',$priority_date);
        
    //构造数组 & 写入
		for($i=1;$i<4;$i++){
      $data_case_file['case_id']	=	$case_id;
      $data_case_file['file_type_id']	=	$file_type_id[$i];
      $data_case_file['due_date']	=	$due_date[$i];
      
      //写入对应的提醒任务
      $result_case_file	=	M('CaseFile')->add($data_case_file);
      if(!$result_case_file){			
        $this->error('增加失败');			
      }
    }
    
    $this->success('PCT进入国家阶段提醒任务增加完成，请检查是否正确', U('Case/view','case_id='.$case_id));

		
	}
  
	//更新	
	public function update(){
		if(IS_POST){
			$case_file_data['case_file_id']	=	trim(I('post.case_file_id'));
			$case_file_data['case_id']	=	trim(I('post.case_id'));
			$case_file_data['file_type_id']	=	trim(I('post.file_type_id'));
			$case_file_data['oa_date']	=	strtotime(trim(I('post.oa_date')));			
			$case_file_data['due_date']	=	strtotime(trim(I('post.due_date')));
			$case_file_data['completion_date']	=	strtotime(trim(I('post.completion_date')));
			$case_file_data['service_fee']	=	100*trim(I('post.service_fee'));
			$case_file_data['bill_id']	=	trim(I('post.bill_id'));
			$case_file_data['invoice_id']	=	trim(I('post.invoice_id'));
			$case_file_data['claim_id']	=	trim(I('post.claim_id'));
			$case_file_data['inner_balance_id']	=	trim(I('post.inner_balance_id'));
			$case_file_data['cost_amount']	=	100*trim(I('post.cost_amount'));
			
			$result	=	M('CaseFile')->save($case_file_data);
			
			if(false !== $result){
				$this->success('修改成功', U('Case/view','case_id='.$case_file_data['case_id']));
			}else{
				$this->error('修改失败');
			}
			
		} else{
			$case_file_id = I('get.case_file_id',0,'int');

			if(!$case_file_id){
				$this->error('未指明要编辑的文件编号');
			}

			$case_file_list = M('CaseFile')->getByCaseFileId($case_file_id);

			$this->assign('case_file_list',$case_file_list);
			
			//获取本条费用的案子的 $case_type_name
			$case_type_name	=	D('CaseFile')->returnCaseTypeName($case_file_id);
			//获取本案子的 $case_type_name
			
		
			//根据 $case_type_name 是否包含“法律事务”来构造对应的检索条件
			if(false	!==	strpos($case_type_name,'法律事务')){
				$map_file_type['file_type_name']	=	array('like','%法律事务%');
			}else{
				$map_file_type['file_type_name']	=	array('notlike','%法律事务%');
			}
							
			//取出 FileType 表的内容以及数量
			$file_type_list	=	D('FileType')->where($map_file_type)->listBasic();
			$file_type_count	=	count($file_type_list);
			$this->assign('file_type_list',$file_type_list);
			$this->assign('file_type_count',$file_type_count);
			
			
			//取出其他变量
			$row_limit  =   C("ROWS_PER_SELECT");
			$this->assign('row_limit',$row_limit);
			
			$this->display();
		}
	}
	
	//删除
	public function delete(){
		if(IS_POST){
			
			//通过 I 方法获取 post 过来的 case_file_id 和 case_id
			$case_file_id	=	trim(I('post.case_file_id'));
			$case_id	=	trim(I('post.case_id'));
			$no_btn	=	I('post.no_btn');
			$yes_btn	=	I('post.yes_btn');

			if(1==$no_btn){
				$this->success('取消删除', U('Case/view','case_id='.$case_id));
			}
			
			if(1==$yes_btn){
				
				$map['case_file_id']	=	$case_file_id;
				
				//获取基本信息
				$case_file_list	=	M('CaseFile')->getByCaseFileId($map['case_file_id']);
				
				//判断
				if($case_file_list['inner_balance_id']	>	0){
					$this->error('本费用已关联到内部结算单，不可删除', U('Case/view','case_id='.$case_id));
				}
				if($case_file_list['cost_amount']	>	0){
					$this->error('本费用已关联到内部结算单，不可删除', U('Case/view','case_id='.$case_id));
				}
				if($case_file_list['bill_id']	>	0){
					$this->error('本费用已关联到账单，不可删除', U('Case/view','case_id='.$case_id));
				}				
				if($case_file_list['invoice_id']	>	0){
					$this->error('本费用已关联到发票，不可删除', U('Case/view','case_id='.$case_id));
				}
				if($case_file_list['claim_id']	>	0){
					$this->error('本费用已关联到收支认领单，不可删除', U('Case/view','case_id='.$case_id));
				}

				$result = M('CaseFile')->where($map)->delete();
				if($result){
					$this->success('删除成功', U('Case/view','case_id='.$case_id));
				}
			}
			
		} else{
			$case_file_id = I('get.case_file_id',0,'int');

			if(!$case_file_id){
				$this->error('未指明要删除的流水');
			}
			
			$case_file_list = D('CaseFileView')->field(true)->getByCaseFileId($case_file_id);
			
			$this->assign('case_file_list',$case_file_list);

      //返回当前时间
      $today = time();
      $this->assign('today',$today);			

			$this->display();
		}
	}
	
	//查看主键为 $case_id 的收支流水的所有 case_file
	public function view(){
		$case_id = I('get.case_id',0,'int');

		if(!$case_id){
			$this->error('未指明要查看的案件');
		}

		//取出案件的基本信息
		$case_list = D('CaseView')->field(true)->getByCaseId($case_id);
		
		//定义查询
		$map['case_id']	=	$case_id;
		
		//取出文件信息
		$case_file_list	=	D('CaseFileView')->where($map)->listAll();
		$case_file_count	=	count($case_file_list);
		
		$this->assign('case_list',$case_list);
		$this->assign('case_file_list',$case_file_list);
		$this->assign('case_file_count',$case_file_count);
		
		//获取本案子的 $case_type_name
		$case_type_name	=	$case_list['case_type_name'];
		
		//根据 $case_type_name 是否包含“法律事务”来构造对应的检索条件
		if(false	!==	strpos($case_type_name,'法律事务')){
			$map_file_type['file_type_name']	=	array('like','%法律事务%');
		}else{
			$map_file_type['file_type_name']	=	array('notlike','%法律事务%');
            $annual_list	=	tenYearOption(20);
		}
        
        //输出 annual_list
		$this->assign('annual_list',$annual_list);
			
		//取出 FileType 表的内容以及数量
		$file_type_list	=	D('FileType')->where($map_file_type)->listBasic();
		$file_type_count	=	count($file_type_list);
		$this->assign('file_type_list',$file_type_list);
		$this->assign('file_type_count',$file_type_count);
		
		//取出其他变量
		$row_limit  =   C("ROWS_PER_SELECT");
		$today	=	time();

		$this->assign('row_limit',$row_limit);
		$this->assign('today',$today);
		$this->display();
	}
	
	//搜索法律事务文件
	public function searchLawsFileByDueDate(){
		
		//取出 Client 表的内容以及数量
		$client_list	=	D('Client')->listBasic();
		$this->assign('client_list',$client_list);
		
		//取出 Member 表的内容以及数量
		$member_list	=	D('Member')->listBasic();
		$this->assign('member_list',$member_list);
		
		//默认查询前3个月、未来3个月期限
		$start_due_date	=	strtotime('-3 month');;
		$end_due_date	=	strtotime('+3 month');
		$this->assign('start_due_date',$start_due_date);
		$this->assign('end_due_date',$end_due_date);
		
		if(IS_POST){
			
			//接收搜索参数			
			$client_id	=	I('post.client_id','0','int');
			$applicant_id	=	I('post.applicant_id','0','int');
			$follower_id	=	I('post.follower_id','0','int');
			$is_filed	=	I('post.is_filed','0','int');
			$is_paid	=	I('post.is_paid','0','int');
			
			$end_due_date	=	trim(I('post.end_due_date'));
			$end_due_date	=	$end_due_date ? strtotime($end_due_date) : strtotime('+3 month',time());
			$start_due_date	=	trim(I('post.start_due_date'));
			$start_due_date	=	$start_due_date ? strtotime($start_due_date) : strtotime('-6 month',$end_due_date);
			
			//构造 maping
			if($client_id){
				$map_case_file['client_id']	=	$client_id;
			}
			if($applicant_id){
				$map_case_file['applicant_id']	=	$applicant_id;
			}
			if($follower_id){
				$map_case_file['follower_id']	=	$follower_id;
			}	
			
			if(1==$is_filed){
				$map_case_file['completion_date']	=	array('GT',1);
			}
			if(2==$is_filed){
				$map_case_file['completion_date']	=	array('LT',1);
			}
			
			$map_case_file['due_date']	=	array('between',$start_due_date.','.$end_due_date);
			
			//获取法律事务的 case_type_id 集合
			$case_type_list	=	D('CaseType')->listLawsCaseTypeId();
			$map_case_file['case_type_id']  = array('in',$case_type_list);
			
			//根据是否开了账单进行处理
			//不限是否开了账单
			if(0==$is_billed){
				$case_file_list = D('CaseFileView')->field(true)->where($map_case_file)->listAll();
			}			
			//开了账单
			if(1==$is_billed){
				$map_case_file['bill_id']  = array('GT',0);
				$case_file_list = D('CaseFileView')->field(true)->where($map_case_file)->listAll();
			}
			//未开账单
			if(2==$is_billed){
				$map_case_file['bill_id']  = array('LT',0);
				$case_file_list = D('CaseFileView')->field(true)->where($map_case_file)->listAll();
			}
			
			//返回搜索结果
			$case_file_count	=	count($case_file_list);
			$this->assign('case_file_list',$case_file_list);
			$this->assign('case_file_count',$case_file_count);
			
			//返回统计结果
			$total_service_fee	=	0;
			for($j=0;$j<$case_file_count;$j++){
				$total_service_fee	+=	$case_file_list[$j]['service_fee'];
			}
			$this->assign('total_service_fee',$total_service_fee);
			
			//返回所接受的检索条件
			$this->assign('client_id',$client_id);
			$this->assign('applicant_id',$applicant_id);
			$this->assign('follower_id',$follower_id);
			$this->assign('is_filed',$is_filed);
			$this->assign('is_billed',$is_billed);
			$this->assign('start_due_date',$start_due_date);
			$this->assign('end_due_date',$end_due_date);
		
		} 
	//返回当前时间
  $today = time();
  $this->assign('today',$today);
    
	$this->display();
	}
	
	//搜索盈方文件
	public function searchIPinfoFileByDueDate(){
		
		//取出 Client 表的内容以及数量
		$client_list	=	D('Client')->listBasic();
		$this->assign('client_list',$client_list);
		
		//取出 Member 表的内容以及数量
		$member_list	=	D('Member')->listBasic();
		$this->assign('member_list',$member_list);
		
		//默认查询前3个月、未来3个月期限
		$start_due_date	=	strtotime('-3 month');;
		$end_due_date	=	strtotime('+3 month');
		$this->assign('start_due_date',$start_due_date);
		$this->assign('end_due_date',$end_due_date);
		
		if(IS_POST){
			
			//接收搜索参数			
			$client_id	=	I('post.client_id','0','int');
			$applicant_id	=	I('post.applicant_id','0','int');
			$follower_id	=	I('post.follower_id','0','int');
			$is_filed	=	I('post.is_filed','0','int');
			$is_paid	=	I('post.is_paid','0','int');
			
			$end_due_date	=	trim(I('post.end_due_date'));
			$end_due_date	=	$end_due_date ? strtotime($end_due_date) : strtotime('+3 month',time());
			$start_due_date	=	trim(I('post.start_due_date'));
			$start_due_date	=	$start_due_date ? strtotime($start_due_date) : strtotime('-6 month',$end_due_date);
			
			//构造 maping
			if($client_id){
				$map_case_file['client_id']	=	$client_id;
			}
			if($applicant_id){
				$map_case_file['applicant_id']	=	$applicant_id;
			}
			if($follower_id){
				$map_case_file['follower_id']	=	$follower_id;
			}	
			
			if(1==$is_filed){
				$map_case_file['completion_date']	=	array('GT',1);
			}
			if(2==$is_filed){
				$map_case_file['completion_date']	=	array('LT',1);
			}
			
			$map_case_file['due_date']	=	array('between',$start_due_date.','.$end_due_date);
			
			//获取法律事务的 case_type_id 集合
			$case_type_list	=	D('CaseType')->listIPinfoCaseTypeId();
			$map_case_file['case_type_id']  = array('in',$case_type_list);
			
			
			//根据是否开了账单进行处理
			//不限是否开了账单
			if(0==$is_billed){
				$case_file_list = D('CaseFileView')->field(true)->where($map_case_file)->listAll();
			}			
			//开了账单
			if(1==$is_billed){
				$map_case_file['bill_id']  = array('GT',0);
				$case_file_list = D('CaseFileView')->field(true)->where($map_case_file)->listAll();
			}
			//未开账单
			if(2==$is_billed){
				$map_case_file['bill_id']  = array('LT',0);
				$case_file_list = D('CaseFileView')->field(true)->where($map_case_file)->listAll();
			}
			
			//返回搜索结果
			$case_file_count	=	count($case_file_list);
			$this->assign('case_file_list',$case_file_list);
			$this->assign('case_file_count',$case_file_count);
			
			//返回统计结果
			$total_service_fee	=	0;
			for($j=0;$j<$case_file_count;$j++){
				$total_service_fee	+=	$case_file_list[$j]['service_fee'];
			}
			$this->assign('total_service_fee',$total_service_fee);
			
			//返回所接受的检索条件
			$this->assign('client_id',$client_id);
			$this->assign('applicant_id',$applicant_id);
			$this->assign('follower_id',$follower_id);
			$this->assign('is_filed',$is_filed);
			$this->assign('start_due_date',$start_due_date);
			$this->assign('end_due_date',$end_due_date);

		
		} 
	      
  //返回当前时间
  $today = time();
  $this->assign('today',$today);
      
	$this->display();
	}
	
	//搜索
	public function searchLawsFileByCompletionDate(){
		
		//取出 Client 表的内容以及数量
		$client_list	=	D('Client')->listBasic();
		$this->assign('client_list',$client_list);
		
		//取出 Member 表的内容以及数量
		$member_list	=	D('Member')->listBasic();
		$this->assign('member_list',$member_list);
		
		//默认查询最近1个月
		$start_completion_date	=	strtotime('-1 month');
		$end_completion_date	=	time();
		$this->assign('start_completion_date',$start_completion_date);
		$this->assign('end_completion_date',$end_completion_date);
		
		if(IS_POST){
			
			//接收搜索参数			
			$client_id	=	I('post.client_id','0','int');
			$applicant_id	=	I('post.applicant_id','0','int');
			$follower_id	=	I('post.follower_id','0','int');
			$is_filed	=	I('post.is_filed','0','int');
			$is_billed	=	I('post.is_billed','0','int');
			$end_completion_date	=	trim(I('post.end_completion_date'));
			$end_completion_date	=	$end_completion_date ? strtotime($end_completion_date) : time();			
			$start_completion_date	=	trim(I('post.start_completion_date'));
			$start_completion_date	=	$start_completion_date ? strtotime($start_completion_date) : strtotime('-1 month',$end_completion_date);			
			
			//构造 maping
			if($client_id){
				$map_case_file['client_id']	=	$client_id;
			}
			if($applicant_id){
				$map_case_file['applicant_id']	=	$applicant_id;
			}
			if($follower_id){
				$map_case_file['follower_id']	=	$follower_id;
			}	
			
			$map_case_file['completion_date']	=	array('between',$start_completion_date.','.$end_completion_date);
			
			//获取法律事务的 case_type_id 集合
			$case_type_list	=	D('CaseType')->listLawsCaseTypeId();
			$map_case_file['case_type_id']  = array('in',$case_type_list);
			
			//根据是否开了账单进行处理
			//不限是否开了账单
			if(0==$is_billed){
				$case_file_list = D('CaseFileView')->field(true)->where($map_case_file)->listAll();
			}			
			//开了账单
			if(1==$is_billed){
				$map_case_file['bill_id']  = array('GT',0);
				$case_file_list = D('CaseFileView')->field(true)->where($map_case_file)->listAll();
			}
			//未开账单
			if(2==$is_billed){
				$map_case_file['bill_id']  = array('LT',1);
				$case_file_list = D('CaseFileView')->field(true)->where($map_case_file)->listAll();
			}
			
			//返回搜索结果
			$case_file_count	=	count($case_file_list);
			$this->assign('case_file_list',$case_file_list);
			$this->assign('case_file_count',$case_file_count);
			
			//返回统计结果
			$total_service_fee	=	0;
			for($j=0;$j<$case_file_count;$j++){
				$total_service_fee	+=	$case_file_list[$j]['service_fee'];
			}
			$this->assign('total_service_fee',$total_service_fee);
			
			//返回所接受的检索条件
			$this->assign('client_id',$client_id);
			$this->assign('applicant_id',$applicant_id);
			$this->assign('follower_id',$follower_id);
			$this->assign('is_filed',$is_filed);
			$this->assign('is_billed',$is_billed);
			$this->assign('start_completion_date',$start_completion_date);
			$this->assign('end_completion_date',$end_completion_date);
		
		} 
	//返回当前时间
  $today = time();
  $this->assign('today',$today);
  
	$this->display();
	}
	
	//搜索
	public function searchIPinfoFileByCompletionDate(){
		
		//取出 Client 表的内容以及数量
		$client_list	=	D('Client')->listBasic();
		$this->assign('client_list',$client_list);
		
		//取出 Member 表的内容以及数量
		$member_list	=	D('Member')->listBasic();
		$this->assign('member_list',$member_list);
		
		//默认查询最近1个月
		$start_completion_date	=	strtotime('-1 month');
		$end_completion_date	=	time();
		$this->assign('start_completion_date',$start_completion_date);
		$this->assign('end_completion_date',$end_completion_date);
		
		if(IS_POST){
			
			//接收搜索参数			
			$client_id	=	I('post.client_id','0','int');
			$applicant_id	=	I('post.applicant_id','0','int');
			$follower_id	=	I('post.follower_id','0','int');
			$is_filed	=	I('post.is_filed','0','int');
			$is_billed	=	I('post.is_billed','0','int');
			$end_completion_date	=	trim(I('post.end_completion_date'));
			$end_completion_date	=	$end_completion_date ? strtotime($end_completion_date) : time();			
			$start_completion_date	=	trim(I('post.start_completion_date'));
			$start_completion_date	=	$start_completion_date ? strtotime($start_completion_date) : strtotime('-1 month',$end_completion_date);
			
			//构造 maping
			if($client_id){
				$map_case_file['client_id']	=	$client_id;
			}
			if($applicant_id){
				$map_case_file['applicant_id']	=	$applicant_id;
			}
			if($follower_id){
				$map_case_file['follower_id']	=	$follower_id;
			}	
			
			$map_case_file['completion_date']	=	array('between',$start_completion_date.','.$end_completion_date);
			
			//获取法律事务的 case_type_id 集合
			$case_type_list	=	D('CaseType')->listIPinfoCaseTypeId();
			$map_case_file['case_type_id']  = array('in',$case_type_list);
			
			//根据是否开了账单进行处理
			//不限是否开了账单
			if(0==$is_billed){
				$case_file_list = D('CaseFileView')->field(true)->where($map_case_file)->listAll();
			}			
			//开了账单
			if(1==$is_billed){
				$map_case_file['bill_id']  = array('GT',0);
				$case_file_list = D('CaseFileView')->field(true)->where($map_case_file)->listAll();
			}
			//未开账单
			if(2==$is_billed){
				$map_case_file['bill_id']  = array('LT',0);
				$case_file_list = D('CaseFileView')->field(true)->where($map_case_file)->listAll();
			}
			
			//返回搜索结果
			$case_file_count	=	count($case_file_list);
			$this->assign('case_file_list',$case_file_list);
			$this->assign('case_file_count',$case_file_count);
			
			//返回统计结果
			$total_service_fee	=	0;
			for($j=0;$j<$case_file_count;$j++){
				$total_service_fee	+=	$case_file_list[$j]['service_fee'];
			}
			$this->assign('total_service_fee',$total_service_fee);
			
			//返回所接受的检索条件
			$this->assign('client_id',$client_id);
			$this->assign('applicant_id',$applicant_id);
			$this->assign('follower_id',$follower_id);
			$this->assign('is_filed',$is_filed);
			$this->assign('start_completion_date',$start_completion_date);
			$this->assign('end_completion_date',$end_completion_date);
		
		} 
    
	//返回当前时间
  $today = time();
  $this->assign('today',$today);
  
	$this->display();
	}
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPageLawsFile(){
		$p	= I("p",1,"int");
		$page_limit  =   C("RECORDS_PER_PAGE");
		
		//获取法律事务的 case_type_id 集合
		$case_type_list	=	D('CaseType')->listLawsCaseTypeId();
		$map_case_file['case_type_id']  = array('in',$case_type_list);
		
		$case_file_list = D('CaseFileView')->listPageSearch($p,$limit,$map_case_file);
		$this->assign('case_file_list',$case_file_list['list']);
		$this->assign('case_file_page',$case_file_list['page']);
		$this->assign('case_file_count',$case_file_list['count']);
		
    //返回当前时间
    $today = time();
    $this->assign('today',$today);
    
		$this->display();
	}
	
	//分页显示，其中，$p为当前分页数，$limit为每页显示的记录数
	public function listPageIPinfoFile(){
		$p	= I("p",1,"int");
		$page_limit  =   C("RECORDS_PER_PAGE");
		
		//获取法律事务的 case_type_id 集合
		$case_type_list	=	D('CaseType')->listIPinfoCaseTypeId();
		$map_case_file['case_type_id']  = array('in',$case_type_list);
		
		$case_file_list = D('CaseFileView')->listPageSearch($p,$limit,$map_case_file);
		$this->assign('case_file_list',$case_file_list['list']);
		$this->assign('case_file_page',$case_file_list['page']);
		$this->assign('case_file_count',$case_file_list['count']);
		
    //返回当前时间
    $today = time();
    $this->assign('today',$today);
    
		$this->display();
	}
	
}