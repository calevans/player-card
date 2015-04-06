<?php
$audio_file = urldecode(filter_input(INPUT_GET,'a',FILTER_SANITIZE_STRING));
$type       = filter_input(INPUT_GET,'t',FILTER_SANITIZE_STRING);

if (empty($audio_file)) {
	die();
}
if ($type!=='audio' and $type!=='video') {
	die();
}

?>
<!DOCTYPE html>
<html>
<body>

<style type="text/css">
<?php echo $type;?> {  
   width:100%; 
   max-width:600px; 
   height:auto; 
}
</style>

<<?php echo $type;?> width="100%" controls>
  <source src="<?php echo $audio_file;?>" type="audio/mp3">
Your browser does not support audio
</video>

</body>
</html>

