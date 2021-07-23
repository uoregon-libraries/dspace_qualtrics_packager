<?php
  //requires one argument, the project id, i.e. chc, pppm, or urs
  include "base_record.php";
  include "{$argv[1]}/full_record.php";
  include "specialchars.php";
  include "dublin_core_xml.php";

  $record = new FullRecord();
  $record->init($argv[1]);
  $record->get_configs();
  $lines = File("{$argv[1]}/data.csv");
  $arr = $record->set_metadata($lines[1]);
  var_dump($record);
  $str = $record->assemble_properties();
  var_dump($str);
?>