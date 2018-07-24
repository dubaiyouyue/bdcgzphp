<?php
/**
 * 
 * IndexAction.class.php (前台首页)
 *
 * @package      	GZPHP
 * @author          wen QQ:52009619 <admin@resonance.com.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.resonance.com.cn)
 * @license         http://www.resonance.com.cn/license.txt
 * @version        	GzPHP企业网站管理系统 v2.1 2011-03-01 resonance.com.cn $
 */
 




//import ( "@.ORG.SignatureHelper" );
 
if(!defined("GZPHP")) exit("Access Denied"); 



class MemberuserAction extends BaseAction
{

    public function index(){
		global $new_config_info;
		
		global $user_tel;
		
		if(!$user_tel){
			echo '未登录';
			header('Location:/');
			exit;
		}
		$nnn=md5($user_tel.(cookie('admin_userid')+0)).'.jpg';
		$admin_userid=cookie('admin_userid');
		if(!file_exists('tx/'.$nnn)) $user_tx='/tx/0.jpg';
		else $user_tx='/tx/'.$nnn;
		$this->assign('user_tx',$user_tx);
		
		$mm=$_GET['mm'];
		if($mm=='buy'){
			//获取购买车辆
			$lo=M('user_list')->where('paraid=100 and listid=\''.$admin_userid.'\'')->find();
			$loinfo=str_ireplace('，',',',$lo['info']);
			$low['id']  = array('in',$loinfo);
			$loidlist=M('news')->where($low)->select();
			$this->assign('loidlist',$loidlist);
			$this->assign('loidlisttoname','买车记录');
			//dump($loidlist);exit;
			$this->display('./GZphp/Tpl/Home/Default/Memberuser_buy.html');
			exit;
		}else if($mm=='sel'){
			//获取卖出车辆
			$lo=M('user_list')->where('paraid=103 and listid=\''.$admin_userid.'\'')->find();
			$loinfo=str_ireplace('，',',',$lo['info']);
			$low['id']  = array('in',$loinfo);
			$loidlist=M('news')->where($low)->select();
			$this->assign('loidlist',$loidlist);
			$this->assign('loidlisttoname','卖车记录');
			//dump($loidlist);exit;
			$this->display('./GZphp/Tpl/Home/Default/Memberuser_buy.html');
			exit;
		}
		
			$lo=M('user_list')->where('paraid=103 and listid=\''.$admin_userid.'\'')->find();
			$loinfo=str_ireplace('，',',',$lo['info']);
			$low['id']  = array('in',$loinfo);
			$loidlist=M('news')->where($low)->select();
			$this->assign('loidlist',$loidlist);
			$this->assign('loidlisttoname','卖车记录');
			
		$blist['img_path']=$new_config_info['gz_descriptionhuypp'];
		$this->assign('blist',$blist);
		$this->display('./GZphp/Tpl/Home/Default/Memberuser_index.html');
    }
	function out(){
		global $user_tel;
		
		if(!$user_tel){
			echo '未登录';
			header('Location:/');
			exit;
		}
		
			$add['admin_msn']='';
			$add['admin_taobao']='';
			$admin_userida=M('user')->where('tel='.$user_tel)->limit(1)->save($add);
		
		cookie('admin_msn',null);
		cookie('admin_userid',null);
		header('Location:/');
		exit;
	}
	//修改密码
	function epass(){
		global $new_config_info;
		
		global $user_tel;
		
		if(!$user_tel){
			echo '未登录';
			header('Location:/');
			exit;
		}
		$nnn=md5($user_tel.(cookie('admin_userid')+0)).'.jpg';
		if(!file_exists('tx/'.$nnn)) $user_tx='/tx/0.jpg';
		else $user_tx='/tx/'.$nnn;
		$this->assign('user_tx',$user_tx);
		
		
		//dump($config_info);exit;
		$blist['img_path']=$new_config_info['gz_descriptionhuypp'];
		$this->assign('blist',$blist);
		$this->display('./GZphp/Tpl/Home/Default/Memberuser_epass.html');
	}
	
	
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>