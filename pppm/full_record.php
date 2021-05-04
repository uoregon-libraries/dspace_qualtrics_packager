<?php

  class FullRecord extends BaseRecord{
    public $advisor;
    public $abstract;
    public $description;
    public $pages;
    public $id;
    public $filename;
    public $issued;
    
    public function set_metadata($string){
      $arr = parent::set_metadata($string);
      $this->author['val'] = $this->char_handler->clean($arr[$this->author['ind']]);
      $this->advisor['val'] = $this->char_handler->clean($arr[$this->advisor['ind']]);
      $this->abstract['val'] = $this->char_handler->clean($arr[$this->abstract['ind']]);
      $this->pages['val'] = trim($arr[$this->pages['ind']]);
      $this->description['val'] = $this->construct_description($arr);
      $this->filename['val'] = $this->construct_filename($arr);
      return $arr;
    }
    
    public function construct_dirname(){
      return str_replace([" ", "'", ","], "", $this->author['val']);
    }

    //somewhate confusing: working filename = <id>_<uploaded filename>
    public function construct_filename($arr){
      return trim($arr[$this->id['ind']]) . "_" . trim($arr[$this->filename['ind']]);
    }
    
    public function assemble_properties(){
      $string = $this->dc_formatter->begin_xml();
      $string .= parent::assemble_properties();
      $string .= $this->dc_formatter->end_xml();
      return $string;
    }
  }

?>