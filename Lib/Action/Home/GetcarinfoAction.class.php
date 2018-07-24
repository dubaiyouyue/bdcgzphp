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
class GetcarinfoAction extends BaseAction{
	//获取品牌
	function ajax_get_pp(){
		$plist=M('car')->order('initial asc,tid asc')->select();
			echo '<option value ="">请选择品牌</option>';
		foreach($plist as $pk=>$pv){
			echo '<option value ="'.$pv['logo'].'">'.$pv['name'].'</option>';
		}
		exit;
	}
	
	//获取检测人员
	function jiancesrr(){
		$plist=M('user')->where('groupid=2')->select();
			echo '<option value ="">请选择检测人员</option>';
		foreach($plist as $pk=>$pv){
			echo '<option value ="'.$pv['id'].'">'.$pv['username'].'</option>';
		}
		exit;
	}
	
	function ajax_get_cx(){
		//获取车型
		$parentid=$_GET['parentid']+0;
		$plist=M('cart')->where('parentid='.$parentid)->select();
		if(!empty($plist)){
			echo '<option value ="">请选择车型</option>';
			foreach($plist as $pk=>$pv){
				echo '<option value ="'.$pv['logo'].'">'.$pv['name'].'</option>';
			}
		}/*else{
			if($this->upcart($parentid)){
				echo '<option value ="">请选择车型</option>';
				foreach($plist as $pk=>$pv){
					echo '<option value ="'.$pv['logo'].'">'.$pv['name'].'</option>';
				}
			}
		}*/

		exit;
	}
	//后台查看品牌列表
	function admingetcarinfo(){
		$plist=M('car')->select();
		echo '<span style="color:red;">可修改|前面的文字，请勿修改|后面的数字 按住Ctrl+F搜索</span><br><br>';
		foreach($plist as $pk=>$pv){
			echo $pv['name'].'|'.$pv['logo'].'<br>';
		}
	}
	function admingetcarinfocx(){
		$plist=M('cart')->select();
		echo '<span style="color:red;">可修改|前面的文字，请勿修改|后面的数字 按住Ctrl+F搜索</span><br><br>';
		foreach($plist as $pk=>$pv){
			echo $pv['name'].'|'.$pv['logo'].'<br>';
		}
	}
	//end
	//具体更新单独一个信息
	function getinfoonemore($oneid,$twoid,$freid){
		exit;
		if(!$oneid) return false;
		//去查找是否已经有车辆info
		$f=M('carinfo')->where('logo='.$oneid)->order('tid desc')->find();
		$carinfo=$f['carinfo'];
		/*dump($f);
		echo $carinfo;exit;*/
		if(!$carinfo){
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
					$fs=M('carinfo')->add($fa);
					
					$content=(json_decode($content,true));
					$content=$content['result'];
					//$carinfo=$content;
		}else{
			$content=(json_decode($carinfo,true));
			$content=$content['result'];
			//$carinfo=$content;
		}
		if(!$content){
			echo 'no content';
			exit;
		}
		if($freid){
			//dump($content);exit;
			//车型》》具体车型
			foreach($content as $ak=>$av){
				if($av['id']==$twoid){
					foreach($av['carlist'] as $bk=>$bv){
						if($bv['id']==$freid){
							foreach($bv['list'] as $fk=>$fv){
								$this->jt($fv['id'],$oneid,$twoid);
							}
							
						}//$nlist=$bv['list'];
					}
				}
			}
		}
		//dump($nlist);
	}
	//end
	
	function ajax_get_jbcx(){
		//具体车型
		$parentid=$_GET['parentid']+0;
		$oneid=$_GET['oneid']+0;
		$twoid=$_GET['twoid']+0;
		$freid=$_GET['freid']+0;
		$plist=M('carfre')->where('freid='.$freid)->select();
		
				echo '<option value ="">请选择具体车型</option>';
				foreach($plist as $pk=>$pv){
					echo '<option value ="'.$pv['logo'].'">'.$pv['name'].'</option>';
				}
			
		exit;
	}
	function ajax_get_cxq(){
		//获取子品牌
		$parentid=$_GET['parentid']+0;
		$plist=M('cartt')->where('parentid='.$parentid)->select();
		if(!empty($plist)){
			echo '<option value ="">请选择子品牌</option>';
			foreach($plist as $pk=>$pv){
				echo '<option value ="'.$pv['logo'].'">'.$pv['name'].'</option>';
			}
		}else{
			if($this->getcarttone($parentid)){
				$plist=M('cartt')->where('parentid='.$parentid)->select();
				echo '<option value ="">请选择子品牌</option>';
				foreach($plist as $pk=>$pv){
					echo '<option value ="'.$pv['logo'].'">'.$pv['name'].'</option>';
				}
			}
		}
		exit;
	}
	function getcarinfomore(){
		//获取车辆信息内容
		$logoid=$_GET['logoid']+0;
		
		if(!$logoid) return false;
		
			$host = "http://jisucxdq.market.alicloudapi.com";
			$path = "/car/detail";
			$method = "GET";
			$appcode = CARINFOAPPCODE;
			$headers = array();
			array_push($headers, "Authorization:APPCODE " . $appcode);
			$querys = "carid=".$logoid;
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
			$content=json_decode($content,true);
			$infoarr=$content;
			$content=$content['result'];
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
		
		
		/*$a['oneid']=$oneid;
		$a['twoid']=$twoid;
		$a['freid']=$content['parentid'];*/
		
		//if(!$a['oneid'] || !$a['freid'] ||!$a['twoid']) return false;
		
		//$a['depth']=$content['depth'];
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
		$a['info']=str_replace("\r\n\t",'',$a['info']);
		//dump($a);
		
		
				if(file_exists('carico/300/'.$content['parentid'].'.jpg')) $logosrc='/carico/300/'.$content['parentid'].'.jpg';
				else if(file_exists('carico/300/'.$content['parentid'].'.png')) $logosrc='/carico/300/'.$content['parentid'].'.png';
			
			if($logosrc) $a['logosrc']=$logosrc;
		
		echo json_encode($a);
		exit;
		
		
		
		
		
		
		
		$plist=M('carf')->where('logo='.$logoid)->find();
		$plist['info']=str_replace("\r\n\t",'',$plist['info']);
		//$plist['info']=str_replace("\r",'',$plist['info']);
			
			
				if(file_exists('carico/300/'.$plist['freid'].'.jpg')) $logosrc='/carico/300/'.$plist['freid'].'.jpg';
				else if(file_exists('carico/300/'.$plist['freid'].'.png')) $logosrc='/carico/300/'.$plist['freid'].'.png';
			
			if($logosrc) $plist['logosrc']=$logosrc;
			
		echo json_encode($plist);
	}
	
	//更新车辆数据
	function upgetcaringfo($logoid){
		exit;
		if(!$logoid) return false;
		/*exit;
		//4 获取具体车型
		$admin_userid=M('car')->where('logo=71')->select(); //33
		//dump($admin_userid);exit;
		foreach($admin_userid as $ak=>$av){
			if($av['logo']) $ggtt=$this->getjt($av['logo']);
			//if(!$ggtt) echo 'no';
			//else $this>upgetcaringfo();
		}*/
		/*$ss=M('carf')->order('tid desc')->find();
		echo $ss['oneid'];*/
		$ggtt=$this->getjt($logoid);
	}
	function getjt($pid){
				exit;
					if(!$pid) return false;
		
					//获取车型--具体车型
					$host = "http://jisucxdq.market.alicloudapi.com";
					$path = "/car/carlist";
					$method = "GET";
					$appcode = CARINFOAPPCODE;
					$headers = array();
					array_push($headers, "Authorization:APPCODE " . $appcode);
					
					$querys = "parentid=".$pid;
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
					//$content=explode('result',curl_exec($curl));
					//$content = json_decode(json_encode(curl_exec($curl),true),true);
					//var_dump(curl_exec($curl));
					$content=(json_decode($content,true));
					$content=$content['result'];
					//dump($content);exit;
						foreach($content as $ka=>$va){
							foreach($va['carlist'] as $kb=>$vb){
								foreach($vb['list'] as $kc=>$vc){
									/*echo $vc['id'];
									break;*/
									if($vc['id'] && $pid && $va['id']) $tttgg=$this->jt($vc['id'],$pid,$va['id']);
									//if(!$tttgg) echo 'buno';
								}
							}
							
						}
					//dump($content);exit;
		return;
		exit;
		
		$plist=M('cart')->select();
		foreach($plist as $k=>$v){
			if($v['logo']) $this->jt($v['logo']);
			else{
				echo $k;
				break;
			}
		}
	}
	function jt($carid,$oneid,$twoid){
		exit;
		if(!$carid || !$oneid || !$twoid) return false;
	
		$content=M('carinfo')->where('logo='.$oneid)->find();
	
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
	
	//获取车型 前一品牌
	function indexpr(){
			
				exit;
				$admin_userid=M('car')->where('logo>41212')->select();
				foreach($admin_userid as $ak=>$av){	
					//获取车型
					$host = "http://jisucxdq.market.alicloudapi.com";
					$path = "/car/carlist";
					$method = "GET";
					$appcode = CARINFOAPPCODE;
					$headers = array();
					array_push($headers, "Authorization:APPCODE " . $appcode);
					
					$querys = "parentid=".$av['logo'];
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
					//$content=explode('result',curl_exec($curl));
					//$content = json_decode(json_encode(curl_exec($curl),true),true);
					//var_dump(curl_exec($curl));
					$content=(json_decode($content,true));
					$content=$content['result'];
					//if(empty($content)) $parentid+1;
					//dump($content);exit;
					foreach($content as $k=>$vl){
						if($vl['depth']=='2'){
							//foreach($v['carlist'] as $kl=>$vl){
								$a['name']=$vl['name'];
								$a['fullname']=$vl['fullname'];
								$a['parentname']=$vl['name'];
								$a['oneid']=$av['logo'];
								$a['salestate']=$vl['salestate'];
								$a['initial']=$vl['initial'];
								$a['parentid']=$vl['parentid'];//$vl['parentid'];
								$a['logo']=$vl['id'];
								$a['depth']=$vl['depth'];
								//dump($a);
								$admin_userid=M('cartt')->add($a);
								if(!$admin_userid){
									echo $k.'-'.$kl;
									break;
								}
							//}
						}
					}
			}
	}
	//end
	
	function index(){
			
				exit;
				$admin_userid=M('car')->where('logo>41212')->select();
				foreach($admin_userid as $ak=>$av){	
					//获取车型
					$host = "http://jisucxdq.market.alicloudapi.com";
					$path = "/car/carlist";
					$method = "GET";
					$appcode = CARINFOAPPCODE;
					$headers = array();
					array_push($headers, "Authorization:APPCODE " . $appcode);
					
					$querys = "parentid=".$av['logo'];
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
					//$content=explode('result',curl_exec($curl));
					//$content = json_decode(json_encode(curl_exec($curl),true),true);
					//var_dump(curl_exec($curl));
					$content=(json_decode($content,true));
					$content=$content['result'];
					//if(empty($content)) $parentid+1;
					//dump($content);exit;
					foreach($content as $k=>$v){
						if($v['depth']=='2'){
							foreach($v['carlist'] as $kl=>$vl){
								$a['name']=$vl['name'];
								$a['fullname']=$vl['fullname'];
								$a['parentname']=$v['name'];
								$a['oneid']=$av['logo'];
								$a['salestate']=$vl['salestate'];
								$a['initial']=$vl['initial'];
								$a['parentid']=$vl['parentid'];//$vl['parentid'];
								$a['logo']=$vl['id'];
								$a['depth']=$vl['depth'];
								//dump($a);
								$admin_userid=M('cart')->add($a);
								if(!$admin_userid){
									echo $k.'-'.$kl;
									break;
								}
							}
						}
					}
			}
	}
    /*public function index(){
			exit;//获取所有品牌
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
			//$content=explode('result',curl_exec($curl));
			//$content = json_decode(json_encode(curl_exec($curl),true),true);
			//var_dump(curl_exec($curl));
			$content=(json_decode($content,true));
			$content=$content['result'];
			foreach($content as $k=>$v){
				$a['name']=$v['name'];
				$a['initial']=$v['initial'];
				$a['parentid']=$v['parentid'];
				$a['logo']=$v['id'];
				$a['depth']=$v['depth'];
				$admin_userid=M('car')->add($a);
				if(!$admin_userid){
					echo $k;
					break;
				}
			}
    }*/
}
?>