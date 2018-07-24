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



class UptxAction extends BaseAction
{
    public function index(){
		//global $new_config_info;
		global $user_tel;
		if(!$user_tel){
			echo '未登录';
			exit;
		}
		$nnn=md5($user_tel.(cookie('admin_userid')+0));
		if(move_uploaded_file($_FILES["file"]["tmp_name"],'tx/'.$nnn.'.jpg')){
			echo 'upok';
			exit;
		}
		echo '上传失败';
		exit;
		
		
		dump($_FILES);exit;
		$blist['img_path']=$new_config_info['gz_descriptionhuypp'];
		$this->assign('blist',$blist);
		$this->display('./GZphp/Tpl/Home/Default/Memberuser_index.html');
    }

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>