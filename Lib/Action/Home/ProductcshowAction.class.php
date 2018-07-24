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
if(!defined("GZPHP")) exit("Access Denied"); 
class ProductcshowAction extends BaseAction
{
    public function index()
    {
		$pagesss=$_GET['pagesss'];
		$cid=$_GET['cid']+0;
		$c=$_GET['c']+0;
		$id=$_GET['id']+0;
		global $now_cid_first;
		global $namemark;
		global $foldername;
		global $namemark_tpl;
		$namemark_tpl='index';
		if($_GET['new']){
			$namemark_tpl=$_GET['new'];
		}



		
	$prev= M('news')->where(array('id'=>array('GT',$id),'class2'=>$cid))->order('id asc')->find();
    $next= M('news')->where(array('id'=>array('LT',$id),'class2'=>$cid))->order('id desc')->find();
		//dump($list);exit;

		$now_lanmu=$this->get_column_info($cid);
		$lanmu=M('column')->where('bigclass=140')->select();
		   foreach($lanmu as $kuccc=>&$kuccv){
				$kuccv['foldername']=ucfirst($kuccv['foldername']);
			}
		$lan=M('column')->where("id=$cid")->find();
		if($pagesss) $list=M('column')->where("id=$cid")->find();
		else $list=$this->newinfo($id);
		$this->assign('list',$list);
		
		//获取检修师傅
		if($list['jiancesrr']){
			$jiancesrr=M('user')->where('id='.$list['jiancesrr'])->find();
			
					$nnn=md5($jiancesrr['tel'].$jiancesrr['id']).'.jpg';
					if(!file_exists('tx/'.$nnn)) $user_tx='/tx/0.jpg';
					else $user_tx='/tx/'.$nnn;
					$jiancesrr['user_tx']=$user_tx;
					$this->assign('jiancesrr',$jiancesrr);
		}
		
		
		$getinfolamu=$this->get_column_info($c);$this->assign('getinfolamu',$getinfolamu);
		
		//print_r($lan);exit;
		$blist=$this->bannerlists($c);
		$this->assign('blist',$blist);
		$this->assign('id',$id);
		$this->assign('now_lanmu',$now_lanmu);
          $this->assign('lanmu',$lanmu);
            $this->assign('prev',$prev);
              $this->assign('next',$next);
              $this->assign('lan',$lan);
		//dump($list);exit;
		//$this->assign('ishome','home');namemark_tpl
        $this->display('./GZphp/Tpl/Home/'.SHOUJIZHANOK.'/Productcshow_index.html');
    }
	
	function xdjyykc(){
		global $new_config_info;
		global $user_tel;
		$tel=$_GET['tel']+0;
		if($user_tel) $yhhysrdtt='<span style="color:red;">[会员]</span> ';
		if(!$user_tel && !$tel){
			echo '未登录';
			//header('Location:/');
			exit;
		}
		
		if($tel) $user_tel=$tel;
		
		$id=$_GET['id']+0;
		if(!$id){
			echo '车辆ID错误';
			exit;
		}
		
		$list=$this->newinfo($id);
		//dump($list);
		$title=$list['title'];
		$data['message']=$yhhysrdtt.'预约车辆：'.$title.' <a href="/index.php/Home/Productcshow/index/cid/'.$list['class1'].'/c/'.$list['class1'].'/id/'.$id.'" target="_blank">查看车辆</a>';
		$data['addtime']=date('Y-m-d H:i:s',time());
		$data['company']=3;
		$data['tel']=$user_tel;
		
		if(M('message')->add($data)){
			echo 'OK';
			exit;
		}else{
			echo '预约失败';
			exit;
		}
	}
	
}
?>