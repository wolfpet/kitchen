<?php

function do_bbcode($str) {
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
      '#\[img=(https?://\S*?)\s*\]#i', // Image ([img=http://url_to_image[/img])
      '#\[img=(\S*?)\s*\]#i', // Image ([img=url_to_image[/img])
      '#\[img\](https?://\S*?)\s*\[/img\]#i', // Image ([img]http://url_to_image[/img])
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
      '<img src="$1" alt=""/>',
      '<img src="//$1" alt=""/>',
      '<img src="$1" alt=""/>'
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
  // print('before naked called:-->'.$str.'<--');
  
  // Uncoded images & URLs   
  return bbcode_naked_urls(bbcode_naked_images($str));
}

// ================ Handling of URLs and Images outside of bb code ==========================

function unless_in_quotes($pattern) {
return '>'.$pattern.'<\/a>|="'.$pattern.'"|('.$pattern.')';
}

function bbcode_naked_images($str) {
  // Deal with untagged images
  $str = preg_replace_callback('#'.unless_in_quotes('(?:ftp|https?):\/\/[^\s>"]+\.(?:jpg|jpeg|gif|png|bmp)(?:[^\s<"]*)?').'#is', // unprocessed images (i.e. without quotes around them)
    function ($m) {
      if(empty($m[1])) return $m[0];
					else return '<img src="' . $m[1] . '" alt=""/>';
    }, $str);
  // print("--->".$str."<---");
  
  // Google pic URLs are also images
  $str = preg_replace_callback('#'.unless_in_quotes('https:\/\/[a-z0-9]+\.googleusercontent.com\/[^\s<]+').'#is', // unprocessed images (i.e. without quotes around them)
    function ($m) {
      if(empty($m[1])) return $m[0];
					else return '<img src="' . $m[1] . '" alt=""/>';
    }, $str);
  return $str;
}

function bbcode_naked_urls($str) {
  // '[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]'
  return preg_replace_callback('#'.unless_in_quotes('[[:alpha:]]+://[^<>[:space:]\"]+').'#is', // unprocessed URLs(i.e. without quotes around them)
    function ($m) {
      if(empty($m[1])) return $m[0];
					else return '<a target="_blank" href="' . $m[1] . '">' . $m[1] . '</a>';
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
    // imgur
    '#(?<!(\[url(=|]))|\[img=)((?:https?://)(?:www\.)?i\.imgur\.com/([^\s\.]*)\.?(?:[a-z]+)?(?:(?:\?|&)[^\s<\]"]*)?)#is',
    // youtube with no http(s) prefix
    '#(?<!(\]|/|\.|=))((?:www\.|m\.)?(?:\byoutu\b\.be/|\byoutube\b\.com/(?:embed|v|watch\?(?:[^\s<\]"]*?)?v=))([\w-]{10,12})(?:(?:\?|&)[^\s<\]"]*)?)#is'
     ), array (
    '<div class="vimeo"><iframe src="https://player.vimeo.com/video/$3" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe><br/>Link: <a href="$2" target="_blank">$2</a></div>',
    '<div class="coub"><iframe src="//coub.com/embed/$3?muted=false&autostart=false&originalSize=false&hideTopBar=false&startWithHD=false" width="500" height="281" frameborder="0" allowfullscreen="true"></iframe><br/>Link: <a href="$2" target="_blank">$2</a></div>',  
    '<div class="fb-video" data-href="$2" data-width="500"></div><br/>Link: <a href="$2" target="_blank">$2</a>',  
    '<div class="fb-video" data-href="$2" data-width="500"></div><br/><a href="$2">Please note that this link is only temporary and will not be available in the future</a>',
    '<div class="fb-video" data-href="$2" data-width="500"></div><br/>Link: <a href="$2" target="_blank">$2</a>',  
    '<div class="imgur"><blockquote class="imgur-embed-pub" lang="en" data-id="$4"><a href="//imgur.com/$4">Direct Link</a></blockquote><script async src="//s.imgur.com/min/embed.js" charset="utf-8"></script></div>',
    '<div class="youtube"><iframe type="text/html" width="480" height="320" src="http://www.youtube-nocookie.com/embed/$3?enablejsapi=1&start=0&wmode=transparent&origin=http://' . $host . '" frameborder="0"></iframe><br/>Link: <a href="$2" target="_blank">$2</a></div>'
    ), $original_body);    
    
  if (isset($has_video) && !is_null($has_video)) $has_video = strcmp($body, $original_body) != 0;

  // Embedding Twitter and other links
  $body = instagram(gfycat(twitter($body)));
  
  // Fix postimage.org tags
  $body = fix_postimage_tags($body);

  // other replacements
  $body = preg_replace( array (
    '#\s*$#s'
     ), array (
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

  $body = preg_replace( array (
    // search
    '#\[b\](.*?)\[/b\]#is', // Bold ([b]text[/b]
    '#\[i\](.*?)\[/i\]#is', // Italics ([i]text[/i]
    '#\[u\](.*?)\[/u\]#is', // Underline ([u]text[/u])
    '#\[s\](.*?)\[/s\]#is', // Strikethrough ([s]text[/s])
    '#(<img src=)#is',
    '#\((?:c|C|с|С)\)#is'
    ), array (
    // replace
    '<strong>$1</strong>',
    '<em>$1</em>',
    '<span style="text-decoration: underline;">$1</span>',
    '<span style="text-decoration: line-through;">$1</span>',
    '<img style="max-width: 99%;max-height: 99%;" src=',
    '©'
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
  if (!is_null($body) && strpos($body, 'class="fb-video"') !== false) { ?><div id="fb-root"></div><script>(function(d, s, id) {
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
// [url=http://postimage.org/][img]http://s29.postimg.org/gi2p1c6pz/spasibo.jpg[/img][/url]
  return preg_replace("#\[url=http:\/\/postimage\.org\/\]\[img\]([^\[]+)\[\/img\]\[\/url\]#i", "[img]$1[/img]", $str);
}

/**
 * Renderers
 */
function render_for_display($msgbody, $render_smiles=true) {

  $msgbody = preg_replace("#\[render=([^\]]*?)\](.*?)\[\/render\]#is", "$2", $msgbody);

  $msgbody = render_but_exclude_tags($msgbody, function($body) use ($render_smiles) {
    global $smileys;

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
	  $body = do_bbcode( $body );
	  $body = nl2br($body);
	  $body = after_bbcode($body);

    $body = grammar_nazi($body);
    
	  return $body;
  }, '[code]','<code>');
  
  return $msgbody;
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
    '#(8\)|8-\))#i',
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

      return '<img src="http://'.$host.$root_dir.$path.'" alt="'.$name.'" title="'.$name.'"/>';
		},
		$body
	);
    
  return $body;
}

function has_images($body) {
  return stristr(render_for_display($body, false), "<img style");
}

function grammar_nazi($body) {
  return preg_replace( array (
    // search
    '#оффис#', 
    '#рассо#', 
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
    'расо',
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