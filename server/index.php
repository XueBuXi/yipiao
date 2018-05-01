<?php
/* 
 * ApiBoxFramework
 * By:Weng 
 */
ini_set("display_errors","On");
error_reporting(E_ALL ^ E_NOTICE);


define("CDN_URL","/");
define("REWRITEMODE",false);
define("SQL_HOST","");
define("SQL_PORT","");
define("SQL_USER","");
define("SQL_NAME","");
define("SQL_PASS","");
define("CPAY_ID","码支付ID");
define("CPAY_KEY","码支付KEY");
define("AUTH_GATEWAY","第三方登录网关");
define("AUTH_KEY","第三方登录KEY");

date_default_timezone_set('PRC'); ini_set('date.timezone','Asia/Shanghai');
define ('_Host', (empty($_SERVER["HTTPS"]) || $_SERVER['HTTPS'] == 'off' ? 'http://' : 'https://') . $_SERVER['HTTP_HOST']);		// 主机网址
define ('_Http', _Host . str_ireplace('//','/',str_ireplace('/index.php', '', $_SERVER['SCRIPT_NAME']) . '/'));
session_name("EasyUserAuth");
if(isset($_SERVER['HTTP_X_EASY_AUTH']))session_id($_SERVER['HTTP_X_EASY_AUTH']);
if(isset($_GET['HTTP_X_EASY_AUTH']))session_id($_GET['HTTP_X_EASY_AUTH']);
session_set_cookie_params(7*60*60*24);
session_start();
class ApiBox{
    function page_index(){
        header("HTTP/1.1 404 Not Found");
        $this->_json(array("code"=>404,"msg"=>"PAGE `".$this->method.">".$this->func."` NOT FOUND"));
    }
    function page_qr($qr){
        //if(!isset($_COOKIE['yipiao_admin']))die('<meta name="viewport" content="width=device-width, initial-scale=1">'."<title></title><h1>你根本不是司机！</h1>");
        $r=$this->_query("SELECT * FROM `yipiao_ticket` where ticket=?","s",[explode("@",$qr)[0]]);
        $msg="Success";
        $r2=$this->_exec("UPDATE `yipiao_ticket` SET `status` = 'use' , `use_time`=now() WHERE `ticket` = ?","s",array(
            explode("@",$qr)[0]
        ));
        if($r2['affected_rows']==0)$msg="Faild";
        ?>
        <!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>YipiaoGate</title>
  </head>
  <body style="text-align:center">
    <h1><?php echo $r[0]['status'];?></h1>
    <h2><?php echo $msg;?></h2>
    <h4><?php echo $r[0]['level'];?></h4>
    <h4><?php echo $r[0]['phone'];?></h2>
    <h4><?php echo $r[0]['seat'];?></h4>
    <?php echo nl2br(print_r($r,true));?>
  </body>
</html>
        <?php
    }
    function page_(){
        //include "weihu.php";die();
        echo file_get_contents("index.html");
    }
    function page_f(){
        echo file_get_contents("index.html");
    }
    function api_pay_callback(){
        ksort($_POST); //排序post参数
        reset($_POST); //内部指针指向数组中的第一个元素
        $sign = '';
        foreach ($_POST AS $key => $val) {
            if ($val == '') continue; //跳过空值
            if ($key != 'sign') { //跳过sign
                $sign .= "$key=$val&"; //拼接为url参数形式
            }
        }
        if (!$_POST['pay_no']||md5(substr($sign, 0, -1) . CPAY_KEY) != $_POST['sign']) { //KEY密钥为你的密钥
          //不合法的数据 不做处理
           exit('fail');
        }else{
            $p=explode(',',$_POST['param']);
            //if($p['money']<51)exit('fail');
            $phone=isset($p[1])?$p[1]:"1008611"; 
            $tpass=rand(0,9)."".rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
            $r1=$this->_exec(
                "INSERT INTO `yipiao_ticket` (`user`,`ticket`, `tpass`, `phone`,`level`,  `sold_time`,`status`) VALUES (?,?,?,?,?,now(),'active')","sssss",
                [$p[0],$_POST['pay_no'],$tpass,$phone,$p[2]<"75"?"normal":"vip"]);
        
            //合法的数据
            //业务处理
           // $_POST['pay_id'] 这是付款人的唯一身份标识或订单ID
           // $_POST['pay_no'] 这是流水号 没有则表示没有付款成功 流水号不同则为不同订单
           // $_POST['money'] 这是付款金额
           //  $_POST['param'] 这是自定义的参数
           //var_dump($_POST['param'],[$p[0],$_POST['pay_no'],$tpass,$phone,$p[2]<"75"?"normal":"vip"],$r1);
           exit('success');
        }
    }
    function page_pay_go($type){
        if(!isset($_SESSION['userid']))$this->_json("Login Required",401);
        $data = array(
            "id" => CPAY_ID,//你的码支付ID
            "pay_id" => $_SESSION['userid'], //唯一标识 可以是用户ID,用户名,session_id(),订单ID,ip 付款后返回
            "type" => 1,//1支付宝支付 3微信支付 2QQ钱包
            "price" => 50,//金额100元
            "param" => implode(",",[$_SESSION['userid'],isset($_SESSION['phone'])?$_SESSION['phone']:"1008611",$type]),//自定义参数
            "notify_url"=>_Http."?api/pay/callback",//通知地址
            "return_url"=>_Http."#/pay/success?&",//跳转地址
        ); //构造需要传递的参数
        
        ksort($data); //重新排序$data数组
        reset($data); //内部指针指向数组中的第一个元素
        
        $sign = ''; //初始化需要签名的字符为空
        $urls = ''; //初始化URL参数为空
        
        foreach ($data AS $key => $val) { //遍历需要传递的参数
            if ($val == ''||$key == 'sign') continue; //跳过这些不参数签名
            if ($sign != '') { //后面追加&拼接URL
                $sign .= "&";
                $urls .= "&";
            }
            $sign .= "$key=$val"; //拼接为url参数形式
            $urls .= "$key=" . urlencode($val); //拼接为url参数形式并URL编码参数值
        
        }
        $query = $urls . '&sign=' . md5($sign .CPAY_KEY); //创建订单所需的参数
        $url = "http://api2.fateqq.com:52888/creat_order/?{$query}"; //支付页面
        
        header("Location:{$url}"); //跳转到支付页面
    }
    function api_index(){
        header("HTTP/1.1 404 Not Found");
        $this->_json(array("code"=>404,"msg"=>"API `".$this->method.">".$this->func."` NOT FOUND"));
    }
    function page_debug_setuid($uid){
        $_SESSION['userid']=$uid;
    }
    function page_login(){
        header("Location: ".AUTH_GATEWAY._Http."?login/callback&");
    }
    function page_logout(){
        unset($_SESSION['login']);
        unset($_SESSION['userid']);
        header("Location: ?");
    }
    function page_login_callback(){
        if(!isset($_GET['data']))$this->_json("Login Required",401);
        $_SESSION['login']=json_decode(decrypt("wengauth",$_GET['data']),true);
        $_SESSION['userid']=$_SESSION['login']['openid'];
        header("Location: ?");
    }
    function api_user_status(){
        if(!$_SESSION['userid'])$this->_json(array("url"=>_Http."?login"));
        $this->_json(array("id"=>$_SESSION['userid']));
    }
    //查票
    function api_ticket_byuser(){
        if(!$_SESSION['userid'])$this->_json("Login Required",401);
        $r=$this->_query("SELECT * FROM `yipiao_ticket` where user=?","s",[$_SESSION['userid']]);
        $this->_json(["data"=>$r]);
    }
    //已占用
    function api_seat_occupied(){
        if(!$_SESSION['userid'])$this->_json("Login Required",401);
        $r=$this->_query("SELECT `seat` FROM `yipiao_ticket` where seat is not null;");
        $rs=[];
        foreach($r as $a)$rs[]=$a['seat'];
        $this->_json(["data"=>$rs]);
    }
    //激活票
    function api_ticket_active($ticketid){
        if(!$_SESSION['userid'])$this->_json("Login Required",401);
        $r1=$this->_exec("UPDATE `yipiao_ticket` SET `user` = ?,`status` = 'active',`active_time` = now() WHERE `ticket` = ? AND `phone` = ? AND `type` = ? AND `user` is null","ssss",[$_SESSION['userid'],$ticketid,$_GET['phone'],$_GET['type']]);
        if($r1['affected_rows']==1){
            $this->_json("Success");
        }else{
            $r=$this->_query("SELECT `user`,`status` FROM `yipiao_ticket` where ticket=? AND `type` = ?","ss",[$ticketid,$_GET['type']]);
            if(!isset($r[0]))$this->_json(["msg"=>"Input Error","text"=>"信息输入有误"],403);
            if($r[0]['user']!=null)$this->_json(["msg"=>"Already actived","text"=>"该票已被激活"],403);
            $this->_json(["msg"=>"Unknown Error","text"=>"未知错误","ticket"=>$r,"sq"=>$r1],500);
        }
    }
    //选座
    function api_seat_choose($ticketid){
        $seatA=["13-20","13-18","13-16","13-14","13-12","13-10","13-8","13-6","13-4","13-2","13-1","13-3","13-5","13-7","13-9","13-11","13-13","13-15","13-17","12-20","12-18","12-16","12-14","12-12","12-10","12-8","12-6","12-4","12-2","12-1","12-3","12-5","12-7","12-9","12-11","12-13","12-15","11-20","11-18","11-16","11-14","11-12","11-10","11-8","11-6","11-4","11-2","11-1","11-3","11-5","11-7","11-9","11-11","11-13","11-15","11-17","10-18","10-16","10-14","10-12","10-10","10-8","10-6","10-4","10-2","10-1","10-3","10-5","10-7","10-9","10-11","10-13","10-15","10-17","9-18","9-16","9-14","9-12","9-10","9-8","9-6","9-4","9-2","9-1","9-3","9-5","9-7","9-9","9-11","9-13","9-15","8-18","8-16","8-14","8-12","8-10","8-8","8-6","8-4","8-2","8-1","8-3","8-5","8-7","8-9","8-11","8-13","8-15","7-16","7-14","7-12","7-10","7-8","7-6","7-4","7-2","7-1","7-3","7-5","7-7","7-9","7-11","7-13","7-15","6-16","6-14","6-12","6-10","6-8","6-6","6-4","6-2","6-1","6-3","6-5","6-7","6-9","6-11","6-13","6-15","5-16","5-14","5-12","5-10","5-8","5-6","5-4","5-2","5-1","5-3","5-5","5-7","5-9","5-11","5-13","4-14","4-12","4-10","4-8","4-6","4-4","4-2","4-1","4-3","4-5","4-7","4-9","4-11","4-13","3-12","3-10","3-8","3-6","3-4","3-2","3-1","3-3","3-5","3-7","3-9","3-11","3-13","2-12","2-10","2-8","2-6","2-4","2-2","2-1","2-3","2-5","2-7","2-9","2-11","2-13","1-12","1-10","1-8","1-6","1-4","1-2","1-1","1-3","1-5","1-7","1-9","1-11","1-13"];
        $seat=explode("-",$_GET['seat']);
        $seat[0]=(string)(int)$seat[0];
        $seat[1]=(string)(int)$seat[1];
        $seat=$seat[0]."-".$seat[1];
        $seatasql="and `level`='normal'";
        if(in_array($seat,$seatA))$seatasql="and `level`='vip'";
        if(!in_array($seat,$seatA))$this->_json(["msg"=>"Unknown Error","text"=>"未知错误"],500);
        if(!$_SESSION['userid'])$this->_json("Login Required",401);
        $r=$this->_exec("UPDATE `yipiao_ticket` SET `seat` = ? , `seat_time`=now() WHERE `ticket` = ? and `user` = ? $seatasql","sss",array(
            $seat,$ticketid,$_SESSION['userid']
        ));
        if($r['affected_rows']==-1)$this->_json(["msg"=>"Seat occupied","text"=>"座位已被占用"],403);
        if($r['affected_rows']==0)$this->_json(["msg"=>"Ticket not exists","text"=>"票不存在"],403);
        $this->_json(["data"=>$r]);
    }
    function _query($sql,$param=false,$data=array()){
        $this->sql_stmt = $this->db->prepare($sql);
        if($param!==false){
            array_unshift($data,$param);
            array_unshift($data,$this->sql_stmt);
            call_user_func_array("mysqli_stmt_bind_param",$this->_refValues($data));
        }
        $this->sql_stmt->execute();
        $res = $this->sql_stmt->get_result();
        if(!$res)$this->_json(array("msg"=>"Database error","info"=>$this->db->error,"sql"=>$sql),500);
        $r=$res->fetch_all(MYSQLI_ASSOC);
        $this->sql_stmt->close();
        return $r;
    }
    function _exec($sql,$param=false,$data=array()){
        $this->sql_stmt = $this->db->prepare($sql);
        if($param!==false){
            array_unshift($data,$param);
            array_unshift($data,$this->sql_stmt);
            call_user_func_array("mysqli_stmt_bind_param",$this->_refValues($data));
        }
        $this->sql_stmt->execute();
        $d=array("insert_id"=>$this->sql_stmt->insert_id,"affected_rows"=>$this->sql_stmt->affected_rows,"error"=>$this->sql_stmt->error);
        $this->sql_stmt->close();
        return $d;
    }
    function _tpl($tpl,$data=array()){
        foreach($data as $a=>$b)$$a=$b;
        ob_start();
        require_once("./tpl/".$tpl.".html");
        $o=ob_get_contents();
        ob_end_clean();
        echo str_replace("///",CDN_URL,$o);
    }
    function _json($data,$code=0){
        if(!is_array($data))$data=["msg"=>$data];
        if($code!=0){
            $msg="API Error";
            if(isset($data['msg']))$msg=$data['msg'];
            header("HTTP/1.1 ".$code." ".$msg);
        }
        if(!isset($data['code']))$data['code']=$code;
        header('Content-type: application/json');
    	die($this->_prettyJson($data));
    }
    function _prettyJson($data){
        $json=json_encode($data,JSON_UNESCAPED_UNICODE);
        $result = ''; 
    	$pos = 0; 
    	$strLen = strlen($json); 
    	$indentStr = '    '; 
    	$newLine = "\n"; 
    	$prevChar = ''; 
    	$outOfQuotes = true; 
    	for ($i=0; $i<=$strLen; $i++) { 
    		$char = substr($json, $i, 1); 
    		if ($char == '"' && $prevChar != '\\') { 
    			$outOfQuotes = !$outOfQuotes; 
    		} else if(($char == '}' || $char == ']') && $outOfQuotes) { 
    			$result .= $newLine; 
    			$pos --; 
    			for ($j=0; $j<$pos; $j++) { 
    			$result .= $indentStr; 
    		} 
    	} 
    	$result .= $char; 
    	if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) { 
    		$result .= $newLine; 
    		if ($char == '{' || $char == '[') { 
    			$pos ++; 
    		} 
    		for ($j = 0; $j < $pos; $j++) { 
    			$result .= $indentStr; 
    		} 
    	} 
    		$prevChar = $char; 
    	} 
        return $result;
    }
    function _sql($array, $type='insert', $exclude = array()){
     
    $sql = '';
    if(count($array) > 0){
      foreach ($exclude as $exkey) {
        unset($array[$exkey]);//剔除不要的key
      }
 
      if('insert' == $type){
        $keys = array_keys($array);
        $values = array_values($array);
        $col = implode("`, `", $keys);
        $val = implode("', '", $values);
        $sql = "(`$col`) values('$val')";
      }else if('update' == $type){
        $tempsql = '';
        $temparr = array();
        foreach ($array as $key => $value) {
          $tempsql = "`$key` = '$value'";
          $temparr[] = $tempsql;
        }
 
        $sql = implode(",", $temparr);
      }
    }
    return $sql;
  }
    function __construct(){
        header('Access-Control-Max-Age: 1728000');
        header('Access-Control-Allow-Origin: '.(isset($_SERVER['HTTP_ORIGIN'])?$_SERVER['HTTP_ORIGIN']:"*"));
        header('Access-Control-Allow-Methods: GET,POST,OPTIONS');
        header('Access-Control-Allow-Headers: X-Easy-Auth');
        header('Access-Control-Allow-Credentials: true');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
        header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT"); 
        header("Cache-Control: no-cache, must-revalidate"); 
        header("Pramga: no-cache"); 
        $this->db = new mysqli(SQL_HOST,SQL_USER,SQL_PASS,SQL_NAME,SQL_PORT);
        if(mysqli_connect_error()){
            $err=mysqli_connect_error();
            header("HTTP/1.1 500 Database Error");
            $this->_json(array("code"=>500,"msg"=>$err));
        }
        $this->db->set_charset("utf8");
        if(REWRITEMODE){
            $r=explode("/",str_replace("?".$_SERVER['QUERY_STRING'],"",$_SERVER['REQUEST_URI']));
            array_shift($r);
            $this->path=implode("/",$r);
        }else{
            $r=explode("&",$_SERVER['QUERY_STRING']);
            $this->path=$r[0];
            $r=explode("/",$r[0]);
        }
        if($r[0]=="")array_shift($r);
        if($r[0]=="api"){
            $prefix="api";
            array_shift($r);
        }else{
            $prefix="page";
        }
        $this->method=$r[0];
        $this->func=$r[1];
        if(method_exists($this,$prefix."_".$r[0]."_".$r[1])){
            $rr=$prefix."_".$r[0]."_".$r[1];
            $this->route=array_slice($r,2);
            $this->$rr(implode("/",$this->route));
        }elseif(method_exists($this,$prefix."_".$r[0])){
            $rr=$prefix."_".$r[0];
            $this->route=array_slice($r,1);
            $this->$rr(implode("/",$this->route));
        }else{
            $p=$prefix."_index";
            $this->$p();
        }
    }
    function _refValues($arr){  
        if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+  
        {  
            $refs = array();  
            foreach($arr as $key => $value)  
                $refs[$key] = &$arr[$key];  
            return $refs;  
        }  
        return $arr;  
    }  
}
function encrypt ($key, $encrypt)  
{  
    // 根據 PKCS#7 RFC 5652 Cryptographic Message Syntax (CMS) 修正 Message 加入 Padding  
    $block = mcrypt_get_block_size(MCRYPT_DES, MCRYPT_MODE_ECB);  
    $pad = $block - (strlen($encrypt) % $block);  
    $encrypt .= str_repeat(chr($pad), $pad);  

    // 不需要設定 IV 進行加密  
    $passcrypt = @mcrypt_encrypt(MCRYPT_DES, $key, $encrypt, MCRYPT_MODE_ECB);  
    return base64_encode($passcrypt);  
}  

/** 
 * PHP DES 解密程式 
 * 
 * @param $key 密鑰（八個字元內） 
 * @param $decrypt 要解密的密文 
 * @return string 明文 
 */  
function decrypt ($key, $decrypt)  
{  
    // 不需要設定 IV  
    $str = @mcrypt_decrypt(MCRYPT_DES, $key, base64_decode($decrypt), MCRYPT_MODE_ECB);  

    // 根據 PKCS#7 RFC 5652 Cryptographic Message Syntax (CMS) 修正 Message 移除 Padding  
    $pad = ord($str[strlen($str) - 1]);  
    return substr($str, 0, strlen($str) - $pad);  
}  
new ApiBox();