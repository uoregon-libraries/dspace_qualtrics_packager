<?php

  $projname = $argv[1];
  
  include "base_record.php";
  include "specialchars.php";
  include "dublin_core_xml.php";
  include "{$projname}/full_record.php";
  
  $curdir = getcwd();
  $projpath = "{$curdir}/{$projname}/";
  $workpath = "{$projpath}work/";
  $filepath = "{$projpath}/files/";

  function process_everything(){
    $lines = File("{$GLOBALS['projpath']}data.csv");
    foreach($lines as $line){
      $record = create_record($line);
      $recordpath = create_dir($record->construct_dirname());
      write_xml($record, $recordpath);
      copy_content_file($record, $recordpath);
    }
  }
  
  function create_record($line){
    $record = new FullRecord();
    $record->init($GLOBALS['projname']);
    $record->get_configs();
    $record->set_metadata($line);
    return $record;
  }
  
  function create_dir($dirname){
    $recorddir = $GLOBALS['workpath'] . $dirname;
    mkdir($recorddir);
    return $recorddir . "/";
  }
  
  function write_xml($record, $recordpath){
    $content = $record->assemble_properties();
    $fp = fopen($recordpath . "dublin_core.xml",'w');
    fwrite($fp, $content);
    fclose($fp);
  }

  function copy_content_file($record, $recordpath){
    $newfilename = str_replace(" ", "_", $record->filename['val']);
    $oldfilename = str_replace(" ", "%20", $record->filename['val']);
    copy($GLOBALS['filepath'] . $oldfilename, $recordpath . $newfilename);
  }

  process_everything();
?>