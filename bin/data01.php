#!/usr/local/bin/php -q
<?php
class jnb01{
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
	function get_itacode($str){
		return $str;
	}
/******************************************************************************/
	function get_fundname($str){
		return $str;
	}
/******************************************************************************/
	function get_nickname($str){
		return $str;
	}
/******************************************************************************/
	function get_bunrui($str){
		$ret[0] = substr($str, 0, 1);
		$ret[1] = substr($str, 1, 1);
		$ret[2] = substr($str, 2, 1);
		return $ret;
	}
/******************************************************************************/
	function get_code($str){
		$ret[0] = substr($str, 0, 1);
		$ret[1] = substr($str, 1, 1);
		$ret[2] = substr($str, 2, 1);
		$ret[3] = substr($str, 3, 1);
		$ret[4] = substr($str, 4, 1);
		$ret[5] = substr($str, 5, 1);
		$ret[6] = substr($str, 6, 1);
		$ret[7] = substr($str, 7, 1);
		$ret[8] = substr($str, 8, 1);
		$ret[9] = substr($str, 9, 1);
		$ret[10] = substr($str, 10, 1);
		$ret[11] = substr($str, 11, 1);
		$ret[12] = substr($str, 12, 1);
		$ret[13] = substr($str, 13, 1);
		$ret[14] = substr($str, 14, 1);
		$ret[15] = substr($str, 15, 1);
		$ret[16] = substr($str, 16, 1);
		$ret[17] = substr($str, 17, 1);
		$ret[18] = substr($str, 18, 1);
		$ret[19] = substr($str, 19, 1);
		$ret[20] = substr($str, 20, 1);
		$ret[21] = substr($str, 21, 2);
		return $ret;
	}
/******************************************************************************/
	function get_settei($str){
		$y = substr($str, 0, 4);
		$m = substr($str, 4, 2);
		$d = substr($str, 6, 2);
		return $y . "-" . $m . "-" . $d;
	}
/******************************************************************************/
	function get_shokan($str){
		$ret = trim($str);
		if(strlen($ret) != 8){
			return "";
		}
		$y = substr($ret, 0, 4);
		$m = substr($ret, 4, 2);
		$d = substr($ret, 6, 2);
		return $y . "-" . $m . "-" . $d;
	}
/******************************************************************************/
	function get_compname($str){
		return $str;
	}
/******************************************************************************/
	function get_compname_s($str){
		return $str;
	}
/******************************************************************************/
	function get_dividend($str){
		$ret = trim($str);
		if(strlen($ret) != 8){
			return "";
		}
		$y = substr($ret, 0, 4);
		$m = substr($ret, 4, 2);
		$d = substr($ret, 6, 2);
		return $y . "-" . $m . "-" . $d;
	}
/******************************************************************************/
	function get_numdivd($str){
		$ret = ltrim($str, "0");
		return $ret;
	}
/******************************************************************************/
	function get_index($str){
		return $str;
	}
/******************************************************************************/
	function get_tesuryou($str){
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
		$fn_moto = $this->path . "dat/jnb01.csv";
		if(file_exists($fn_moto) == false){
			printf("01ファイルありません\n");
			return;
		}
		$fn_saki = $this->path . "dat/jnb01.csv." . $ymd;
		$comm = "/bin/nkf -wLu " . $fn_moto . " > " . $fn_saki;
		system($comm);
printf("%s\n", $fn_saki);
		$fp = fopen($fn_saki, "r");
		fgetcsv($fp, 4000, ",");
		while(($ret = fgetcsv($fp, 4000, ",")) !== FALSE) {
			$cnt = count($ret);
			if($cnt != 17){
				printf("データ除外\n");
				continue;
			}
			for($i = 0; $i < $cnt; $i++){
				switch($i){
					case 0:	$this->fund_code = $this->get_fundcode($ret[$i]); break;
					case 1: $this->ita_code = $this->get_itacode($ret[$i]); break;
					case 2:	$this->fund_name = $this->get_fundname($ret[$i]); break;
					case 3: $this->nick_name = $this->get_nickname($ret[$i]); break;
					case 4: $this->code = $this->get_code($ret[$i]); break;
					case 5: $this->settei_date = $this->get_settei($ret[$i]); break;
					case 6: $this->shokan_date = $this->get_shokan($ret[$i]); break;
					case 7: $this->comp_name = $this->get_compname($ret[$i]); break;
					case 8: $this->comp_name_s = $this->get_compname_s($ret[$i]); break;
					case 9: $this->dividend_date = $this->get_dividend($ret[$i]); break;
					case 10: $this->num_dividend = $this->get_numdivd($ret[$i]); break;
					case 11: $this->index_flg = $this->get_index($ret[$i]); break;
					case 12: $this->index_code = $this->get_index($ret[$i]); break;
					case 13: $this->index_name = $this->get_index($ret[$i]); break;
					case 14: $this->ms_code = $this->get_index($ret[$i]); break;
					case 15: $this->ms_name = $this->get_index($ret[$i]); break;
					case 16: $this->tesuryou = $this->get_tesuryou($ret[$i]); break;
				}
			}
			$sql = "replace into ms_fund values(";
			$sql .= "'" . $this->fund_code . "',";
			$sql .= "'" . $this->ita_code . "',";
			$sql .= "'" . $this->fund_name . "',";
			$sql .= "'" . $this->nick_name . "',";
			$sql .= "'" . $this->code[0] . "',";
			$sql .= "'" . $this->code[1] . "',";
			$sql .= "'" . $this->code[2] . "',";
			$sql .= "'" . $this->code[3] . "',";
			$sql .= "'" . $this->code[4] . "',";
			$sql .= "'" . $this->code[5] . "',";
			$sql .= "'" . $this->code[6] . "',";
			$sql .= "'" . $this->code[7] . "',";
			$sql .= "'" . $this->code[8] . "',";
			$sql .= "'" . $this->code[9] . "',";
			$sql .= "'" . $this->code[10] . "',";
			$sql .= "'" . $this->code[11] . "',";
			$sql .= "'" . $this->code[12] . "',";
			$sql .= "'" . $this->code[13] . "',";
			$sql .= "'" . $this->code[14] . "',";
			$sql .= "'" . $this->code[15] . "',";
			$sql .= "'" . $this->code[16] . "',";
			$sql .= "'" . $this->code[17] . "',";
			$sql .= "'" . $this->code[18] . "',";
			$sql .= "'" . $this->code[19] . "',";
			$sql .= "'" . $this->code[20] . "',";
			$sql .= "'" . $this->code[21] . "',";
			$sql .= "'" . $this->settei_date . "',";
			if(strlen($this->shokan_date) == 0){
				$sql .= "null,";
			}else{
				$sql .= "'" . $this->shokan_date . "',";
			}
			$sql .= "'" . $this->comp_name . "',";
			$sql .= "'" . $this->comp_name_s . "',";
			if(strlen($this->dividend_date) == 0){
				$sql .= "null,";
			}else{
				$sql .= "'" . $this->dividend_date . "',";
			}
			$sql .= $this->num_dividend . ",";
			$sql .= "'" . $this->index_flg . "',";
			$sql .= "'" . $this->index_code . "',";
			$sql .= "'" . $this->index_name . "',";
			$sql .= "'" . $this->ms_code . "',";
			$sql .= "'" . $this->ms_name . "',";
			$sql .= "'" . $this->tesuryou . "');";
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
$proc = new jnb01;
$proc->startProcess();
?>
