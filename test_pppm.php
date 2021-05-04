<?php
  include "base_record.php";
  include "pppm/full_record.php";
  include "specialchars.php";
  include "dublin_core_xml.php";

  $record = new FullRecord();
  $record->init('pppm');
  $record->get_configs();
  $lines = File("pppm/data.csv");
  $arr = $record->set_metadata($lines[1]);
  var_dump($record);
  $str = $record->assemble_properties();
  var_dump($str);
?>