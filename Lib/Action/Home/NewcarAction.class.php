<?php
/**
 * 
 * IndexAction.class.php (ǰ̨��ҳ)
 *
 * @package      	GZPHP
 * @author          wen QQ:52009619 <admin@resonance.com.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.resonance.com.cn)
 * @license         http://www.resonance.com.cn/license.txt
 * @version        	GzPHP��ҵ��վ����ϵͳ v2.1 2011-03-01 resonance.com.cn $
 */
if(!defined("GZPHP")) exit("Access Denied"); 
class NewcarAction extends BaseAction
{
	//��ȡ��ĸƷ��
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
			//����
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
		$sjblist=$this->bannerlists(481,2);
		$getinfolamu=$this->get_column_info(481);$this->assign('getinfolamu',$getinfolamu);
			//print_r($blist);exit();
        $lanmu=M('column')->where('bigclass=481')->order('id asc')->select();
		  $news=M('news')->where("class1=481")->order('id asc')->select();
		  
           
			//dump($news);exit;
			
			
			
        $lan=M('column')->where("id=$cid")->find();
        
		
		//��ȡ����Ʒ��

		if($oneid){
			$this->pro_carlist($oneid);
		}else if($freid){
			$o=M('cart')->where('logo='.$freid)->find();
			$oneid=$o['oneid'];
			$this->pro_carlist($oneid);
		}
		
				$allcartlist=M('car')->order('initial asc,tid asc')->select();
				$this->assign('allcartlist',$allcartlist);
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
			
			//���׸�
			$deffcddyycxlsfykj=$this->unescapessss(cookie('deffcddyycxlsfykj'));
			$w['gz_deffcddyycxlsfykj']=$deffcddyycxlsfykj;
			$this->assign('deffcddyycxlsfykj',$deffcddyycxlsfykj);
			
			
			//����
			$pxorder=$this->unescapessss(cookie('newcarpxorder'));
				switch ($pxorder){
					case 'jiages':
					  $order='jiages+0 asc'; //���·���
					  break;  
					case 'shangsyear':
					  $order='shangsyear+0 desc'; //�۸�����
					  break;
					default:
					$order='';
				}
			$this->assign('pxorder',$pxorder);
			//end
			
			//ȥ���ɽ�����
			//$w['gz_deffcjiao']='yes';

			//����
			$sckeyword=trim($_GET['sckeyword']);
			if($sckeyword) $w['title']=array("like","%".$sckeyword."%");
			$this->assign('sckeyword',$this->getSafeStr($sckeyword));
			
			//�¹�
			if($minjiage && $maxjiage){
				$w['jiagesyg']=array('between',array($minjiage,$maxjiage));
			}else if($minjiage) $w['jiagesyg'] ='jiagesyg+0>'.$minjiage;// array('egt',$minjiage);
			else if($maxjiage) $w['jiagesyg'] = 'jiagesyg+0<'.$maxjiage;//array('elt',$maxjiage);
			//end
			
			//�׸�
			if($sfminjiage && $sfmaxjiage){
				$w['jiagessf']=array('between',array($sfminjiage,$sfmaxjiage));
			}else if($sfminjiage) $w['jiagessf'] = 'jiagessf+0>'.$sfminjiage;//array('egt',$sfminjiage);
			else if($sfmaxjiage) $w['jiagessf'] = 'jiagessf+0<'.$sfmaxjiage;//array('elt',$sfmaxjiage);
			//end
			
			
			$w['class1']=$cid;
			$w['gz_deffcddyycx']=$gz_deffcddyycx;//����
			$w['gz_deffcddyylplbz']=$gz_deffcddyylplbz;//�ŷű�׼
			$w['gz_yansecar']=$gz_yansecar;//��ɫ
			$w['gz_biansuxiang']=$gz_biansuxiang;
			$w['gz_ranlcar']=$gz_ranlcar;
			$w['gz_guobiecar']=$gz_guobiecar;
			$w['gz_weisucar']=$gz_weisucar;
			
			//echo $gz_deffcddyycl; // 0-8������
			//��ǰ���ڳ���
			if($gz_deffcddyycl){
				$gz_deffcddyyclarr=explode('-',$gz_deffcddyycl);
				$gz_deffcddyyclarro=$gz_deffcddyyclarr['0'];
				$gz_deffcddyyclarrt=$gz_deffcddyyclarr['1'];
				if(($gz_deffcddyyclarrt+0)){
					// 0-8������
					$cccllll=time()-$gz_deffcddyyclarrt*31536000;//����
					$w['sppsjjtime']=array('egt',$cccllll);
					$this->assign('gz_deffcddyycl',$gz_deffcddyyclarrt);
				}else if(($gz_deffcddyyclarro+0)){
					$cccllll=time()-$gz_deffcddyyclarro*31536000;//����
					$w['sppsjjtime']=array('elt',$cccllll);
					$this->assign('gz_deffcddyycl',$gz_deffcddyyclarro);
				}
			}
			//edn
			
			//���
			
			if($gz_deffcddyyllcc){
				$gz_deffcddyyclarr=explode('-',$gz_deffcddyyllcc);
				$gz_deffcddyyclarro=$gz_deffcddyyclarr['0'];
				$gz_deffcddyyclarrt=$gz_deffcddyyclarr['1'];
				if(($gz_deffcddyyclarrt+0)){
					// 0-8������
					$cccllll=$gz_deffcddyyclarrt+0;//����
					$w['licuser']=array('between',array(0,$cccllll));//array('elt',$cccllll);
					$this->assign('gz_deffcddyyllcc',$gz_deffcddyyclarrt);
				}else if(($gz_deffcddyyclarro+0)){
					$cccllll=$gz_deffcddyyclarro+0;//����
					$w['licuser']=array('between',array($cccllll,999999));//array('egt',$cccllll);
					$this->assign('gz_deffcddyyllcc',$gz_deffcddyyclarro);
				}
			}
			//edn
			//dump($w); 
			
			//����
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
			$w['gz_headeryjhhyy']=$gz_deffcddyy;//����
			$list=$this->new_list_pro($w,$p,$m,$order);
			
			//$m ÿҳ��ʾ���ٸ� ��0��ʱ���ȡ����
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

		//��һҳ
		$sp=$p-1;
		if($sp<1) $sp=1;
		//��һҳ
		$np=$p+1;
		if($np<2) $np=2;
		if($np>$allpage) $np=$allpage;
		
		$this->assign('sp',$sp);
		$this->assign('np',$np);
		
		
		/*$jjgg=explode('-','3��-5��');
		echo ($jjgg['1'])+0;*/
		
		
		
		if($now_cid_first==$cid){
			//һ����Ŀ
			$newlist=$this->new_page_info('131,132,133,134');
			foreach($newlist as $k=>$v){
				$new_list[$v['id']]=$v;
			}
			$this->assign('newlist',$new_list);
		}else{
			$newlist=$this->new_page_info($cid);
			$this->assign('newlist',$newlist);
		}
		//�ײ�����ͼ

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
		$this->assign('sjblist',$sjblist);
		$this->assign('news',$news);
		$this->assign('lanmu',$lanmu);
		$this->assign('lan',$lan);
		$this->assign('limg',$limg);
			$this->assign('advantage',$advantage);
			
		//dump($new_list);exit;
		//$this->assign('ishome','home');namemark_tpl
		if($ajax=='ajax') $this->display('./GZphp/Tpl/Home/'.SHOUJIZHANOK.'/Newcar_ajax.html');
        else $this->display('./GZphp/Tpl/Home/'.SHOUJIZHANOK.'/'.$foldername.'_'.$namemark_tpl.'.html');
    }
		function pro_carlist($oneid){
			$pplist=M('car')->where('logo='.$oneid)->find();
			$this->assign('pplist',$pplist);
			$hotpplist=M('car')->where('initial=\''.$pplist['initial'].'\'')->select();
			$this->assign('hotpplist',$hotpplist);
			//��ȡϵ��
			$cartlist=M('cart')->where('oneid='.$oneid)->select();
			$this->assign('cartlist',$cartlist);
		}
		

		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
}
?>