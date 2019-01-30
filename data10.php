#!/usr/local/bin/php -q
<?php
class jnb10{
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
		$fn_moto = $this->path . "dat/jnb10.csv";
		if(file_exists($fn_moto) == false){
			printf("01ファイルありません\n");
			return;
		}
		$fn_saki = $this->path . "dat/jnb10.csv." . $ymd;
		$comm = "/bin/nkf -wLu " . $fn_moto . " > " . $fn_saki;
		system($comm);
printf("%s\n", $fn_saki);
		$fp = fopen($fn_saki, "r");
		fgetcsv($fp, 4000, ",");
		while(($ret = fgetcsv($fp, 4000, ",")) !== FALSE) {
			$cnt = count($ret);
			if($cnt < 30){
				printf("データ除外%d\n", $cnt);
				continue;
			}
			for($i = 0; $i < $cnt; $i++){
				switch($i){
					case 0:	$this->fund_code = $this->get_fundcode($ret[$i]); break;
					case 1:	$this->standard_date = $this->get_standard_date($ret[$i]); break;
					case 2:	$this->p1m = $this->get_val($ret[$i]); break;
					case 3:	$this->p3m = $this->get_val($ret[$i]); break;
					case 4:	$this->p6m = $this->get_val($ret[$i]); break;
					case 5:	$this->p1a = $this->get_val($ret[$i]); break;
					case 6:	$this->p3a = $this->get_val($ret[$i]); break;
					case 7:	$this->p5a = $this->get_val($ret[$i]); break;
					case 8:	$this->p3mterm = $this->get_term($ret[$i]); break;
					case 9:	$this->p6mterm = $this->get_term($ret[$i]); break;
					case 10:$this->p1term = $this->get_term($ret[$i]); break;
					case 11:$this->p3term = $this->get_term($ret[$i]); break;
					case 12:$this->p5term = $this->get_term($ret[$i]); break;
					case 13:$this->std1 = $this->get_val($ret[$i]); break;
					case 14:$this->std3 = $this->get_val($ret[$i]); break;
					case 15:$this->std5 = $this->get_val($ret[$i]); break;
					case 16:$this->sr1 = $this->get_val($ret[$i]); break;
					case 17:$this->sr3 = $this->get_val($ret[$i]); break;
					case 18:$this->sr5 = $this->get_val($ret[$i]); break;
					case 19:$this->a1 = $this->get_val($ret[$i]); break;
					case 20:$this->a3 = $this->get_val($ret[$i]); break;
					case 21:$this->a5 = $this->get_val($ret[$i]); break;
					case 22:$this->b1 = $this->get_val($ret[$i]); break;
					case 23:$this->b3 = $this->get_val($ret[$i]); break;
					case 24:$this->b5 = $this->get_val($ret[$i]); break;
					case 25:$this->t1 = $this->get_val($ret[$i]); break;
					case 26:$this->t3 = $this->get_val($ret[$i]); break;
					case 27:$this->t5 = $this->get_val($ret[$i]); break;
					case 28:$this->i1 = $this->get_val($ret[$i]); break;
					case 29:$this->i3 = $this->get_val($ret[$i]); break;
					case 30:$this->i5 = $this->get_val($ret[$i]); break;
					case 31:$this->rat = $this->get_val($ret[$i]); break;
				}
			}
			$sql = "replace into ms_tech values(";
			$sql .= "'" . $this->fund_code . "',";
			$sql .= "'" . $this->standard_date . "',";
			if(strlen($this->p1m) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->p1m . ",";
			}
			if(strlen($this->p3m) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->p3m . ",";
			}
			if(strlen($this->p6m) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->p6m . ",";
			}
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
			$sql .= "'" . $this->p3mterm . "',";
			$sql .= "'" . $this->p6mterm . "',";
			$sql .= "'" . $this->p1term . "',";
			$sql .= "'" . $this->p3term . "',";
			$sql .= "'" . $this->p5term . "',";
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
				$sql .= "null,";
			}else{
				$sql .= $this->std5 . ",";
			}
			if(strlen($this->sr1) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->sr1 . ",";
			}
			if(strlen($this->sr3) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->sr3 . ",";
			}
			if(strlen($this->sr5) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->sr5 . ",";
			}
			if(strlen($this->a1) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->a1 . ",";
			}
			if(strlen($this->a3) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->a3 . ",";
			}
			if(strlen($this->a5) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->a5 . ",";
			}
			if(strlen($this->b1) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->b1 . ",";
			}
			if(strlen($this->b3) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->b3 . ",";
			}
			if(strlen($this->b5) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->b5 . ",";
			}
			if(strlen($this->t1) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->t1 . ",";
			}
			if(strlen($this->t3) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->t3 . ",";
			}
			if(strlen($this->t5) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->t5 . ",";
			}
			if(strlen($this->i1) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->i1 . ",";
			}
			if(strlen($this->i3) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->i3 . ",";
			}
			if(strlen($this->i5) == 0){
				$sql .= "null,";
			}else{
				$sql .= $this->i5 . ",";
			}
			if(strlen($this->rat) == 0){
				$sql .= "null);";
			}else{
				$sql .= "'" . $this->rat . "');";
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
$proc = new jnb10;
$proc->startProcess();
?>
