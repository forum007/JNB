#!/usr/local/bin/php -q
<?php
class jnb04{
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
		$fn_moto = $this->path . "dat/jnb04.csv";
		if(file_exists($fn_moto) == false){
			printf("01ファイルありません\n");
			return;
		}
		$fn_saki = $this->path . "dat/jnb04.csv." . $ymd;
		$comm = "/bin/nkf -wLu " . $fn_moto . " > " . $fn_saki;
		system($comm);
printf("%s\n", $fn_saki);
		$fp = fopen($fn_saki, "r");
		fgetcsv($fp, 4000, ",");
		while(($ret = fgetcsv($fp, 4000, ",")) !== FALSE) {
			$cnt = count($ret);
			if($cnt != 12){
				printf("データ除外\n");
				continue;
			}
			for($i = 0; $i < $cnt; $i++){
				switch($i){
					case 0:	$this->fund_code = $this->get_fundcode($ret[$i]); break;
					case 1: $this->sec01 = $this->get_sec($ret[$i]); break;
					case 2:	$this->ave01 = $this->get_ave($ret[$i]); break;
					case 3: $this->sec02 = $this->get_sec($ret[$i]); break;
					case 4:	$this->ave02 = $this->get_ave($ret[$i]); break;
					case 5: $this->sec03 = $this->get_sec($ret[$i]); break;
					case 6:	$this->ave03 = $this->get_ave($ret[$i]); break;
					case 7: $this->sec04 = $this->get_sec($ret[$i]); break;
					case 8:	$this->ave04 = $this->get_ave($ret[$i]); break;
					case 9: $this->sec05 = $this->get_sec($ret[$i]); break;
					case 10:$this->ave05 = $this->get_ave($ret[$i]); break;
					case 11:$this->ave = $this->get_ave($ret[$i]); break;
				}
			}
			$sql = "replace into ms_asset_info values(";
			$sql .= "'" . $this->fund_code . "',";
			$sql .= "curdate(),";
			$sql .= "'" . $this->sec01 . "',";
			$sql .= $this->ave01 . ",";
			$sql .= "'" . $this->sec02 . "',";
			$sql .= $this->ave02 . ",";
			$sql .= "'" . $this->sec03 . "',";
			$sql .= $this->ave03 . ",";
			$sql .= "'" . $this->sec04 . "',";
			$sql .= $this->ave04 . ",";
			$sql .= "'" . $this->sec05 . "',";
			$sql .= $this->ave05 . ",";
			$sql .= $this->ave . ");";
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
$proc = new jnb04;
$proc->startProcess();
?>
