<?php

/* 
	Replace the text with database variables 
	accept 2 parameters
	1 - value of database.
	2 - company id optional. bydefault false.
*/
function database_variable($variable,$company_id=false) {
	$CI=& get_instance();
	if($company_id!='') {
		$CI->db->where('companyid',$company_id);
	}
	$CI->db->where('key',$variable);
	$rs=$CI->db->get('variable');

	if($rs->num_rows() > 0) {
		$result=$rs->result_array();
		return $result[0]['value'];
	}
	else {
		return false;
	}
}

/* 
	Send SMS 
	accept 3 parameters
	1 - mobile no. on which message to be send.
	2 - message.
	3 - company id optional. bydefault false.
*/
function send_sms($mobile_no,$message,$company_id=false) {
    $CI=& get_instance();
	$company_id=$CI->session->userdata('companyid');
	/*$secure_app_key=database_variable('secure_app_key');
	$secure_sms=database_variable('secure_sms');	*/	
	if($company_id!='') {
		$secure_app_key=database_variable('secure_app_key',$company_id);
		$secure_sms=database_variable('secure_sms',$company_id);
	}
	
    $message=urlencode($message);
    /* application based key */
    /* user based key */
    $url=SMSAPP_API_SENDSMS."securekey=".$secure_app_key."&token=".$secure_sms."&to=".$mobile_no."&message=".$message;
    $response=curl_send($url);
    if(strpos(strtolower($response),'sent') === false) {
    	return false;
    }
    else {
    	return true;
    }
}

/* 
	get thumb image 
	accept 2 parameters
	1 - image path.
	2 - thumb dimension. bydefault false.
*/
function get_thumb($image,$thumb=false) {
    if($image=='') {
        return "";
    }
    $url_array=explode('/',$image);
    $path_array=pathinfo($image);
    $last=count($url_array);
    if($thumb!='') {
        $url_array[$last]=$path_array['filename'].'_thumb.'.$path_array['extension'];
        $url_array[$last-1]='thumb_'.$thumb;
        $thumb_url=implode('/',$url_array);
    }
    else {
        $thumb_url=implode('/',$url_array);
    }
    return $thumb_url;
}				


function count_message($text,$unicode=0) {
  $strlen=strlen($text);
  $chars=160;
  if(empty($unicode)) {
    $unicode=0;

  }
  ini_set('default_charset', 'utf-8');
  if($unicode==1) {
    $chars=65;
    $strlen=mb_strlen($text,"UTF-8");
  }

  if($strlen < 1) {
    return 1;
  }
  $count=ceil(($strlen/$chars));
  return $count;
}

/* checking user directory */
function check_dir($dir) {
    $referer=$_SERVER['HTTP_REFERER'];
    $server=parse_url($referer);

    $dir=strtolower($dir); /* converting to lowercase */

    $dir=explode('/', $dir); /* seprateing the dir by / */

    $str_dir='.';
    foreach ($dir as $folder_name) {
        $str_dir.='/'.$folder_name;
        if(!file_exists($str_dir) || !is_dir($str_dir)) {
            mkdir($str_dir);
        }
    }

    if(!file_exists($str_dir.'/150') || !is_dir($str_dir.'/150')) {
        mkdir($str_dir.'/150');
    }

    if(!file_exists($str_dir.'/300') || !is_dir($str_dir.'/300')) {
        mkdir($str_dir.'/300');
    }

    if(!file_exists($str_dir.'/500') || !is_dir($str_dir.'/500')) {
        mkdir($str_dir.'/500');
    }
}