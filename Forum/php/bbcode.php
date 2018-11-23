<?php

function do_bbcode($str, $auth_id, $msg_id) {

  // The array of regex patterns to look for
  $format_search = array(
      '#\[b\](.*?)\[/b\]#is', // Bold ([b]text[/b]
      '#\[i\](.*?)\[/i\]#is', // Italics ([i]text[/i]
      '#\[u\](.*?)\[/u\]#is', // Underline ([u]text[/u])
      '#\[s\](.*?)\[/s\]#is', // Strikethrough ([s]text[/s])
      '#\[code=([^\]\s]*)\s*\](.*?)\[/code\]#is', // Monospaced code [code]text[/code])
      '#\[code\](.*?)\[/code\]#is', // Monospaced code [code]text[/code])
      '#\[sarcasm\](.*?)\[/sarcasm\]#is', // Sarcasm
      '#\[size=([1-9]|1[0-9]|20)\](.*?)\[/size\]#is', // Font size 1-20px [size=20]text[/size])
      '#\[color=([A-F0-9]{3}|[A-F0-9]{6})\](.*?)\[/color\]#is', // Font color ([color=00F]text[/color])
      '#\[color=(.*?)\](.*?)\[/color\]#is', // Font color ([color=#00F]text[/color]) or Font color ([color={color_name}]text[/color])      
//      '#\[url=((?:ftp|https?)://[^\]\s]*)\s*\](.*?)\[/url\]#is', // Hyperlink with descriptive text ([url=http://url]text[/url])
      '#\[url=((?:ftp|https?):\/\/[^\]\s]*)\s*\](.*?)\[\/url\]#is',
      '#\[url=([^\]\s]*)\s*\](.*?)\[/url\]#is', // Hyperlink with descriptive text ([url=http://url]text[/url])
      '#\[url\]((?:ftp|https?)://[^\s<\["]*)\s*\[/url\]#i', // Hyperlink ([url]http://url[/url]),
      '#\[url\]([^\s<\["]*)\s*\[/url\]#i', // Hyperlink ([url]http://url[/url]) 
      '#\[img=(https?://\S*?)\s*\](.*)\[/img\]#i', // Image ([img=http://url_to_image]tooltip[/img])
      '#\[img=(\S*?)\s*\](.*)\[/img\]#i', // Image ([img=url_to_image]tooltip[/img])
      '#\[img=(https?://\S*?)\s*\]#i', // Image ([img=http://url_to_image])
      '#\[img=(\S*?)\s*\]#i', // Image ([img=url_to_image])
      '#\[img\](https?://\S*?)\s*\[/img\]#i', // Image ([img]http://url_to_image[/img])
      '#\[POLL\](.*?)\[/POLL\]#is', // Bold ([b]text[/b]
      
  );
   
  // The matching array of strings to replace matches with
  $format_replace = array(
      '<strong>$1</strong>',
      '<i>$1</i>',
      '<span style="text-decoration: underline;">$1</span>',
      '<span style="text-decoration: line-through;">$1</span>',
      '<pre><code class="$1">$2</'.'code></'.'pre>',
      '<pre><code>$1</'.'code></'.'pre>',
      '<em>$1</em>',
      '<span style="font-size: $1px;">$2</span>',
      '<span style="color: #$1;">$2</span>',
      '<span style="color: $1;">$2</span>',
      '<a target="_blank" href="$1">$2</a>',
      '<a target="_blank" href="//$1">$2</a>',
      '<a target="_blank" href="$1">$1</a>',
      '<a target="_blank" href="//$1">$1</a>',
      '<img src="$1" alt="" title="$2"/>',
      '<img src="//$1" alt="" title="$2"/>',
      '<img src="$1" alt=""/>',
      '<img src="//$1" alt=""/>',
      '<img src="$1" alt=""/>',
      '<iframe style="border-style: none; width: 100%; max-width: 460px;" id="poll$1" class="poll" src="polls_display.php?poll=$1" onload="resizeMe(this);"></iframe>'
       
  );
  
  // Perform the actual conversion
  $str = preg_replace($format_search, $format_replace, $str);

  // Deal with quotes
  $format_search =  array(
    '#\[quote(?!.*\[quote)\](.*?)\[/quote\]\s*#is', // Quote ([quote]text[/quote])
	  '#\[quote(?!.*\[quote)=(.*?)\](.*?)\[/quote\]\s*#is', // Quote with author ([quote=author]text[/quote])
  );
   
  // The matching array of strings to replace matches with
  $format_replace = array(
    '<blockquote><div>$1</div></blockquote><br/>',
    '<blockquote><div><cite>$1:</cite>$2</div></blockquote><br/>',
  );
   
  // Perform the actual quotes conversion
  $count = 1;
  while ($count > 0) {
    $str = preg_replace($format_search, $format_replace, $str, -1, $count);
  }
   //print('before naked called:-->'.$str.'<--');
  
  // Uncoded images & URLs   
  return bbcode_naked_urls(bbcode_naked_images($str, $auth_id, $msg_id));
}

// ================ Handling of URLs and Images outside of bb code ==========================

function unless_in_quotes($pattern) {
return '>'.$pattern.'<\/a>|="'.$pattern.'"|('.$pattern.')';
}

function bbcode_naked_images($str) {
 global $auth_id;
 global $msg_id; 
  // Deal with untagged images
  $str = preg_replace_callback('#'.unless_in_quotes('(?:ftp|https?):\/\/[^\s>"]+\.(?:jpg|jpeg|gif|png|bmp)(?:[^\s<"]*)?').'#is', // unprocessed images (i.e. without quotes around them)
    function ($m) {
      global $auth_id;
      global $msg_id;
      if(empty($m[1])) return $m[0];
	else 
	{
            //return '<img style="cursor: pointer;max-width: 99%;max-height: 99%;" src="' . $m[1] . '" alt=""/>';
            return '<img onload="loadimage(this);" style="cursor: pointer;max-width: 99%;max-height: 79vh;opacity: 0.5;" src="images/empty-image.png" alt="' . $m[1] . '"/>';
	}
    }, $str);
  
  // Google pic URLs are also images
  $str = preg_replace_callback('#'.unless_in_quotes('https:\/\/[a-z0-9]+\.googleusercontent.com\/[^\s<]+').'#is', // unprocessed images (i.e. without quotes around them)
    function ($m) {
      if(empty($m[1])) return $m[0];
	else {return '<img src="' . $m[1] . '" alt=""/>';}
    }, $str);
  return $str;
}

function bbcode_naked_urls($str) {

  // '[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]'
  return preg_replace_callback('#'.unless_in_quotes('[[:alpha:]]+://[^<>[:space:]\"]+').'#is', // unprocessed URLs(i.e. without quotes around them)
    function ($m) {
      if(empty($m[1])) return $m[0];
					else {
						return '<a target="_blank" href="' . $m[1] . '">' . $m[1] . '</a>';
					     }
    }, $str);
}

/** 
 * Run this before bbcode is called to render content before bbcode() had a chance to mess it up
 */
function before_bbcode($original_body, &$has_video=null) {
  global $host;
  
  $body = preg_replace( array (
    // Vimeo on-the-fly e.g. https://vimeo.com/129252030
    '#(?<!\[url(=|\]))((?:https?://)(?:www\.)?vimeo\.com/([0-9]*)(?:(?:\?|&)[^\s<\]"]*)?)#is', 
    // Coub on the fly e.g. http://coub.com/view/3lbz7
    '#(?<!\[url(=|\]))((?:https?://)(?:www\.)?coub\.com/(?:view|embed)/([0-9a-zA-Z]*)(?:(?:\?|&)[^\s<\]"]*)?)#is', 
    // FB video clip (permanent link) e.g. https://www.facebook.com/kolesiko.taiskoe/videos/vb.100006902082868/1524658977774157/?type=2&theater
    '#(?<!\[url(=|\]))((?:https?://)(?:www\.)?facebook\.com/\S+/videos/[^\s<\]"]+(?:(?:\?|&)[^\s<\]"]*)?)#is',
    // FB video clip (temporary link) e.g. https://video-ord1-1.xx.fbcdn.net/hvideo-xap1/v/t42.1790-2/10444296_1524659357774119_1276856449_n.mp4?efg=eyJybHIiOjM2NSwicmxhIjo1MTJ9&rl=365&vabr=203&oh=e9a02a9d91fe8de7d59750a03447dc42&oe=55A5D0C0
    '#(?<!\[url(=|\]))((?:https?://)?video-[^\s<\]"]+\.mp4(?:(?:\?)[^\s<\]"]*)?)#is',
    // FB video clip (yet another) e.g. https://www.facebook.com/video.php?v=911326538908142
    '#(?<!\[url(=|\]))((?:https?://)(?:www\.)?facebook\.com/video\.php\?v=[^\s<\]"]+(?:(?:\?|&)[^\s<\]"]*)?)#is',
    // FB post e.g. https://www.facebook.com/pablitomoiseevich/posts/10154405819774010
    '#(?<!\[url(=|\]))((?:https?://)(?:www\.)?facebook\.com/\S+/posts/[^\s<\]"]+(?:(?:\?|&)[^\s<\]"]*)?)#is',    
    // FB photo e.g. https://www.facebook.com/photo.php?fbid=1845794888790800
    '#(?<!\[url(=|\]))((?:https?://)(?:www\.)?facebook\.com/photo\.php\?fbid=[^\s<\]"]+(?:(?:\?|&)[^\s<\]"]*)?)#is',
    // Twitter moments e.g. https://twitter.com/i/moments/1047551358948319233
    '#(?<!\[url(=|\]))((?:https?://)(?:www\.)?twitter\.com\/i\/moments\/[^\s<\]"]+(?:(?:\?|&)[^\s<\]"]*)?)#is',
    // imgur
    '#(?<!(\[url(=|]))|\[img=)((?:https?:\/\/)(?:www\.)?i\.imgur\.com\/([^\s\.]+)\.?(?:[a-z]+)?(?:(?:\?|&)[^\s<\]"]*)?)#is',
    '#(?<!(\[url(=|]))|\[img=)((?:https?:\/\/)(?:www\.)?imgur\.com\/gallery\/([^\s\.]+)\.?(?:[a-z]+)?(?:(?:\?|&)[^\s<\]"]*)?)#is',
    // youtube with no http(s) prefix
    '#(?<!(\]|/|\.|=))((?:www\.|m\.)?(?:\byoutu\b\.be/|\byoutube\b\.com/(?:embed|v|watch\?(?:[^\s<\]"]*?)?v=))([\w-]{10,12})(?:(?:\?|&)[^\s<\]"]*)?)#is',
    // s3 vipvip videos e.g. https://s3.amazonaws.com/vipvip.ca/mLxY9XSZWRVIDEO0296.mp4
    '#(?<!\[url(=|\]))((?:https?://)(?:s3\.)?amazonaws\.com/vipvip.ca/([\w_-]*\.mp4)(?:(?:\?|&)[^\s<\]"]*)?)#is'
     ), array (
    '<div class="vimeo"><iframe src="https://player.vimeo.com/video/$3" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe><br/>Link: <a href="$2" target="_blank">$2</a></div>',
    '<div class="coub"><iframe src="//coub.com/embed/$3?muted=false&autostart=false&originalSize=false&hideTopBar=false&startWithHD=false" width="500" height="281" frameborder="0" allowfullscreen="true"></iframe><br/>Link: <a href="$2" target="_blank">$2</a></div>',  
    '<div class="fb-video" data-href="$2" data-width="500"></div><br/>Link: <a href="$2" target="_blank">$2</a>',  
    '<div class="fb-video" data-href="$2" data-width="500"></div><br/><a href="$2">Please note that this link is only temporary and will not be available in the future</a>',
    '<div class="fb-video" data-href="$2" data-width="500"></div><br/>Link: <a href="$2" target="_blank">$2</a>',  
    '<div class="fb-post" data-href="$2" data-width="500" data-show-text="true"></div><br/>Link: <a href="$2" target="_blank">$2</a>',  
    '<div class="fb-post" data-href="$2" data-width="500"></div><br/>Link: <a href="$2" target="_blank">$2</a>',  
    '<a class="twitter-moment" href="$2?ref_src=twsrc%5Etfw">$2</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>',    
    '<div class="imgur"><blockquote class="imgur-embed-pub" lang="en" data-id="$4"><a href="//imgur.com/$4">Direct Link</a></blockquote><script async src="//s.imgur.com/min/embed.js" charset="utf-8"></script></div>',
    '<div class="imgur"><blockquote class="imgur-embed-pub" lang="en" data-id="a/$4"><a href="//imgur.com/$4">Direct Link</a></blockquote><script async src="//s.imgur.com/min/embed.js" charset="utf-8"></script></div>',
    '<div class="youtube"><iframe type="text/html" width="480" height="320" src="http://www.youtube-nocookie.com/embed/$3?enablejsapi=1&start=0&wmode=transparent&origin=http://' . $host . '" frameborder="0"></iframe><br/>Link: <a href="$2" target="_blank">$2</a></div>',
    '<div class="s3"><video width=480" height="320" controls><source src="$2" type="video/mp4"></video><br/>Link: <a href="$2" target="_blank">$2</a></div>'    
    ), $original_body);    
    
  if (isset($has_video) && !is_null($has_video)) $has_video = strcmp($body, $original_body) != 0;

  // Embedding Twitter and other links
  $body = instagram(gfycat(twitter($body)));
  
  // Fix postimage.org tags
  $body = fix_postimage_tags($body);

  // other replacements
  $body = preg_replace( array (
    // WhatsApp formatting
    '#(?:(?<=[\.,\;\-\s])|^)_(.+?)_(?:(?=[\.,\;\-\s\!])|$)#s',
    '#(?:(?<=[\.,\;\-\s])|^)\*(.+?)\*(?:(?=[\.,\;\-\s\!])|$)#s',
    '#(?:(?<=[\.,\;\-\s])|^)~(.+?)~(?:(?=[\.,\;\-\s\!])|$)#s',
    //
    '#\s*$#s', // extra trailing spaces 
    '#^\s#s' // extra leading spaces 
     ), array (
    // WhatsApp formatting
    '[i]$1[/i]',
    '[b]$1[/b]',
    '[s]$1[/s]',
    //
    '', 
    ''
     ), $body);
  
  return $body;
}

/** 
 * Run this after bbcode is called to finalize the rendering of the message body 
 */
function after_bbcode($body) {
  
  // handle [iframe] tag
  $body = preg_replace_callback('#\[iframe (.*)\]#i',
		function ($matches) {
      return "<iframe ".html_entity_decode($matches[1])."></iframe>";
    }, $body);
  
  // remove <br /> from <pre> tags
  if (preg_match_all('/\<pre\>(.*?)\\<\/pre\>/s', $body, $matches)) {
    foreach($matches[1] as $a) {
      $body = str_replace($a, str_replace("<br />", '', $a), $body);
    }
  }
  
  // remove <br /> from title="
  if (preg_match_all('/title="(.*?)"/s', $body, $matches)) {
    foreach($matches[1] as $a) {
      $body = str_replace($a, str_replace("<br />", '&#013;', $a), $body);
    }
  }   

  $body = preg_replace( array (
    // search
    '#\[b\](.*?)\[/b\]#is', // Bold ([b]text[/b]
    '#\[i\](.*?)\[/i\]#is', // Italics ([i]text[/i]
    '#\[u\](.*?)\[/u\]#is', // Underline ([u]text[/u])
    '#\[s\](.*?)\[/s\]#is', // Strikethrough ([s]text[/s])
    '#(<img src=)#is',
    '#\((?:c|C|с|С)\)#is',
    '#\[div\](.*?)\[/div\]#is', // div ([div]anything[/div]
    '#(^|\s)(\#[\w|\\x{0400}-\\x{04FF}]+)#ius'
    ), array (
    // replace
    '<strong>$1</strong>',
    '<em>$1</em>',
    '<span style="text-decoration: underline;">$1</span>',
    '<span style="text-decoration: line-through;">$1</span>',
    '<img style="cursor: pointer;max-width: 99%;max-height: 99%;" src=',
    '©',
    '<div>$1</div>',
    '$1<a href="javascript:hashtag(\'$2\')">$2</a>'
    ), $body);    
       
  return fix_msg_target($body);
}

/** 
 * Replaces target for URLs that reference messages of this forum
 */
function fix_msg_target($body) {
  global $host;
  return str_replace('<a target="_blank" href="http://'.$host.'/msg.php?id=', '<a target="bottom" href="http://'.$host.'/msg.php?id=', $body);
}

/**
 * FaceBook API support
 */
function include_facebook_api_if_required($body) {
  if (!is_null($body) && (strpos($body, 'class="fb-video"') !== false || strpos($body, 'class="fb-post"') !== false)) { ?><div id="fb-root"></div><script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=1588477758092680";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script><?php
  } 
}

function initialize_highlightjs_if_required($body) {
  if (!is_null($body) && strpos($body, '<pre>') !== false) { ?><script>hljs.initHighlightingOnLoad();</script><?php  
  }
}

function fix_postimage_tags( $str ) {
 //[url=http://postimage.org/][img]http://s29.postimg.org/gi2p1c6pz/spasibo.jpg[/img][/url]
 //return preg_replace("#\[url=http:\/\/postimage\.org\/\]\[img\]([^\[]+)\[\/img\]\[\/url\]#i", "[img]$1[/img]", $str);

 $temp_str =  preg_replace("/\[url=https:\/\/postimg\.org\/image\/([^\[]+)\/\]\[img\]([^\[]+)\[\/img\]\[\/url\]/", "[img]$2[/img]", $str);
 //die($temp_str);
 return $temp_str;
}

function getPicTags($str, $startDelimiter, $endDelimiter) {
  $contents = array();
  $startDelimiterLength = strlen($startDelimiter);
  $endDelimiterLength = strlen($endDelimiter);
  $startFrom = $contentStart = $contentEnd = 0;
  while (false !== ($contentStart = strpos($str, $startDelimiter, $startFrom))) {
  $contentStart += $startDelimiterLength;
  $contentEnd = strpos($str, $endDelimiter, $contentStart);
  if (false === $contentEnd) {
        break;
  }
  $contents[] = substr($str, $contentStart, $contentEnd - $contentStart);
  $startFrom = $contentEnd + $endDelimiterLength;
  }
  return $contents;
}

/**
 * Renderers
 */
function render_for_display($msgbody, $render_smiles=true, $auth_name, $auth_id, $msg_id) {

  $msgbody = preg_replace("#\[render=([^\]]*?)\]\s*(.*?)\[\/render\]#is", "[div]$2[/div]", $msgbody);

  $msgbody = render_but_exclude_tags($msgbody, function($body) use ($render_smiles) {
    global $smileys;
    global $auth_id;
    global $msg_id;
    if ($smileys) {
      // do nothing
    } else 
      $render_smiles = false;
    
    if ($render_smiles) {
      $body = render_smileys_step1($body); 
    }

    $body = htmlentities($body, HTML_ENTITIES,'UTF-8');

    if ($render_smiles) {
      $body = render_smileys_step2($body); 
    }
	  $body = before_bbcode($body);
	  $body = do_bbcode($body, $auth_id, $msg_id);
	  $body = nl2br($body);
	  $body = after_bbcode($body);
	  $body = grammar_nazi($body);
	  $body = gallery_cleanup($body);
	  return $body;
  }, '[code]','<code>');
  
  return $msgbody;
}

function gallery_cleanup($body)
{
    global $auth_id;
    global $msg_id;
    $newimgstr = '<img onclick="parent.openPicInGallery(this, ' . $auth_id .', ' . $msg_id . ');"';    
    $processedBody = str_replace ('<img' , $newimgstr , $body);
    return $processedBody; 
}


function render_for_db($msgbody) {

  $msgbody = youtube( $msgbody );
  $msgbody = fix_postimage_tags( $msgbody );
  $msgbody = grammar_nazi($msgbody);
  return $msgbody;
}

function render_for_editing($msgbody) {
  // process [render] tags 
  $msgbody = preg_replace("#\[render=([^\]]*?)\](.*?)\[\/render\]#is", "$1", $msgbody);
  return $msgbody;
}

function render_smileys_but_exclude_pre_tags($body) {
	return render_but_exclude_tags($body, 'render_smileys');
}

function render_but_exclude_tags($body, $func, $tag='<pre>', $rendered_tag='<pre>') {
  $pres = array();
  
  $taglen = strlen($tag);
  $closing_tag = substr($tag,0,1) . '/' . substr($tag, 1);
  
  // exclude tags
  $pos = 0;
  do {
    $pos = strpos($body, $tag, $pos);
    if ($pos !== FALSE) {
      $pos += $taglen; 
      $end = strpos($body, $closing_tag, $pos);
      if ($end != FALSE) {
        $pres[] = substr($body, $pos, $end-$pos);
        $body = substr($body, 0, $pos) . substr($body, $end);
        $pos += $taglen + 1; // length of closing tag
      }
    }
  } while ($pos !== FALSE);  
  // print(' Body without '.$tag.' tags: "'.htmlentities($body, HTML_ENTITIES,'UTF-8').'" taglen='.$taglen.' closing tag='.$closing_tag);
  
  // do actual work
  $body = $func($body);
  
  if (count($pres) > 0) {
    // restore pre tags
    $i = $pos = 0;
    do {
      $pos = strpos($body, $rendered_tag, $pos);
      if ($pos !== FALSE) {
        $pos += strlen($rendered_tag); 
        $body = substr($body, 0, $pos) . htmlentities($pres[$i++], HTML_ENTITIES,'UTF-8') . substr($body, $pos);
      }
    } while ($pos !== FALSE);
    // print(' Body with restored '.$tag.' tags: "'.htmlentities( $body, HTML_ENTITIES,'UTF-8').'"');
  }
  
  return $body;
}

function render_smileys($body) {
  return render_smileys_step2(render_smileys_step1($body));
}

function render_smileys_step1($body) {
  // first translate short smiles e.g. :)
  //  :D  :)  :(  :o :? 8) etc
  $body = preg_replace( array (
    // search
    '#(:D)|(:\)\)+)#', 
    '#(:\)|:-\))#', 
    '#:\(+#i',
    '#:o(?!\w)#i',
    '#:\?#',
    '#([;]\)|[;]\-\))#i',
    '#(8-\))#i',
    '#(:\||:-\|)#'
    ), array (
    // replace
    ':biggrin:',
    ':smile:',
    ':sad:',
    ':surprised:',
    ':confused:',
    ':wink:',
    ':cool:',
    ':neutral:'
    ), $body);    

  return $body;
}

function render_smileys_step2($body) {
  global $host, $root_dir;  
  // then :<word>: e.g.  :shock: or :lol: 
  $body = preg_replace_callback('#:([a-z]+):#is',
    function ($matches) use ($host, $root_dir) {
      // var_dump($matches);
			$name = $matches[1];
      $path = "images/smiles/".$name.".gif";
      $exists = file_exists($path);
      
      if(!$exists) 
        return $matches[0];

      return '<img width="25px" src="http://'.$host.$root_dir.$path.'" alt="'.$name.'" title="'.$name.'"/>';
		},
		$body
	);
    
  return $body;
}

function has_images($body) {
  return stristr(render_for_display($body, false), "<img onclick");
}

function grammar_nazi($body) {
  return preg_replace( array (
    // search
    '#оффис#', 
    '#рассов#', 
    '#расса#',
    '#рассе#',
    '#расси#',
    '#бизнесс#',
    '#дессерт#',
    '#галлере#',
    '#аддресс#',
    '#адресс#',
    '#p\.s\.\s*#'
    ), array (
    // replace
    'офис',
    'расов',
    'раса',
    'расе',
    'раси',
    'бизнес',
    'десерт',
    'галере',
    'адрес',
    'адрес',
    'P.S. '
    ), $body);    
}

?>