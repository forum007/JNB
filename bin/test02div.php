#!/usr/local/bin/php -q
<?php
class test01{
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
			elseif($ret[$i][0] == "CDB_HOST"){	$this->jdbHost	= $ret[$i][1]; }
			elseif($ret[$i][0] == "CDB_USER"){	$this->jdbUser	= $ret[$i][1]; }
			elseif($ret[$i][0] == "CDB_PASS"){	$this->jdbPass	= $ret[$i][1]; }
			elseif($ret[$i][0] == "CDB_NAME"){	$this->jdbName	= $ret[$i][1]; }
		}
	}
/******************************************************************************/
	function startProcess(){
		$ms = mysqli_connect($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName);
		if(!$ms){
			printf("データベースに接続出来ませんでした\n");
			return;
		}
		$con = mysqli_connect($this->jdbHost, $this->jdbUser, $this->jdbPass, $this->jdbName);
		if(!$con){
			printf("データベースに接続出来ませんでした\n");
			return;
		}
		$sql = "select fund_code, ita_code from ms_fund; ";
		$res = mysqli_query($ms, $sql);
		if(!$res) {
			$message  = 'Invalid query: ' . mysqli_error($ms) . "\n";
			$message .= 'Whole query: ' . $sql;
			printf("%s\n", $message);
			return;
		}
		while($row = mysqli_fetch_assoc($res)) {
			if($row['ita_code'] == '1985092001' ||
				$row['ita_code'] == '2003122501' ||
				$row['ita_code'] == '2009073120'){
				break;
			}
			$sql = "select lipper_id from lipper_fund_info ";
			$sql .= "where ita_code = '" . $row['ita_code'] . "' ";
			$res2 = mysqli_query($con, $sql);
			if(!$res2) {
				$message  = 'Invalid query: ' . mysqli_error($con) . "\n";
				$message .= 'Whole query: ' . $sql;
				printf("%s\n", $message);
			}
			if(mysqli_num_rows($res2) == 0){
//				printf("DATA無[%s][%s]\n", $row['ita_code'],$row['fund_name']);
			}else{
				$row2 = mysqli_fetch_assoc($res2);
				$sql = "select settle_date, dividend ";
				$sql .= "from lipper_dividend ";
				$sql .= "where lipper_id = '" . $row2['lipper_id'] . "';";
				$res2 = mysqli_query($con, $sql);
				if(!$res2) {
					$message  = 'Invalid query: ' . mysqli_error($con) . "\n";
					$message .= 'Whole query: ' . $sql;
					printf("%s\n", $message);
					return;
				}
				while($row3 = mysqli_fetch_assoc($res2)) {
					$ins = "replace into ms_dividend values(";
					$ins .= "'" . $row['fund_code'] . "',";
					$ins .= "'" . $row3['settle_date'] . "',";
					$ins .= $row3['dividend'] . ");";
printf("%s\n", $ins);
					$res3 = mysqli_query($ms, $ins);
					if(!$res3) {
						$message  = 'Invalid query: ' . mysqli_error($con) . "\n";
						$message .= 'Whole query: ' . $ins;
						printf("%s\n", $message);
						return;
					}
				}
			}
		}
	}
}
$proc = new test01;
$proc->startProcess();
?>
