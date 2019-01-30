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
				$sql = "select p.calc_standard_date, p.ann_1y, p.ann_3y, p.ann_5y, ";
				$sql .= "s.ann_1y s1, s.ann_3y s3, s.ann_5y s5, ";
				$sql .= "sr.value_1y sr1, sr.value_3y sr3, sr.value_5y sr5, ";
				$sql .= "a.ann_1y a1, a.ann_3y a3, a.ann_5y a5, ";
				$sql .= "b.value_1y b1, b.value_3y b3, b.value_5y b5, ";
				$sql .= "t.ann_1y t1, t.ann_3y t3, t.ann_5y t5, ";
				$sql .= "i.ann_1y i1, i.ann_3y i3, i.ann_5y i5 ";
				$sql .= "from lipper_f_tech_monthly_P p ";
				$sql .= ",lipper_f_tech_monthly_Std s ";
				$sql .= ",lipper_f_tech_monthly_Shr sr ";
				$sql .= ",lipper_f_tech_monthly_A a ";
				$sql .= ",lipper_f_tech_monthly_B b ";
				$sql .= ",lipper_f_tech_monthly_T t ";
				$sql .= ",lipper_f_tech_monthly_I i ";
				$sql .= "where p.lipper_id = '" . $row2['lipper_id'] . "' ";
				$sql .= "and p.lipper_id = s.lipper_id ";
				$sql .= "and p.lipper_id = sr.lipper_id ";
				$sql .= "and p.lipper_id = a.lipper_id ";
				$sql .= "and p.lipper_id = b.lipper_id ";
				$sql .= "and p.lipper_id = t.lipper_id ";
				$sql .= "and p.lipper_id = i.lipper_id ";
				$sql .= "and p.calc_standard_date = s.calc_standard_date ";
				$sql .= "and p.calc_standard_date = sr.calc_standard_date ";
				$sql .= "and p.calc_standard_date = a.calc_standard_date ";
				$sql .= "and p.calc_standard_date = b.calc_standard_date ";
				$sql .= "and p.calc_standard_date = t.calc_standard_date ";
				$sql .= "and p.calc_standard_date = i.calc_standard_date ";
				$sql .= ";";
				$res2 = mysqli_query($con, $sql);
				if(!$res2) {
					$message  = 'Invalid query: ' . mysqli_error($con) . "\n";
					$message .= 'Whole query: ' . $sql;
					printf("%s\n", $message);
					return;
				}
				while($row3 = mysqli_fetch_assoc($res2)) {
					$ins = "replace into ms_tech values(";
					$ins .= "'" . $row['fund_code'] . "',";
					$ins .= "'" . $row3['calc_standard_date'] . "',";
					if(strlen($row3['ann_1y']) == 0){
						continue;
						$ins .= "null,";
					}else{
						$ins .= $row3['ann_1y'] . ",";
					}
					if(strlen($row3['ann_3y']) == 0){
						$ins .= "null,";
					}else{
						$ins .= $row3['ann_3y'] . ",";
					}
					if(strlen($row3['ann_5y']) == 0){
						$ins .= "null,";
					}else{
						$ins .= $row3['ann_5y'] . ",";
					}
					$ins .= "null,";
					$ins .= "null,";
					$ins .= "null,";
					if(strlen($row3['s1']) == 0){
						$ins .= "null,";
					}else{
						$ins .= $row3['s1'] . ",";
					}
					if(strlen($row3['s3']) == 0){
						$ins .= "null,";
					}else{
						$ins .= $row3['s3'] . ",";
					}
					if(strlen($row3['s5']) == 0){
						$ins .= "null,";
					}else{
						$ins .= $row3['s5'] . ",";
					}
					if(strlen($row3['sr1']) == 0){
print_r($row3);
printf("%s\n", $row['lipper_id']);
continue;
						$ins .= "null,";
					}else{
						$ins .= $row3['sr1'] . ",";
					}
					if(strlen($row3['sr3']) == 0){
						$ins .= "null,";
					}else{
						$ins .= $row3['sr3'] . ",";
					}
					if(strlen($row3['sr5']) == 0){
						$ins .= "null,";
					}else{
						$ins .= $row3['sr5'] . ",";
					}
					if(strlen($row3['a1']) == 0){
						$ins .= "null,";
					}else{
						$ins .= $row3['a1'] . ",";
					}
					if(strlen($row3['a3']) == 0){
						$ins .= "null,";
					}else{
						$ins .= $row3['a3'] . ",";
					}
					if(strlen($row3['a5']) == 0){
						$ins .= "null,";
					}else{
						$ins .= $row3['a5'] . ",";
					}
					if(strlen($row3['b1']) == 0){
						$ins .= "null,";
					}else{
						$ins .= $row3['b1'] . ",";
					}
					if(strlen($row3['b3']) == 0){
						$ins .= "null,";
					}else{
						$ins .= $row3['b3'] . ",";
					}
					if(strlen($row3['b5']) == 0){
						$ins .= "null,";
					}else{
						$ins .= $row3['b5'] . ",";
					}
					if(strlen($row3['t1']) == 0){
						$ins .= "null,";
					}else{
						$ins .= $row3['t1'] . ",";
					}
					if(strlen($row3['t3']) == 0){
						$ins .= "null,";
					}else{
						$ins .= $row3['t3'] . ",";
					}
					if(strlen($row3['t5']) == 0){
						$ins .= "null,";
					}else{
						$ins .= $row3['t5'] . ",";
					}
					if(strlen($row3['i1']) == 0){
						$ins .= "null,";
					}else{
						$ins .= $row3['i1'] . ",";
					}
					if(strlen($row3['i3']) == 0){
						$ins .= "null,";
					}else{
						$ins .= $row3['i3'] . ",";
					}
					if(strlen($row3['i5']) == 0){
						$ins .= "null);";
					}else{
						$ins .= $row3['i5'] . ");";
					}
printf("%s\n", $ins);
					$res3 = mysqli_query($ms, $ins);
					if(!$res3) {
						$message  = 'Invalid query: ' . mysqli_error($ms) . "\n";
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
