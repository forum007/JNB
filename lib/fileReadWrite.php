<?php
class fileReadWrite{
  var $fp,$fileType,$delimiter,$fileName,$errMsg,$procMsg,$debug,$fileData;
  var $typearray,$linelenth;
/******************************************************************************/
  function __construct(){
    $this->fp = "";
    $this->fileType = "";
    $this->delimiter = "";
    $this->fileName = "";
    $this->errMsg = "";
    $this->procMsg = "";
    $this->debug = FALSE;
    $this->fileData = "";
    //$this->typearray = array("csv","txt","xml");
    $this->typearray = array("csv","txt");
    $this->linelength = 1000;
  }
/******************************************************************************/
  function setDebugMode($flg){
    if(!empty($flg)){
      if($flg == TRUE || $flg == FALSE){ $this->debug = $flg; }
    }
  }
/******************************************************************************/
  function setFileName($filename){
    if(strlen($filename) < 1){
      $this->errMsg = "ファイル名が指定されていません。\n";
      if($this->debug){ print $this->getErrorMsg(); }
      return FALSE;
    }elseif(!file_exists($filename)){
      $this->errMsg = "指定したファイルが存在しません。\n";
      $this->errMsg .= "ファイル名 : ".$filename."\n";
      if($this->debug){ print $this->getErrorMsg(); }
      return FALSE;
    }elseif(!is_file($filename)){
      $this->errMsg = "指定したファイル名はファイルではありません。\n";
      $this->errMsg .= "ファイル名 : ".$filename."\n";
      if($this->debug){ print $this->getErrorMsg(); }
      return FALSE;
    }
    $this->fileName = $filename;
    $this->procMsg = "ファイル名を指定しました。\n";
    $this->procMsg .= "ファイル名 : ".$filename."\n";
    if($this->debug){ print $this->getProcessMsg(); }
    return TRUE;
  }
/******************************************************************************/
  function setFileType($filetype){
    if(strlen($filetype) < 1){
      $this->errMsg = "ファイルタイプが指定されていません。\n";
      if($this->debug){ print $this->getErrorMsg(); }
      return FALSE;
    }elseif(!in_array(strtolower($filetype),$this->typearray)){
      $this->errMsg = "対応していないタイプが指定されています。\n";
      $this->errMsg .= "ファイルタイプ : ".$filetype."\n";
      if($this->debug){ print $this->getErrorMsg(); }
      return FALSE;
    }
    $this->fileType = strtolower($filetype);
    $this->procMsg = "ファイルタイプを指定しました。\n";
    $this->procMsg .= "ファイルタイプ : ".$filetype."\n";
    if($this->debug){ print $this->getProcessMsg(); }
    return TRUE;
  }
/******************************************************************************/
  function setFileDelimiter($delimiter){
    if(strlen($delimiter) < 1){
      $this->errMsg = "区切文字が指定されていません。\n";
      if($this->debug){ print $this->getErrorMsg(); }
      return FALSE;
    }
    $this->delimiter = $delimiter;
    $this->procMsg = "区切文字を指定しました。\n";
    $this->procMsg .= "区切文字 : ".$delimiter."\n";
    if($this->debug){ print $this->getProcessMsg(); }
    return TRUE;
  }
/******************************************************************************/
  function setLineLength($linelength){
    if(strlen($linelength) < 1){
      $this->errMsg = "読み込み１行の文字数が指定されていません。\n";
      if($this->debug){ print $this->getErrorMsg(); }
      return FALSE;
    }
    $this->linelength = $linelength;
    $this->procMsg = "読み込む１行の長さを指定しました。\n";
    $this->procMsg .= "１行の読み込み文字数 : ".$linelength."\n";
    if($this->debug){ print $this->getProcessMsg(); }
    return TRUE;
  }
/******************************************************************************/
  function setOutputFileData($filedata){
    if(strlen($filedata) < 1){
      $this->errMsg = "ファイルに書き込むデータが空です。\n";
      if($this->debug){ print $this->getErrorMsg(); }
      return FALSE;
    }
    $this->outputData = $filedata;
    $this->procMsg = "ファイルに書き込むデータをセットしました。\n";
    $this->procMsg .= "全体の文字数 : ".strlen($filedata)."\n";
    if($this->debug){ print $this->getProcessMsg(); }
    return TRUE;
  }
/******************************************************************************/
  function openReadFile(){
    $this->fp = @fopen($this->fileName,"r");
    if(!$this->fp){
      $this->errMsg = "ファイルオープンに失敗しました。\n";
      $this->errMsg .= $this->fileName."\n";
      if($this->debug){ print $this->getErrorMsg(); }
      return FALSE;
    }
    $this->procMsg = "ファイルをオープンしました。\n";
    $this->procMsg .= $this->fileName."\n";
    if($this->debug){ print $this->getProcessMsg(); }
    return TRUE;
  }
/******************************************************************************/
  function openWriteFile(){
    $this->fp = @fopen($this->fileName,"w");
    if(!$this->fp){
      $this->errMsg = "ファイルオープンに失敗しました。\n";
      $this->errMsg .= $this->fileName."\n";
      if($this->debug){ print $this->getErrorMsg(); }
      return FALSE;
    }
    $this->procMsg = "ファイルをオープンしました。\n";
    $this->procMsg .= $this->fileName."\n";
    if($this->debug){ print $this->getProcessMsg(); }
    return TRUE;
  }
/******************************************************************************/
  function openAddFile(){
    $this->fp = @fopen($this->fileName,"a");
    if(!$this->fp){
      $this->errMsg = "ファイルオープンに失敗しました。\n";
      $this->errMsg .= $this->fileName."\n";
      if($this->debug){ print $this->getErrorMsg(); }
      return FALSE;
    }
    $this->procMsg = "ファイルをオープンしました。\n";
    $this->procMsg .= $this->fileName."\n";
    if($this->debug){ print $this->getProcessMsg(); }
    return TRUE;
  }
/******************************************************************************/
  function outputFileData(){
    if($ret = fwrite($this->fp,$this->outputData) === FALSE){
      $this->errMsg = "ファイル出力に失敗しました。\n";
      $this->errMsg .= $this->fileName."\n";
      if($this->debug){ print $this->getErrorMsg(); }
      return FALSE;
    }
    $this->procMsg = "ファイルに出力しました。\n";
    $this->procMsg .= "書き込みバイト数 : ".$ret."\n";
    if($this->debug){ print $this->getProcessMsg(); }
    return TRUE;
  }
/******************************************************************************/
  function closeFile(){
    if(!fclose($this->fp)){
      $this->errMsg = "ファイルクローズに失敗しました。\n";
      $this->errMsg .= $this->fileName."\n";
      if($this->debug){ print $this->getErrorMsg(); }
      return FALSE;
    }
    $this->procMsg = "ファイルをクローズしました。\n";
    $this->procMsg .= $this->fileName."\n";
    if($this->debug){ print $this->getProcessMsg(); }
    return TRUE;
  }
/******************************************************************************/
  function readFile(){
    if($this->fileType == "csv"){
      if($this->delimiter == ""){ $this->delimiter = ","; }
      $this->fileData = array();
      while(!feof($this->fp)){
        $line = fgetcsv($this->fp,$this->linelength,$this->delimiter);
        if(is_array($line)){ array_push($this->fileData,$line); }
      }
//    }elseif($this->fileType == "xml"){
    }elseif($this->fileType == "txt"){
      $this->fileData = array();
      while(!feof($this->fp)){
        $line = fgets($this->fp,$this->linelength);
        if(strlen($line)>0){ array_push($this->fileData,$line); }
      }
    }else{
      $this->errMsg = "ファイルタイプが指定されていないか、";
      $this->errMsg .= "対応していないタイプが設定されています。\n";
      if($this->debug){ print $this->getErrorMsg(); }
      return FALSE;
    }
    return TRUE;
  }
/******************************************************************************/
  function getFileData($filename,$filetype,$delimiter,$linelength){
    // ファイル名の設定
    if(!$this->setFIleName($filename)){ return FALSE; }
    // ファイルのタイプ設定
    if(!$this->setFileType($filetype)){ return FALSE; }
    // 区切り文字の設定
    if(strlen($delimiter) > 0){
      if(!$this->setFileDelimiter($delimiter)){ return FALSE; }
    }
    // 読み込む１行の長さ設定
    if(strlen($linelength) > 0){
      if(!$this->setLineLength($linelength)){ return FALSE; }
    }
    // ファイルを開く
    if(!$this->openReadFile()){ return FALSE; }
    // ファイルのデータを取得
    if(!$this->readFile()){ return FALSE; }
    // ファイルを閉じる
    if(!$this->closeFile()){ return FALSE; }
    // ファイルのデータを戻す
    return $this->fileData;
  }
/******************************************************************************/
  function addFileData($filename,$filedata){
    // 出力ファイル名の設定
    if(!$this->setFIleName($filename)){ return FALSE; }
    // 出力ファイルを開く
    if(!$this->openAddFile()){ return FALSE; }
    // 出力ファイルのデータをセット
    if(!$this->setOutputFileData($filedata)){ return FALSE; }
    // データ出力
    if(!$this->outputFileData()){ return FALSE; }
    // ファイルを閉じる
    if(!$this->closeFile()){ return FALSE; }
    return TRUE;
  }
/******************************************************************************/
  function writeFileData($filename,$filedata){
    // 出力ファイル名の設定
    if(!$this->setFIleName($filename)){ return FALSE; }
    // 出力ファイルを開く
    if(!$this->openWriteFile()){ return FALSE; }
    // 出力ファイルのデータをセット
    if(!$this->setOutputFileData($filedata)){ return FALSE; }
    // データ出力
    if(!$this->outputFileData()){ return FALSE; }
    // ファイルを閉じる
    if(!$this->closeFile()){ return FALSE; }
    return TRUE;
  }
/******************************************************************************/
  function getErrorMsg(){
    return $this->errMsg;
  }
/******************************************************************************/
  function getProcessMsg(){
    return $this->procMsg;
  }
/******************************************************************************/
}
?>
