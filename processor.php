<?php

  $projname = $argv[1];
  $datafile = $argv[2];

  include "base_record.php";
  include "specialchars.php";
  include "dublin_core_xml.php";
  include "{$projname}/full_record.php";

  $curdir = getcwd();
  $projpath = "{$curdir}/{$projname}/";
  $workpath = "{$projpath}work/";
  $filepath = "{$projpath}/files/";

  function process_everything(){
    $lines = File("{$GLOBALS['projpath']}{$GLOBALS['datafile']}");
    foreach($lines as $line){
      try{
        $record = create_record($line);
        $recordpath = create_dir($record->construct_dirname());
        write_xml($record, $recordpath);
        copy_content_file($record, $recordpath);
      }
      catch(Exception $e){
        echo $e->getMessage() . "\n";
      }
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
    echo "making {$dirname}\n";
    $recorddir = $GLOBALS['workpath'] . $dirname;
    if(is_dir($recorddir))
      throw new Exception("Possible duplicate: {$dirname}");
    mkdir($recorddir);
    return $recorddir . "/";
  }

  function write_xml($record, $recordpath){
    $content = $record->assemble_properties();
    $fp = fopen($recordpath . "dublin_core.xml",'w');
    fwrite($fp, $content);
    fclose($fp);
  }
  // 2023 this may need tweaking
  function copy_content_file($record, $recordpath){
    if(property_exists($record, "filename")===false)
      return;
    $newfilename = str_replace(" ", "_", $record->filename['val']);
    copy($GLOBALS['filepath'] . $record->filename['val'], $recordpath . $newfilename);
  }

  process_everything();
?>
