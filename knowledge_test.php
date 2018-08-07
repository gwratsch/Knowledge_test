<?php

function jsRequest(){
    $namerequest='';
    $nameRequestNid=0;
    $organisationNid=0; 
    $nameRequestContent='';
    $result='';

    if(array_key_exists("namerequest", $_POST)){
        $namerequest=$_POST["namerequest"];
        
        $nameRequestNid = $_POST["nameRequestNid"];
        $organisationNid = $_POST["organisationNid"];
    }
    $nameRequestContent = getNameRequestContent($nameRequestNid);
    if($namerequest == "saveInfo"){
        saveInfo();
    }

    if($nameRequestNid>0){
        saveInfo();
        $nameRequestContent = addExistingContent($nameRequestContent,$organisationNid,$nameRequestNid);
    }
    if($namerequest == "popup"){
        $result = getKnoledgeInfo($nameRequestNid);
        if($result == ''){$result = "Geen gegevens gevonden voor nr: ".$nameRequestNid;}
    }else{
        $result = buildHTML($nameRequestContent,$namerequest,$nameRequestNid);
    }
    
    echo $result;
}
function addExistingContent($nameRequestContent,$organisationNid,$nameRequestNid){
    $query = db_select($organisationNid, 'n') ;            
    $query->fields('n', array('subject','body'));
    $query->condition('nid', $nameRequestNid , '=');
    $result = $query->execute();
    $node_list =  $result->fetchAll();
    $list=array();
    foreach ($node_list as $key => $value) {
        $list[$value->subject]=[$value->body];
    }

    $updateNameRequestContent = $nameRequestContent;
    foreach ($nameRequestContent as $key => $value) {
        $word=array();
        $word = $value->word->word;
        if(array_key_exists($word,$list)){
            $updateNameRequestContent[$key]->body = $list[$word][0];
        }
    }
    return $updateNameRequestContent;
}
function getKnoledgeInfo($nameRequestNid){
    $nid = intval($nameRequestNid);
    $nodeContent='No information is found for number : '.$nid;
    if($nid>0){
        $node = node_load($nid);
        $language = key($node->body);
        $nodeContent = $node->body[$language][0]['safe_value'];
    }
    return $nodeContent;
}
function buildHTML($subjectList,$namerequest,$nameRequestNid){
    $value='';
    $result = '<H1>'.$namerequest.'</H1><a href="/node/'.$nameRequestNid.'/edit" target="_blank">Edit</a><form id="formRequest" >';
        $result = $result.'<div id="requestNID" style="display:none">'.$nameRequestNid.'</div><div id="requestName" style="display:none" >'.$namerequest.'</div>';
    $chapterList = array();
    foreach ($subjectList as $key => $value) {
        $body='';
        $nid=0;
        if(array_key_exists("body", $value)){$body=$value->body;}
        $chaptercode = createChapterSection($chapterList,$value->code);
        $nid= $value->word->nid;
        $word = $value->word->word;
        $result = $result.'<div class="requestRow"><div class="chapternumber"><strong>'.$chaptercode.'</strong> : </div> <p nid="'.$nid.'" class="requestRowKey">'.$word.'</p><span class="clickinfo" onclick="getInfo(\'popup\','.$nid.')">Info</span><textarea class="requestRowValue" >'.$body.'</textarea></div>';
    }
    $result = $result.'</form>';
    return $result;
} 
function createChapterSection(&$chapterList,$code){
    if(array_key_exists($code, $chapterList)){
        $chapterList[$code] += 1;
        $changeLowerCode = $chapterList;
        foreach ($changeLowerCode as $key => $value) {
            if($key > $code){
                $chapterList[$key] = 0;
            }
        }
    }else{
        $chapterList[$code] = 1;
    }
    $chaptercode = '';
    for($i=1; $i<=$code; $i++){
        $chapterNumber = (array_key_exists($i, $chapterList)) ? $chapterList[$i] : 1 ;
        if($i == 1){
            $chaptercode .= $chapterNumber;
        }else{
            $chaptercode .= '.'.$chapterNumber;
        }
    }
    return $chaptercode;
}
function getNameRequestContent($nameRequestNid){
    $node_list='';
    $list=array();
    $query = db_select('kennisstructuur', 'n') ;            
    $query->fields('n', array('nameslist'));
    $query->condition('node_id', $nameRequestNid , '=');
    $result = $query->execute();
    $node_list =  $result->fetchAll();
    if(array_key_exists('0', $node_list)){
        $list = json_decode($node_list[0]->nameslist);
    }
    $namelist = array();
    foreach ($list as $key => $value) {
        $namelist[]=$value;
    }

    return $namelist;
}
function saveInfo(){
    $PostContent = $_POST["content"];
    $content = json_decode($PostContent);
    $contentNid = $_POST["contentNid"];
    $organisationNid = $_POST["organisationNid"];

    if(checkRequestNidExists($organisationNid,$contentNid)){
        db_delete($organisationNid)
          ->condition('nid', $contentNid , '=')
          ->execute();
    }
    if(is_array($content)){
        foreach ($content as $key => $value) {
             db_insert($organisationNid)
                  ->fields(array(
                    'nid' => $contentNid,
                    'subject' => strip_tags($value->name),
                    'body'=>strip_tags($value->content)
                  ))
                  ->execute();
        }
    }
    $message = json_encode($content);
    $type = 'saveInfo_content';
    watchdog($type, $message );
}
function checkRequestNidExists($organisationNid,$nameRequestNid){
    $result=false;
    $query = db_select($organisationNid, 'n') ;            
    $query->fields('n', array('nid'));
    $query->condition('nid', $nameRequestNid , '=');
    $result = $query->execute();
    $node_list =  $result->fetchAll();
    if(array_key_exists('0', $node_list)){
        $result= true;
    }
    return $result;
}
function printarray($list,$name){
    echo"<p>".$name."</p><pre>";
    print_r(print_r($list),true);
    echo"</pre>";
}
function displayarray($list,$name){
    $content = "<p>".$name."</p><pre>";
    $content = $content.print_r(print_r($list),true);
    $content = $content."</pre>";
}