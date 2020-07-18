<?php 

namespace app;

use think\Exception;

class Common{
     
     /**
      * url参数加密类.
      * [encrypt description]
      * @param  [type] $string    [description]
      * @param  [type] $operation [description]
      * @param  string $key       [description]
      * @return [type]            [description]
      */
	 public function encrypt($string,$operation,$key='')
	 {
	 	    $key=md5($key);
		    $key_length=strlen($key);
		    $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
		    $string_length=strlen($string);
		    $rndkey=$box=array();
		    $result='';
		    for($i=0;$i<=255;$i++)
		    {
		      $rndkey[$i]=ord($key[$i%$key_length]);
		      $box[$i]=$i;
		    }
		    for($j=$i=0;$i<256;$i++)
		    {
		      $j=($j+$box[$i]+$rndkey[$i])%256;
		      $tmp=$box[$i];
		      $box[$i]=$box[$j];
		      $box[$j]=$tmp;
		    }
		    for($a=$j=$i=0;$i<$string_length;$i++)
		    {
		      $a=($a+1)%256;
		      $j=($j+$box[$a])%256;
		      $tmp=$box[$a];
		      $box[$a]=$box[$j];
		      $box[$j]=$tmp;
		      $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
		    }
		    if($operation=='D')
		    {
		      if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8))
		      {
		        return substr($result,8);
		      }
		      else
		      {
		        return'';
		      }
		    }
		    else
		    {
		      return str_replace('=','',base64_encode($result));
		    }
	 }


	  /**
  * 判断访问地址是PC和移动方法
  * [is_mobile description]
  * @return boolean [description]
  */
  public function isMobile()
   { 
      // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
      if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
      } 
      // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
      if (isset($_SERVER['HTTP_VIA'])) { 
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
      } 
      // 脑残法，判断手机发送的客户端标志,兼容性有待提高。其中'MicroMessenger'是电脑微信
      if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile','MicroMessenger'); 
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
          return true;
        } 
      } 
      // 协议法，因为有可能不准确，放到最后判断
      if (isset ($_SERVER['HTTP_ACCEPT'])) { 
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
          return true;
        } 
      } 
      return false;
  }


  /**
   * 唯一随机数方法
   * [rand description]
   * @param  [type] $len [description]
   * @return [type]      [description]
  */
  public function rand($len)
  {
        $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $string=substr(time(),-3);
        for(;$len>=1;$len--)
        {
            $position=rand()%strlen($chars);
            $position2=rand()%strlen($string);
            $string=substr_replace($string,substr($chars,$position,1),$position2,0);
        }
        return $string;
  }

  public function dianqilai_access_domain()
  {
      static $domain = null;
      if ($domain) {
          return $domain;
      }
      $file = ROOT_PATH . '/domain.json';
      if (!file_exists($file)) {
          throw new Exception('Domain not found');
      }
      $res = json_decode(file_get_contents($file), true);
      if (!is_array($res)) {
          throw new Exception('Domain cannot be decoded');
      }
      return $version = $res['domain'];
  }

  public function dianqilai_version()
  {
      static $version = null;
      if ($version) {
          return $version;
      }
      $file = ROOT_PATH . '/version.json';
      if (!file_exists($file)) {
          throw new Exception('version not found');
      }
      $res = json_decode(file_get_contents($file), true);
      if (!is_array($res)) {
          throw new Exception('version cannot be decoded');
      }
      return $version = $res['version'];
  }

  public function remove_emoji($nickname) {
        // Match Emoticons
        $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clean_text = preg_replace($regexEmoticons, '', $nickname);
        // Match Miscellaneous Symbols and Pictographs
        $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clean_text = preg_replace($regexSymbols, '', $clean_text);
        // Match Transport And Map Symbols
        $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clean_text = preg_replace($regexTransport, '', $clean_text);
        // Match Miscellaneous Symbols
        $regexMisc = '/[\x{2600}-\x{26FF}]/u';
        $clean_text = preg_replace($regexMisc, '', $clean_text);
        // Match Dingbats
        $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);
        return $clean_text;
    }
}