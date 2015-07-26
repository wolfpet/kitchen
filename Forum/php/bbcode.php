<?php

function do_bbcode($str) {
/*  
  if(!extension_loaded('fastbbcode')) { */
//  print('<div id="stamp" style="font-size: 8px;color:gray;position:fixed;left: 0px;top: 0px;width: 100%;height: 8px;z-index: 9999;text-align: right;">phpBBCode&nbsp;</div>');
/*    return bbcode_format($str);
  } else 
    return bbcode($str);
}

function bbcode_format($str){
*/  
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
      '#\[img=(\S*?)\s*\]#i' // Image ([img=url_to_image[/img])
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
      '<img src="//$1" alt=""/>'
  );
  
  // Perform the actual conversion
  $str = preg_replace($format_search, $format_replace, $str);

  // Deal with quotes
  $format_search =  array(
    '#\[quote(?!.*\[quote)\](.*?)\[/quote\]#is', // Quote ([quote]text[/quote])
	  '#\[quote(?!.*\[quote)=(.*?)\](.*?)\[/quote\]#is', // Quote with author ([quote=author]text[/quote])
  );
   
  // The matching array of strings to replace matches with
  $format_replace = array(
    '<blockquote><div>$1</div></blockquote>',
    '<blockquote><div><cite>$1:</cite>$2</div></blockquote>',
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
function before_bbcode($body) {
  
  $body = preg_replace( array (
    // Vimeo on-the-fly e.g. https://vimeo.com/129252030
    '#(?<!\[url(=|]))((?:https?://)(?:www\.)?vimeo\.com/([0-9]*)(?:(?:\?|&)[^\s<\]"]*)?)#is', 
    // Coub on the fly e.g. http://coub.com/view/3lbz7
    '#(?<!\[url(=|]))((?:https?://)(?:www\.)?coub\.com/(?:view|embed)/([0-9a-zA-Z]*)(?:(?:\?|&)[^\s<\]"]*)?)#is', 
    // FB video clip (permanent link) e.g. https://www.facebook.com/kolesiko.taiskoe/videos/vb.100006902082868/1524658977774157/?type=2&theater
    '#(?<!\[url(=|]))((?:https?://)(?:www\.)?facebook\.com/\S+/videos/[^\s<\]"]+(?:(?:\?|&)[^\s<\]"]*)?)#is',
    // FB video clip (temporary link) e.g. https://video-ord1-1.xx.fbcdn.net/hvideo-xap1/v/t42.1790-2/10444296_1524659357774119_1276856449_n.mp4?efg=eyJybHIiOjM2NSwicmxhIjo1MTJ9&rl=365&vabr=203&oh=e9a02a9d91fe8de7d59750a03447dc42&oe=55A5D0C0
    '#(?<!\[url(=|]))((?:https?://)?video-[^\s<\]"]+\.mp4(?:(?:\?)[^\s<\]"]*)?)#is',
    // imgur
    '#(?<!(\[url(=|]))|\[img=)((?:https?://)(?:www\.)?i\.imgur\.com/([^\s\.]*)\.?(?:[a-z]+)?(?:(?:\?|&)[^\s<\]"]*)?)#is'
    ), array (
    '<div class="vimeo"><iframe src="https://player.vimeo.com/video/$3" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe><br/>Link: <a href="$2">$2</a></div>',
    '<div class="coub"><iframe src="//coub.com/embed/$3?muted=false&autostart=false&originalSize=false&hideTopBar=false&startWithHD=false" width="500" height="281" frameborder="0" allowfullscreen="true"></iframe><br/>Link: <a href="$2">$2</a></div>',  
    '<div class="fb-video" data-href="$2" data-width="500"></div><br/>Link: <a href="$2">$2</a>',  
    '<div class="fb-video" data-href="$2" data-width="500"></div><br/><a href="$2">Please note that this link is only temporary and will not be available in the future</a>',
    '<div class="imgur"><blockquote class="imgur-embed-pub" lang="en" data-id="$4"><a href="//imgur.com/$4">Direct Link</a></blockquote><script async src="//s.imgur.com/min/embed.js" charset="utf-8"></script></div>'
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
    '#(<img src=)#is'
    ), array (
    // replace
    '<strong>$1</strong>',
    '<em>$1</em>',
    '<span style="text-decoration: underline;">$1</span>',
    '<span style="text-decoration: line-through;">$1</span>',
    '<img style="max-width: 99%;max-height: 99%;" src='
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

/**
 * Renderers
 */
function render_for_display($msgbody) {

  $msgbody = htmlentities( $msgbody, HTML_ENTITIES,'UTF-8');
  $msgbody = before_bbcode($msgbody);
  $msgbody = do_bbcode ( $msgbody );
  $msgbody = nl2br($msgbody);
  $msgbody = after_bbcode($msgbody);
  
  return $msgbody;
}

function render_for_db($msgbody) {

  $msgbody = youtube( $msgbody );
  
  return $msgbody;
}

function render_for_editing($msgbody) {

  // TODO:
  
  return $msgbody;
}
?>