<?php

  class FullRecord extends BaseRecord{
    public $advisors;
    public $authors;
    public $abstract;
    public $description;
    public $abstracts;
    
    public function set_metadata($string){
      $arr = parent::set_metadata($string);
      $this->authors['val'] = $this->construct_authors($arr);
      $this->advisors['val'] = $this->construct_advisors($arr);
      //$this->issued['val'] = $arr[$this->issued['ind']];
      $this->abstract['val'] = $this->char_handler->clean($arr[$this->abstract['ind']]);
      //$this->abstract['val'] = $this->get_abstract($arr);
      //$this->pages['val'] = $arr[$this->pages['ind']];
      //$this->description['val'] = $this->construct_description($arr);
      $this->description['val'] = "";
      //$this->filename['val'] = $arr[$this->filename['ind']];
      $this->orcid['val'] = $arr[$this->orcid['ind']];
      $this->rights['val'] = $this->construct_rights($arr);
      $this->embargo['val'] = $this->construct_embargo($arr);
      return $arr;
    }
    
    public function construct_dirname(){
      return preg_replace('/[^A-Za-z]/','', $this->authors['val'][0]);
    }
    
    public function construct_rights($arr){
      $rights = $arr[$this->rights['ind']];
      if($rights == "")
        return "Creative Commons BY-NC-ND 4.0-US";
      return $rights;
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

    //2Y embargo.
    public function construct_embargo($arr){
      if(strstr($arr[$this->embargo['ind']], 'restrict') !== false)
        if(strstr($arr[$this->embargo['ind']], 'permanently') !== false)
          return "2145-01-01";
        else
          return $this->add2Y();
      return "";
    }
    //adding forever embargo in 2023
    public function construct_forever_embargo($arr){
      if(strstr($arr[$this->embargo['ind']], 'openly') === false)
        return "2145-01-01";
      return "";
    }

    public function permission_granted($arr){
      if (trim($arr[$this->permission['ind']]) != 'Yes' ||
        strstr($arr[$this->embargo['ind']], "concerns") !== false){
          return false;
        }
      return true;
    }

    //ok this is hard coded. will add term to the config, and make cutoff a settable variable
    /*
    public function materials_unavailable($arr){
      $terms = array("Winter"=>"1", "Spring"=>"2","Summer"=>"3", "Fall"=>"4");
      $cutoff = 20242;
      $term = $arr[4] . $terms[$arr[3]];
      if((int)$term > $cutoff){
        echo $term;
        return true;
      }
      return false;
    }
    */
    //adding this to deal with separate abstracts source 2024
    public function get_abstract($arr){
      $id = $arr[0];
      $str = $GLOBALS['abstracts'][trim($id)];
      return $this->char_handler->clean($str);
    }
  }

?>
