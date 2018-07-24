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
class IndexAction extends BaseAction
{
	
	function hcttjjjss(){
			//去掉成交车辆
			$w['gz_deffcjiao']='yes';
			//搜索
			$sckeyword=trim($_GET['sckeyword']);
			if($sckeyword) $w['title']=array("like","%".$sckeyword."%");
			$this->assign('sckeyword',$sckeyword);
			
			//$ww['class1']='461';
			
			$haochetuij=$this->new_list_pro($w,0,9);
			
			if(empty($haochetuij)) exit;
			
			$this->assign('haochetuij',$haochetuij);
			$this->display('./GZphp/Tpl/Home/'.SHOUJIZHANOK.'/Hcetuij_index.html');
			
			exit;
			dump($list);exit;
		
			//好车推荐
			$haochetuij=M('news')->where("class1=461 and gz_deffcjiao!=1")->limit(9)->select();
			$this->assign('haochetuij',$haochetuij);
	}
	
    public function index()
    {
        $cid=$_GET['cid']+0;

    	//print_r($cid);exit();
   // print_r($config_info);exit;
 		$blist=$this->bannerlist(10001);
		
		$shoujiblist=$this->bannerlist(10001,2);
		
		
		$alist=$this->bannerlist('314,318');
  //dump($alist);exit;
 		$pro=M('news')->where('class1=136')->order('id desc')->limit(1)->find();
 		$product=M('news')->where('class1=136')->order('id desc')->select();

        $introduce=M('column')->where('id=314')->find();
         //dump($introduce);exit();
 		//print_r($advantages);exit();
        $news=M('news')->where("class1=140")->order('id desc')->find();
		
		$cjlistcc=M('news')->field('id')->where('gz_deffcjiao=1')->select();
		$cjlistccnum=count($cjlistcc);//成交数量
		$this->assign('cjlistccnum',$cjlistccnum);
		
		//获取服务保障文章
		 $fwbzlist=M('news')->where("class1=482")->order('no_order desc,id desc')->limit(9)->select();
			//dump($fwbzlist);exit;
			foreach($fwbzlist as $fwk=>&$fwv){
				$fwv= str_replace("\r\n",'<br/>',$fwv);
			}
			$this->assign('fwbzlist',$fwbzlist);
			
			//好车推荐
			/*$haochetuij=M('news')->where("class1=461")->limit(9)->select();
			$this->assign('haochetuij',$haochetuij);*/
			
			//新车
			$necliiss=M('news')->where('class1=481')->order('no_order desc,id desc')->limit(6)->select();
			$this->assign('necliiss',$necliiss);
			
			//分期购车
			$necliissfq=M('news')->where('class1=483 and fctitlef=\'\'')->order('no_order desc,id desc')->limit(4)->select();
			$this->assign('necliissfq',$necliissfq);
			$necliissfqright=M('news')->where('class1=483 and fctitlef!=\'\'')->order('no_order desc,id desc')->limit(1)->select();
			$this->assign('necliissfqright',$necliissfqright);
			
			//获取保卖车
			$bmmccc=M('news')->field('id,title,imgurl')->where("class1=461 and gz_deffcjiao!=1 and gz_deffcdzbmm like '%保卖车%'")->order('no_order desc,id desc')->limit(9)->select();
			$this->assign('bmmccc',$bmmccc);
			//dump($necliissfq);exit;
			
			
			
			$bmmcccshouji=M('news')->field('id,title,imgurl,sppsjj,licuser,jiageold,jiages')->where("class1=461 and gz_deffcjiao!=1")->order('no_order desc,id desc')->limit(5)->select();
			$this->assign('bmmcccshouji',$bmmcccshouji);
			
			
			
			//获取超值车
			$bmmccczz=M('news')->field('id,title,imgurl,jiageoldzttt')->where("class1=461 and gz_deffcjiao!=1 and gz_deffcdzbmm like '%超值%'")->order('no_order desc,id desc')->limit(9)->select();
			$this->assign('bmmccczz',$bmmccczz);
			
			
			
        $newss=M('news')->where("class1=140")->order('addtime desc')->limit(3)->select();
		
		$whlmmmm=M('column')->where('bigclass=314')->order('no_order asc,id asc')->limit(2)->select();
		$this->assign('whlmmmm',$whlmmmm);
		
		$whlmmmmfffwww=M('column')->where('bigclass=464')->order('no_order asc,id asc')->limit(2)->select();
		$this->assign('whlmmmmfffwww',$whlmmmmfffwww);
		//dump($whlmmmm);exit;
		
		$newssal=M('news')->where("class1=136")->order('no_order desc,id desc')->limit(5)->select();//案例
		
		$whlmmmmccc=M('news')->where("class1=462")->order('no_order desc,id desc')->limit(4)->select();
		$whlmmmmcccaaa=M('news')->where("class1=461")->order('no_order desc,id desc')->limit(4)->select();
		
		$this->assign('whlmmmmccc',$whlmmmmccc);
		$this->assign('whlmmmmcccaaa',$whlmmmmcccaaa);
		
		$newssalgg=M('news')->where("class1=140")->order('no_order desc,id desc')->limit(3)->select();//公告
		$newssalggzx=M('news')->where("class2=460")->order('no_order desc,id desc')->limit(3)->select();//咨询
			
		  $heart=M('news')->where("class1=406")->order('id asc')->select();
		  
		  $sso=M('column')->where('bigclass=445')->order('no_order asc,id asc')->select();
		  $lanmuzx=M('column')->where('bigclass=452')->order('no_order asc,id asc')->select();
		  $sso=array_merge($sso,$lanmuzx);
		  $this->sortArrByField($sso,'no_order',true);
		  foreach($sso as $ks=>$vs){
			  if($ks<3) $nsso[]=$vs;
		  }
		  //$sso=array_slice($sso,0,3);
		  //dump($nsso);exit;
 		//$advantage=$this->new_list(1,1,6,1);
        //dump($heart);exit();
		$this->assign('bcid',0);//顶级栏目 
		$this->assign('sso',$nsso);
		$this->assign('ishome','home');
		$this->assign('heart',$heart);
		$this->assign('blist',$blist);
		$this->assign('shoujiblist',$shoujiblist);
		$this->assign('alist',$alist);
        $this->assign('news',$newss);
        $this->assign('newssalgg',$newssalgg);
        $this->assign('newssalggzx',$newssalggzx);
        $this->assign('newssal',$newssal);
		$this->assign('cid',$$cid);

		$this->assign('advantage',$advantage);
        $this->assign('product',$product);
        $this->assign('news',$news);
          
		$this->assign('introduce',$introduce);
        $this->display();
    }
 public function sortArrByField(&$array, $field, $desc = false){
  $fieldArr = array();
  foreach ($array as $k => $v) {
    $fieldArr[$k] = $v[$field];
  }
  $sort = $desc == false ? SORT_ASC : SORT_DESC;
  array_multisort($fieldArr, $sort, $array);
}
}
?>