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
class NewcarAction extends BaseAction
{
	//获取字母品牌
	function getzmpp(){
		global $new_config_info;
		$cid=$_GET['cid']+0;
		$zm=$this->getSafeStr($_GET['zm']);
		if($zm){
			$g=M('car')->where('initial=\''.$zm.'\'')->select();
			foreach($g as $k=>$v){
				echo '<span class="buy_br_c_each"><a href="/index.php/Home/Newcar/index/cid/'.$cid.'/oneid/'.$v['logo'].'.html">'.$v['name'].'</a></span>';
			}
		}else{
			//热门
			$gz_remenpp=$new_config_info['gz_remenpp'];
			$hhyy=explode("\r\n",$gz_remenpp);
			foreach($hhyy as $hk=>$hv){
				$nhv=explode('|',$hv);
				echo '<span class="buy_br_c_each"><a href="/index.php/Home/Newcar/index/cid/'.$cid.'/oneid/'.$nhv['1'].'.html">'.$nhv['0'].'</a></span>';
			}
		}
	}
	
    public function index(){
		$ajax=$_GET['ajax'];
		$oneid=$_GET['oneid']+0;
		$twoid=$_GET['twoid']+0;
		$freid=$_GET['freid']+0;
		
		$kwww=$this->getSafeStr($_GET['k']);
		$hyid=$_GET['hyid']+0;
		$cid=$_GET['cid']+0;
		$minjiage=$_GET['minjiage']+0;
		$maxjiage=$_GET['maxjiage']+0;
		
		$sfminjiage=$_GET['sfminjiage']+0;
		$sfmaxjiage=$_GET['sfmaxjiage']+0;
		
		
		$gz_deffcddyy=$_GET['gz_deffcddyy'];
		//$pid=$_GET['pid']+0;
         //print_r($pid);exit;
		global $now_cid_first;
		global $namemark;
		global $foldername;
		global $namemark_tpl;
		$blist=$this->bannerlists(481);
		$getinfolamu=$this->get_column_info(481);$this->assign('getinfolamu',$getinfolamu);
			//print_r($blist);exit();
        $lanmu=M('column')->where('bigclass=481')->order('id asc')->select();
		  $news=M('news')->where("class1=481")->order('id asc')->select();
		  
           
			//dump($news);exit;
			
			
			
        $lan=M('column')->where("id=$cid")->find();
        
		
		//获取汽车品牌

		if($oneid){
			$this->pro_carlist($oneid);
		}else if($freid){
			$o=M('cart')->where('logo='.$freid)->find();
			$oneid=$o['oneid'];
			$this->pro_carlist($oneid);
		}
		
		
		
		   //dump($lan);exit; 

         $p=$_GET['p']+0;
		//echo $p;exit;
		$m=5;
		
		if($namemark=='new') $m=5;
		$m=8;
		$m=$lan['listnumber']?$lan['listnumber']:16;
		$cclslss='class2';
		if($cid==481) $cclslss='class1';
		
			//$list=$this->new_list($cclslss,$cid,$p,$m,0,$kwww);
			
			$gz_deffcddyycx=$this->unescapessss(cookie('deffcddyycx'));
			$gz_deffcddyycl=$this->unescapessss(cookie('deffcddyycl'));
			$gz_ranlcar=$this->unescapessss(cookie('ranlcar'));
			$gz_deffcddyyllcc=$this->unescapessss(cookie('deffcddyyllcc'));
			$gz_deffcddyylplbz=$this->unescapessss(cookie('deffcddyylplbz'));
			$gz_guobiecar=$this->unescapessss(cookie('guobiecar'));
			$gz_weisucar=$this->unescapessss(cookie('weisucar'));
			$gz_yansecar=$this->unescapessss(cookie('yansecar'));
			$gz_biansuxiang=$this->unescapessss(cookie('biansuxiang'));
			$gz_deffcddyylpl=$this->unescapessss(cookie('deffcddyylpl'));
			$this->assign('gz_deffcddyycx',$gz_deffcddyycx);
			$this->assign('gz_deffcddyylplbz',$gz_deffcddyylplbz);
			$this->assign('gz_weisucar',$gz_weisucar);
			$this->assign('gz_yansecar',$gz_yansecar);
			$this->assign('gz_biansuxiang',$gz_biansuxiang);
			$this->assign('gz_guobiecar',$gz_guobiecar);
			$this->assign('gz_ranlcar',$gz_ranlcar);
			
			//零首付
			$deffcddyycxlsfykj=$this->unescapessss(cookie('deffcddyycxlsfykj'));
			$w['gz_deffcddyycxlsfykj']=$deffcddyycxlsfykj;
			$this->assign('deffcddyycxlsfykj',$deffcddyycxlsfykj);
			
			
			//排序
			$pxorder=$this->unescapessss(cookie('newcarpxorder'));
				switch ($pxorder){
					case 'jiages':
					  $order='jiages asc'; //最新发布
					  break;  
					case 'shangsyear':
					  $order='shangsyear desc'; //价格升序
					  break;
					default:
					$order='';
				}
			$this->assign('pxorder',$pxorder);
			//end
			
			//去掉成交车辆
			//$w['gz_deffcjiao']='yes';

			//搜索
			$sckeyword=trim($_GET['sckeyword']);
			if($sckeyword) $w['title']=array("like","%".$sckeyword."%");
			$this->assign('sckeyword',$this->getSafeStr($sckeyword));
			
			//月供
			if($minjiage && $maxjiage){
				$w['jiagesyg']=array('between',array($minjiage,$maxjiage));
			}else if($minjiage) $w['jiagesyg'] = array('egt',$minjiage);
			else if($maxjiage) $w['jiagesyg'] = array('elt',$maxjiage);
			//end
			
			//首付
			if($sfminjiage && $sfmaxjiage){
				$w['jiagessf']=array('between',array($sfminjiage,$sfmaxjiage));
			}else if($sfminjiage) $w['jiagessf'] = array('egt',$sfminjiage);
			else if($sfmaxjiage) $w['jiagessf'] = array('elt',$sfmaxjiage);
			//end
			
			
			$w['class1']=$cid;
			$w['gz_deffcddyycx']=$gz_deffcddyycx;//车型
			$w['gz_deffcddyylplbz']=$gz_deffcddyylplbz;//排放标准
			$w['gz_yansecar']=$gz_yansecar;//颜色
			$w['gz_biansuxiang']=$gz_biansuxiang;
			$w['gz_ranlcar']=$gz_ranlcar;
			$w['gz_guobiecar']=$gz_guobiecar;
			$w['gz_weisucar']=$gz_weisucar;
			
			//echo $gz_deffcddyycl; // 0-8年以内
			//当前日期车龄
			if($gz_deffcddyycl){
				$gz_deffcddyyclarr=explode('-',$gz_deffcddyycl);
				$gz_deffcddyyclarro=$gz_deffcddyyclarr['0'];
				$gz_deffcddyyclarrt=$gz_deffcddyyclarr['1'];
				if(($gz_deffcddyyclarrt+0)){
					// 0-8年以内
					$cccllll=time()-$gz_deffcddyyclarrt*31536000;//以内
					$w['sppsjjtime']=array('egt',$cccllll);
					$this->assign('gz_deffcddyycl',$gz_deffcddyyclarrt);
				}else if(($gz_deffcddyyclarro+0)){
					$cccllll=time()-$gz_deffcddyyclarro*31536000;//以上
					$w['sppsjjtime']=array('elt',$cccllll);
					$this->assign('gz_deffcddyycl',$gz_deffcddyyclarro);
				}
			}
			//edn
			
			//里程
			
			if($gz_deffcddyyllcc){
				$gz_deffcddyyclarr=explode('-',$gz_deffcddyyllcc);
				$gz_deffcddyyclarro=$gz_deffcddyyclarr['0'];
				$gz_deffcddyyclarrt=$gz_deffcddyyclarr['1'];
				if(($gz_deffcddyyclarrt+0)){
					// 0-8年以内
					$cccllll=$gz_deffcddyyclarrt+0;//以内
					$w['licuser']=array('between',array(0,$cccllll));//array('elt',$cccllll);
					$this->assign('gz_deffcddyyllcc',$gz_deffcddyyclarrt);
				}else if(($gz_deffcddyyclarro+0)){
					$cccllll=$gz_deffcddyyclarro+0;//以上
					$w['licuser']=array('between',array($cccllll,999999));//array('egt',$cccllll);
					$this->assign('gz_deffcddyyllcc',$gz_deffcddyyclarro);
				}
			}
			//edn
			//dump($w); 
			
			//排量
			if($gz_deffcddyylpl){
				$gz_deffcddyyclarrtsrc=$gz_deffcddyylpl;
				$gz_deffcddyyclarr=explode('-',$gz_deffcddyylpl);
				$gz_deffcddyyclarro=$gz_deffcddyyclarr['0']+0;
				$gz_deffcddyyclarrt=$gz_deffcddyyclarr['1']+0;
				if(!$gz_deffcddyyclarrt){
					$gz_deffcddyyclarrt=999999;
					$gz_deffcddyyclarrtsrc=$gz_deffcddyyclarr['0'];
				}
				$w['displacement']=array('between',array($gz_deffcddyyclarro,$gz_deffcddyyclarrt));
				
				if(!$gz_deffcddyyclarro) $gz_deffcddyyclarrtsrc=$gz_deffcddyyclarr['1'];
				
				$this->assign('gz_deffcddyylpl',$gz_deffcddyyclarrtsrc);
			}
			//edn
			
			
			$w['ajax_get_pp']=$oneid;
			$w['ajax_get_cxq']=$twoid;
			$w['ajax_get_cx']=$freid;
			$w['gz_headeryjhhyy']=$gz_deffcddyy;//地域
			$list=$this->new_list_pro($w,$p,$m,$order);
			
			//$m 每页显示多少个 当0的时候获取总数
			$allpage=$this->new_list_pro($w,$p,0,$order);
			
			//dump($w);exit;
		    if(empty($list) && $ajax=='ajax') exit;
		
		
		//if(empty($list)) exit('no data');
		$lanmumm=$this->lanmu_list('bigclass',481,$p,$m);
		$this->assign('list',$list);
		$this->assign('kwww',$kwww);
		$this->assign('lanmumm',$lanmumm);
		//dump($lanmumm);exit;
		$now_lanmu=$this->get_column_info($cid);
		$this->assign('now_lanmu',$now_lanmu);
		//$allpage=$this->Pagenewinfo($cclslss,$cid,0,$k,$hyid);
		$allpage=ceil($allpage/$m);

		//上一页
		$sp=$p-1;
		if($sp<1) $sp=1;
		//下一页
		$np=$p+1;
		if($np<2) $np=2;
		if($np>$allpage) $np=$allpage;
		
		$this->assign('sp',$sp);
		$this->assign('np',$np);
		
		
		/*$jjgg=explode('-','3万-5万');
		echo ($jjgg['1'])+0;*/
		
		
		
		if($now_cid_first==$cid){
			//一级栏目
			$newlist=$this->new_page_info('131,132,133,134');
			foreach($newlist as $k=>$v){
				$new_list[$v['id']]=$v;
			}
			$this->assign('newlist',$new_list);
		}else{
			$newlist=$this->new_page_info($cid);
			$this->assign('newlist',$newlist);
		}
		//底部流程图

		//dump($now_lanmu);exit;
		

		//echo $c_gz_deffcddyy = cookie('gz_deffcddyy');exit;
		
        $this->assign('p',$p);
		//$this->assign('c_gz_deffcddyy',$c_gz_deffcddyy);
		$this->assign('allpage',$allpage);
		$this->assign('oneid',$oneid);
		$this->assign('twoid',$twoid);
		$this->assign('freid',$freid);
		$this->assign('sfmaxjiage',$sfmaxjiage);
		$this->assign('sfminjiage',$sfminjiage);
		$this->assign('maxjiage',$maxjiage);
		$this->assign('minjiage',$minjiage);
		$this->assign('gz_deffcddyy',$gz_deffcddyy);
		$limg=$this->bannerlist($cid);
		$this->assign('blist',$blist);
		$this->assign('news',$news);
		$this->assign('lanmu',$lanmu);
		$this->assign('lan',$lan);
		$this->assign('limg',$limg);
			$this->assign('advantage',$advantage);
			
		//dump($new_list);exit;
		//$this->assign('ishome','home');namemark_tpl
		if($ajax=='ajax') $this->display('./GZphp/Tpl/Home/Default/Newcar_ajax.html');
        else $this->display('./GZphp/Tpl/Home/Default/'.$foldername.'_'.$namemark_tpl.'.html');
    }
		function pro_carlist($oneid){
			$pplist=M('car')->where('logo='.$oneid)->find();
			$this->assign('pplist',$pplist);
			$hotpplist=M('car')->where('initial=\''.$pplist['initial'].'\'')->select();
			$this->assign('hotpplist',$hotpplist);
			//获取系列
			$cartlist=M('cart')->where('oneid='.$oneid)->select();
			$this->assign('cartlist',$cartlist);
		}
		

		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
}
?>