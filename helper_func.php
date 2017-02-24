<?php 

function dsm($var,$die=false,$direct_call=true) {
	if(is_array($var) || is_object($var)) {
		echo "<pre>".print_r($var,true)."</pre>";
	}
	else {
		echo "<pre>".$var."</pre>";
	}
	$debug=debug_backtrace();
	if($direct_call) {
		echo "<pre>".$debug[0]['file'].", line :".$debug[0]['line']."</pre><br/>";  
	}
	else {
		echo "<pre>".$debug[1]['file'].", line :".$debug[1]['line']."</pre><br/>";  	
	}
	if($die) {
		die;
	}		
}

/* 
	CURL Execution
	accept 1 parameter.
	1 - url where page will send the request.
*/
function curl_send($url) {
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$curl_response = curl_exec($curl);
	curl_close($curl);
	return $curl_response;
}


/* 
	Create combobox from array 
	accept 7 parameter.
	1 - name of select box or combobox.
	2 - array of values.
	3 - option value of select box or combobox.
	4 - text which will display in select box or combobox.
	5 - bydefault selected value in select box or combobox.
	6 - HTML css attributes.
	7 - 'SELECT' will be first option or not.
	8 - array('Attributr name','array or string','saperator') -
		 ex1. array('data-opt',invoice_value')
		 ex2. array('data-opt',array('invoice_value','invoice_numver'),',')
*/
function generate_combobox($name,$array,$key,$value,$selected=false,$other=false,$defaultoption=true,$datavalue=false) {
	//dsm(func_get_args());
	if(empty($array)) {
		$output = "<select name=\"{$name}\" ".$other.">";
		if($defaultoption) {
			$output .= "<option value=\"\">SELECT</option>";    
		}
		$output .= "</select>";
	}
	else{  
		$output = "<select name=\"{$name}\" ".$other.">";
		if($defaultoption) {
			$output .= "<option value=\"\">SELECT</option>";    
		}
		$keys=array_column($array,$key);
		$sap_char=' ';			
		if(is_array($value)) {
			$args=array();
			$args[]="combine";
			foreach($value as $val) {
				$args[]=array_column($array,$val);
			}

			/* pass saperator array */
			foreach ($args[1] as $key=>$value) {
				$saperator[$key]=$sap_char;
			}
			$args[]=$saperator;
			$vals=call_user_func_array('array_map',$args);
		}
		else {
			$vals=array_column($array,$value);
		}
		$new_array=array_combine($keys,$vals);

		$opt_attr=array();

		/* for setting extra attribute with dynamic value */
		if($datavalue) {
			/* attribute name */
			$attr=$datavalue[0];
			/* attribute value column */
			$attr_value=$datavalue[1];
			/* create separator array */
			$data_saperator=array();

			/* if multiple column value need to supply */
			if(is_array($attr_value)) {
				/* column value separator  */
				$datasap_char=$datavalue[2];
				$val_columns=array();
				$val_columns[]="combine";

				/* add each column value into val column */
				foreach($attr_value as $attr_val) {
					$val_columns[]=array_column($array,$attr_val);
				}

				/* pass separator array */
				foreach ($attr_value as $key=>$blank) {
					$data_saperator[$key]=$datasap_char;
				}
				$val_columns[]=$data_saperator;

				/* combine multiple column in to string */
				$opt_attr=call_user_func_array('array_map',$val_columns);
				
			}
			else {
				$opt_attr=array_column($array,$attr_value);
			}
		}

		$k=0;
		foreach ($new_array as $key => $value) {
			$str_attr='';
			if($datavalue) {
				$str_attr=' '.$datavalue[0].'="'.$opt_attr[$k].'" ';
			}
			if(is_array($selected)) {
				if (in_array($key,$selected)) {
					$output .= "<option value=\"{$key}\" ".$str_attr." selected>{$value}</option>";
				} 
				else {
					$output .= "<option value=\"{$key}\" ".$str_attr.">{$value}</option>";
				}
			}
			else {
				if ($selected !== false && $selected == $key) {
					$output .= "<option value=\"{$key}\" ".$str_attr." selected>{$value}</option>";
				} 
				else {
					$output .= "<option value=\"{$key}\" ".$str_attr.">{$value}</option>";
				}
			}
			$k++;
		}

		$output .= "</select>";
	}
	return $output;
}


/* 
	Create textbox from array 
	accept 4 parameters
	1 - name of textbox control.
	2 - value to set in placeholder attribute.
	3 - value to set bydefault in value attribute.
	4 - HTML css attributes.
*/
function generate_textbox($name,$placeholder=false,$default=false,$other=false) {
	$output = "<input type=\"text\" name=\"{$name}\" value=\"{$default}\" placeholder=\"{$placeholder}\" ".$other.">";
	return $output;
}

/* 
	Create number textbox from array 
	accept 4 parameters
	1 - name of numeric textbox control.
	2 - value to set in placeholder attribute.
	3 - value to set bydefault in value attribute.
	4 - HTML css attributes.		
*/
function generate_numberbox($name,$placeholder=false,$default=false,$other=false) {
  $output = "<input type=\"number\" name=\"{$name}\" value=\"{$default}\" placeholder=\"{$placeholder}\" ".$other.">";
  return $output;
}

/* 
	Create file from array
	accept 2 parameters
	1 - name of file control.
	2 - HTML css attributes.	
*/
function generate_filebox($name,$other=false) {
  $output = "<input type=\"file\" name=\"{$name}\" ".$other.">";
  return $output;
}

/* 
	Create textarea from array 
	accept 4 parameters
	1 - name of textarea control.
	2 - value to set in placeholder attribute.
	3 - value to set bydefault in value attribute.
	4 - HTML css attributes.		
*/
function generate_textarea($name,$placeholder=false,$default=false,$other=false) {
  $output = "<textarea name=\"{$name}\" placeholder=\"{$placeholder}\" ".$other.">".$default."</textarea>";
  return $output;
}	

/* 
	Date formatting 
	accept 2 parameters
	1 - date which will be convert to format.
	2 - format in which given date will convert. bydefault false.	
*/
function dateformat($date,$format=false) {
	if(!$format) {
		$format="d M Y";
	}
	return date($format,strtotime($date));
}

/* 
	add minute in time 
	accept 1 parameter
	1 - integer value as minute to be added on current time.
*/
function add_min($min) {
	$now = time();
	$add_time = $now + ($min * 60);
	$end_time = date('Y-m-d H:i:s', $add_time);
	return $end_time;
}


/* 
	calculate date difference 
	accept 3 parameter
	1 - smaller date.
	2 - larger date.
	3 - type in which difference will return.
*/
function datedifference($date1,$date2,$type='dd') {
	$datetime1 = new DateTime($date1);
	$datetime2 = new DateTime($date2);
	$interval = $datetime2->diff($datetime1);
	if($type=='dd') {
		return str_replace('+','',$interval->format('%R%a'));
	}
	elseif($type=='mm') {
		return str_replace('+','',$interval->format('%R%m'));
	}
	elseif($type=='yr') {
		return str_replace('+','',$interval->format('%R%y'));
	}
	elseif($type=='hr') {
		return str_replace('+','',$interval->format('%R%h'));
	}
}

/* 
	invoice no. increment
	accept 1 parameter
	1 - last invoice number.		
*/
function increment_invoice_no($matches) {
  //return ++$matches[1];
  $val=$matches[1];
  $len=strlen($val);
  ++$val;
  return str_pad($val, $len, "0", STR_PAD_LEFT);  
}

/* 
	Replaces all spaces with hyphens. & Removes special chars. 
	accept 1 parameter
	1 - string need to be clean.		
*/
function clean_url($string) {
   $string = str_replace('', '-', $string); 
   return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
}

/* Generate token */
function token($length=8) {
	$md5=md5(uniqid(rand(), true));
	return substr($md5,2,$length);
}

/* 
	Replace the text from message 
	accept 2 parameters
	1 - message string.
	2 - array of string that need to be replace.	
*/
function replaces($string,$array) {
	foreach($array as $key=>$val) {
		$string=str_replace('|*'.$key.'*|',$val,$string);
	}
	return $string;
}

/* 
	CSV EXPORT 
	accept 2 parameters
	1 - array of record.
	2 - header option.
*/
function convertToCSV($data, $options) {
	/* setting the csv header*/
	if (is_array($options) && isset($options['headers']) && is_array($options['headers'])) {
		$headers = $options['headers'];
	} 
	else {
		$filename=date('d-M').".csv";
		$headers = array(
			'Content-Type' => 'text/csv',
			'Content-Disposition' => 'attachment; filename="'.$filename.'"'
		);
	}

	$output = '';
	/* setting the first row of the csv if provided in options array */
	if (isset($options['firstRow']) && is_array($options['firstRow'])) {
		$output .= implode(',', $options['firstRow']);
		$output .= "\n"; /* new line after the first line */
	}

	/* setting the columns for the csv. if columns provided, then fetching the or else object keys */
	if (isset($options['columns']) && is_array($options['columns'])) {
		$columns = $options['columns'];
	}
	else {
		$objectKeys = get_object_vars($data[0]);
		$columns = array_keys($objectKeys);
	}

	/* populating the main output string */
	foreach ($data as $row) {
		foreach ($columns as $column) {
			$output .= str_replace(',', ';', $row[$column]);
			$output .= ',';
		}
		$output .= "\n";
	}
	/* $file="./".date('d-m-y').".csv"; */
	$file_name=date('d-m-y').".csv";
	/* file_put_contents($file,$output); */
	force_download($file_name, $output);  
}

/* 
	Currency format 
	accept 1 parameter
	1 - integer to be converted into proper format.
*/
function moneyFormatIndia($num){
	$explrestunits = "" ;
	if(strlen($num)>3){
		$lastthree = substr($num, strlen($num)-3, strlen($num));
		$restunits = substr($num, 0, strlen($num)-3); /* extracts the last three digits */
		$restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; /* explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping. */
		$expunit = str_split($restunits, 2);
		for($i=0; $i<sizeof($expunit); $i++){
			/* creates each of the 2's group and adds a comma to the end */
			if($i==0) {
				$explrestunits .= (int)$expunit[$i].","; /* if is first value , convert into integer */
			}
			else{
				$explrestunits .= $expunit[$i].",";
			}
		}
		$thecash = $explrestunits.$lastthree;
	} 
	else {
		$thecash = $num;
	}
	return $thecash; /* writes the final format where $currency is the currency symbol. */
}

/* 
	Convert Amount to word 
	accept 1 parameter
	1 - integer to be converted into words.		
*/
function number_to_words($number) {
	$hyphen      = '-';
	$conjunction = ' and ';
	$separator   = ', ';
	$negative    = 'negative ';
	$decimal     = ' point ';
	$dictionary  = array(
		0                   => 'zero',
		1                   => 'one',
		2                   => 'two',
		3                   => 'three',
		4                   => 'four',
		5                   => 'five',
		6                   => 'six',
		7                   => 'seven',
		8                   => 'eight',
		9                   => 'nine',
		10                  => 'ten',
		11                  => 'eleven',
		12                  => 'twelve',
		13                  => 'thirteen',
		14                  => 'fourteen',
		15                  => 'fifteen',
		16                  => 'sixteen',
		17                  => 'seventeen',
		18                  => 'eighteen',
		19                  => 'nineteen',
		20                  => 'twenty',
		30                  => 'thirty',
		40                  => 'fourty',
		50                  => 'fifty',
		60                  => 'sixty',
		70                  => 'seventy',
		80                  => 'eighty',
		90                  => 'ninety',
		100                 => 'hundred',
		1000                => 'thousand',
		1000000             => 'million',
		1000000000          => 'billion',
		1000000000000       => 'trillion',
		1000000000000000    => 'quadrillion',
		1000000000000000000 => 'quintillion'
	);

	if (!is_numeric($number)) {
		return false;
	}

	if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
		// overflow
		trigger_error(
			'number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
			E_USER_WARNING
		);
		return false;
	}

	if ($number < 0) {
		return $negative . number_to_words(abs($number));
	}

	$string = $fraction = null;

	if (strpos($number, '.') !== false) {
		list($number, $fraction) = explode('.', $number);
	}

	switch (true) {
		case $number < 21:
			$string = $dictionary[$number];
			break;
		case $number < 100:
			$tens   = ((int) ($number / 10)) * 10;
			$units  = $number % 10;
			$string = $dictionary[$tens];
			if ($units) {
				$string .= $hyphen . $dictionary[$units];
			}
		break;
		case $number < 1000:
			$hundreds  = $number / 100;
			$remainder = $number % 100;
			$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
			if ($remainder) {
				$string .= $conjunction . number_to_words($remainder);
			}
		break;
		default:
			$baseUnit = pow(1000, floor(log($number, 1000)));
			$numBaseUnits = (int) ($number / $baseUnit);
			$remainder = $number % $baseUnit;
			$string = number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
			if ($remainder) {
				$string .= $remainder < 100 ? $conjunction : $separator;
				$string .= number_to_words($remainder);
			}
		break;
	}

	if (null !== $fraction && is_numeric($fraction)) {
		$string .= $decimal;
		$words = array();
		foreach (str_split((string) $fraction) as $number) {
			$words[] = $dictionary[$number];
		}
		$string .= implode(' ', $words);
	}

	return strtoupper($string);
}	


/* 
	Validation rules for controls.
	accept 1 parameter
	1 - value of control.
	2 - define rules for HTML control.
*/
function validate($value,$rule) {
	if($rule=="") {
		return true;
	}
	$return=false;
	$arr_value=explode("|", $rule);
	foreach($arr_value as $case_value) {	
		switch($case_value) {
			case "required": 
				if ($value=="") { 
					$return=false;
				} 
				else { 
					$return=true;
				};
			break;
			case "valid_email": 
				if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
					$return=true;
				}
				else {
					$return=false;
				}
			break;
			case "mobile": 
				if(preg_match('/^\d{10}$/',$value)) {
					$return=true;
				}
				else {
					$return=false;
				}
			break;			
			case "numeric":
				if (is_numeric($value)) {
					$return=true;
				} 
				else {
					$return=false;
				}
			break;	
			default: 
				$return=true;

		}
	}
	return $return;
}


function httpRequest($url) {
    $pattern = "/http...([0-9a-zA-Z-.]*).([0-9]*).(.*)/";
    preg_match($pattern,$url,$args);
    $in = "";
    $fp = fsockopen($args[1],80, $errno, $errstr, 30);
    if (!$fp) {
       return("$errstr ($errno)");
    } else {
  $args[3] = "C".$args[3];
        $out = "GET /$args[3] HTTP/1.1\r\n";
        $out .= "Host: $args[1]:$args[2]\r\n";
        $out .= "User-agent: PARSHWA WEB SOLUTIONS\r\n";
        $out .= "Accept: */*\r\n";
        $out .= "Connection: Close\r\n\r\n";

        fwrite($fp, $out);
        while (!feof($fp)) {
           $in.=fgets($fp, 128);
        }
    }
    fclose($fp);
    return($in);
}

function get_file_extension($file_name) {
  return strtolower(substr(strrchr($file_name,'.'),1));
}


/* 
	Parent child array 
	accept 2 parameters
	1 - array.
	2 - parent column name.	
*/
function parent_child_array($array,$parent_col) {
	$return = array();
	$i=0;
	foreach($array as $key=>$row) {
		$return[$row[$parent_col]][] =$row;
	}
	return $return;
}

function calc_age($date) {
  if($date=='0000-00-00') {
    return false;
  }
  $old_date=date_create($date);
  $cur_date=date_create(date('Y-m-d'));
  $age = $old_date->diff($cur_date)->y;
  return $age;
}

function defaultdate_format($date,$format="d M Y") {
	 $date_time=strtotime($date);
	 $time=date("H:i",$date_time);
	 if($time=='00:00') {
	  return date($format,$date_time);
	 }
	 else {
	  $format.=" h:i a";
	 return date($format,$date_time);
	 }
}


function hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   //return implode(",", $rgb); // returns the rgb values separated by commas
   return $rgb; // returns an array with the rgb values
}

/* calculate simple interest by month */
function calculate_interest($p,$r,$days) {
  $time=$days/365;
  $si = $p*$r*12*$time/100;
  return round(round($si*1000)/1000);
}