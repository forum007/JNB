#!/usr/local/bin/php -q
<?php
class jnb12{
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
		return $str;
	}
/******************************************************************************/
	function startProcess(){
		$con = mysqli_connect($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName);
		if(!$con){
			printf("データベースに接続出来ませんでした\n");
			return;
		}

		$ymd = date("Ymd");
		$fn_moto = $this->path . "dat/jnb12.csv";
		if(file_exists($fn_moto) == false){
			printf("01ファイルありません\n");
			return;
		}
		$fn_saki = $this->path . "dat/jnb12.csv." . $ymd;
		$comm = "/bin/nkf -wLu " . $fn_moto . " > " . $fn_saki;
		system($comm);
printf("%s\n", $fn_saki);
		$fp = fopen($fn_saki, "r");
		fgetcsv($fp, 4000, ",");
		while(($ret = fgetcsv($fp, 4000, ",")) !== FALSE) {
			$cnt = count($ret);
			if($cnt < 7){
				printf("データ除外%d\n", $cnt);
				continue;
			}
			for($i = 0; $i < $cnt; $i++){
				switch($i){
					case 0:	$this->code = $this->get_fundcode($ret[$i]); break;
					case 1:	$this->no1_nm = $this->get_val($ret[$i]); break;
					case 2:	$this->no1_ave = $this->get_val($ret[$i]); break;
					case 3:	$this->no2_nm = $this->get_val($ret[$i]); break;
					case 4:	$this->no2_ave = $this->get_val($ret[$i]); break;
					case 5:	$this->no3_nm = $this->get_val($ret[$i]); break;
					case 6:	$this->no3_ave = $this->get_val($ret[$i]); break;
				}
			}
			$sql = "replace into ms_country values(";
			$sql .= "'" . $this->code . "',";
			if(strlen($this->no1_nm) == 0){
				$sql .= "null,";
			}else{
				$sql .= "'" . $this->no1_nm . "',";
			}
			if(strlen($this->no1_ave) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->no1_ave . ",";
			}
			if(strlen($this->no2_nm) == 0){
				$sql .= "null,";
			}else{
				$sql .= "'" . $this->no2_nm . "',";
			}
			if(strlen($this->no2_ave) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->no2_ave . ",";
			}
			if(strlen($this->no3_nm) == 0){
				$sql .= "null,";
			}else{
				$sql .= "'" . $this->no3_nm . "',";
			}
			if(strlen($this->no3_ave) == 0){
				$sql .= "null);";
			}else{
				$sql .= $this->no3_ave . ");";
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
$proc = new jnb12;
$proc->startProcess();
?>
