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



class AlismsmAction extends BaseAction
{
    public function index(){
		
		$tel=$_POST['retel'];
		if(!$tel) $tel=$_POST['lotel'];
		$tel=$tel+0;
		$luotest_response=$_POST['recodeluo'];
		if(!$luotest_response) $luotest_response=$_POST['locodeluo'];
		
        if(empty($tel)) {// || empty($_POST['message'])
            //$this->error(L('信息填写不完整!'));
            exit('1');
        }
		
		//人机验证
			$url = 'https://captcha.luosimao.com/api/site_verify';
			$data =array('api_key'=>LUOSIMAO_API_KEY,'response'=>$luotest_response);
			$result=$this->vpost($url,$data);
			$result=json_decode($result,true);
			$result=$result['res'];
			//echo $_POST['luotest_response'];
			//echo $result;exit;
			if($result!='success'){// && !$_SESSION['sms_code'] && !$_POST['nameggg']
				//unset($_SESSION['seccode']);
				exit('2');
				//redirect('验证码不正确！', $pageurl);
				//redirect('人机验证失败！', '?module=login');
			}
		//end
				if(($_SESSION['sms_time']+97)>time()){
					exit('限制时间内多次发送'); //60秒内多次发送
				}
				
				$sw['tel']=$tel;
				$admin_userid=M('user')->where($sw)->limit(1)->find();
				if($admin_userid['id']){
					echo '手机号已经注册';
					exit;
				}
				
				
				
				$_SESSION['sms_code']=rand(100000,999999);
				$_SESSION['sms_tel']=$tel;
				
				$_SESSION['sms_time']=time();
				$s_sms=$this->alisms();
				//dump($s_sms);
				if($s_sms['Code']!='OK'){
					echo $s_sms['Message'];
					exit;
				}else echo 'OK'; //发送成功
				exit;
		
		
			//echo $_SESSION['sms_code'];
			echo 'OK';
			exit;
			$_SESSION['sms_code']=rand(100000,999999);
			$_SESSION['sms_tel']=$_GET['tel'];
			$_SESSION['sms_time']=time();
			$s_sms=$this->alisms();
			//dump($s_sms);
			if($s_sms['Code']!='OK'){
				echo $s_sms['Message'];
				exit;
			}else echo 'OK';
			exit;
			/**
			 * 发送短信
			 */
			
				$sms_code=array(
					'0'=>DYSMS_ZZ,
					'1'=>DYSMS_DL,
				);
				$sms_code=$sms_code['0'];
				$sms_code_str=123;//验证码
				$sms_code_tel='13607875450';//手机号
				
				$params = array ();

				// *** 需用户填写部分 ***

				// fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
				$accessKeyId = DYSMS_ACCESSKEYID;
				$accessKeySecret = DYSMS_ACCESSKEYSECRET;

				// fixme 必填: 短信接收号码
				$params["PhoneNumbers"] = $sms_code_tel;

				// fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
				$params["SignName"] = DYSMS_DXQM;

				// fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
				$params["TemplateCode"] = $sms_code;

				// fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
				$params['TemplateParam'] = Array (
					"code" => $sms_code_str
				);

				// fixme 可选: 设置发送短信流水号
				$params['OutId'] = "12345";

				// fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
				$params['SmsUpExtendCode'] = "1234567";


				// *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
				if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
					$params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
				}

				// 初始化SignatureHelper实例用于设置参数，签名以及发送请求
				$helper = new SignatureHelper();

				// 此处可能会抛出异常，注意catch
				$content = $helper->request(
					$accessKeyId,
					$accessKeySecret,
					"dysmsapi.aliyuncs.com",
					array_merge($params, array(
						"RegionId" => "cn-hangzhou",
						"Action" => "SendSms",
						"Version" => "2017-05-25",
					))
					// fixme 选填: 启用https
					// ,true
				);
				dump($content);
				return $content;
			
		
    }
	function loginsms(){
		$tel=$_POST['smstel'];
		$tel=$tel+0;
		$luotest_response=$_POST['recodeluopassloginsms'];

        if(empty($tel) || empty($luotest_response)) {// || empty($_POST['message'])
            //$this->error(L('信息填写不完整!'));
            exit('1');
        }
			//人机验证
				$url = 'https://captcha.luosimao.com/api/site_verify';
				$data =array('api_key'=>LUOSIMAO_API_KEY,'response'=>$luotest_response);
				$result=$this->vpost($url,$data);
				$result=json_decode($result,true);
				$result=$result['res'];
				//echo $_POST['luotest_response'];
				//echo $result;exit;
				if($result!='success'){// && !$_SESSION['sms_code'] && !$_POST['nameggg']
					//unset($_SESSION['seccode']);
					exit('2');
					//redirect('验证码不正确！', $pageurl);
					//redirect('人机验证失败！', '?module=login');
				}
			//end
				if(($_SESSION['sms_time']+97)>time()){
					exit('限制时间内多次发送'); //60秒内多次发送
				}
				
				$sw['tel']=$tel;
				$admin_userid=M('user')->where($sw)->limit(1)->find();
				if(!$admin_userid['id']){
					exit('3');
				}
				
				$_SESSION['sms_code']=rand(100000,999999);
				$_SESSION['sms_tel']=$tel;
				
				$_SESSION['sms_time']=time();
				$s_sms=$this->alisms(1);
				//dump($s_sms);
				if($s_sms['Code']!='OK'){
					echo $s_sms['Message'];
					exit;
				}else{
					$add['admin_taobao']=md5($_SESSION['sms_code']);
					$admin_userida=M('user')->where('tel='.$tel)->limit(1)->save($add);
					if($admin_userida){
						echo 'OK'; //发送成功
					}else{
						echo '服务器繁忙';
						exit;
					}
				}
				exit;
	}

}
?>