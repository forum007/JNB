<?php
class mysqlDB{
  var $procMsg,$errMsg,$debugFlg,$mysqliFlg;
  var $dbHost,$dbUser,$dbPass,$dbName,$conn,$result,$sqlStr,$rowsCount;
  var $selectArray;
/******************************************************************************/
  function mysqlDB(){
    $this->procMsg = "";
    $this->errMsg = "";
    $this->dbHost = "";
    $this->dbUser = "";
    $this->dbPass = "";
    $this->dbName = "";
    $this->conn = "";
    $this->result = "";
    $this->sqlStr = "";
    $this->rowsCount = 0;
    $this->debugFlg = FALSE;

    $this->mysqliFlg = FALSE;
    if(function_exists('mysqli_connect')){ $this->mysqliFlg = TRUE; }
  }
/******************************************************************************/
  function setDebugMode($flg){
    if(!empty($flg)){
      if($flg == TRUE || $flg == FALSE){ $this->debug = $flg; }
    }
  }
/******************************************************************************/
  function checkConnectDBParam(){
    $errmsg = "";
    if(empty($this->dbHost)){
      $errmsg .= "DBホスト名が設定されていません。\n"; }
    if(empty($this->dbUser)){
      $errmsg .= "DBユーザ名が設定されていません。\n"; }
    if(empty($this->dbPass)){
      $errmsg .= "DBパスワードが設定されていません。\n"; }
    if(empty($this->dbName)){
      $errmsg .= "DB名が設定されていません。\n"; }
    if(!empty($errmsg)){
      $this->errMsg = $errmsg;
      if($this->debugFlg){ print $this->errMsg; }
      return FALSE;
    }
    return TRUE;
  }
/******************************************************************************/
  function setConnectDBParam($db_host,$db_user,$db_pass,$db_name){
    if(!empty($db_host)){ $this->dbHost = $db_host; }
    if(!empty($db_user)){ $this->dbUser = $db_user; }
    if(!empty($db_pass)){ $this->dbPass = $db_pass; }
    if(!empty($db_name)){ $this->dbName = $db_name; }
  }
/******************************************************************************/
  function checkSqlStr(){
    if(empty($this->sqlStr)){
      $this->errMsg = "実行するSQL文が設定されていません。\n";
      if($this->debugFlg){ print $this->errMsg; }
      return FALSE;
    }
    return TRUE;
  }
/******************************************************************************/
  function setSqlStr($sqlstr){
    if(!empty($sqlstr)){ $this->sqlStr = $sqlstr; }
  }
/******************************************************************************/
  function connectDB($db_host,$db_user,$db_pass,$db_name){
    $this->setConnectDBParam($db_host,$db_user,$db_pass,$db_name);
    if(!$this->checkConnectDBParam()){
      return FALSE;
    }
    if($this->mysqliFlg){
      $this->conn = new mysqli(
       $this->dbHost,$this->dbUser,$this->dbPass,$this->dbName);
      if(mysqli_connect_errno()){
        $this->errMsg = "DB接続に失敗しました。:".mysqli_connect_error()."\n";
        if($this->debugFlg){ print $this->errMsg; }
        return FALSE;
      }
    }else{
      $this->conn = mysql_connect($this->dbHost,$this->dbUser,$this->dbPass);
      if(!$this->conn){
        $this->errMsg = "DB接続に失敗しました。:".mysql_error()."\n";
        if($this->debugFlg){ print $this->errMsg; }
        return FALSE;
      }
      if(!mysql_select_db($this->dbName,$this->conn)){
        $this->errMsg = "データベースが存在しません。:".mysql_error()."\n";
        if($this->debugFlg){ print $this->errMsg; }
        return FALSE;
      }
    }
    $this->procMsg = "DB接続に成功しました。\n";
    $this->procMsg .= $this->dbHost." : ".$this->dbUser." : ";
    $this->procMsg .= $this->dbName."\n";
    if($this->debugFlg){ print $this->procMsg; }
    return TRUE;
  }
/******************************************************************************/
  function execQuery($sqlstr){
    $this->setSqlStr($sqlstr);
    if(!$this->checkSqlStr()){ return FALSE; }
    if($this->mysqliFlg){
      $this->result = $this->conn->query($this->sqlStr);
      if(!$this->result){
        $this->errMsg = $this->conn->error."\n";
        if($this->debugFlg){ print $this->errMsg; }
        return FALSE;
      }
    }else{
      $this->result = mysql_query($this->sqlStr,$this->conn);
      if(!$this->result){
        $this->errMsg = mysql_error."\n";
        if($this->debugFlg){ print $this->errMsg; }
        return FALSE;
      }
    }
    $this->procMsg = "SQLを実行しました。\n";
    if($this->debugFlg){ print $this->procMsg; }

    return TRUE;
  }
/******************************************************************************/
  function freeResult(){
    if($this->mysqliFlg){
      $this->result->close();
    }else{
      if(!mysql_free_result($this->result)){
        $this->errMsg = "結果セットの解放に失敗しました。\n";
        if($this->debugFlg){ print $this->errMsg; }
        return FALSE;
      }
    }
    $this->procMsg = "結果セットを解放しました。\n";
    if($this->debugFlg){ print $this->procMsg; }
    return TRUE;
  }
/******************************************************************************/
  function closeDB(){
    if($this->mysqliFlg){
      if(!$this->conn->close()){
        $this->errMsg = "DB接続の切断に失敗しました。\n";
        if($this->debugFlg){ print $this->errMsg; }
        return FALSE;
      }
    }else{
      if(!$this->conn){
        $this->errMsg = "DB接続の切断に失敗しました。\n";
        if($this->debugFlg){ print $this->errMsg; }
        return FALSE;
      }
      if(!mysql_close($this->conn)){
        $this->errMsg = "DB接続の切断に失敗しました。\n";
        if($this->debugFlg){ print $this->errMsg; }
        return FALSE;
      }
    }
    $this->procMsg = "DB接続の切断に成功しました。\n";
    if($this->debugFlg){ print $this->procMsg; }
    return TRUE;
  }
/******************************************************************************/
  function setSelectRowsCount(){
    if($this->mysqliFlg){ $this->rowsCount = $this->result->num_rows; }
    else{ $this->rowsCount = mysql_num_rows($this->result); }
  }
/******************************************************************************/
  function setSelectData(){
    $this->selectArray = array();
    while($row=$this->result->fetch_row()){
      array_push($this->selectArray,$row);
    }
  }
/******************************************************************************/
  function setChangeRowsCount(){
    if($this->mysqliFlg){ $this->rowsCount = $this->conn->affected_rows; }
    else{ $this->rowsCount = mysql_affected_rows(); }
  }
/******************************************************************************/
  function deleteSqlExec($db_host,$db_user,$db_pass,$db_name,$sqlstr){
    $this->procMsg = "データを削除します。\n";
    if($this->debugFlg){ print $this->procMsg; }
    if(!$this->connectDB($db_host,$db_user,$db_pass,$db_name)){ return FALSE; }
    if(!$this->execQuery($sqlstr)){ $this->closeDB();return FALSE; }
    $this->setChangeRowsCount();
    if(!$this->closeDB()){ return FALSE; }
    $this->procMsg = "データを削除しました。: ".$this->rowsCount."件\n";
    if($this->debugFlg){ print $this->procMsg; }
    return TRUE;
  }
/******************************************************************************/
  function updateSqlExec($db_host,$db_user,$db_pass,$db_name,$sqlstr){
    $this->procMsg = "データを更新します。\n";
    if($this->debugFlg){ print $this->procMsg; }
    if(!$this->connectDB($db_host,$db_user,$db_pass,$db_name)){ return FALSE; }
    if(!$this->execQuery($sqlstr)){ $this->closeDB();return FALSE; }
    $this->setChangeRowsCount();
    if(!$this->closeDB()){ return FALSE; }
    $this->procMsg = "データを更新しました。: ".$this->rowsCount."件\n";
    if($this->debugFlg){ print $this->procMsg; }
    return TRUE;
  }
/******************************************************************************/
  function insertSqlExec($db_host,$db_user,$db_pass,$db_name,$sqlstr){
    $this->procMsg = "データを登録します。\n";
    if($this->debugFlg){ print $this->procMsg; }
    if(!$this->connectDB($db_host,$db_user,$db_pass,$db_name)){ return FALSE; }
    if(!$this->execQuery($sqlstr)){ $this->closeDB();return FALSE; }
    $this->setChangeRowsCount();
    if(!$this->closeDB()){ return FALSE; }
    $this->procMsg = "データを登録しました。: ".$this->rowsCount."件\n";
    if($this->debugFlg){ print $this->procMsg; }
    return TRUE;
  }
/******************************************************************************/
  function selectSqlExec($db_host,$db_user,$db_pass,$db_name,$sqlstr){
    $this->procMsg = "データを検索します。\n";
    if($this->debugFlg){ print $this->procMsg; }
    if(!$this->connectDB($db_host,$db_user,$db_pass,$db_name)){ return FALSE; }
    if(!$this->execQuery($sqlstr)){ $this->closeDB();return FALSE; }
    $this->setSelectRowsCount();
    $this->setSelectData();
    if(!$this->closeDB()){ return FALSE; }
    $this->procMsg = "データを検索しました。: ".$this->rowsCount."件\n";
    if($this->debugFlg){ print $this->procMsg; }
    return TRUE;
  }
/******************************************************************************/
  function getProcessMsg(){
    return $this->procMsg;
  }
/******************************************************************************/
  function getErrorMsg(){
    return $this->errMsg;
  }
/******************************************************************************/
  function getRowsCount(){
    return $this->rowsCount;
  }
/******************************************************************************/
  function getSelectData(){
    return $this->selectArray;
  }
/******************************************************************************/
}
?>
