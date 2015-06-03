<?php
/*$Id: send_inc.php 830 2012-11-08 14:44:45Z dmitriy $*/

    if (strlen($err) != 0) {
        print('<B><font color="red">' . $err . '</font></B><BR>');
    }
    if (is_null($to)) {
      $to = '';
    }
?>
<script language="JavaScript" src="js/translit.js"></script>
<script language="JavaScript" src="js/func.js"></script>
<script language="Javascript">
function bbcode_on() {
    document.getElementById('translit_help').style.display='none';
    if (document.getElementById('bbcode_help').style.display != 'block') {
        document.getElementById('bbcode_help').style.display='block';
    } else {
        document.getElementById('bbcode_help').style.display='none';
    }
}
</script>

<form action="<?php print($root_dir . $page_pm); ?>" method="post" id="msgform" name="msgform">
<input type="hidden" name="re" id="re" value="<?php print($re); ?>"/>

<table width="100%">
<tr><td width="60%" valign="top">
<table>
<tr>
<td nowrap>From:</td><td><B><?php print($user); ?></B></td>
<td colspan="2" />
</tr>
<tr>
<td nowrap>To:</td><td><B><input type="text" name="to" id="to" value="<?php print($to); ?>"/></B></td>
<td colspan="2"/>
</tr>

<?php

    if (!isset($_SERVER['HTTP_USER_AGENT']) || FALSE === strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mini' ) ) {
        $keyboard = true;
    } else {
        $keyboard = false;
    }
?>
<tr>
<td>Subject:</td>
<td colspan="2"><input type="text" <?php if ($keyboard) { ?> onfocus="javascript:RegisterField(this, true, false);" onkeypress="javascript:translate2(event);" onkeydown="javascript:text_OnKeydown(event);" <?php } ?> name="subj" id="subj" tabindex="1" value='<?php /*print(htmlentities($subj, HTML_ENTITIES,'UTF-8'));*/ print($subj); ?>' maxlength="128" size="54"/></td>
</tr>
<!--<tr>
<td colspan="3" align="right">
  <a href="#" onclick="javascript:insertTag('body', 1);return false;">[url=]<font color="gray">Title</font>[/url]</a>&nbsp;
  <a href="#" onclick="javascript:insertTag('body', 2);return false;">[img=]</a>
</td>
</tr>
-->
<tr>
<td colspan="2" nowrap>
<!--<input type="checkbox" name="chkRussian" id="chkRussian" ><label for="chkRussian" id="lang">Press [ESC] for Russian</label>-->
<?php
    if ($keyboard) {
?>
<div id="ruschars" style="display:none;"><a href="javascript:toggleCharset();">Russian keyboard</a></div>
<div id="latchars" style="display:block;"><a href="javascript:toggleCharset();">Latin keyboard</a></div>
<?php
    }
?>
</td>
<td align="right">
  <a href="#" onclick="javascript:insertTag('body', 1);return false;">[url=]<font color="gray">Title</font>[/url]</a>&nbsp;
  <a href="#" onclick="javascript:insertTag('body', 2);return false;">[img=]</a>
</td>
</tr>
<tr>
<td colspan="3" width="100%">
<textarea id="body" name="body" <?php if ($keyboard) { ?> onfocus="javascript:RegisterField(this, true, false);" onkeypress="javascript:translate2(event);" onkeydown="javascript:text_OnKeydown(event);" <?php } ?> cols="90" tabindex="2" rows="8"><?php

    print($body);
 //print(nl2br(htmlentities($body, HTML_ENTITIES,'UTF-8')));
?></textarea>
</td>
</tr>
<tr>
<td colspan="3"><input name="preview" id="preview" type="checkbox" readonly value="off"/> Preview first</td>
</td></tr>
<tr>
<td colspan="3"><input tabindex="3" value="Send!" type="submit"></td>
</tr>
</tbody></table>
</td><td width="40%" valign="top">

<table width="100%">
<tbody>
<tr>
<td align="left" valign="top" width="100%">
<!--<a href="javascript:translit_on()"><U>Translit help</U><a>&nbsp;&nbsp;--><a href="javascript:bbcode_on();"><U>BBCode help</U></a></td></tr>
<tr><td align="left" valign="top" width="100%">
<div id="bbcode_help"><table border="1">
<tbody><tr><td>[b]<font color="gray">bolded text</font>[/b]</td><td><strong>bolded text</strong></td></tr>
<tr><td>[i]<font color="gray">italicized text</font>[/i]</td><td><em>italicized text</em></td></tr>
<tr><td>[u]<font color="gray">underlined text</font>[/u]</td><td><span style="text-decoration: underline;">underlined text</span></td></tr>
<tr><td>[s]<font color="gray">strikethrough text[</font>/s]</td><td><del>strikethrough text</del></td></tr>
<tr><td>[url]<font color="gray">http://example.org</font>[/url]</td><td><a target="_blank" href="http://example.org/">http://example.org</a></td></tr>
<tr><td>[url=<font color="gray">http://example.com]Example</font>[/url]</td><td><a target="_blank" href="http://example.com/">Example</a></td></tr>
<tr><td>[quote]<font color="gray">quoted text</font>[/quote]</td><td><q>quoted text</q></td></tr>
<tr><td>[code]<font color="gray">monospaced text[</font>/code]</td><td><code>monospaced text</code></td></tr>
<tr><td>[color=red]<font color="gray">Red Text</font>[/color]</td><td><span style="color: red;">Red Text</span></td></tr>
<tr><td>[color=#FF0000]<font color="gray">Red Text</font>[/color]</td><td><span style="color: rgb(255, 0, 0);">Red Text</span></td></tr>
<tr><td>[color=FF0000]<font color="gray">Red Text</font>[/color]</td><td><span style="color: rgb(255, 0, 0);">Red Text</span></td></tr>
<tr><td>[size=15]<font color="gray">Large Text</font>[/size]</td><td><span style="font-size: 15pt;">Large Text</span></td></tr>
<tr><td>[img=<font color="gray">http://<?php print( $host); ?>/images/Tip-Hat.gif</font>]</td><td><img src="http://<?php print( $host); ?>/images/Tip-Hat.gif"></td></tr>
</tbody></table>
</div>
  
<div id="translit_help"><font color="gray"><table><tbody><tr><td>А=<font color="gray">A</font></td><td>Б=<font color="gray">B</font></td><td>В=<font color="gray">V</font></td><td>Г=<font color="gray">G</font></td><td>Д=<font color="gray">D</font></td><td>Е=<font color="gray">E</font></td><td>Ё=<font color="gray">JO</font></td><td>Ж=<font color="gray">ZH</font></td><td>З=<font color="gray">Z</font></td><td>И=<font color="gray">I</font></td></tr>
               <tr><td>Й=<font color="gray">J</font></td><td>К=<font color="gray">K</font></td><td>Л=<font color="gray">L</font></td><td>М=<font color="gray">M</font></td><td>Н=<font color="gray">N</font></td><td>О=<font color="gray">O</font></td><td>П=<font color="gray">P</font></td><td>Р=<font color="gray">R</font></td><td>С=<font color="gray">S</font></td><td>Т=<font color="gray">T</font></td></tr>
               <tr><td>У=<font color="gray">U</font></td><td>Ф=<font color="gray">F</font></td><td>Х=<font color="gray">H</font></td><td>Ц=<font color="gray">C</font></td><td>Ч=<font color="gray">CH</font></td><td>Ш=<font color="gray">SH</font></td><td>Щ=<font color="gray">XH</font></td><td>Ъ=<font color="gray">##</font></td><td>Ы=<font color="gray">Y</font></td><td>Ь=<font color="gray">''</font></td></tr>
			   <tr><td>Э=<font color="gray">W</font></td><td>Ю=<font color="gray">JU</font></td><td>Я=<font color="gray">JA</font></td></tr>
			   <tr><td>а=<font color="gray">a</font></td><td>б=<font color="gray">b</font></td><td>в=<font color="gray">v</font></td><td>г=<font color="gray">g</font></td><td>д=<font color="gray">d</font></td><td>е=<font color="gray">e</font></td><td>ё=<font color="gray">jo</font></td><td>ж=<font color="gray">zh</font></td><td>з=<font color="gray">z</font></td><td>и=<font color="gray">i</font></td></tr>
			   <tr><td>й=<font color="gray">j</font></td><td>к=<font color="gray">k</font></td><td>л=<font color="gray">l</font></td><td>м=<font color="gray">m</font></td><td>н=<font color="gray">n</font></td><td>о=<font color="gray">o</font></td><td>п=<font color="gray">p</font></td><td>р=<font color="gray">r</font></td><td>с=<font color="gray">s</font></td><td>т=<font color="gray">t</font></td></tr>
			   <tr><td>у=<font color="gray">u</font></td><td>ф=<font color="gray">f</font></td><td>х=<font color="gray">h</font></td><td>ц=<font color="gray">c</font></td><td>ч=<font color="gray">ch</font></td><td>ш=<font color="gray">sh</font></td><td>щ=<font color="gray">xh</font></td><td>ъ=<font color="gray">#</font></td><td>ы=<font color="gray">y</font></td><td>ь=<font color="gray">'</font></td></tr>
			   <tr><td>э=<font color="gray">w</font></td><td>ю=<font color="gray">ju</font></td><td>я=<font color="gray">ja</font></td></tr>
		</tbody></table></div><!--</div>-->
	</td>
<!--</tr>
<tr> -->
</tr>

</tbody></table>

</td>
</tr>
</table>
</form>
<?php
//print($log);
?>
</body></html>

</html>
