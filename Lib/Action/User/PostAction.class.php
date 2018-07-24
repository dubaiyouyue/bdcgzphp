<?php

/**
 *
 * PostAction.class.php (前台内容管理)
 *
 * @package      	GZPHP
 * @author          wen QQ:52009619 <wei2l99@qq.com>
 * @copyright     	Copyright (c) 2008-2011  (http://www.resonance.com.cn)
 * @license         http://www.resonance.com.cn/license.txt
 * @version        	GzPHP企业网站管理系统 v2.1 2011-03-01 resonance.com.cn $
 */
if (!defined("GZPHP"))
    exit("Access Denied");

class PostAction extends BaseAction {

    protected $dao, $fields;

    public function _initialize() {
        parent::_initialize();
        //$this->assign('jumpUrl', U('User/Login/index'));
        if (empty($this->Role[$this->_groupid]['allowpost'])) {
            $this->error(L('nologin'));
        }
        $this->assign('bcid', 0);
        $this->moduleid = intval($_REQUEST['moduleid']);

        if (!in_array($this->_groupid, explode(',', $this->module[$this->moduleid]['postgroup'])) && $this->moduleid != 0)
            $this->error(L('add_no_postgroup'));
        if ($this->moduleid) {
            $this->assign('moduleid', $this->moduleid);
            $module = $this->module[$this->moduleid]['name'];
            $this->dao = D($module);
            $fields = F($this->moduleid . '_Field');
            foreach ($fields as $key => $res) {
                if ($res['unpostgroup'])
                    $res['unpostgroup'] = explode(',', $res['unpostgroup']);
                if (!in_array($this->_groupid, $res['unpostgroup']) && $res['status']) {
                    $res['setup'] = string2array($res['setup']);
                    $this->fields[$key] = $res;
                }
            }
            unset($fields);
            unset($res);
            $this->assign('fields', $this->fields);
        }
    }

    /**
     * 列表
     *
     */
    public function index() {
        if (!$this->_userid) {
            $this->error(L('nologin'));
        }
        if ($this->module[$this->moduleid]['type'] == 1) {

            if ($this->categorys) {
                foreach ($this->categorys as $r) {
                    if ($r['type'] == 1 || !in_array($this->_groupid, explode(',', $r['postgroup'])))
                        continue;
                    if ($r['moduleid'] != $this->moduleid || $r['child']) {
                        $arr = explode(",", $r['arrchildid']);
                        $show = 0;
                        foreach ((array) $arr as $rr) {
                            if ($this->categorys[$rr]['moduleid'] == $this->moduleid)
                                $show = 1;
                        }
                        if (empty($show))
                            continue;
                        $r['disabled'] = $r['child'] ? ' disabled' : '';
                    }else {
                        $r['disabled'] = '';
                    }
                    $array[] = $r;
                }
                import('@.ORG.Tree');
                $str = "<option value='\$id' \$disabled \$selected>\$spacer \$catname</option>";
                $tree = new Tree($array);
                $select_categorys = $tree->get_tree(0, $str);
                $this->assign('select_categorys', $select_categorys);
                $this->assign('categorys', $this->categorys);
            }
            $this->assign('posids', F('Posid'));
        }

        import('@.Action.Adminbase');
        $c = new AdminbaseAction();
        $module = $this->module[$this->moduleid]['name'];
        $map['userid'] = array('eq', $this->_userid);
        if (APP_LANG)
            $map['lang'] = array('eq', $this->Lang[LANG_NAME][id]);
        $c->_list($module, $map);
        $this->display();
    }

    public function add() {
        $form = new Form();
        $form->isadmin = 0;
        $form->doThumb = $this->Role[$this->_groupid]['allowattachment'] ? 1 : 0;
        $form->doAttach = $this->Role[$this->_groupid]['allowattachment'] ? 1 : 0;
        ;
        $this->assign('form', $form);
        $module = $this->module[$this->moduleid]['name'];
        $template = file_exists(TMPL_PATH . 'User/' . $this->sysConfig['DEFAULT_THEME'] . '/' . $module . '_edit.html') ? $module . ':edit' : 'Post:edit';
        $this->display($template);
    }

    public function edit() {
        if (!$this->_userid) {
            $this->error(L('nologin'));
        }
        $id = intval($_REQUEST ['id']);
        $vo = $this->dao->getById($id);
        $form = new Form($vo);
        $form->isadmin = 0;
        $form->doAttach = $this->Role[$this->_groupid]['allowattachment'] ? 1 : 0;
        ;
        $form->doThumb = $this->Role[$this->_groupid]['allowattachment'] ? 1 : 0;
        $this->assign('vo', $vo);
        $this->assign('form', $form);
        $module = $this->module[$this->moduleid]['name'];
        $template = file_exists(TMPL_PATH . 'User/' . $this->sysConfig['DEFAULT_THEME'] . '/' . $module . '_edit.html') ? $module . ':edit' : 'Post:edit';
        $this->display($template);
    }
	
	

	
	public function Message() {
        /*dump($_POST);exit;
        if ($this->moduleid != 6 && $this->moduleid != 0 && !in_array($this->_groupid, explode(',', $this->categorys[$_POST['catid']]['postgroup'])))
           $this->error(L('add_no_postgroup'));*/
        $c = A('Admin/Content');
        //$_POST['ip'] = get_client_ip();
		$_POST['tel']=$_POST['tel']+0;

		
		//意见反馈
		if($_GET['yj']=='fk'){
			if(empty($_POST['feedbackcontent'])) {// || empty($_POST['message'])
				//$this->error(L('信息填写不完整!'));
				exit('1');
			}
			$_POST['message']=$_POST['feedbackcontent'];
			$_POST['tel']=$_POST['yijktel'];
			if($_POST['my']) $_POST['message']='#'.$_POST['my'].'# '.$_POST['message'];
		//end
		}else{
		
		
		
				if(empty($_POST['tel'])) {// || empty($_POST['message'])
					//$this->error(L('信息填写不完整!'));
					exit('1');
				}
				
				if($_POST['message']!=5){
					//人机验证
						$url = 'https://captcha.luosimao.com/api/site_verify';
						$data =array('api_key'=>LUOSIMAO_API_KEY,'response'=>$_POST['luotest_response']);
						$result=$this->vpost($url,$data);
						$result=json_decode($result,true);
						$result=$result['res'];
						//echo $_POST['luotest_response'];
						//echo $result;exit;
						if($result!='success' && !$_SESSION['sms_code'] && !$_POST['nameggg']){
							//unset($_SESSION['seccode']);
							exit('2');
							//redirect('验证码不正确！', $pageurl);
							//redirect('人机验证失败！', '?module=login');
						}
					//end
				}
				
				
				//短信验证码
				if($_POST['message']==1){
					if($_GET['sms']=='ok'){
						//echo 'OK';exit;
						
						if(($_SESSION['sms_time']+97)>time()){
							exit('5'); //60秒内多次发送
						}
						
						$_SESSION['sms_code']=rand(100000,999999);
						$_SESSION['sms_tel']=$_POST['tel'];
						$_POST['tel']=$_SESSION['sms_tel'];
						$_SESSION['sms_time']=time();
						$s_sms=$this->alisms();
						//dump($s_sms);
						if($s_sms['Code']!='OK'){
							echo $s_sms['Message'];
							exit;
						}else echo 'OK'; //发送成功
						exit;
					}else{
						if(!$_POST['yzm'] || $_POST['yzm']!=$_SESSION['sms_code']){
							exit('3'); //短信验证码错误
						}else if(($_SESSION['sms_time']+330)<time()){
							exit('4'); //短信验证码过期
						}
					}
				}
				
				
				$_POST['company']=$_POST['message'];
				$xl_message_str=array(
					'1'=>'金融-购车'
					,'2'=>'卖车'
				);
				if($_POST['message']==5) $_POST['message']='估计卖车 卖车地点：'.$_POST['mcdd'].' 车型：'.$_POST['mccx'].' 上牌时间：'.$_POST['mcspsj'].' 行驶里程：'.$_POST['mcxslc'].'万公里 手机：'.$_POST['tel'];
				else if(!$_POST['nameggg']) $_POST['message']=$xl_message_str[$_POST['message']];
		
		
		}
		

		
		
		$_POST['addtime']=date('Y-m-d H:i:s',time());
 $this->fields['name'] = htmlspecialchars(trim($this->fields['name']));
  $this->fields['tel'] = htmlspecialchars(trim($this->fields['tel']));
   $this->fields['email'] = htmlspecialchars(trim($this->fields['email']));
    //$this->fields['company'] = htmlspecialchars(trim($this->fields['company']));
    $this->fields['message'] = htmlspecialchars(trim($this->fields['message']));
    $this->fields['company'] = htmlspecialchars(trim($this->fields['company']));

			//ajax
			if($_GET['ajax']=='ajax'){
				$ajax_date['addtime']=date('Y-m-d H:i:s',time());
				$ajax_date['name']=htmlspecialchars(trim($_POST['name']));
				$ajax_date['tel'] = htmlspecialchars(trim($_POST['tel']));
				$ajax_date['email'] = htmlspecialchars(trim($_POST['email']));
				//$ajax_date['company'] = htmlspecialchars(trim($_POST['company']));
				$ajax_date['message'] = htmlspecialchars(trim($_POST['message']));
				$ajax_date['company'] = htmlspecialchars(trim($_POST['company']));
				if(M('message')->add($ajax_date)){
					unset($_SESSION['sms_code']);
					unset($_SESSION['sms_tel']);
					unset($_SESSION['sms_time']);
					echo 'addok';
				}
				return false;
			}
        if ($this->moduleid == 0) {
            //$this->fields['content'] = $_POST['content'];
            //$this->fields['lang'] = $_POST['lang'];
            $this->fields['tableid'] = $_POST['tid'];
            $this->fields['tablemodule'] = $_POST['mid'];
        }
        $userid = $this->_userid;
        $username = $this->_username ? $this->_username : get_safe_replace($_POST['username']);
        if (!isset($this->module[$this->moduleid]['name'])) {
            if (!empty($this->fields['name']))
                $this->fields['name'] = htmlspecialchars(trim($this->fields['name']));
            if (!empty($this->fields['content']))
                $this->fields['content'] = htmlspecialchars(trim($this->fields['content']));

            $c->insert('Message', $this->fields, $userid, $username, $this->_groupid);
        } else {
			$c->insert('Message', $this->fields, $userid, $username, $this->_groupid);
            //$c->insert($this->module[$this->moduleid]['name'], $this->fields, $userid, $username, $this->_groupid);
        }
    }
    /**
     * 录入
     *
     */
    public function insert() {
        if ($this->moduleid != 6 && $this->moduleid != 0 && !in_array($this->_groupid, explode(',', $this->categorys[$_POST['catid']]['postgroup'])))
            $this->error(L('add_no_postgroup'));
        $c = A('Admin/Content');
        $_POST['ip'] = get_client_ip();
        if (empty($_POST['content']) && $this->moduleid == 0) {
            $this->error(L('留言内容不能为空!'));
        }
        if ($this->moduleid == 0) {
            $this->fields['content'] = $_POST['content'];
            $this->fields['lang'] = $_POST['lang'];
            $this->fields['tableid'] = $_POST['tid'];
            $this->fields['tablemodule'] = $_POST['mid'];
        }
        $userid = $this->_userid;
        $username = $this->_username ? $this->_username : get_safe_replace($_POST['username']);
        if (!isset($this->module[$this->moduleid]['name'])) {
            if (!empty($this->fields['title']))
                $this->fields['title'] = htmlspecialchars(trim($this->fields['title']));
            if (!empty($this->fields['content']))
                $this->fields['content'] = htmlspecialchars(trim($this->fields['content']));

            $c->insert('guestbook', $this->fields, $userid, $username, $this->_groupid);
        } else {
            $c->insert($this->module[$this->moduleid]['name'], $this->fields, $userid, $username, $this->_groupid);
        }
    }

    function update() {
        if (!$this->_userid) {
            $this->error(L('nologin'));
        }
        if ($this->moduleid != 6 && !in_array($this->_groupid, explode(',', $this->categorys[$_POST['catid']]['postgroup'])))
            $this->error(L('add_no_postgroup'));

        $c = A('Admin/Content');
        $c->update($this->module[$this->moduleid]['name'], $this->fields);
    }

}

?>