<?php
/*$Id: search.php 841 2012-11-22 02:17:40Z dmitriy $*/

require_once('head_inc.php');
    $title = 'Search';
require_once('html_head_inc.php');
require_once('custom_colors_inc.php'); 
?>

<base target="contents">
</head>
<body>
<H3><?=$title.(is_null($mode) ? '' : (' ' . $mode))?></H3>
<?php

    if (strlen($err) != 0) {
        print('<B><font color="red">' . $err . '</font></B><BR>');
    }
?>
<form action="<?php print($root_dir . $page_do_search); ?>" method="post" target="contents">
<?php 
if (!is_null($mode)) { ?>
<input type="hidden" name="mode" id="mode" value="<?=$mode?>"/><?php 
}?>
<table >
<tr>
<td>Author:</td><td><input type="text" id="author" name="author" value="<?php print($author); ?>" size="32" maxlength="64"/></td>
<td/>
</tr>
<tr>
<td nowrap>Text:</td><td><input type="text" id="text" name="text" /></td>
<td/>
</tr>
<tr>
<td>Search in:</td>
<td>
<select name="searchin">
  <option value="1">Body and Subject</option>
  <option value="2">Body</option>
  <option value="3">Subject</option>
</select></td>
<td/>
</tr>
<tr>
<td>Liked by:</td>
<td>
<select name="likedby">
  <option value="1">Me</option>
  <option value="2">Anyone</option>
</select>
at least <input type="text" size="2" name="howmanylikes"/> times
</td>
<td/>
</tr>
<tr>
<td>
Date from: 
</td>
<td>
<select name="fromday">
<option value="0"> </option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
<option value="13">13</option>
<option value="14">14</option>
<option value="15">15</option>
<option value="16">16</option>
<option value="17">17</option>
<option value="18">18</option>
<option value="19">19</option>
<option value="20">20</option>
<option value="21">21</option>
<option value="22">22</option>
<option value="23">23</option>
<option value="24">24</option>
<option value="25">25</option>
<option value="26">26</option>
<option value="27">27</option>
<option value="28">28</option>
<option value="29">29</option>
<option value="30">30</option>
<option value="31">31</option>
</select>
<select name="frommonth">
<option value="0"> </option>
<option value="1">January</option>
<option value="2">February</option>
<option value="3">March</option>
<option value="4">April</option>
<option value="5">May</option>
<option value="6">June</option>
<option value="7">July</option>
<option value="8">August</option>
<option value="9">September</option>
<option value="10">October</option>
<option value="11">November</option>
<option value="12">December</option>
</select>
<select name="fromyear">
<option value="0"> </option>
<option value="2009">2009</option>
<option value="2010">2010</option>
<option value="2011">2011</option>
<option value="2012">2012</option>
<option value="2013">2013</option>
<option value="2014">2014</option>
<option value="2015">2015</option>
</select>
</td>
<td/>
</tr>
<tr>
<td>
Date To:</td>
<td>
<select name="today">
<option value="0"> </option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
<option value="13">13</option>
<option value="14">14</option>
<option value="15">15</option>
<option value="16">16</option>
<option value="17">17</option>
<option value="18">18</option>
<option value="19">19</option>
<option value="20">20</option>
<option value="21">21</option>
<option value="22">22</option>
<option value="23">23</option>
<option value="24">24</option>
<option value="25">25</option>
<option value="26">26</option>
<option value="27">27</option>
<option value="28">28</option>
<option value="29">29</option>
<option value="30">30</option>
<option value="31">31</option>
</select>
<select name="tomonth">
<option value="0"> </option>
<option value="1">January</option>
<option value="2">February</option>
<option value="3">March</option>
<option value="4">April</option>
<option value="5">May</option>
<option value="6">June</option>
<option value="7">July</option>
<option value="8">August</option>
<option value="9">September</option>
<option value="10">October</option>
<option value="11">November</option>
<option value="12">December</option>
</select>
<select name="toyear">
<option value="0"> </option>
<option value="2009">2009</option>
<option value="2010">2010</option>
<option value="2011">2011</option>
<option value="2012">2012</option>
<option value="2013">2013</option>
<option value="2014">2014</option>
<option value="2015">2015</option>
</select>
</td>
<td/>
</tr>
<tr>
<td colspan="3"><input tabindex="3" value="Search" type="submit"></td>
</tr>
</table>

</form>
</body>
</html>
<?php

require_once('tail_inc.php');

?>

