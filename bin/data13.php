#!/usr/local/bin/php -q
<?php
class jnb13{
	function __construct() {
		$this->path = "/export/ms/";
		require_once($this->path . "lib/fileReadWrite.php");
		$fr = new fileReadWrite();
		$ret = $fr->getFileData($this->path."/ini/ms.ini","csv","=",1000);
		if($ret == FALSE){
			print "設定ファイルの読み込みに失敗しました。\n";
			exit;
		}
		$cnt = count($ret);
		for($i = 0;$i < $cnt; $i++){
			if($ret[$i][0] == "DB_HOST"){		$this->dbHost	= $ret[$i][1]; }
			elseif($ret[$i][0] == "DB_USER"){	$this->dbUser	= $ret[$i][1]; }
			elseif($ret[$i][0] == "DB_PASS"){	$this->dbPass	= $ret[$i][1]; }
			elseif($ret[$i][0] == "DB_NAME"){	$this->dbName	= $ret[$i][1]; }
		}
	}
/******************************************************************************/
	function get_fundcode($str){
		return $str;
	}
/******************************************************************************/
    function get_standard_date($str){
        $y = substr($str, 0, 4);
        $m = substr($str, 4, 2);
        $ret = date("Y-m-t", strtotime($y . "/" . $m . "/01"));
		$ret = str_replace("/", "-", $ret);
		return $ret;
    }
/******************************************************************************/
	function get_term($str){
		return $str;
	}
/******************************************************************************/
	function get_val($str){
		if(strlen($str) == 0){
			return "";
		}
		$ret1 = substr($str, 4, 2);
		$ret2 = substr($str, 6, 2);
		return $ret1 . "-" . $ret2;
	}
/******************************************************************************/
	function startProcess(){
		$con = mysqli_connect($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName);
		if(!$con){
			printf("データベースに接続出来ませんでした\n");
			return;
		}

		$ymd = date("Ymd");
		$fn_moto = $this->path . "dat/jnb13.csv";
		if(file_exists($fn_moto) == false){
			printf("01ファイルありません\n");
			return;
		}
		$fn_saki = $this->path . "dat/jnb13.csv." . $ymd;
		$comm = "/bin/nkf -wLu " . $fn_moto . " > " . $fn_saki;
		system($comm);
printf("%s\n", $fn_saki);
		$fp = fopen($fn_saki, "r");
		fgetcsv($fp, 4000, ",");
		while(($ret = fgetcsv($fp, 4000, ",")) !== FALSE) {
			$cnt = count($ret);
			if($cnt < 13){
				printf("データ除外%d\n", $cnt);
				continue;
			}
			for($i = 0; $i < $cnt; $i++){
				switch($i){
					case 0:	$this->code = $this->get_fundcode($ret[$i]); break;
					case 1:	$this->div01 = $this->get_val($ret[$i]); break;
					case 2:	$this->div02 = $this->get_val($ret[$i]); break;
					case 3:	$this->div03 = $this->get_val($ret[$i]); break;
					case 4:	$this->div04 = $this->get_val($ret[$i]); break;
					case 5:	$this->div05 = $this->get_val($ret[$i]); break;
					case 6:	$this->div06 = $this->get_val($ret[$i]); break;
					case 7:	$this->div07 = $this->get_val($ret[$i]); break;
					case 8:	$this->div08 = $this->get_val($ret[$i]); break;
					case 9:	$this->div09 = $this->get_val($ret[$i]); break;
					case 10:$this->div10 = $this->get_val($ret[$i]); break;
					case 11:$this->div11 = $this->get_val($ret[$i]); break;
					case 12:$this->div12 = $this->get_val($ret[$i]); break;
				}
			}
			$sql = "replace into ms_dividend_yote values(";
			$sql .= "'" . $this->code . "',";
			if(strlen($this->div01) == 0){
				$sql .= "null,";
			}else{
				$sql .= "'" . $this->div01 . "',";
			}
			if(strlen($this->div02) == 0){
				$sql .= "null,";
			}else{
				$sql .= "'" . $this->div02 . "',";
			}
			if(strlen($this->div03) == 0){
				$sql .= "null,";
			}else{
				$sql .= "'" . $this->div03 . "',";
			}
			if(strlen($this->div04) == 0){
				$sql .= "null,";
			}else{
				$sql .= "'" . $this->div04 . "',";
			}
			if(strlen($this->div05) == 0){
				$sql .= "null,";
			}else{
				$sql .= "'" . $this->div05 . "',";
			}
			if(strlen($this->div06) == 0){
				$sql .= "null,";
			}else{
				$sql .= "'" . $this->div06 . "',";
			}
			if(strlen($this->div07) == 0){
				$sql .= "null,";
			}else{
				$sql .= "'" . $this->div07 . "',";
			}
			if(strlen($this->div08) == 0){
				$sql .= "null,";
			}else{
				$sql .= "'" . $this->div08 . "',";
			}
			if(strlen($this->div09) == 0){
				$sql .= "null,";
			}else{
				$sql .= "'" . $this->div09 . "',";
			}
			if(strlen($this->div10) == 0){
				$sql .= "null,";
			}else{
				$sql .= "'" . $this->div10 . "',";
			}
			if(strlen($this->div11) == 0){
				$sql .= "null,";
			}else{
				$sql .= "'" . $this->div11 . "',";
			}
			if(strlen($this->div12) == 0){
				$sql .= "null);";
			}else{
				$sql .= "'" . $this->div12 . "');";
			}
printf("%s\n", $sql);
			$res = mysqli_query($con, $sql);
			if (!$res) {
				$message  = 'Invalid query: ' . mysqli_error($con) . "\n";
				$message .= 'Whole query: ' . $sql;
				printf("%s\n", $message);
				return;
			}
		}
		fclose($fp);
	}
}
$proc = new jnb13;
$proc->startProcess();
?>
