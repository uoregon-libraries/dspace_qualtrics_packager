<?php
// call this with the projectname and a list of extensions separated by commas, eg:
// php postprocess.php urs pdf,mp4
// Note: will need to screen out dublin_core.xml if the users include xml files in their uploads.
// 2023 tried to make this to work with new chc workflow, in which the content files are added manually. This can be reverted once the chc input stage is not interrupted by manual file editing.
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

// processing starts here. Modified to enable no content file
if(sizeof($argv) > 2)
  $types = explode(",", $argv[2]);
else
  $types = [];

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
