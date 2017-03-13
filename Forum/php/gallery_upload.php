
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
  $dumpFile = $output_dir. $randPicName . $_FILES["file"]["name"];
  move_uploaded_file($_FILES["file"]["tmp_name"], $dumpFile);
  //resize to 1200px max
  $img = resize_image($dumpFile, 1200, 1200);
  $ext = pathinfo($dumpFile, PATHINFO_EXTENSION);
  if($ext == 'jpg'){ imagejpeg($img, $dumpFile);}
  //if($ext == 'png'){ imagepng($img, $dumpFile);} //commented for now. the library seems to be corrupting the PNG
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

function resize_image($file, $w, $h, $crop=FALSE) {
    list($width, $height) = getimagesize($file);
    $r = $width / $height;
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width-($width*abs($r-$w/$h)));
        } else {
            $height = ceil($height-($height*abs($r-$w/$h)));
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }
    $src = imagecreatefromjpeg($file);
    $dst = imagecreatetruecolor($newwidth, $newheight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    return $dst;
}
?>
