<?php
### dbconn() : DB접속
### error() : 오류출력
### result($result) : 결과물 검사
### recordCount($query) : 해당쿼리 개수
### passType($password) : 해당변수 MySQL 암호화하기
### nowTime() : mysql Type 시간
### Select($query, $how) : $how(row, field) 에 따라 결과물 얻음. 여러개일경우 배열로..
### Insert($data, $table) : insert문.. ($data[])
### Update($data, $table, $where) : update문.. ($data[])
### dbclose() : DB닫기

class cDB {

    function getMicroTime() {
      $microtimestmp = explode(" ",microtime());
      return $microtimestmp[0]+$microtimestmp[1];
    }

	//데이타베이스에 접속
	function cDB($db_setting, $transaction = false){
        //$this->err_report = false;
        $this->transaction = $transaction;
        $this->query_count = 0;
		
		if(DEBUG) $debug[db_conn_stime]=$this->getMicroTime();


		$db_host=$db_setting[DB_HOST];
		$db_id=$db_setting[DB_ID];
		$db_pass=$db_setting[DB_PW];
		$db_name=$db_setting[DB_NAME];
		$this->conn = mysql_connect($db_host,$db_id,$db_pass) or $this->Error("MySQL - DB Connect Error!!!","echo");
		if(mysql_error()) $this->Error(mysql_error());
		mysql_select_db($db_name, $this->conn) or $this->Error("MySQL - DB Select Error!!!","echo");

        if($transaction) {
            mysql_query('SET AUTOCOMMIT=0', $this->conn) or exit('mysql_transaction_autocommit error');
            mysql_query('BEGIN', $this->conn) or exit('mysql_transaction_begin error');
            register_shutdown_function(array(&$this, 'close_error'));
        }

		$this->query("set names utf8");

		if(DEBUG){
			$debug[db_conn_etime]=$this->getMicroTime()-$debug[db_conn_stime];
			$GLOBALS[db_conn_time]+=$debug[db_conn_etime];
		}

		return true;
	}

/*
    function MyDB($db_setting, $transaction = false)
    {
        $this->err_report = false;
        $this->transaction = $transaction;
        $this->query_count = 0;
        static $db_set_file = array();

        if(false === array_key_exists($db_setting, $db_set_file))
            $db_set_file[$db_setting] = parse_ini_file($db_setting);
        $myset = &$db_set_file[$db_setting];
        $this->conn = mysql_connect($myset['ip'], $myset['user'], $myset['pass']) or exit('mysql_connect error');
        mysql_select_db($myset['db'], $this->conn) or exit('mysql_select_db error');
        if($transaction) {
            mysql_query('SET AUTOCOMMIT=0', $this->conn) or exit('mysql_transaction_autocommit error');
            mysql_query('BEGIN', $this->conn) or exit('mysql_transaction_begin error');
            register_shutdown_function(array(&$this, 'close_error'));
        }
        return true;
    }
*/



	//결과물 검사
	function result($result){
		if(!$result){
			$this->Error(mysql_error());
		exit;
		}
	}

	//오류 출력
	function Error($err=null, $query){
		if(mysql_error()) echo "Cause : "."[".mysql_errno()."] ".mysql_error()." ($query)<br>";
		else echo $err;
		if(!DEBUG) exit;
	}
    function transaction_error($message = 'mysql_error exit'){
        if($this->conn === false) return false;
        if($this->transaction) mysql_query('ROLLBACK', $this->conn) or $this->Error('mysql_transaction_rollback error');
        mysql_close($this->conn) or $this->Error('mysql_close_error error');
        return true;
    }
	
	/*
    function close()
    {
        if($this->conn === false)
            return false;
        if($this->transaction)
            mysql_query('COMMIT', $this->conn) or exit('mysql_transaction_commit error');
        mysql_close($this->conn) or exit('mysql_close error');
        $this->conn = false;
        return true;
    }*/



	#Query()
	function Query($query){
		if(DEBUG) $temp[db_stime]=$this->getMicroTime();

		$this->query_count++;

		$ret=mysql_query($query, $this->conn);
		if(mysql_error()) return $this->Error(mysql_error($this->conn), 'Query : '.$query.' (count:'.$this->query_count.')');
		else return $ret;

		if(DEBUG){
			$temp[db_etime]=$this->getMicroTime()-$temp[db_stime];
			$GLOBALS[db_time]+=$temp[db_etime];
		}
	}


	//데이터 불러오기(1row or 1field)
	function Select($query, $how="field", $object="array"){
		if(DEBUG) $debug[db_stime]=$this->getMicroTime();

		$ret=mysql_query($query, $this->conn);
		if(mysql_error()) return $this->error($query);

		if($how == "row") $ret_cnt=mysql_num_rows($ret);
		elseif($how == "field") $ret_cnt=mysql_num_fields($ret);
		else $this->Error("Can not use '$how' command.");

		if($how=="row"){
			while($row_data=mysql_fetch_row($ret)){
				$ret_data[]=$row_data[0];
			}
		}else{
			if($object=="obj") $ret_data=mysql_fetch_object($ret);
			else $ret_data=mysql_fetch_array($ret);
		}

		if(DEBUG){
			$debug[db_etime]=$this->getMicroTime()-$debug[db_stime];
			$GLOBALS[db_time]+=$debug[db_etime];
		}

		if($ret_cnt == 0) $this->Error("Empty.. ($query)");
		if($ret_cnt == 1) return $ret_data[0];
		else return $ret_data;
	}




	function getDateFormat($date, $type="%Y-%m-%d"){
		return $this->Select("select date_format('".$date."', '".$type."')");
	}
	function getTimeFormat() {
		$ret=mysql_fetch_row(mysql_query("select now()"));
		return $ret[0];
	}

	//데이타를 디비로 입력하기
	function Insert($data, $table) {
		//$query = mysql_query("select no from $table");
		$query=1;
		if($query) {
			$i=0;
			while(list($key,$val)=each($data)) {
				if($i=='0') {
					$values="'$val'";
					$keys="$key";
				}else {
					$values.=", '$val'";
					$keys.=", $key";
				}
				$i++;
			}
			$ret = $this->Query("insert into $table ($keys) values ($values)");
			Return mysql_insert_id();
		}else {
			return false;
		}
	}

	//데이타를 디비로 업데이트하기
	function Update($data, $table, $where, $update_key="no") {
		//$query=mysql_query("select no from $table");
		$query = 1;
		if($query) {
			$i=0;
			while(list($key,$val)=each($data)) {
				if($i=='0') {
					$update_data="$key = '$val'";
				}else {
					$update_data.=", $key = '$val'";
				}
				$i++;
			}
			$ret = $this->Query("update $table set $update_data where $update_key='$where'");
			return true;
		}else {
			return false;
		}
	}

	//데이타 삭제하기
	function DeleteNo($no,$table) {
		$query=mysql_query("select * from $table where no='$no'");
		if($query) {
			$ret = $this->Query("delete from $table where no='$no'");
			Return true;
		}else {
			echo"에러입니다.. Delete ERROR!";
			exit;
			Return true;
		}
	}

	//$password를 Mysql Password 형태로 리턴한다.
	function getPassword($password) {
		$ret=mysql_fetch_row(mysql_query("select password('".$password."')"));
		return $ret[0];
	}

	//해당 쿼리 결과물 개수
	function recordCount($query){
		return mysql_num_rows(mysql_query($query));
	}

	//DB닫기
	function dbclose(){
		global $conn;
		if($conn) {
			@mysql_close($conn);
			unset($conn);
		}
	}

}
?>