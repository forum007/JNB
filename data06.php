#!/usr/local/bin/php -q
<?php
class jnb06{
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
        $d = substr($str, 6, 2);
        return $y . "-" . $m . "-" . $d;
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
		$fn_moto = $this->path . "dat/jnb06.csv";
		if(file_exists($fn_moto) == false){
			printf("01ファイルありません\n");
			return;
		}
		$fn_saki = $this->path . "dat/jnb06.csv." . $ymd;
		$comm = "/bin/nkf -wLu " . $fn_moto . " > " . $fn_saki;
		system($comm);
printf("%s\n", $fn_saki);
		$fp = fopen($fn_saki, "r");
		fgetcsv($fp, 4000, ",");
		while(($ret = fgetcsv($fp, 4000, ",")) !== FALSE) {
			$cnt = count($ret);
			if($cnt < 6){
				printf("データ除外%d\n", $cnt);
				continue;
			}
			for($i = 0; $i < $cnt; $i++){
				switch($i){
					case 0:	$this->fund_code = $this->get_fundcode($ret[$i]); break;
					case 1:	$this->standard_date = $this->get_standard_date($ret[$i]); break;
					case 2:	$this->nav = $this->get_val($ret[$i]); break;
					case 3: $this->tna = $this->get_val($ret[$i]); break;
					case 4:	$this->sa = $this->get_val($ret[$i]); break;
					case 5: $this->hi = $this->get_val($ret[$i]); break;
				}
			}
			$sql = "replace into ms_daily values(";
			$sql .= "'" . $this->fund_code . "',";
			$sql .= "'" . $this->standard_date . "',";
			$sql .= $this->nav . ",";
			if(strlen($this->tna) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->tna . ",";
			}
			if(strlen($this->sa) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->sa . ",";
			}
			if(strlen($this->hi) == 0){
				$sql .= "null);";
			}else{
				$sql .= $this->hi . ");";
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
$proc = new jnb06;
$proc->startProcess();
?>
