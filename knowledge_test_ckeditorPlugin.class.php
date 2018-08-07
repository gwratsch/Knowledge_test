<?php

/**
 *
 * @author Gerd Ratsch
 */
class knowledge_test_ckeditorPlugin extends ckeditor_plugin_ckeditorPlugin{
    public function __construct() {
        parent::__construct();
    }
}
class h extends ckeditor_plugin_ckeditorPlugin{
    public function __construct() {
        parent::__construct();
        $this->startString = '<h';
        $this->endString = '</h';
        $this->label = 'kennisstructure';
        $this->command = 'H';
        $this->icon = '';
    }
    public function pluginAction(&$contentList, &$node) {
        parent::pluginAction($contentList, $node);
        if($this->validationcheck == TRUE){
            $jsonString = $this->BildJsonString();
            $this->saveJsonString($node->nid,$jsonString);
        }
    }
    public function stringReplace(&$body){}
    public function getContent(&$body ){
        foreach ($this->contentList as $rowkey => $itemInfo) {
            if(array_key_exists('startpos', $itemInfo)){
                $startCodePosition = $itemInfo['startpos'] + strlen($itemInfo['startString']);
                $this->contentList[$rowkey]['code'] = substr($body, $startCodePosition , 1);
                $startWordPosition = strpos($body, '>',$itemInfo['startpos'] ) + 1;
                $length = $itemInfo['endpos'] - $startWordPosition;
                $content = trim(substr($body, $startWordPosition , $length));
                $this->validationcheck = TRUE;
                $this->checkWordLink($content, $rowkey); 
            }
        }
    }

    public function createContentList($list, $body){
        $lastAddedString='';
        $row=0;
        foreach ($list as $key => $value) {
            if($value == $this->startString){
                $this->contentList[$row]['startString'] = $value;
                $this->contentList[$row]['startpos'] = $key;
            }
            if($value == $this->endString){
                $this->contentList[$row]['endString'] = $value;
                $this->contentList[$row]['endpos'] = $key;
                $row += 1;
            }
        }
        
    }
    function checkWordLink($word, $rowkey){
        $nid=0;
        $firstString = 'href="/node/';
        $startString = '<a';
        $endString =  '">';
        $word = str_replace('&nbsp;', '', $word);
        $stringReplaceValues = array('</a>');
        $word= trim($word);
        if(strpos($word, $firstString)>0){
            $start = strpos($word, $firstString)+strlen($firstString);
            $startstringpos = strpos($word, $startString);
            $end = strpos($word, $endString);
            $stringReplaceValues[] = substr($word, $startstringpos,$end + strlen($endString) - $startstringpos);
            $nid = substr($word, $start,$end-$start );
            foreach ($stringReplaceValues as $key => $removeString) {
                $word = str_replace($removeString, '', $word);
            }
        }
        $word = trim($word);
        $this->contentList[$rowkey]['nid'] = $nid;
        $this->contentList[$rowkey]['content'] = $word;
    }
    function BildJsonString(){
        $arrayList = array();
        foreach ($this->contentList as $key => $value) {
            $code = $value['code'];
            $nid = ($value['nid']) ? $value['nid'] : 0;
            $word = $value['content'];
            $arrayList[]=array(
                'code'=>$code,
                'word' => array(
                'nid'=>$nid,
                'word'=>$word
            ));
        }
        return json_encode($arrayList);
    }   

    function saveJsonString($nodeId, $jsonString){
        $query = db_select('kennisstructuur', 'n') ;            
        $query->fields('n', array('node_id'));
        $query->condition('node_id', $nodeId , '=');
        $result = $query->execute();
        $node_list =  $result->fetchAll();

        if(!empty($node_list)){
            db_update('kennisstructuur')
            ->fields(array(
                'nameslist' => $jsonString
            ))
                ->condition('node_id',$nodeId, '=')
                ->execute();
        }else{
        db_insert('kennisstructuur')
        ->fields(array(
          'node_id' => $nodeId,
          'nameslist' => $jsonString
        ))
        ->execute();
        }
    }
}