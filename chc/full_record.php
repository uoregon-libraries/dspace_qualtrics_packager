<?php

  class FullRecord extends BaseRecord{
    public $advisors;
    public $authors;
    public $abstract;
    public $description;
    
    public function set_metadata($string){
      $arr = parent::set_metadata($string);
      $this->authors['val'] = $this->construct_authors($arr);
      $this->advisors['val'] = $this->construct_advisors($arr);
      $this->abstract['val'] = $this->char_handler->clean($arr[$this->abstract['ind']]);
      //$this->pages['val'] = $arr[$this->pages['ind']];
      //$this->description['val'] = $this->construct_description($arr);
      $this->description['val'] = "";
      //$this->filename['val'] = $arr[$this->filename['ind']];
      $this->orcid['val'] = $arr[$this->orcid['ind']];
      $this->rights['val'] = $arr[$this->rights['ind']];
      $this->embargo['val'] = $this->construct_forever_embargo($arr);
      return $arr;
    }
    
    public function construct_dirname(){
      return str_replace([" ", "'", ","], "", $this->authors['val'][0]);
    }
    
    //chc lists one author, first, last
    public function construct_authors($arr){
      $authors = [];
      for($i = $this->authors['ind'][0]; $i <= $this->authors['ind'][1]; $i+=2){
        $authors[] = $this->char_handler->clean($arr[$i+1]) . ", " . 
          $this->char_handler->clean($arr[$i]);  
      }
      return $authors;
    }
    
    //chc lists multiple advisors first last
    public function construct_advisors($arr){
      $advisors = [];
      for($i = $this->advisors['ind'][0]; $i <= $this->advisors['ind'][1]; $i+=2){
        $advisors[] = $this->char_handler->clean($arr[$i+1]) . ", " . 
          $this->char_handler->clean($arr[$i]); 
      }
      return $advisors; 
    }
        
    public function assemble_properties(){
      $string = $this->dc_formatter->begin_xml();
      $string .= parent::assemble_properties();
      foreach($this->authors['val'] as $author){
        $string .= $this->dc_formatter->contributor($author);
      }
      foreach($this->advisors['val'] as $advisor){
        $string .= $this->dc_formatter->advisor($advisor);
      }
      $string .= $this->dc_formatter->embargo($this->embargo['val']);
      $string .= $this->dc_formatter->orcid($this->orcid['val']);
      $string .= $this->dc_formatter->end_xml();
      return $string;
    }

    //for now, there is only a 2Y embargo offered.
    public function construct_embargo($arr){
      if(strstr($arr[$this->embargo['ind']], 'restrict') !== false)
        return $this->add2Y();
      else return "";
    }
    //adding forever embargo in 2023
    public function construct_forever_embargo($arr){
      if(strstr($arr[$this->embargo['ind']], 'restrict') !== false)
        return "9999";
      else return "";

    }

  }

?>
