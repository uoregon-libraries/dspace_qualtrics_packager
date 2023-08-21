<?php

class BaseRecord{

  public $project_type;
  public $title;
  public $subjects;
  public $issued;
  public $filename;
  public $type;
  public $publisher;
  public $dc_formatter;
  public $char_handler;
  public $rights;
  public $permission;

  public function init($project_type){
    $this->project_type = $project_type;
    $this->dc_formatter = new DublinCoreXML();
    $this->char_handler = new SpecialChars();
  }

  //it is not necessary to set the val for any field that has a val assigned in the config
  public function set_metadata($string){
    $arr = explode("\t", $string);
    if (trim($arr[$this->permission['ind']]) != 'Yes')
      throw new Exception("Permission to publish is not granted.");
    $this->title['val'] = $this->char_handler->clean($arr[$this->title['ind']]);
    $this->subjects['val'] = $this->construct_subjects($arr);
    return $arr;
  }

  public function get_configs(){
    $str = file_get_contents($this->project_type . "/config");
    $configs = json_decode($str);
    foreach($configs as $key=>$val){
      foreach($val as $k=>$v){
        $this->$key[$k] = $v;
      }
    }
  }

  public function construct_description(){
    return $this->pages['val'] . " pages";
  }

  public function construct_subjects($arr){
    $subjects = [];
    for($i = $this->subjects['ind'][0]; $i <= $this->subjects['ind'][1]; $i++ ){
      $subjects[] = $this->char_handler->clean($arr[$i]);
    }
    return $subjects;
  }


  public function add2Y(){
    $d = new DateTime();
    $diff = new DateInterval('P2Y');
    $d2 = $d->add($diff);
    return $d2->format('Y-m-d');
  }

  //will add empty string if there is no value for a given field
  public function assemble_properties(){
    $string = $this->dc_formatter->title($this->title['val']);
    $string .= $this->dc_formatter->issuedate($this->issued['val']);
    foreach($this->subjects['val'] as $subject){ 
      $string .= $this->dc_formatter->subject($subject);
    }
    $string .= $this->dc_formatter->description($this->description['val']);
    $string .= $this->dc_formatter->publisher($this->publisher['val']);
    $string .= $this->dc_formatter->type($this->type['val']);
    $string .= $this->dc_formatter->rights($this->rights['val']);
    $string .= $this->dc_formatter->lang($this->lang['val']);
    $string .= $this->dc_formatter->abstract($this->abstract['val']);

    return $string;
  }
}

?>
