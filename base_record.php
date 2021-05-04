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
  
  public function init($project_type){
    $this->project_type = $project_type;
    $this->dc_formatter = new DublinCoreXML();
    $this->char_handler = new SpecialChars();
  }
  
  public function set_metadata($string){
    $arr = explode("\t", $string);
    $this->title['val'] = $this->char_handler->clean($arr[$this->title['ind']]);
    $this->author['val'] = $this->construct_author($arr);
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
  
  //default: separate fields for author first and last name
  public function construct_dirname(){
    $last = str_replace([" ", "'"], "", trim($this->author['ind']['last']));
    $first = str_replace([" ", "'"], "", trim($this->author['ind']['first']));
    return $last . "_" . $first;
  }
  
  public function construct_description(){
    return $this->pages['val'] . " pages";
  }
  
  //default: separate fields for first and last name
  public function construct_author($arr){
    return $this->char_handler->clean($arr[$this->author['ind']['last']]) . ", " . 
       $this->char_handler->clean($arr[$this->author['ind']['first']]);  
  }
  
  public function construct_subjects($arr){
    $subjects = [];
    for($i = $this->subjects['ind'][0]; $i <= $this->subjects['ind'][1]; $i++ ){
      $subjects[] = $this->char_handler->clean($arr[$i]);
    }
    return $subjects;
  }
  
  //will add empty string if there is no value for a given field
  public function assemble_properties(){
    $string = $this->dc_formatter->title($this->title['val']);
    $string .= $this->dc_formatter->contributor($this->author['val']);
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
    $string .= $this->dc_formatter->advisor($this->advisor['val']);

    return $string;
  }
}
?>