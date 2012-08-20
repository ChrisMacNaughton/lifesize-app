<?php
function l($string) {
	global $lang;

	// Check if the string exists
	if(!isset($lang[$string])) return '['.$string.']';

	// Get the locale string
	$string = $lang[$string];

	// Check if the $vars is an array or use the function args.
	if(!is_array($vars)) $vars = array_slice(func_get_args(),1);

	// Loop through the vars and replace the the {x} stuff
	foreach($vars as $var)
	{
		if(!isset($v)) $v = 0;
		++$v;
		$string = str_replace('{'.$v.'}',$var,$string);
	}

	return $string;
}
function settings($setting)
{
	global $CACHE, $db;

	// Check if the setting has already been fetched
	// and return it if it has.
	if(isset($CACHE['settings'][$setting])) return $CACHE['settings'][$setting];

	// Looks like the setting isn't in the cache,
	// lets fetch it now...
	$stmt = $db->prepare("SELECT setting, value FROM settings WHERE setting= :setting");
	$stmt->execute(array(
		':setting'=>$setting
	));
	$result = $stmt->fetch();
	$CACHE['settings'][$setting] = $result['value'];

	return $CACHE['settings'][$setting];
}

function microtime_diff( $start, $end=NULL ) { 
        if( !$end ) { 
            $end= microtime(); 
        } 
        list($start_usec, $start_sec) = explode(" ", $start); 
        list($end_usec, $end_sec) = explode(" ", $end); 
        $diff_sec= intval($end_sec) - intval($start_sec); 
        $diff_usec= floatval($end_usec) - floatval($start_usec); 
        return floatval( $diff_sec ) + $diff_usec; 
    } 
	
function _ago($time)
{
   $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
   $lengths = array("60","60","24","7","4.35","12","10");

   $now = time();
	if ($now > $time )
	{
       $difference     = $now - $time;
	}else{
		$difference = $time - $now;
	}
      

   for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
       $difference /= $lengths[$j];
   }

   $difference = round($difference);

   if($difference != 1) {
       $periods[$j].= "s";
   }

   return "$difference $periods[$j] ";
}

function to_seconds($duration) {
	$i = explode(':', $duration);
	return ($i[0] * 60 * 60) + ($i[1] * 60) + $i[2];
}