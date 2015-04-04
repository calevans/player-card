<?php
$audio_file = urldecode($_GET['a']);
if (empty($audio_file)) {
die();
}

?>
<!DOCTYPE html>
<html>
<body>

<style type="text/css">
video {  
   width:100%; 
   max-width:600px; 
   height:auto; 
}
</style>

<audio width="100%" controls>
  <source src="<?php echo $audio_file;?>" type="audio/mp3">
Your browser does not support audio
</video>

</body>
</html>

