
<?php
 use Aws\S3\S3Client;
 use Aws\Common\Aws;

require_once('head_inc.php');

$result = '';
$randPicName = generateRandomString();

//file browser or drag and drop?
$output_dir = $imageGalleryDumpFolder;

if(isset($_FILES["file"]))
{
 //Filter the file types , if you want.
 if ($_FILES["file"]["error"] > 0)
 {
  echo "Error: " . $_FILES["file"]["error"] . "";
 }
 else
 {
  move_uploaded_file($_FILES["file"]["tmp_name"],$output_dir. $randPicName . $_FILES["file"]["name"]);
  //echo "Uploaded File :".$_FILES["file"]["name"];
  $result = "http://" . $host . $root_dir . $imageGalleryDumpFolder . $randPicName. $_FILES["file"]["name"];
 }
}


if($imageGallery == 'amazon')
{
//upload to s3

 require 's3/vendor/autoload.php';

 // Create the AWS service builder, providing the path to the config file
 $aws = Aws::factory('gallery_s3config.php');
 $s3Client = $aws->get('s3');


 $bucket = $imageGalleryBucket;
 $keyname = $randPicName . $_FILES["file"]["name"];
 // $filepath should be absolute path to a file on disk
 $filepath = realpath ($output_dir. $randPicName. $_FILES["file"]["name"]);
 //echo $filepath;
 // Upload a file.
 $result = $s3Client->putObject(array(
    'Bucket'       => $bucket,
    'Key'          => $keyname,
    'SourceFile'   => $filepath,
    'ContentType'  => 'text/plain',
    'ACL'          => 'public-read',
    'StorageClass' => 'REDUCED_REDUNDANCY',
    'Metadata'     => array(    
    'param1' => 'value 1',
    'param2' => 'value 2'
    )
 ));


 //delete the temp file
 unlink($filepath);

 if($_POST['fileBrowser'] == 'yes')
 {
    //old fashion form submit
    header('Location: gallery_append_url.php?image='. $result['ObjectURL']); 
 }
 else
 {
    //ajax request
    echo $result['ObjectURL'];
 }

}

if($imageGallery == 'local')
{

 if($_POST['fileBrowser'] == 'yes')
 {
     //old fashion form submit
     header('Location: gallery_append_url.php?image='. $result);
 }
  else
 {
    //ajax request
    echo $result;
 }
}


function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
 }
return $randomString;
}

?>
