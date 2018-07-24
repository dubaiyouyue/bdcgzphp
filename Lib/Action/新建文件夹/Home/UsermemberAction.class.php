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



class UsermemberAction extends BaseAction
{
    public function re(){
		/*
		user
		tel //手机号
		admin_qq //盐
		admin_msn //登录验证
		password //密码
		*/
		$retel=$_SESSION['sms_tel'];
		$recode=$_POST['recode'];
		$repass=$_POST['repass'];
		if(empty($retel) || empty($recode) || empty($repass)) {// || empty($_POST['message'])
            //$this->error(L('信息填写不完整!'));
            exit('1');
        }
		if(!$recode || $recode!=$_SESSION['sms_code']){
			exit('3'); //短信验证码错误
		}else if(($_SESSION['sms_time']+330)<time()){
			exit('4'); //短信验证码过期
		}
		$add['tel']=$retel;
		
		
				$sw['tel']=$retel;
				$admin_userid=M('user')->where($sw)->limit(1)->find();
				if($admin_userid['id']){
					echo '手机号已经注册';
					exit;
				}
		
		$admin_qq=rand(100000,999999);
		$add['admin_qq']=$admin_qq;
		$add['password']=md5(md5($repass).$admin_qq);
		$admin_msn=md5(md5($admin_qq).md5($repass));
		$add['admin_msn']=$admin_msn;
		$add['register_time']=time();//date('Y-m-d H:i:s');
		$admin_userid=M('user')->add($add);
		if($admin_userid){
			unset($_SESSION['sms_code']);
			unset($_SESSION['sms_tel']);
			unset($_SESSION['sms_time']);
			cookie('admin_msn', $admin_msn, time()+36000000);
			cookie('admin_userid', $admin_userid, time()+36000000);
			echo 'addok';
		}
    }

	function loginc(){
		$lm=$_GET['lm'];
		if($lm=='pass'){
			$tel=$_POST['passtel']+0;
			$pass=$_POST['lpass'];
			$recodeluopasslogin=$_POST['recodeluopasslogin'];
				if(empty($tel) || empty($pass) || empty($recodeluopasslogin)) {// || empty($_POST['message'])
					//$this->error(L('信息填写不完整!'));
					exit('信息填写不完整');
				}
		
				//人机验证
					$url = 'https://captcha.luosimao.com/api/site_verify';
					$data =array('api_key'=>LUOSIMAO_API_KEY,'response'=>$recodeluopasslogin);
					$result=$this->vpost($url,$data);
					$result=json_decode($result,true);
					$result=$result['res'];
					//echo $_POST['luotest_response'];
					//echo $result;exit;
					if($result!='success'){// && !$_SESSION['sms_code'] && !$_POST['nameggg']
						//unset($_SESSION['seccode']);
						exit('人机验证失败');
						//redirect('验证码不正确！', $pageurl);
						//redirect('人机验证失败！', '?module=login');
					}
				//end
				
				$sw['tel']=$tel;
				$admin_userid=M('user')->where($sw)->limit(1)->find();
				if($admin_userid['id']){
					$postpass=md5(md5($pass).$admin_userid['admin_qq']);
					if($postpass==$admin_userid['password']){
						
						$admin_msn=md5(md5(md5($tel).rand(100000,999999).md5(time())).rand(100000,999999).time());
						$add['admin_msn']=$admin_msn;
						$admin_userida=M('user')->where('tel='.$tel)->limit(1)->save($add);
						if($admin_userida){
							cookie('admin_msn', $admin_msn, time()+36000000);
							cookie('admin_userid', $admin_userid['id'], time()+36000000);
							echo 'addok';
							exit;
						}else{
							echo '服务器繁忙';
							exit;
							
						}
					}else{
						echo '账号密码错误';
						exit;
					}
				}else{
					echo '手机号未注册';
					exit;
				}
			
		}else{
			$tel=$_SESSION['sms_tel'];
			$recode=$_POST['smscode'];
				if(!$recode || $recode!=$_SESSION['sms_code']){
					exit('3'); //短信验证码错误
				}else if(($_SESSION['sms_time']+90)<time()){
					exit('4'); //短信验证码过期
				}
			
			$smscode=md5($recode);
				$sw['tel']=$tel;
				$admin_userid=M('user')->where($sw)->limit(1)->find();
				if($admin_userid['id']){
					if($smscode==$admin_userid['admin_taobao']){
						$admin_msn=md5(md5(md5($tel).rand(100000,999999).md5(time())).rand(100000,999999).time());
						$add['admin_msn']=$admin_msn;
						$admin_userida=M('user')->where('tel='.$tel)->limit(1)->save($add);
						if($admin_userida){
							cookie('admin_msn', $admin_msn, time()+36000000);
							cookie('admin_userid', $admin_userid['id'], time()+36000000);
							echo 'addok';
							exit;
						}else{
							echo '服务器繁忙';
							exit;
							
						}
					}else{
						echo '验证码错误';
						exit;
					}
				}else{
					echo '非法提交';
					exit;
				}
			
		}
		
	}
	
	//修改密码
	function epass(){
		//$opass=$_POST['opass'];
		
		
		global $user_tel;
		
		if(!$user_tel){
			echo '未登录';
			header('Location:/');
			exit;
		}
		
		
		$npass=$_POST['npass'];
		if(!$npass){
			echo '信息填写不完整';
			exit;
		}
			
			/*$w['id']=$admin_userid;
				$admin_userid=M('user')->where($w)->limit(1)->find();
				
				
				$opass=md5(md5($opass).$admin_userid['admin_qq']);
				if(!$opass!=$admin_userid['password']){
					echo '旧密码错误';
					exit;
				}*/
				
				$admin_qq=rand(100000,999999);
				$se['admin_qq']=$admin_qq;
				$se['password']=md5(md5($npass).$admin_qq);
				$se['admin_msn']='';
				$se['admin_taobao']='';
				
				$admin_useridea=M('user')->where('tel='.$user_tel)->limit(1)->save($se);
				if($admin_useridea){
					echo 'addok';
					exit;
				}else{
					echo '修改密码失败！';
					exit;
				}

	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>