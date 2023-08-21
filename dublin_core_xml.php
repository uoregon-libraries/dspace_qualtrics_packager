<?php
  class DublinCoreXML{

  function title($title){
     if($title != "")
      return '<dcvalue element="title" qualifier="none">' . $title . '</dcvalue>';
    return "";
  }

  function contributor($author){
    if($author != "") 
      return '<dcvalue element="contributor" qualifier="author">' . $author . '</dcvalue>';
    return "";
  }

  function advisor($advisor){
    if($advisor != "")
      return '<dcvalue element="contributor" qualifier="advisor">' . $advisor . '</dcvalue>';
    return "";
  }
  function description($descrip){
    if($descrip != "")
      return '<dcvalue element="description">' . $descrip . '</dcvalue>';
    return "";
  }

  function identifier($identi){
    if($identi != "")
      return '<dcvalue element="identifier">' . $identi . '</dcvalue>';
    return "";
  }

  function coverage($cov){
    if($cov != "")
      return '<dcvalue element="coverage" qualifier="spatial" language="en_US">' . $cov . '</dcvalue>';
    return "";
  }

  function publisher($pub){
    if($pub != "")
      return '<dcvalue element="publisher" qualifier="none">' . $pub .'</dcvalue>';
    return "";
  }

  function issuedate($date){
    if($date != "")
      return '<dcvalue element="date" qualifier="issued">' . $date .'</dcvalue>';
    return "";
  }

  function submitted($date){
    if($date != "")
      return '<dcvalue element="date" qualifier="submitted">' . $date . '</dcvalue>';
    return "";
  }

  function published($date){
    if($date != "")
      return '<dcvalue element="date" qualifier="published">' . $date . '</dcvalue>';
    return "";
  }

  function subject($subject){
    if($subject != "")
      return '<dcvalue element="subject" qualifier="none" language="en_US">'. $subject .'</dcvalue>';
    return "";
  }

  function type($type){
    if($type != "")
      return '<dcvalue element="type" qualifier="none">'. $type . '</dcvalue>';
    return "";
  }

  function lang($lang){
    if($lang != "")
      return '<dcvalue element="language" qualifier="iso">' . $lang . '</dcvalue>';
    return "";
  }

  function rights($rights){
    if($rights != "")
      return '<dcvalue element="rights" qualifier="none">' . $rights . '</dcvalue>';
    return "";
  }

  function source($source){
    if($source != "")
      return '<dcvalue element="source" qualifier="none">'.$source .'</dcvalue>';
    return "";
  }

  function abstract($abstract){
    if($abstract != "")
      return '<dcvalue element="description" qualifier="abstract" language="en_US">' . $abstract .'</dcvalue>';
    return "";
  }

  function ispartofseries($series){
    if($series != "")
      return '<dcvalue element="relation" qualifier="ispartofseries">' . $series .'</dcvalue>';
    return "";
  }

  function orcid($orcid){
    if($orcid != "")
      return '<dcvalue element="identifier" qualifier="orcid">' . $orcid . '</dcvalue>';
    return "";
  }

  function sponsor($sponsor){
    if($sponsor != "")
      return '<dcvalue element="description" qualifier="sponsorship">' . $sponsor . '</dcvalue>';
    else return "";
  }

  function format($format){
    if($format != "")
      return '<dcvalue element="format" qualifier="mimetype">' . $format . '</dcvalue>';
    return "";
  }

  function embargo($date){
    if($date != "")
      return '<dcvalue element="description" qualifier="embargo" language="en_US">' . $date . '</dcvalue>';
    return "";
  }

  function begin_xml(){
    return '<?xml version="1.0" ?><dublin_core schema="dc">';
  }
  function end_xml(){
    return '</dublin_core>';
  }

}
?>
