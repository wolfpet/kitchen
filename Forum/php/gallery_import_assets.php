<?php
require_once('head_inc.php');
require_once('html_head_inc.php');
require_once('mysql_log.php');

//clear assets table

$clearQuery = 'delete from confa_assets;';
$clearresult = mysql_query($clearQuery);

$i=0; //post counter
$imageUrls[]= null; //well formed image URLs after varrious cleanup procedures

$importquery = "SELECT * from confa_posts;";
$importresult = mysql_query($importquery);
if ($importresult) 
{
    while ($importrow = mysql_fetch_assoc($importresult))
    {
        $importrow = mysql_fetch_assoc($importresult);
        $importpost=$importrow['body'];
        $import_msg_id = $importrow['id'];
        $import_user_id = $importrow['author'];
        $i++;
        //analyze the post body and detect image URLs
        $detectedImageUrls = detect_picture_urls($importpost);
        for($j=1; $j<count($detectedImageUrls); $j++)
        {
            array_push($imageUrls, $detectedImageUrls[$j]);
            //print($detectedImageUrls[$j]. "- <br><br>");
            add_picture_asset($detectedImageUrls[$j], $import_user_id,  $import_msg_id);
        }
    }
}
print("<hr>" . $i . " posts have been analyzed<br>");
print( count($imageUrls) . " images found");

?>