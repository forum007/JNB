#!/usr/local/bin/php -q
<?php
class jnb11{
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
		$fn_moto = $this->path . "dat/jnb11.csv";
		if(file_exists($fn_moto) == false){
			printf("01ファイルありません\n");
			return;
		}
		$fn_saki = $this->path . "dat/jnb11.csv." . $ymd;
		$comm = "/bin/nkf -wLu " . $fn_moto . " > " . $fn_saki;
		system($comm);
printf("%s\n", $fn_saki);
		$fp = fopen($fn_saki, "r");
		fgetcsv($fp, 4000, ",");
		while(($ret = fgetcsv($fp, 4000, ",")) !== FALSE) {
			$cnt = count($ret);
			if($cnt < 8){
				printf("データ除外%d\n", $cnt);
				continue;
			}
			for($i = 0; $i < $cnt; $i++){
				switch($i){
					case 0:	$this->code = $this->get_fundcode($ret[$i]); break;
					case 1:	$this->standard_date = $this->get_standard_date($ret[$i]); break;
					case 2:	$this->p1a = $this->get_val($ret[$i]); break;
					case 3:	$this->p3a = $this->get_val($ret[$i]); break;
					case 4:	$this->p5a = $this->get_val($ret[$i]); break;
					case 5:	$this->std1 = $this->get_val($ret[$i]); break;
					case 6:	$this->std3 = $this->get_val($ret[$i]); break;
					case 7: $this->std5 = $this->get_val($ret[$i]); break;
				}
			}
			$sql = "replace into ms_index_tech values(";
			$sql .= "'" . $this->code . "',";
			$sql .= "'" . $this->standard_date . "',";
			if(strlen($this->p1a) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->p1a . ",";
			}
			if(strlen($this->p3a) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->p3a . ",";
			}
			if(strlen($this->p5a) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->p5a . ",";
			}
			if(strlen($this->std1) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->std1 . ",";
			}
			if(strlen($this->std3) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->std3 . ",";
			}
			if(strlen($this->std5) == 0){
				$sql .= "null);";
			}else{
				$sql .= $this->std5 . ");";
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
$proc = new jnb11;
$proc->startProcess();
?>
