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
      $this->filename['val'] = $arr[$this->filename['ind']];
      $this->orcid['val'] = $arr[$this->orcid['ind']];
      $this->rights['val'] = $arr[$this->rights['ind']];
      return $arr;
    }

    public function construct_dirname(){
      if(count($this->authors['val']) == 1)
        return str_replace([" ", "'", ".", ","], "", $this->authors['val'][0]);
      $lastnames = [];
      foreach($this->authors['val'] as $author){
        if (trim($author) == "")
          continue;
        $lastname = explode(",", $author)[0];
        $lastnames[] = str_replace([" ", "'", "."], "", $lastname);
      }
      return implode("_", $lastnames);
    }

    public function construct_authors($arr){
      $authors = [];
      for($i = $this->authors['ind'][0]; $i <= $this->authors['ind'][1]; $i+=2){
        if(trim($arr[$i]) == "")
          continue;
        $authors[] = $this->char_handler->clean($arr[$i]) . ", " . 
          $this->char_handler->clean($arr[$i+1]);  
      }
      if(count($authors) == 0)
        throw new Exception("Record found with no authors.");
      return $authors;
    }

    public function construct_advisors($arr){
      $advisors = [];
      for($i = $this->advisors['ind'][0]; $i <= $this->advisors['ind'][1]; $i+=2){
        if(trim($arr[$i]) == "")
          continue;
        $advisors[] = $this->char_handler->clean($arr[$i]) . ", " . 
          $this->char_handler->clean($arr[$i+1]); 
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
      $string .= $this->dc_formatter->orcid($this->orcid['val']);
      $string .= $this->dc_formatter->format($this->format['val']);
      $string .= $this->dc_formatter->end_xml();
      return $string;
    }
  }
?>