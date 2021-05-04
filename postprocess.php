<?php
// call this with the projectname and a list of extensions separated by commas, eg:
// php postprocess.php urs pdf,mp4
// Possible future todo: will need to screen out the metadata file if the users include xml files in their uploads.

$APPDIR = getcwd();
$PROJPATH = "{$APPDIR}/{$argv[1]}";
$WORKDIR = "{$PROJPATH}/work";

function write_list($dirpath, $types){
  $fp = fopen($dirpath . "/contents", 'w');
  $string = "";
  $iterator = new DirectoryIterator($dirpath);
  foreach($iterator as $fileinfo ){
    $filename = $fileinfo->getFilename();
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if (in_array($ext, $types))
      $string .= $filename ."\tbundle:ORIGINAL\n";
  }
  $string .= "license.txt" . "\t" . "bundle:LICENSE\n";
  fwrite($fp, $string);
  fclose($fp);
}
if(sizeof($argv) != 3)
  exit("wrong number of args");

$types = explode(",", $argv[2]);
$iterator = new DirectoryIterator($WORKDIR);
foreach($iterator as $dir){
  if($dir->isDot()) continue;
  if($dir->isDir()){
    $fullpath = $dir->getPath() . "/" . $dir->getFilename();
    write_list($fullpath, $types);
    copy("{$APPDIR}/license.txt", "{$fullpath}/license.txt");
  }
}
?>