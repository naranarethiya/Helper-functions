<?php

/*
*/
function set_message($message,$type="error") {
	$CI =& get_instance();
	if($type=="error") {
		$add=$CI->session->userdata('error');
		$set_message=$add."<li>".$message."</li>";
		$CI->session->set_userdata('error',$set_message);
	}
	else if($type=="success") {
		$add=$CI->session->userdata('success');
		$set_message=$add."<li>".$message."</li>";
		$CI->session->set_userdata('success',$set_message);
	}
	else if($type=="warning") {
		$add=$CI->session->userdata('warning');
		$set_message=$add."<li>".$message."</li>";
		$CI->session->set_userdata('warning',$set_message);
	}  
}

/* print last execulated query */
function print_last_query($die=false) {
	$CI =& get_instance();
	dsm($CI->db->last_query(),$die,false);
}	

function dsm_post($die=false) {
	$CI =& get_instance();
	dsm($CI->input->post(),$die,false);
}

/* 
	redirect back - redirect to request page.
*/
function redirect_back() {
	if(isset($_SERVER['HTTP_REFERER'])) {
		$url=$_SERVER['HTTP_REFERER'];  
	}
	else {
		$url=base_url();
	}
	redirect($url);
}

/* 
	creating the image thumb 
	accept 5 parameters
	1 - source image path.	
	2 - thumb image width.		
	3 - thumb image height.
	4 - original file name.
	5 - thumb folder path where image thumb is to be create.
*/
function image_thumb($image_path, $width, $height,$ofilename,$thumb_folder) {
    $CI =& get_instance();
    // Path to image thumbnail
    $image_thumb = $thumb_folder . '/' . $ofilename;
    if (!file_exists($image_thumb)) {
        // LOAD LIBRARY
        $CI->load->library('image_lib');

        // CONFIGURE IMAGE LIBRARY
        $config['image_library']    = 'gd2';
        $config['source_image']     = $image_path;
        $config['new_image']        = $image_thumb;
        $config['create_thumb']     = TRUE;
        $config['maintain_ratio']   = TRUE;
        $config['width']            = $width;
        $config['height']           = $height;
        $CI->image_lib->initialize($config);
        $CI->image_lib->resize();
        $CI->image_lib->clear();
    }
}

/* Generate otp */
function generateOTP() {
	$password=random_string("numeric",4);
    return $password;
}

function apply_filter($filter) {
  $CI=& get_instance();
  if(is_array($filter)) {
      foreach($filter as $key => $val) {

        /* limit */
        if($key==='LIMIT') {
            if(is_array($val)) {
                $CI->db->limit($val[0],$val[1]);
            }
            else {
              $CI->db->limit($val);
            }
        }

        /* for more complex where 
            ex:name='Joe' AND status='boss' OR status='active'
        */
        else if($key==='WHERE') {
          $CI->db->where($val,null,FALSE);
        }
        else if($key==='WHERE_IN') {
           foreach($val as $column => $value) {
              $CI->db->where_in($column,$value);
            }
          
        }
        else if($key==='HAVING') {
          if(is_array($val)) {
            foreach($val as $col=>$value) {
              $CI->db->having($col,$value);
            }
          }
          else {
            $CI->db->having($val,null,FALSE);
          }
        }

        /* order by */
        elseif($key=='ORDER_BY') {
          foreach($val as $col => $order) {
            $CI->db->order_by($col,$order);
          }
        }

        /* group by */
        elseif($key=='GROUP_BY') {
          $CI->db->group_by($val);
        }

        /* select */
        elseif($key=='SELECT') {
          $CI->db->select($val);
        }

        /* simple key=>value where condtions */
        else {
          $CI->db->where($key,$val);  
        }

      }
    }
}