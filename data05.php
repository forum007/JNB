#!/usr/local/bin/php -q
<?php
class jnb05{
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
    function get_update($str){
        $y = substr($str, 0, 4);
        $m = substr($str, 4, 2);
        $d = substr($str, 6, 2);
        return $y . "-" . $m . "-" . $d;
    }
/******************************************************************************/
	function get_sec($str){
		return $str;
	}
/******************************************************************************/
	function get_ave($str){
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
		$fn_moto = $this->path . "dat/jnb05.csv";
		if(file_exists($fn_moto) == false){
			printf("01ファイルありません\n");
			return;
		}
		$fn_saki = $this->path . "dat/jnb05.csv." . $ymd;
		$comm = "/bin/nkf -wLu " . $fn_moto . " > " . $fn_saki;
		system($comm);
printf("%s\n", $fn_saki);
		$fp = fopen($fn_saki, "r");
		fgetcsv($fp, 4000, ",");
		while(($ret = fgetcsv($fp, 4000, ",")) !== FALSE) {
			$cnt = count($ret);
			if($cnt < 22){
				printf("データ除外%d\n", $cnt);
				continue;
			}
			for($i = 0; $i < $cnt; $i++){
				switch($i){
					case 0:	$this->fund_code = $this->get_fundcode($ret[$i]); break;
					case 1:	$this->update = $this->get_update($ret[$i]); break;
					case 2: $this->sec01 = $this->get_sec($ret[$i]); break;
					case 3:	$this->ave01 = $this->get_ave($ret[$i]); break;
					case 4: $this->sec02 = $this->get_sec($ret[$i]); break;
					case 5:	$this->ave02 = $this->get_ave($ret[$i]); break;
					case 6: $this->sec03 = $this->get_sec($ret[$i]); break;
					case 7:	$this->ave03 = $this->get_ave($ret[$i]); break;
					case 8: $this->sec04 = $this->get_sec($ret[$i]); break;
					case 9:	$this->ave04 = $this->get_ave($ret[$i]); break;
					case 10: $this->sec05 = $this->get_sec($ret[$i]); break;
					case 11: $this->ave05 = $this->get_ave($ret[$i]); break;
					case 12: $this->sec06 = $this->get_sec($ret[$i]); break;
					case 13: $this->ave06 = $this->get_ave($ret[$i]); break;
					case 14: $this->sec07 = $this->get_sec($ret[$i]); break;
					case 15: $this->ave07 = $this->get_ave($ret[$i]); break;
					case 16: $this->sec08 = $this->get_sec($ret[$i]); break;
					case 17: $this->ave08 = $this->get_ave($ret[$i]); break;
					case 18: $this->sec09 = $this->get_sec($ret[$i]); break;
					case 19: $this->ave09 = $this->get_ave($ret[$i]); break;
					case 20: $this->sec10 = $this->get_sec($ret[$i]); break;
					case 21: $this->ave10 = $this->get_ave($ret[$i]); break;
				}
			}
			$sql = "replace into ms_stock_ratio values(";
			$sql .= "'" . $this->fund_code . "',";
			$sql .= "'" . $this->update . "',";
			$sql .= "'" . $this->sec01 . "',";
			if(strlen($this->ave01) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->ave01 . ",";
			}
			$sql .= "'" . $this->sec02 . "',";
			if(strlen($this->ave02) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->ave02 . ",";
			}
			$sql .= "'" . $this->sec03 . "',";
			if(strlen($this->ave03) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->ave03 . ",";
			}
			$sql .= "'" . $this->sec04 . "',";
			if(strlen($this->ave04) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->ave04 . ",";
			}
			$sql .= "'" . $this->sec05 . "',";
			if(strlen($this->ave05) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->ave05 . ",";
			}
			$sql .= "'" . $this->sec06 . "',";
			if(strlen($this->ave06) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->ave06 . ",";
			}
			$sql .= "'" . $this->sec07 . "',";
			if(strlen($this->ave07) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->ave07 . ",";
			}
			$sql .= "'" . $this->sec08 . "',";
			if(strlen($this->ave08) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->ave08 . ",";
			}
			$sql .= "'" . $this->sec09 . "',";
			if(strlen($this->ave09) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->ave09 . ",";
			}
			$sql .= "'" . $this->sec10 . "',";
			if(strlen($this->ave10) == 0){
				$sql .= "null);";
			}else{
				$sql .= $this->ave10 . ");";
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
$proc = new jnb05;
$proc->startProcess();
?>
