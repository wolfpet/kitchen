<?php

$arr_liter_orig=array("Ё"=>"JO",	"Ж"=>"ZH",	"Ч"=>"CH",	"Ш"=>"SH",	"Щ"=>"XH",	"Ъ"=>"##",		
    "Ь"=>"''", 	"Ю"=>"JU",	"Я"=>"JA", "ё"=>"jo",	"ж"=>"zh", "ч"=>"ch", 
	"ш"=>"sh",	"щ"=>"xh",	"ю"=>"ju",	"я"=>"ja",	"й"=>"j", "Э"=>"W", "А"=>"A", "э"=>"w",		
    "Б"=>"B",	"В"=>"V",	"Г"=>"G",	"Д"=>"D",	"Е"=>"E", "З"=>"Z",	"И"=>"I", "Й"=>"J", 
	"К"=>"K",	"Л"=>"L",	"М"=>"M",	"Н"=>"N",	"О"=>"O",	"П"=>"P",	"Р"=>"R",	"С"=>"S",	"Т"=>"T",
    "У"=>"U",	"Ф"=>"F",	"Х"=>"H",	"Ц"=>"C",	"Ы"=>"Y",	
    "а"=>"a",	"б"=>"b",	"в"=>"v",	"г"=>"g",	"д"=>"d",	"е"=>"e",	"з"=>"z",	"и"=>"i",
    "к"=>"k",	"л"=>"l",	"м"=>"m",	"н"=>"n",	"о"=>"o",	"п"=>"p",	"р"=>"r",	"с"=>"s",	"т"=>"t",
    "у"=>"u",	"ф"=>"f",	"х"=>"h",	"ц"=>"c",	"ъ"=>"#",	"ы"=>"y",	"ь"=>"'"
);

$arr_liter=array("^J"=>"Й", "^Y"=>"Ы", "^Z"=>"З", "^C"=>"Ц", "^S"=>"С", "^X"=>"Х", 
				"^j"=>"й", "^y"=>"ы", "^z"=>"з", "^c"=>"ц", "^s"=>"с", "^x"=>"х", 
	"Jo"=>"Ё", "JO"=>"Ё",	
	"YO"=>"Ё", "Yo"=>"Ё", "Ju"=>"Ю",  "JU"=>"Ю",	"YU"=>"Ю", "Yu"=>"Ю", "JA"=>"Я", "Ja"=>"Я", "YA"=>"Я", "Ya"=>"Я", 
	"Zh"=>"Ж", "ZH"=>"Ж",	"Ch"=>"Ч", "CH"=>"Ч",	"SHH"=>"Щ", "SHh"=>"Щ", "Shh"=>"Щ", "Sh"=>"Ш", "SH"=>"Ш",	
	"Xh"=>"Щ", "XH"=>"Щ",	"##"=>"Ъ",	"shh"=>"щ",	"''"=>"Ь", 	"jo"=>"ё",	"yo"=>"ё", "zh"=>"ж", "ch"=>"ч", 
	"sh"=>"ш",	"xh"=>"щ",	"ju"=>"ю",	"ja"=>"я",	"yu"=>"ю",	"ya"=>"я", "j"=>"й", "W"=>"Э", "A"=>"А", "w"=>"э",		
    "B"=>"Б",	"V"=>"В",	"G"=>"Г",	"D"=>"Д",	"E"=>"Е", "Z"=>"З",	"I"=>"И", "J"=>"Й", 
	"K"=>"К",	"L"=>"Л",	"M"=>"М",	"N"=>"Н",	"O"=>"О",	"P"=>"П",	"R"=>"Р",	"S"=>"С",	"T"=>"Т",
    "U"=>"У",	"F"=>"Ф",	"H"=>"Х",	"X"=>"Х", 	"C"=>"Ц",	"Y"=>"Ы",	
    "a"=>"а",	"b"=>"б",	"v"=>"в",	"g"=>"г",	"d"=>"д",	"e"=>"е",	"z"=>"з",	"i"=>"и",
    "k"=>"к",	"l"=>"л",	"m"=>"м",	"n"=>"н",	"o"=>"о",	"p"=>"п",	"r"=>"р",	"s"=>"с",	"t"=>"т",
    "u"=>"у",	"f"=>"ф",	"h"=>"х",	"x"=>"х",   "c"=>"ц",	"#"=>"ъ",	"y"=>"ы",	"'"=>"ь"
);

function convert_liter_up($s_string){//latin to cyrillic
	global $arr_liter;
	$s_ret=$s_string;
	foreach($arr_liter as $k=>$v){$s_ret=str_replace($k, $v, $s_ret);}
	return $s_ret;
	}
function convert_liter_down($s_string){//cyrillic to latin
	global $arr_liter;
	$s_ret=$s_string;
	foreach($arr_liter as $k=>$v){$s_ret=str_replace($v, $k, $s_ret);}
	return $s_ret;
	}


function translit($text, &$processed) {
  $processed = false;
  if (is_null($text) || strlen($text) == 0) {
    return $text;
  } 
  
  $trl_start = strpos($text, '[trl]');
  if ($trl_start === false)
  { 
    return $text;
  }
  $text .= '                                ';
  
  $newtext = '';

  $processed = true;
  do {
  $trl_start = strpos($text, '[trl]');
   if ($trl_start === false) {
    $newtext .= $text;
	break;
	}
  //print('<p><font color="grey"> ' . $text . '(' . $trl_start . ')</font>');
  $newtext .= substr($text, 0, $trl_start);
  $text = substr($text, $trl_start + 5);
  $trl_end = strpos($text, '[/trl]');
  if ($trl_end) {
  //print('<p><font color="green"> ' . $text . '</font>');
   $todo_text = substr($text, 0, $trl_end);
   $text = substr($text, $trl_end + 6);
  } else {
   $todo_text = $text;
   $text = '';
  }
  //print('<p><font color="blue"> ' . $todo_text . '</font><P><font color="red">' . $newtext . '</font>');
  $newtext .= convert_liter_up($todo_text);
  $trl_start = strpos($text, '[trl]');
  } while (strlen($text) > 0);
  return $newtext;
  
}

function translit2($text, &$processed) {
  $processed = false;
  if (is_null($text) || strlen($text) == 0) {
    return $text;
  } 
  /*
  $trl_start = strpos($text, '[trl]');
  if ($trl_start === false)
  { 
    return $text;
  }
  */
  $newtext = '';

  $processed = true;
  do {
  $trl_start = strpos($text, '[trl]');
  if ($trl_start === false) {
    $newtext .= $text;
	break;
	}
  print('<p><font color="grey"> ' . $text . '(' . $trl_start . ')</font>');
  $newtext .= substr($text, 0, $trl_start);
  $text = substr($text, $trl_start + 5);
  $trl_end = strpos($text, '[/trl]');
  if ($trl_end) {
  print('<p><font color="green"> ' . $text . '</font>');
   $todo_text = substr($text, 0, $trl_end);
   $text = substr($text, $trl_end + 6);
  } else {
   $todo_text = $text;
   $text = '';
  }
  print('<p><font color="blue"> ' . $todo_text . '</font><P><font color="red">' . $newtext . '</font>');
  $newtext .= convert_liter_down($todo_text);
  $trl_start = strpos($text, '[trl]');
  } while (strlen($text) > 0);
  return $newtext;
  
}

?>
