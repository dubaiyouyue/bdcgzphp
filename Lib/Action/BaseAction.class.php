<?php
/**
 * 
 * Base (前台公共模块)
 *
 * @package      	GZPHP
 * @author          wen QQ:52009619 <admin@resonance.com.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.resonance.com.cn)
 * @license         http://www.resonance.com.cn/license.txt
 * @version        	GzPHP企业网站管理系统 v2.1 2011-03-01 resonance.com.cn $
 */
if(!defined("GZPHP"))  exit("Access Denied");

class BaseAction extends Action
{
	protected   $Config ,$sysConfig,$categorys,$module,$moduleid,$mod,$dao,$Type,$Role,$_userid,$_groupid,$_email,$_username ,$forward ,$user_menu,$Lang,$member_config;
    public function _initialize() {

//print_r($_GET);exit;
			$this->sysConfig = F('sys.config');
			$this->module = F('Module');
			$this->Role = F('Role');
			$this->Type =F('Type');
			$this->mod= F('Mod');
			$this->moduleid=$this->mod[MODULE_NAME];
			if(APP_LANG){
				$this->Lang = F('Lang');
				$this->assign('Lang',$this->Lang);
				if(get_safe_replace($_GET['l'])){
					if(!$this->Lang[$_GET['l']]['status'])$this->error ( L ( 'NO_LANG' ) );
					$lang=$_GET['l'];
				}else{
					$lang=$this->sysConfig['DEFAULT_LANG'];
				}
				define('LANG_NAME', $lang);
				define('LANG_ID', $this->Lang[$lang]['id']);
				$this->categorys = F('Category_'.$lang);
				$this->Config = F('Config_'.$lang);
				$this->assign('l',$lang);
				$this->assign('langid',LANG_ID);
				$T = F('config_'.$lang,'', APP_PATH.'Tpl/Home/'.$this->sysConfig['DEFAULT_THEME'].'/');
				C('TMPL_CACHFILE_SUFFIX','_'.$lang.'.php');
				cookie('think_language',$lang);
			}else{
				$T = F('config_'.$this->sysConfig['DEFAULT_LANG'],'',  APP_PATH.'Tpl/Home/'.$this->sysConfig['DEFAULT_THEME'].'/');
				$this->categorys = F('Category');
				$this->Config = F('Config');
				cookie('think_language',$this->sysConfig['DEFAULT_LANG']);
			}
			$this->assign('T',$T);
			$this->assign($this->Config);
			$this->assign('Role',$this->Role);
			$this->assign('Type',$this->Type);
			$this->assign('Module',$this->module);
			$this->assign('Categorys',$this->categorys);
			
			//友情链接
			$youqinglianjielist=M('link')->where('show_ok=1')->select();
			$this->assign('youqinglianjielist',$youqinglianjielist);
			
			//删除排序等cookie  || $_GET['id']!=481
			if($_GET['cid']!=461){
				//exit;
				cookie('deffcddyycx',null);
				cookie('deffcddyycl',null);
				cookie('ranlcar',null);
				cookie('deffcddyyllcc',null);
				cookie('deffcddyylplbz',null);
				cookie('guobiecar',null);
				cookie('weisucar',null);
				cookie('yansecar',null);
				cookie('deffcddyy',null);
				cookie('biansuxiang',null);
				cookie('deffcddyylpl',null);
				cookie('deffcddyycxlsfykj',null);
				cookie('pxorder',null);
			}
			if($_GET['cid']!=481){
				//exit;
				/*cookie('deffcddyycx',null);
				cookie('deffcddyycl',null);
				cookie('ranlcar',null);
				cookie('deffcddyyllcc',null);
				cookie('deffcddyylplbz',null);
				cookie('guobiecar',null);
				cookie('weisucar',null);
				cookie('yansecar',null);
				cookie('biansuxiang',null);
				cookie('deffcddyylpl',null);
				cookie('deffcddyycxlsfykj',null);*/
				cookie('newcarpxorder',null);
				
			}
			
			global $user_tel;
			$user_tel=$this->chelogins();
			$this->assign('user_tel',$user_tel);
			//echo $this->chelogins();exit;
			import("@.ORG.Form");			
			$this->assign ( 'form',new Form());
 
			C('HOME_ISHTML',$this->sysConfig['HOME_ISHTML']);
			C('PAGE_LISTROWS',$this->sysConfig['PAGE_LISTROWS']);
			C('URL_M',$this->sysConfig['URL_MODEL']);
			C('URL_M_PATHINFO_DEPR',$this->sysConfig['URL_PATHINFO_DEPR']);
			C('URL_M_HTML_SUFFIX',$this->sysConfig['URL_HTML_SUFFIX']);
			C('URL_LANG',$this->sysConfig['DEFAULT_LANG']);
			C('DEFAULT_THEME_NAME',$this->sysConfig['DEFAULT_THEME']);


			import("@.ORG.Online");
			$session = new Online();
			if(cookie('auth')){
				$gzphp_auth_key = sysmd5($this->sysConfig['ADMIN_ACCESS'].$_SERVER['HTTP_USER_AGENT']);
				list($userid,$groupid, $password) = explode("-", authcode(cookie('auth'), 'DECODE', $gzphp_auth_key));
				$this->_userid = $userid;
				$this->_username =  cookie('username');
				$this->_groupid = $groupid; 
				$this->_email =  cookie('email');
			}else{
				$this->_groupid = cookie('groupid') ?  cookie('groupid') : 4;
				$this->_userid =0;
			}


			foreach((array)$this->module as $r){
				if($r['issearch'])$search_module[$r['name']] = L($r['name']);
				if($r['ispost'] && (in_array($this->_groupid,explode(',',$r['postgroup']))))$this->user_menu[$r['id']]=$r;
			}
			if(GROUP_NAME=='User'){
				$langext = $lang ? '_'.$lang : '';
				$this->member_config=F('member.config'.$langext);
				$this->assign('member_config',$this->member_config);
				$this->assign('user_menu',$this->user_menu);
				if($this->_groupid=='5' &&  MODULE_NAME!='Login'){ 
					$this->assign('jumpUrl',URL('User-Login/emailcheck'));
					$this->assign('waitSecond',3);
					$this->success(L('no_regcheckemail'));
					exit;
				}
				$this->assign('header',TMPL_PATH.'Home/'.THEME_NAME.'/Home_header.html');
			}
			if($_GET['forward'] || $_POST['forward']){	
				$this->forward = get_safe_replace($_GET['forward'].$_POST['forward']);
			}else{
				if(MODULE_NAME!='Register' || MODULE_NAME!='Login' )
				$this->forward =isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] :  $this->Config['site_url'];
			}
			$this->assign('forward',$this->forward);
			$this->assign('search_module',$search_module);
			$this->assign('module_name',MODULE_NAME);
			$this->assign('action_name',ACTION_NAME);

			$lanmugywm=M('column')->where('bigclass=314')->order('no_order asc,id asc')->select();
			$lanmuffwz=M('column')->where('bigclass=445')->order('no_order asc,id asc')->select();
		  $lanmuzx=M('column')->where('bigclass=452')->order('no_order asc,id asc')->select();
		  $lanmuffwz=array_merge($lanmuffwz,$lanmuzx);
			$this->assign('getlink',$this->getlink());
			$this->assign('lanmugywm',$lanmugywm);
			$this->assign('lanmuffwz',$lanmuffwz);
			
			
			
			//new start
			//所有栏目
			global $new_column_list;
			$new_column_list_where['lang']=array('eq','cn');
			$new_column_list=M('column')->where($new_column_list_where)->field('id,name,enname,bigclass,foldername,nav,columnimg,namemark,classtype,module,out_url,content,contentsj')->order('no_order asc,id asc')->select();
			foreach($new_column_list as $kuccc=>&$kuccv){
				$kuccv['foldername']=ucfirst($kuccv['foldername']);
			}
			//dump($new_column_list);exit;
			$this->assign('new_column_list',$new_column_list);
			//一级栏目
			$new_column_list_where['bigclass']=array('eq',0);
			$new_column_list_where['nav']=array('gt',0);
			//unset($new_column_list_where['nav']);
			$new_column_list_top=M('column')->where($new_column_list_where)->field('id,name,ctitle,enname,bigclass,foldername,nav,columnimg,namemark,classtype,module,out_url')->order('no_order asc,id asc')->select();
			foreach($new_column_list_top as $kuccct=>&$kuccvt){
				$kuccvt['foldername']=ucfirst($kuccvt['foldername']);
			}
			
			$this->assign('new_column_list_top',$new_column_list_top);
			//dump($new_column_list_top);exit;
			//print_r($d);exit;
			//当前栏目图片
			$cid=$_GET['cid']+0;
			//print_r($cid);exit$now_cid_first
			if($cid){
				foreach($new_column_list as $nc=>$nk){
					if($nk['id']==$cid){
						global $columnimg;
						$columnimg=$nk['columnimg'];
						global $namemark;
						$namemark=$nk['namemark'];
						//当前栏目一级栏目id
						global $now_cid_first;
						global $foldername;
						$foldername=$nk['foldername'];
						//当没有图片，使用一级栏目图片

							//当一级栏目没有图片的时候当前一级栏目id不存在 bug

						if(!$columnimg){
							foreach($new_column_list as $ncb=>$nkb){
								if($nkb['id']==$nk['bigclass']){
									$now_cid_first=$nkb['id'];
									$columnimg=$nkb['columnimg'];
								}
								if(!$columnimg){
									foreach($new_column_list as $ncbf=>$nkbf){
										if($nkbf['id']==$nk['bigclass']){
											$nnnwwwsid=$nkbf['bigclass'];
											$nnnwwwsidimg=$this->get_column_info($nnnwwwsid);
											$now_cid_first=$nnnwwwsid;
										$columnimg=$nnnwwwsidimg['columnimg'];
										}
									}
								}
							}
						}
						//当前位置
						//如果是三级栏目
						//echo $nk['classtype'];exit;
						$cccurld='/index.php/Home/'.$foldername.'/index/cid/';
						if($nk['classtype']==3){
							//三级栏目
							$location_fre_id=$cid;
							$location_fre_name=$nk['name'];
							//二级栏目
							$location_two_id=$nk['bigclass'];
							$location_two_cinfo=$this->get_column_info($location_two_id);
							$location_two_name=$location_two_cinfo['name'];
							//一级栏目
							$location_one_id=$location_two_cinfo['bigclass'];
							$location_one_cinfo=$this->get_column_info($location_one_id);
							$location_one_name=$location_one_cinfo['name'];
							$location_all=array(
								array($cccurld.$location_one_id.'.html',$location_one_name),
								array($cccurld.$location_two_id.'.html',$location_two_name),
								array($cccurld.$location_fre_id.'.html',$location_fre_name)
							);
						}else if($nk['classtype']==2){
							//2级栏目
							$location_two_id=$cid;
							$location_two_name=$nk['name'];
							//一级栏目
							$location_one_id=$nk['bigclass'];
							$location_one_cinfo=$this->get_column_info($location_one_id);
							$location_one_name=$location_one_cinfo['name'];
							$location_all=array(
								array($cccurld.$location_one_id.'.html',$location_one_name),
								array($cccurld.$location_two_id.'.html',$location_two_name)
							);
						}else{
							//一级栏目
							$location_one_id=$cid;
							$location_one_name=$nk['name'];
							$location_all=array(
								array($cccurld.$location_one_id.'.html',$location_one_name)
							);
						}
						$this->assign('location_all',$location_all);
						//dump($location_all);exit;
					}
				}
					//模板文件
					global $namemark_tpl;
					$namemark_tpl='index';
					/*if(!$now_cid_first && $namemark){
						$namemark_tpl=$namemark;
					};*/
					if($namemark){
						$namemark_tpl=$namemark;
					};

					
				//第一个子栏目
				global $now_cid_cfirst;
				if(!$now_cid_first) $now_cid_first=$cid;
				if($now_cid_first){
					foreach($new_column_list as $nnc=>$nnk){
						if($nnk['bigclass']==$now_cid_first){
							$now_cid_cfirst=$nnk['id'];
							//echo $now_cid_cfirst;exit;
							break;
							//return true;
						}
					}
				}/*else{
					$now_cid_cfirst=$cid;
				}*/
				
					$this->assign('foldername',$foldername);
					//不填写栏目修饰名称，跳转到下一个子栏目
					if($now_cid_cfirst && !$namemark && ($now_cid_first==$cid)){
						header('Location:/index.php/Home/'.$foldername.'/index/cid/'.$now_cid_cfirst.'.html');
						exit;
					};
					
				//当前位置
				//一级栏目名称
				//global $now_cid_first_name;
				
			}
			//echo 'fadsfads';
			//echo $now_cid_cfirst;exit;
			$this->assign('cid',$cid);
			$this->assign('now_cid_first',$now_cid_first);
			$this->assign('columnimg',$columnimg);
			//end 20170105
			$this->assign('new_column_list',$new_column_list);
			//关于莱茵
			$gylyjjjj=M('column')->where('id=136')->field('description')->limit(1)->select();
			//print_r($gylyjjjj);exit;
          
			$this->assign('gylyjjjj',$gylyjjjj['0']['description']);
			$pagenow=$_GET['p']+0;
			if(!$pagenow) $pagenow=1;
			$this->assign('pagenow',$pagenow);
			
			//获取基本信息
			global $config_info;
			global $new_config_info;
			$wccongff['lang']='cn';
			$config_info=M('config')->where($wccongff)->select();
			//dump($config_info);exit;
			foreach($config_info as $cck=>$ccv){
				$ccv['value']=str_ireplace('//','/',$ccv['value']);
				$ccv['value']=str_ireplace('../','/',$ccv['value']);
				$new_config_info[$ccv['name']]=$ccv['value'];
			}
			//dump($new_config_info['gz_ewm']);exit;
			$this->assign('config_info',$new_config_info);
			
			//SEO
			if(!$cid){
				//首页
				$seo_title='首页-'.$new_config_info['gz_webname'];
				$seo_keywords=$new_config_info['gz_keywords'];
				$seo_description=$new_config_info['gz_description'];
			}else{
				$wcinfo['id']=$cid;
				$cinfo=M('column')->where($wcinfo)->field('name,keywords,description,ctitle')->find();
				$seo_title=$cinfo['name'].'-'.$new_config_info['gz_webname'];
				if($cinfo['ctitle']) $seo_title=$cinfo['ctitle'].'-'.$new_config_info['gz_webname'];
				$seo_keywords=$cinfo['keywords'];
				$seo_description=$cinfo['description'];
				
				$id=$_GET['id']+0;
			//print_r($id);exit;
				if($id){
					$wnewss['id']=$id;
					$newssinfo=M('news')->where($wnewss)->field('title,ctitle,keywords,description')->find();
				//print_r($newssinfo);exit();
					$seo_title=$newssinfo['title'].'-'.$seo_title;
					if($cinfo['ctitle']) $seo_title=$newssinfo['ctitle'].'-'.$seo_title;
					$seo_keywords=$newssinfo['keywords'];
					$seo_description=$newssinfo['description'];
				}
				
			}
			$this->assign('seo_title',$seo_title);
			$this->assign('seo_keywords',$seo_keywords);
			$this->assign('seo_description',$seo_description);
			
				
	}
	//分页
	public function Pagenewinfo($classname,$cid,$img,$hyid,$k){
		if($img) $w['imgurl']=array('neq','');
		if($hyid) $w['gz_headeryjhhyy']=$hyid;
		if($k) $w['title']=array('like','%'.$k.'%');
		$w[$classname]=$cid;
		$l=M('news')->where($w)->select();
		$all=count($l);
		return $all;
	}
	
	//获取文章内容
	public function newinfo($id){
		$w['id']=$id;
		$l=M('news')->where($w)->find();
		$class1=$l['class1'];
		$class2=$l['class2'];
		$class3=$l['class3'];
		//上下篇
		$ws['id']=array('lt',$id);
		$ws['class1']=$class1;
		$ws['class2']=$class2;
		$ws['class3']=$class3;
		$ls=M('news')->where($ws)->find();
		
		$wx['id']=array('gt',$id);
		$wx['class1']=$class1;
		$wx['class2']=$class2;
		$wx['class3']=$class3;
		$lx=M('news')->where($wx)->find();

		$l['sx']=array(
			's'=>$ls,
			'x'=>$lx
		);
		return $l;
	}
	//获取文章列表
	public function new_list($classname,$cid,$p,$m,$img,$k,$hyid){
		if($img) $w['imgurl']=array('neq','');
		if($k) $w['title']=array('like','%'.$k.'%');
		if($hyid) $w['gz_headeryjhhyy']=$hyid;
		$w[$classname]=$cid;
		$p=$p+0;
		if($p<0) $p=0;
		$n=$p*$m;
		if($p>0) $n=($p-1)*$m;
		//dump($w);exit;
		$l=M('news')->where($w)->order('com_ok desc,no_order desc,id desc')->limit($n,$m)->select();
	//print_r($l);exit();
		return $l;
	}
	public function new_list_pro($w,$p,$m,$order){
		$w=array_filter($w);
		foreach($w as $k=>&$v){
			if($v){
				if(!is_array($v)) $v=$this->getSafeStr($v);
				else foreach($v as $kb=>&$vb){
					if($vb && !is_array($vb)) $vb=$this->getSafeStr($vb);
				}
			}
		}
		$p=$p+0;
		if($p<0) $p=0;
		$n=$p*$m;
		if($p>0) $n=($p-1)*$m;
		if(!$order) $order='com_ok desc,no_order desc,id desc';
		//dump($order);exit;
		
			//去掉成交车辆
			if($w['gz_deffcjiao']=='yes') $w['gz_deffcjiao']=array('neq',1);;
			
			if(!$m){
				//获取总数
				$l=M('news')->field('id')->where($w)->select();
				return count($l);
			}
			
		$l=M('news')->where($w)->order($order)->limit($n,$m)->select();
	//print_r($l);exit();
		return $l;
	}
	
	
	
	//获取栏目列表
	public function lanmu_list($bigclass,$cid,$p,$m,$img){
		if($img) $w['imgurl']=array('neq','');
		$w[$bigclass]=$cid;
		$p=$p+0;
		if($p<0) $p=0;
		$n=$p*$m;
		if($p>0) $n=($p-1)*$m;
		$lmm=M('column')->where($w)->order('id asc')->limit($n,$m)->select();
	//dump($lmm);exit();
		return $lmm;
	}
	
	
	
	
	
	
	
	
	public function new_page_info($cid=0){
		//单页栏目获取
		//$cid=$_GET['cid']+0;
		//global $new_column_list;
			$new_column_list_where['lang']=array('eq','cn');
			$new_column_list_where['id']=$cid;
			$cid_arr=explode(',',$cid);
			if($cid['1']) $new_column_list_where['id']=array('in',$cid);
			//dump($cid_arr);exit;
			$new_column_list=M('column')->where($new_column_list_where)->order('no_order desc,id desc')->select();
			//dump($new_column_list);exit;
			if(!$cid['1']) return $new_column_list['0'];
			return $new_column_list;
			//$this->assign('new_column_list',$new_column_list);
		/*if($cid){
			foreach($new_column_list as $nc=>$nk){
				if($nk['id']==$cid){
					return $nlist[]=$nk;
				}
			}
		}*/
	}
	public function get_column_info($cid){
		global $new_column_list;
		//获取栏目信息
		foreach($new_column_list as $k=>$v){
			if($cid==$v['id']){
				return $v;
			}
		}
	}
	//获取banner
	public function bannerlist($cid,$mobie){
		$cid=','.$cid.',';
		$w['module']=$cid;
		
		$w['flash_path']=array('eq','1');
		//手机
		if($mobie) $w['flash_path']=array('eq','2');
		
		$l=M('flash')->where($w)->select();
		return $l;
	}


	public function bannerlists($cid,$mobie){
		$cid=','.$cid.',';
		$w['module']=$cid;
		
		$w['flash_path']=array('eq','1');
		//手机
		if($mobie) $w['flash_path']=array('eq','2');
		
		$l=M('flash')->where($w)->find();
		return $l;
	}
	
	
    public function index($catid='',$module='')
    {
		

		
                $parentcount = count(explode(',', $this->categorys[$catid]['arrparentid']));
                if($this->categorys[$catid]['child'] != '0'){
                    $jump_url = M('category')->where("parentid = '$catid'")->order('listorder,id')->getfield('url');
                    //redirect("http://".$_SERVER['HTTP_HOST'].$jump_url);
                }
		$this->Urlrule =F('Urlrule');
		if(empty($catid)) $catid =  intval($_REQUEST['id']);
		$p= max(intval($_REQUEST[C('VAR_PAGE')]),1);
		if($catid){
			$cat = $this->categorys[$catid];
			$bcid = explode(",",$cat['arrparentid']); 
			$bcid = $bcid[1]; 
			if($bcid == '') $bcid=intval($catid);
			if(empty($module))$module=$cat['module'];
			$this->assign('module_name',$module);
			unset($cat['id']);
			$this->assign($cat);
			$cat['id']=$catid;
			$this->assign('catid',$catid);
			$this->assign('bcid',$bcid);
		}
		if($cat['readgroup'] && $this->_groupid!=1 && !in_array($this->_groupid,explode(',',$cat['readgroup']))){$this->assign('jumpUrl',URL('User-Login/index'));$this->error (L('NO_READ'));}
		$fields = F($this->mod[$module].'_Field');
		foreach($fields as $key=>$r){
			$fields[$key]['setup'] =string2array($fields[$key]['setup']);
		}
		$this->assign ( 'fields', $fields); 


		$seo_title = $cat['title'] ? $cat['title'] : $cat['catname'];
		$this->assign ('seo_title',$seo_title);
		$this->assign ('seo_keywords',$cat['keywords']);
		$this->assign ('seo_description',$cat['description']);
				
		

		
		
		
		
		
		
		
		
		if($module=='Guestbook'){
			$where['status']=array('eq',1);
			$this->dao= M($module);
			$count = $this->dao->where($where)->count();
			if($count){
				import ( "@.ORG.Page" );
				$listRows =  !empty($cat['pagesize']) ? $cat['pagesize'] : C('PAGE_LISTROWS');		
				$page = new Page ( $count, $listRows );
				$page->urlrule = geturl($cat,'');
				$pages = $page->show();
				$field =  $this->module[$cat['moduleid']]['listfields'];
				$field =  $field ? $field : '*';
				$list = $this->dao->field($field)->where($where)->order('listorder desc,createtime desc,id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
				
				$this->assign('pages',$pages);
				$this->assign('list',$list);
			}
			$template = $cat['module']=='Guestbook' && $cat['template_list'] ? $cat['template_list'] : 'index';
			$this->display(THEME_PATH.$module.'_'.$template.'.html');            
		}elseif($module=='Feedback'){
			$template = $cat['module']=='Feedback' && $cat['template_list'] ? $cat['template_list'] : 'index' ;

			$this->display(THEME_PATH.$module.'_'.$template.'.html');
		}elseif($module=='Page'){
			$modle=M('Page');
			$data = $modle->find($catid);
			unset($data['id']);

			//分页
			$CONTENT_POS = strpos($data['content'], '[page]');
			if($CONTENT_POS !== false) {			
				$urlrule = geturl($cat,'',$this->Urlrule);
				$urlrule[0] =  urldecode($urlrule[0]);
				$urlrule[1] =  urldecode($urlrule[1]);
				$contents = array_filter(explode('[page]',$data['content']));
				$pagenumber = count($contents);
				for($i=1; $i<=$pagenumber; $i++) {
					$pageurls[$i] = str_replace('{$page}',$i,$urlrule);
				} 
				$pages = content_pages($pagenumber,$p, $pageurls);
				//判断[page]出现的位置
				if($CONTENT_POS<7) {
					$data['content'] = $contents[$p];
				} else {
					$data['content'] = $contents[$p-1];
				}
				$this->assign ('pages',$pages);	
			}

			$template = $cat['template_list'] ? $cat['template_list'] :  'index' ;
			$this->assign ($data);	
			$this->display(THEME_PATH.$module.'_'.$template.'.html');

		}else{
			
			if($catid){
				$seo_title = $cat['title'] ? $cat['title'] : $cat['catname'];
				$this->assign ('seo_title',$seo_title);
				$this->assign ('seo_keywords',$cat['keywords']);
				$this->assign ('seo_description',$cat['description']);
				

				$where = " status=1 ";
				if($cat['child']){							
					$where .= " and catid in(".$cat['arrchildid'].")";			
				}else{
					$where .=  " and catid=".$catid;			
				}
				$newcatid=$_GET['newcatid']+0;
				if(!$newcatid){
					//dump($cat['arrchildid']);
					$nnneecattid=explode(',',$cat['arrchildid']);
					$nnneecattid=$nnneecattid['1'];
					if($catid==148 || $catid==156 || $catid==167) $newcatid=$nnneecattid;
				}
				
				if($newcatid){
					$where .=  " and catid=".$newcatid;
				}
				$this->assign('newcatidnew',$newcatid);
				
				if(empty($cat['listtype'])){
					$this->dao= M($module);
					$count = $this->dao->where($where)->count();
					if($count){
						import ( "@.ORG.Page" );
						$listRows =  !empty($cat['pagesize']) ? $cat['pagesize'] : C('PAGE_LISTROWS');
						$page = new Page ( $count, $listRows );
						$page->urlrule = geturl($cat,'',$this->Urlrule);
						$pages = $page->show(1);
						$field =  $this->module[$this->mod[$module]]['listfields'];
						$field =  $field ? $field : 'id,catid,userid,url,username,title,title_style,keywords,description,thumb,createtime,hits';
						$list = $this->dao->field($field)->where($where)->order('listorder desc,createtime desc,id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
						$this->assign('pages',$pages);
						$this->assign('list',$list);
					}
					$template_r = 'list';
				}else{
					$template_r = 'index';
				}
			}else{
				$template_r = 'list';
			}
			$template = $cat['template_list'] ? $cat['template_list'] : $template_r;
			$this->display($module.':'.$template);
		}
    }

 

	public function show($id='',$module='')
    {
		$this->Urlrule =F('Urlrule');
		$p= max(intval($_REQUEST[C('VAR_PAGE')]),1);		
		$id = $id ? $id : intval($_REQUEST['id']);
		$module = $module ? $module : MODULE_NAME;
		$this->assign('module_name',$module);
		$this->dao= M($module);;
		$data = $this->dao->find($id);
		
		
		$catid = $data['catid'];
		$cat = $this->categorys[$data['catid']];
		if(empty($cat['ishtml']))$this->dao->where("id=".$id)->setInc('hits'); //添加点击次数
		$bcid = explode(",",$cat['arrparentid']); 
		$bcid = $bcid[1]; 
		if($bcid == '') $bcid=intval($catid);

		if($data['readgroup']){
			if($this->_groupid!=1 && !in_array($this->_groupid,explode(',',$data['readgroup'])) )$noread=1;
		}elseif($cat['readgroup']){
			if($this->_groupid!=1 && !in_array($this->_groupid,explode(',',$cat['readgroup'])) )$noread=1;
		}
		if($noread==1){$this->assign('jumpUrl',URL('User-Login/index'));$this->error (L('NO_READ'));}

		$chargepoint = $data['readpoint'] ? $data['readpoint'] : $cat['chargepoint']; 
		if($chargepoint && $data['userid'] !=$this->_userid){
			$user = M('User');
			$userdata =$user->find($this->_userid);
			if($cat['paytype']==1 && $userdata['point']>=$chargepoint){
				$chargepointok = $user->where("id=".$this->_userid)->setDec('point',$chargepoint);
			}elseif($cat['paytype']==2 && $userdata['amount']>=$chargepoint){
				$chargepointok = $user->where("id=".$this->_userid)->setDec('amount',$chargepoint);
			}else{
				$this->error (L('NO_READ'));
			}
		}
	/*上一篇下一篇 add by wei 2012-11-09*/
	$pre = M($module)->where("id<$id and catid=$catid and lang=".LANG_ID)->order("id DESC")->find();
    $next = M($module)->where("id>$id and catid=$catid and lang=".LANG_ID)->order("id ASC")->find();
    $this->assign('pre',$pre);
    $this->assign('next',$next);
	/*end*/

	
		$seo_title = $data['title'].'-'.$cat['catname'];
		$this->assign ('seo_title',$seo_title);
		$this->assign ('seo_keywords',$data['keywords']);
		$this->assign ('seo_description',$data['description']);
		$this->assign ( 'fields', F($cat['moduleid'].'_Field') ); 
		

		$fields = F($this->mod[$module].'_Field');
		foreach($data as $key=>$c_d){
			$setup='';
			$fields[$key]['setup'] =$setup=string2array($fields[$key]['setup']);
			if($setup['fieldtype']=='varchar' && $fields[$key]['type']!='text'){
				$data[$key.'_old_val'] =$data[$key];
				$data[$key]=fieldoption($fields[$key],$data[$key]);
			}elseif($fields[$key]['type']=='images' || $fields[$key]['type']=='files'){ 
				if(!empty($data[$key])){
					$p_data=explode(':::',$data[$key]);
					$data[$key]=array();
					foreach($p_data as $k=>$res){
						$p_data_arr=explode('|',$res);					
						$data[$key][$k]['filepath'] = $p_data_arr[0];
						$data[$key][$k]['filename'] = $p_data_arr[1];
					}
					unset($p_data);
					unset($p_data_arr);
				}
			}
			unset($setup);
		}
		$this->assign('fields',$fields); 


		//手动分页
		$CONTENT_POS = strpos($data['content'], '[page]');
		if($CONTENT_POS !== false) {
			
			$urlrule = geturl($cat,$data,$this->Urlrule);
			$urlrule =  str_replace('%7B%24page%7D','{$page}',$urlrule); 
			$contents = array_filter(explode('[page]',$data['content']));
			$pagenumber = count($contents);
			for($i=1; $i<=$pagenumber; $i++) {
				$pageurls[$i] = str_replace('{$page}',$i,$urlrule);
			} 
			$pages = content_pages($pagenumber,$p, $pageurls);
			//判断[page]出现的位置是否在文章开始
			if($CONTENT_POS<7) {
				$data['content'] = $contents[$p];
			} else {
				$data['content'] = $contents[$p-1];
			}
			$this->assign ('pages',$pages);	
		}

		if(!empty($data['template'])){
			$template = $data['template'];
		}elseif(!empty($cat['template_show'])){
			$template = $cat['template_show'];
		}else{
			$template =  'show';
		}

		$this->assign('catid',$catid);
		$this->assign ($cat);
		$this->assign('bcid',$bcid);

		$this->assign ($data);

		$this->display($module.':'.$template); 
    }

	public function down()
	{

		$module = $module ? $module : MODULE_NAME;
		$id = $id ? $id : intval($_REQUEST['id']);
		$this->dao= M($module);
		$filepath = $this->dao->where("id=".$id)->getField('file');
		$this->dao->where("id=".$id)->setInc('downs');

		if(strpos($filepath, ':/')) { 
			header("Location: $filepath");
		} else {	
			$filepath = '.'.$filepath;
			if(!$filename) $filename = basename($filepath);
			$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
			if(strpos($useragent, 'msie ') !== false) $filename = rawurlencode($filename);
			$filetype = strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
			$filesize = sprintf("%u", filesize($filepath));
			if(ob_get_length() !== false) @ob_end_clean();
			header('Pragma: public');
			header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
			header('Cache-Control: no-store, no-cache, must-revalidate');
			header('Cache-Control: pre-check=0, post-check=0, max-age=0');
			header('Content-Transfer-Encoding: binary');
			header('Content-Encoding: none');
			header('Content-type: '.$filetype);
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Content-length: '.$filesize);
			readfile($filepath);
		}
		exit;
	}

	public function hits()
	{
		$module = $module ? $module : MODULE_NAME;
		$id = $id ? $id : intval($_REQUEST['id']);
		$this->dao= M($module);
		$this->dao->where("id=".$id)->setInc('hits');

		if($module=='Download'){
			$r = $this->dao->find($id);
			echo '$("#hits").html('.$r['hits'].');$("#downs").html('.$r['downs'].');';
		}else{
			$hits = $this->dao->where("id=".$id)->getField('hits');
			echo '$("#hits").html('.$hits.');';
		}
		exit;
	}
	public function verify()
    {
		header('Content-type: image/jpeg');
        $type	 =	 isset($_GET['type'])? get_safe_replace($_GET['type']):'jpeg';
        import("@.ORG.Image");
        Image::buildImageVerify(4,1,$type);
    }
	public function getSafeStr($str){
		$str=strip_tags($str);
		$s1 = iconv('utf-8','gbk',$str);
		$s0 = iconv('gbk','utf-8',$s1);
		if($s0 == $str){
			return $str;//'utf-8';
		}else{
			return iconv('gbk','utf-8',$str);//'gbk'
		}
	}
	public function getlink(){
		$w['show_ok']=1;
		//$w['show_ok']=1;
		return $list=M('link')->where($w)->order('com_ok desc,orderno desc')->select();
	}
	
	
	
	
	
	
	
	
    public function alisms($sms_code_m=0){
        
			import ( "@.ORG.SignatureHelper" );
			/**
			 * 发送短信
			 */
			
				$sms_code=array(
					'0'=>DYSMS_ZZ,
					'1'=>DYSMS_DL,
				);
				$sms_code=$sms_code[$sms_code_m];
				$sms_code_str=$_SESSION['sms_code'];//验证码
				$sms_code_tel=$_SESSION['sms_tel'];//手机号
				
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
				//dump($content);
				//$content = json_decode(json_encode(simplexml_load_string($content)),TRUE);
				$content = json_decode(json_encode($content,true),true);
				return $content;
			
		
    }
	
	
	
	
	
			function vpost($url,$data){
				// 模拟提交数据函数
				$curl = curl_init(); // 启动一个CURL会话
				curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
				curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
				curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
				curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
				curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
				curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
				curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
				$tmpInfo = curl_exec($curl); // 执行操作
				if (curl_errno($curl)) {
				   echo 'Errno'.curl_error($curl);//捕抓异常
				}
				curl_close($curl); // 关闭CURL会话
				return $tmpInfo; // 返回数据
			}
	
	
	//验证是否登录
	function chelogins(){
		$admin_userid=cookie('admin_userid')+0;
		$admin_msn=cookie('admin_msn');
		if($admin_userid && $admin_msn){
			$w['id']=$admin_userid;
				$admin_userid=M('user')->where($w)->limit(1)->find();
				if($admin_userid['admin_msn']==$admin_msn){
					return $admin_userid['tel'];
				}
		}else return false;
	}
	
	
	//更新车辆品牌
	function getcarone(){
		$host = "http://jisucxdq.market.alicloudapi.com";
		$path = "/car/brand";
		$method = "GET";
		$appcode = CARINFOAPPCODE;
		$headers = array();
		array_push($headers, "Authorization:APPCODE " . $appcode);
		$querys = "";
		$bodys = "";
		$url = $host . $path;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		if (1 == strpos("$".$host, "https://"))
		{
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		}
		$content=curl_exec($curl);
		$content=(json_decode($content,true));
		$content=$content['result'];
		//dump($content);exit;
		
		//获取现有品牌车辆
		$carlist=M('car')->select();
		foreach($carlist as $ck=>$cv){
			$logolist[$cv['logo']]=$cv['logo'];
		}
		$upnum=0;
		foreach($content as $k=>$v){
			foreach($logolist as $bk=>&$bv){
				if(!$logolist[$v['id']]){
					//更新缺少车辆品牌
					//echo $av['id'].'<br>';
					$a['name']=$v['name'];
					$a['initial']=$v['initial'];
					$a['parentid']=$v['parentid'];
					$a['logo']=$v['id'];
					$a['depth']=$v['depth'];
					$admin_userid=M('car')->add($a);
					if($admin_userid){
						$this->addcarinfoall($v['id']);
						$this->upcarinfoall();
						$this->getcarttone($v['id']);//更新缺少子品牌
						$upnum++;
						break;
					}
				}
			}
		}
		echo $upnum;exit;
	}
	//判断是否有所有车型空info
	function upcarinfoall(){
		$s=M('carinfo')->where('carinfo=\'\'')->delete();
		$slist=M('carinfo')->select();
		foreach($slist as $ck=>$cv){
			$logolist[$cv['logo']]=$cv['logo'];
		}
		$content=M('car')->select();
		foreach($content as $k=>$v){
			foreach($logolist as $bk=>&$bv){
				if(!$logolist[$v['logo']]){
					if($this->addcarinfoall($v['logo'])){
						break;
					}
				}
			}
		}
	}
	//保存所有车型总信息
	function addcarinfoall($oneid){
		if($oneid){
			$host = "http://jisucxdq.market.alicloudapi.com";
			$path = "/car/carlist";
			$method = "GET";
			$appcode = CARINFOAPPCODE;
			$headers = array();
			array_push($headers, "Authorization:APPCODE " . $appcode);
			$querys = "parentid=".$oneid;
			$bodys = "";
			$url = $host . $path . "?" . $querys;

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_FAILONERROR, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			if (1 == strpos("$".$host, "https://"))
			{
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			}
			//var_dump(curl_exec($curl));
					$content=curl_exec($curl);
					$fa['logo']=$oneid;
					$fa['carinfo']=$content;
					if($fa['carinfo']) $fs=M('carinfo')->add($fa);
					return $fs;
					//$carinfo=$content;
		}
	}
	
	//一次性获取更新所以子品牌
	function getcarttall(){
		exit;
		$pplist=M('car')->select();
		foreach($pplist as $k=>$v){
			if($v['logo']){
				$this->getcartt($v['logo']);
			}
		}
	}
	//更新某个品牌缺失的子品牌
	function getcarttone($oneid=1){
		$getoneid=$_GET['oneid']+0;
		if($getoneid) $oneid=$getoneid;
		if(!$oneid) return false;
		//获取这个品牌的所有子品牌
		$onecarinfo=M('carinfo')->where('logo='.$oneid)->find();
		$content=(json_decode($onecarinfo['carinfo'],true));
		$content=$content['result'];
		//dump($content);exit;
		foreach($content as $k=>$vl){
			if($vl['depth']=='2'){
				$thisallid[$vl['id']]=$vl['id'];//该品牌的所有子品牌id
			}
		}
		if(empty($thisallid)){
			echo '该品牌的所有子品牌id获取失败';
			exit;
		}
		//对比
		$carttlist=M('cartt')->where('oneid='.$oneid)->select();
		foreach($carttlist as $kc=>$vc){
			$carttlist_vc[$vc['logo']]=$vc['logo'];
		}

		//dump($carttlist_vc);exit;
		//如果缺失
		$upnum=0;
		if(is_array($carttlist_vc)){
			foreach($thisallid as $bk=>$bv){
				if(!$carttlist_vc[$bv]){
					foreach($content as $kcc=>$vlcc){
						if($vlcc['depth']=='2' && $vlcc['id']==$bv){
							$a['name']=$vlcc['name'];
							$a['fullname']=$vlcc['fullname'];
							$a['parentname']=$vlcc['name'];
							$a['oneid']=$oneid;
							$a['salestate']=$vlcc['salestate'];
							$a['initial']=$vlcc['initial'];
							$a['parentid']=$vlcc['parentid'];//$vlcc['parentid'];
							$a['logo']=$vlcc['id'];
							$a['depth']=$vlcc['depth'];
							$admin_userid=M('cartt')->add($a);
							if($admin_userid){
								$upnum++;
								break;
							}
						}
					}
				}
			}
		}else{
			//如果本来就没有
				foreach($content as $kcca=>$vlcca){
					if($vlcca['depth']=='2'){
						$a['name']=$vlcca['name'];
						$a['fullname']=$vlcca['fullname'];
						$a['parentname']=$vlcca['name'];
						$a['oneid']=$oneid;
						$a['salestate']=$vlcca['salestate'];
						$a['initial']=$vlcca['initial'];
						$a['parentid']=$vlcca['parentid'];//$vlcca['parentid'];
						$a['logo']=$vlcca['id'];
						$a['depth']=$vlcca['depth'];
						$admin_userid=M('cartt')->add($a);
						if($admin_userid){
							$upnum++;
							//break;
						}
					}
				}
		}
		echo '更新子品牌 '.$upnum.'个';
		return $upnum;
		exit;
		
	}
	//更新子品牌
	function getcartt($oneid){
		if(!$oneid) return false;
		$clist=M('carinfo')->where('logo='.$oneid)->find();
		$content=(json_decode($clist['carinfo'],true));
		$content=$content['result'];
		//dump($content);exit;
		
		foreach($content as $k=>$vl){
			if($vl['depth']=='2'){
				$a['name']=$vl['name'];
				$a['fullname']=$vl['fullname'];
				$a['parentname']=$vl['name'];
				$a['oneid']=$oneid;
				$a['salestate']=$vl['salestate'];
				$a['initial']=$vl['initial'];
				$a['parentid']=$vl['parentid'];//$vl['parentid'];
				$a['logo']=$vl['id'];
				$a['depth']=$vl['depth'];
				$admin_userid=M('cartt')->add($a);
			}
		}
	}
	
	//一次性获取所有车型
	function getcartallinfo(){
		exit;
		$pplist=M('car')->select();
		foreach($pplist as $k=>$v){
			$this->upcart($v['logo']);
		}
		
	}
	function admingetupcart(){
		$oneid=$_GET['oneid']+0;
		echo $this->upcart($oneid);
	}
	//更新车型
	function upcart($oneid){
		/*$getoneid=$_GET['oneid']+0;
		if($getoneid) $oneid=$getoneid;*/
		if(!$oneid) return false;
		$onecarinfo=M('carinfo')->where('logo='.$oneid)->find();
		$content=(json_decode($onecarinfo['carinfo'],true));
		$content=$content['result'];
			$upnum=0;
			
			$cartlist=M('cart')->where('oneid='.$oneid)->select();
			
			if(empty($cartlist)){
				foreach($content as $k=>$v){
					if($v['depth']=='2'){
						foreach($v['carlist'] as $kl=>$vl){
							$a['name']=$vl['name'];
							$a['fullname']=$vl['fullname'];
							$a['parentname']=$v['name'];
							$a['oneid']=$oneid;
							$a['salestate']=$vl['salestate'];
							$a['initial']=$vl['initial'];
							$a['parentid']=$vl['parentid'];//$vl['parentid'];
							$a['logo']=$vl['id'];
							$a['depth']=$vl['depth'];
							//dump($a);
							$admin_userid=M('cart')->add($a);
							if($admin_userid){
								$upnum++;
							}
						}
					}
				}
			}else{
				//更新缺失
				foreach($cartlist as $k=>$v){
					$cartlist_vc[$v['logo']]=$v['logo'];
				}
				
					foreach($content as $ks=>$vs){
						if($vs['depth']=='2'){
							foreach($vs['carlist'] as $klc=>$vlc){
								$content_vc[$vlc['id']]=$vlc['id'];
							}
						}
					}
				foreach($content_vc as $bk=>$bv){
					if(!$cartlist_vc[$bv]){
						foreach($content as $kc=>$vc){
							if($vc['depth']=='2'){
								foreach($vc['carlist'] as $kl=>$vl){
									if($vl['id']==$bv){
										$a['name']=$vl['name'];
										$a['fullname']=$vl['fullname'];
										$a['parentname']=$vc['name'];
										$a['oneid']=$oneid;
										$a['salestate']=$vl['salestate'];
										$a['initial']=$vl['initial'];
										$a['parentid']=$vl['parentid'];//$vl['parentid'];
										$a['logo']=$vl['id'];
										$a['depth']=$vl['depth'];
										//dump($a);
										$admin_userid=M('cart')->add($a);
										if($admin_userid){
											$upnum++;
											break;
										}
									}
								}
							}
						}
					}
				}	
					
			}

		return $upnum;
		
	}
	
	//更新具体车型
	function admingetcarfallinfo(){
		$freid=$_GET['freid']+0;
		echo $this->getcarfallinfo($freid);
	}
	//保存具体车型allinfo
	function getcarfallinfo($freid){
		if(!$freid) return;
		M('carfre')->where('logo=\'\'')->delete();
		//获取某车型下面的所有具体车型
			$freidlist=M('carfre')->where('freid='.$freid)->select();
			//dump($freidlist);
			foreach($freidlist as $k=>$v){
				$f_logo[$v['logo']]=$v['logo'];
			}
			//dump($f_logo);
		$oneid=$freidlist['0']['oneid'];
		$twoid=$freidlist['0']['twoid'];
		//获取车型数据库所有
		$carlist=M('carinfo')->where('logo='.$oneid)->find();
		$carlist=$carlist['carinfo'];
		$count=json_decode($carlist,true);
		$upnum=0;
		
		foreach($count['result'] as $lc=>$lk){
			if($lk['depth']=='2'){
				foreach($lk['carlist'] as $ck=>$cv){
					if($cv['depth']=='3' && $cv['id']==$freid){
						foreach($cv['list'] as $dk=>$dv){
							/*$fa_logo[$dv['id']]=$dv;
							$fa_logo_id[]=$dv['id'];*/
							$fa[]=$dv;
						}
					}
				}
			}
		}
		foreach($fa as $k=>$v){
			if(!$f_logo[$v['id']]){
				$a['oneid']=$oneid;
				$a['twoid']=$twoid;
				$a['freid']=$v['parentid'];
				
				$a['logo']=$v['id'];
				$a['name']=$v['name'];
				$a['initial']=$v['initial'];
				$a['price']=$v['price'];
				$a['yeartype']=$v['yeartype'];
				$a['productionstate']=$v['productionstate'];
				$a['salestate']=$v['salestate'];
				$a['sizetype']=$v['sizetype'];
				$a['depth']=$v['depth'];
				$a['parentid']=$v['parentid'];
				$ad=M('carfre')->add($a);
				if($ad){
					$upnum++;
					break;
				}
			}
			
		}
		//echo $upnum;
		return $upnum;
		exit;
		/*$f_c=array_diff($f_logo,$fa_logo_id);
		dump($fa_logo_id);exit;*/
		foreach($fa_logo as $ffk=>$ffv){
			if(!$f_logo[$ffv]){
				foreach($count['result'] as $lc=>$lk){
					if($lk['depth']=='2'){
						foreach($lk['carlist'] as $ck=>$cv){
							if($cv['depth']=='3' && $cv['id']==$freid){
								foreach($cv['list'] as $dk=>$dv){
									if($dv['id']==$ffv){
										//echo $dv['id'];
										$a['oneid']=$lk['parentid'];
										$a['twoid']=$lk['id'];
										$a['freid']=$cv['id'];
										$a['logo']=$dv['logo'];
										$a['name']=$dv['name'];
										$a['initial']=$dv['initial'];
										$a['price']=$dv['price'];
										$a['yeartype']=$dv['yeartype'];
										$a['productionstate']=$dv['productionstate'];
										$a['salestate']=$dv['salestate'];
										$a['sizetype']=$dv['sizetype'];
										$a['depth']=$dv['depth'];
										$a['parentid']=$dv['parentid'];
										$ad=M('carfre')->add($a);
										if($ad){
											$upnum++;
											break;
										}
									}
								}
							}
						}
					}
				}
			}
		}

		echo $upnum;
		//$f_c=array_diff($f_logo,$c_logo);
		//dump($f_c);
		//dump($c_logo);
		exit;
		
		$carlist=M('carinfo')->order('tid asc')->limit(180,20)->select();
		//dump($carlist['carinfo']);exit;
		foreach($carlist as $k=>$v){
			$v=(json_decode($v['carinfo'],true));
			foreach($v['result'] as $bk=>$bv){
				if($bv['depth']=='2'){
					foreach($bv['carlist'] as $ck=>$cv){
						if($cv['depth']=='3'){
							foreach($cv['list'] as $dk=>$dv){
								$a['oneid']=$bv['parentid'];
								$a['twoid']=$bv['id'];
								$a['freid']=$cv['id'];
								$a['logo']=$dv['logo'];
								$a['name']=$dv['name'];
								$a['initial']=$dv['initial'];
								$a['price']=$dv['price'];
								$a['yeartype']=$dv['yeartype'];
								$a['productionstate']=$dv['productionstate'];
								$a['salestate']=$dv['salestate'];
								$a['sizetype']=$dv['sizetype'];
								$a['depth']=$dv['depth'];
								$a['parentid']=$dv['parentid'];
								$ad=M('carfre')->add($a);
							}
						}
					}
				}
			}
		}
		
		
		//carfre
		exit;
		
		$flist=M('cart')->select();
		foreach($flist as $k=>$v){
			$c[$v['tid']]=$v['tid'];
		}
		$f=M('cartallinfo')->select();
		foreach($flist as $kf=>$vf){
			$fc[$vf['tid']]=$vf['tid'];
		}
		array_diff($c,$fc);
		exit;
		//$tid=10848;
		//$map['tid']=array('in','0,12671,12672,12673,12674,12675,12676,12677,12678,12679,12680,12681,12682,12683,12684,12685,12686,12687,12688,12689,12690,12691,12692,12693,12694,12695,12696,12697');
		if($map['tid']) $flist=M('cart')->where($map)->select();
		else $flist=M('cart')->order('tid asc')->limit(1874,200)->select();
		$sibai=0;
		foreach($flist as $k=>$v){
			$host = "http://jisucxdq.market.alicloudapi.com";
			$path = "/car/detail";
			$method = "GET";
			$appcode = CARINFOAPPCODE;
			$headers = array();
			array_push($headers, "Authorization:APPCODE " . $appcode);
			$querys = "carid=".$v['logo'];
			$bodys = "";
			$url = $host . $path . "?" . $querys;

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_FAILONERROR, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			if (1 == strpos("$".$host, "https://"))
			{
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			}
					$content=curl_exec($curl);
					$fa['freid']=$v['logo'];
					$fa['logo']=$v['tid'];//cart tid
					$fa['carinfo']=$content;
					if($fa['carinfo']){
						$fs=M('cartallinfo')->add($fa);
						if(!$fs) $sibai.=','.$v['tid'];
					}else $sibai.=','.$v['tid'];
					//return $fs;
		}
		echo $sibai;
	}
	
	//获取具体车型
	function getjtnews($oneid){
		exit;
		if(!$oneid) return false;
		$content=M('carinfo')->where('logo='.$oneid)->find();
		$content=(json_decode($content['carinfo'],true));
		$content=$content['result'];
		//dump($content);exit;
			foreach($content as $ka=>$va){
				foreach($va['carlist'] as $kb=>$vb){
					foreach($vb['list'] as $kc=>$vc){
						if($vc['id'] && $oneid && $va['id']) $tttgg=$this->jtnes($vc['id'],$oneid,$va['id']);
					}
				}
				
			}
		//dump($content);exit;
		return $tttgg;
	}
function jtnes($carid,$oneid,$twoid){
		exit;
		if(!$carid || !$oneid || !$twoid) return false;
		$host = "http://jisucxdq.market.alicloudapi.com";
		$path = "/car/detail";
		$method = "GET";
		$appcode = CARINFOAPPCODE;
		$headers = array();
		array_push($headers, "Authorization:APPCODE " . $appcode);
		$querys = "carid=".$carid;
		$bodys = "";
		$url = $host . $path . "?" . $querys;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		if (1 == strpos("$".$host, "https://"))
		{
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		}
		$content=curl_exec($curl);
		$infoarr=$content;
		$content=(json_decode($content,true));
		//dump($content);exit;
		$content=$content['result'];
		
		//star
		/*foreach($content as $cok=>&$cov){
			if(!$cov) $cov='无';
		}
		
		foreach($content['basic'] as $cokbasic=>&$covbasic){
			if(!$covbasic) $covbasic='无';
		}
		foreach($content['productionstate'] as $cokproductionstate=>&$covproductionstate){
			if(!$covproductionstate) $covproductionstate='无';
		}
		foreach($content['sizetype'] as $coksizetype=>&$covsizetype){
			if(!$covsizetype) $covsizetype='无';
		}
		foreach($content['gearbox'] as $cokgearbox=>&$covgearbox){
			if(!$covgearbox) $covgearbox='无';
		}
		foreach($content['body'] as $cokbody=>&$covbody){
			if(!$covbody) $covbody='无';
		}
		foreach($content['engine'] as $cokengine=>&$covengine){
			if(!$covengine) $covengine='无';
		}
		foreach($content['chassisbrake'] as $cokchassisbrake=>&$covchassisbrake){
			if(!$covchassisbrake) $covchassisbrake='无';
		}
		foreach($content['safe'] as $coksafe=>&$covsafe){
			if(!$covsafe) $covsafe='无';
		}
		foreach($content['drivingauxiliary'] as $cokdrivingauxiliary=>&$covdrivingauxiliary){
			if(!$covdrivingauxiliary) $covdrivingauxiliary='无';
		}
		foreach($content['doormirror'] as $cokdoormirror=>&$covdoormirror){
			if(!$covdoormirror) $covdoormirror='无';
		}*/
		
			/*function strReplace(&$a){
				echo $a;
				if(!is_array($a) && !$a) $a = '无';
				else foreach($a as $k=>&$v){
					if(!is_array($v) && !$v) $v = '无';
					else strReplace($v);
				}
			}*/
			//strReplace($content);
			//dump($content);exit;
			
			$rrccarr=array(
				'body'=>array(
					'color'
					,'luggagevolume'
					,'fullweight'
					,'len'
					,'width'
					,'height'
				),
				'basic'=>array(
					'price'
					,'comfuelconsumption'
					,'seatnum'
				),
				'gearbox'=>array(
					'gearbox'
					,'shiftpaddles'
				),
				'engine'=>array(
					'displacement'
					,'cylindernum'
					,'maxhorsepower'
					,'environmentalstandards'
					,'maxtorque'
					,'fuelgrade'
					,'startstopsystem'
					,'maxpowerspeed'
					,'maxtorquespeed'
				),
				'chassisbrake'=>array(
					'centerdifferentiallock'
					,'parkingbraketype'
					,'drivemode'
					,'adjustablesuspension'
					,'powersteering'
					,'frontsuspensiontype'
					,'rearsuspensiontype'
				),
				'drivingauxiliary'=>array(
					'nightvisionsystem'
					,'abs'
					,'blindspotdetection'
				),
				'doormirror'=>array(
					'antipinchwindow'
					,'skylightstype'
				)
			);
			foreach($rrccarr as $rk=>$rv){
				foreach($rv as $rrk=>$rrv){
					if(!$content[$rk][$rrv]) $content[$rk][$rrv]='无';
				}
				
			}
			
			
			if(!$content['safe']['tirepressuremonitoring']) $content['safe']['tirepressuremonitoring']='无';
			
			//dump($content);exit;
			
			
			/*$getttarrr=array(
				"content['body']['color']"
				,"content['basic']['price']"
				,"content['basic']['comfuelconsumption']"
				,"content['basic']['seatnum']"
				,"content['gearbox']['gearbox']"
				,"content['body']['luggagevolume']"
				,"content['body']['fullweight']"
				,"content['body']['len']"
				,"content['body']['width']"
				,"content['body']['height']"
				,"content['engine']['displacement']"
				,"content['engine']['cylindernum']"
				,"content['engine']['maxhorsepower']"
				,"content['engine']['environmentalstandards']"
				,"content['engine']['maxtorque']"
				,"content['engine']['fuelgrade']"
				,"content['engine']['startstopsystem']"
				,"content['gearbox']['shiftpaddles']"
				,"content['engine']['maxpowerspeed']"
				,"content['engine']['maxtorquespeed']"
				,"content['chassisbrake']['centerdifferentiallock']"
				,"content['chassisbrake']['parkingbraketype']"
				,"content['chassisbrake']['drivemode']"
				,"content['chassisbrake']['adjustablesuspension']"
				,"content['chassisbrake']['powersteering']"
				,"content['chassisbrake']['frontsuspensiontype']"
				,"content['chassisbrake']['rearsuspensiontype']"
				,"content['safe']['tirepressuremonitoring']"
				,"content['drivingauxiliary']['nightvisionsystem']"
				,"content['doormirror']['antipinchwindow']"
				,"content['drivingauxiliary']['abs']"
				,"content['drivingauxiliary']['blindspotdetection']"
				,"content['doormirror']['skylightstype']"
			);*/
			/*foreach($getttarrr as $gk=>$gv){
				strReplace(${$gv});
			}*/
			//strReplace($content);
			//dump($content);exit;
		//end
		//$content['parentid']=$carid;
		
		
		$a['oneid']=$oneid;
		$a['twoid']=$twoid;
		$a['freid']=$content['parentid'];
		
		if(!$a['oneid'] || !$a['freid'] ||!$a['twoid']) return false;
		
		$a['depth']=$content['depth'];
		$a['sizetype']=$content['sizetype'];
		$a['salestate']=$content['salestate'];
		$a['productionstate']=$content['productionstate'];
		$a['yeartype']=$content['yeartype'];
		$a['price']=$content['price'];
		$a['initial']=$content['initial'];
		$a['name']=$content['name'];
		$a['infoarr']=$infoarr;
		
		$a['displacement']=$content['engine']['displacement'];
		$a['parentid']=$content['parentid'];
		$a['logo']=$content['id'];
		
		foreach($a as $kf=>&$vf){
			if(!$vf) $vf='无';
		}
		//dump($a);exit;
		
			$color=explode('|',$content['body']['color']);
		if($color['1']){
			foreach($color as $ck=>$cv){
				$ncolor=explode(',',$cv);
				$newcolor.='<p class="getinfocaredioo1"><span class="gacinfooooyyssssspan" style="display: inline-block;width:10px;height:10px;background:'.$ncolor['1'].';"></span> <span class="ve_inf_each_span">'.$ncolor['0'].'</span></p>';
			}
		}else $newcolor='无';
$info=<<<EOT
	<div class="ve_inform">
		<div class="ve_inf_title"><span class="getinfocaredioo2">基本信息</span></div>
		<p class="ve_inf_each"><span class="getinfocaredioo3">官方指导价</span><span class="ve_inf_each_span">{$content['basic']['price']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">销售状态</span><span class="ve_inf_each_span">{$a['productionstate']}/{$a['salestate']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">尺寸类型</span><span class="ve_inf_each_span">{$a['sizetype']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">综合油耗(L/100Km)</span><span class="ve_inf_each_span">{$content['basic']['comfuelconsumption']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">乘员人数(区间)(个)</span><span class="ve_inf_each_span">{$content['basic']['seatnum']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">变速箱</span><span class="ve_inf_each_span">{$content['gearbox']['gearbox']}</span></p>
	</div>
	
	<div class="ve_inform gacinfooooyyssss">
		<div class="ve_inf_title"><span class="getinfocaredioo2">外观参数</span></div>
		<p class="ve_inf_each"><span class="getinfocaredioo3">车身颜色</span><span class="ve_inf_each_span">{$newcolor}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">行李厢容积(L)</span><span class="ve_inf_each_span">{$content['body']['luggagevolume']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">满载质量(Kg)</span><span class="ve_inf_each_span">{$content['body']['fullweight']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">车长(mm)</span><span class="ve_inf_each_span">{$content['body']['len']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">车宽(mm)</span><span class="ve_inf_each_span">{$content['body']['width']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">车高(mm)</span><span class="ve_inf_each_span">{$content['body']['height']}</span></p>
	</div>
	
	<div class="ve_inform">
		<div class="ve_inf_title"><span class="getinfocaredioo2">发动机参数</span></div>
		<p class="ve_inf_each"><span class="getinfocaredioo3">排量(L)</span><span class="ve_inf_each_span">{$content['engine']['displacement']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">气缸数(个)</span><span class="ve_inf_each_span">{$content['engine']['cylindernum']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">最大马力(Ps)</span><span class="ve_inf_each_span">{$content['engine']['maxhorsepower']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">燃油标号</span><span class="ve_inf_each_span">{$content['engine']['fuelgrade']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">最大扭矩(Nm)</span><span class="ve_inf_each_span">{$content['engine']['maxtorque']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">环保标准</span><span class="ve_inf_each_span">{$content['engine']['environmentalstandards']}</span></p>
	</div>
	
	<div class="ve_inform">
		<div class="ve_inf_title"><span class="getinfocaredioo2">变速箱参数</span></div>
		<p class="ve_inf_each"><span class="getinfocaredioo3">变速箱</span><span class="ve_inf_each_span">{$content['gearbox']['gearbox']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">启停系统</span><span class="ve_inf_each_span">{$content['engine']['startstopsystem']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">换挡拨片</span><span class="ve_inf_each_span">{$content['gearbox']['shiftpaddles']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">最大功率转速(rpm)</span><span class="ve_inf_each_span">{$content['engine']['maxpowerspeed']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">最大扭矩转速(rpm)</span><span class="ve_inf_each_span">{$content['engine']['maxtorquespeed']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">中央差速器锁</span><span class="ve_inf_each_span">{$content['chassisbrake']['centerdifferentiallock']}</span></p>
	</div>
	
	<div class="ve_inform">
		<div class="ve_inf_title"><span class="getinfocaredioo2">车轮制动参数</span></div>
		<p class="ve_inf_each"><span class="getinfocaredioo3">驻车制动类型</span><span class="ve_inf_each_span">{$content['chassisbrake']['parkingbraketype']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">驱动方式</span><span class="ve_inf_each_span">{$content['chassisbrake']['drivemode']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">可调悬挂</span><span class="ve_inf_each_span">{$content['chassisbrake']['adjustablesuspension']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">转向助力</span><span class="ve_inf_each_span">{$content['chassisbrake']['powersteering']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">前制动类型</span><span class="ve_inf_each_span">{$content['chassisbrake']['frontsuspensiontype']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">后制动类型</span><span class="ve_inf_each_span">{$content['chassisbrake']['rearsuspensiontype']}</span></p>
	</div>
	
	<div class="ve_inform">
		<div class="ve_inf_title"><span class="getinfocaredioo2">更多参数</span></div>
		<p class="ve_inf_each"><span class="getinfocaredioo3">胎压监测装置</span><span class="ve_inf_each_span">{$content['safe']['tirepressuremonitoring']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">夜视系统</span><span class="ve_inf_each_span">{$content['drivingauxiliary']['nightvisionsystem']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">电动窗防夹功能</span><span class="ve_inf_each_span">{$content['doormirror']['antipinchwindow']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">刹车防抱死(ABS)</span><span class="ve_inf_each_span">{$content['drivingauxiliary']['abs']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">盲点检测</span><span class="ve_inf_each_span">{$content['drivingauxiliary']['blindspotdetection']}</span></p>
		<p class="ve_inf_each"><span class="getinfocaredioo3">天窗形式</span><span class="ve_inf_each_span">{$content['doormirror']['skylightstype']}</span></p>
	</div>
EOT;
		$a['info']=$info;

								$admin_userid=M('carf')->add($a);
								/*if(!$admin_userid){
									echo $k.'-'.$kl;
									break;
								}*/
		
		
		//var_dump(curl_exec($curl));
	}
	
	
function unescapessss($str){
    $ret = '';
    $len = strlen($str);
    for ($i = 0; $i < $len; $i++){
        if ($str[$i] == '%' && $str[$i+1] == 'u'){
            $val = hexdec(substr($str, $i+2, 4));
            if ($val < 0x7f) $ret .= chr($val);
            else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
            else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));
            $i += 5;
        }elseif ($str[$i] == '%'){
            $ret .= urldecode(substr($str, $i, 3));
            $i += 2;
        }else{
            $ret .= $str[$i];
        }
    }
    return $this->getSafeStr($ret);
}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>