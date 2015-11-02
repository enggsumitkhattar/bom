<?php

class Pdb extends PDO {

    private $error;
    private $sql;
    private $bind;
    private $errorCallbackFunction;
    private $errorMsgFormat;

    public function __construct() {
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        /** Datebase connection * */
	//elseif(strpos($_SERVER['REQUEST_URI'], 'api') !== false){
			//parent::__construct("mysql:host=beaboss.cqnjfbadyisb.us-east-1.rds.amazonaws.com;dbname=BeABoss;charset=utf8", "badmin", "BeABossweb", $options);
		//}
        try {
            if ($_SERVER['HTTP_HOST'] == 'localhost'){
                parent::__construct("mysql:host=localhost;dbname=businessonmobile;charset=utf8", "root", "", $options);
            } else {
                parent::__construct("mysql:host=businessonmobile.cn8ijtna1jrf.us-west-2.rds.amazonaws.com;dbname=businessonmobile;charset=utf8", "businessonmobile", "12345qwert", $options);
            	
	    }

        } catch (PDOException $e) {
            $this->error = $e->getMessage();
        }
    }

    private function debug() {
        if (!empty($this->errorCallbackFunction)) {
            $error = array("Error" => $this->error);
            if (!empty($this->sql))
                $error["SQL Statement"] = $this->sql;
            if (!empty($this->bind))
                $error["Bind Parameters"] = trim(print_r($this->bind, true));

            $backtrace = debug_backtrace();
            if (!empty($backtrace)) {
                foreach ($backtrace as $info) {
                    if ($info["file"] != __FILE__)
                        $error["Backtrace"] = $info["file"] . " at line " . $info["line"];
                }
            }

            $msg = "";
            if ($this->errorMsgFormat == "html") {
                if (!empty($error["Bind Parameters"]))
                    $error["Bind Parameters"] = "<pre>" . $error["Bind Parameters"] . "</pre>";
                $css = trim(file_get_contents(dirname(__FILE__) . "/error.css"));
                $msg .= '<style type="text/css">' . "\n" . $css . "\n</style>";
                $msg .= "\n" . '<div class="db-error">' . "\n\t<h3>SQL Error</h3>";
                foreach ($error as $key => $val)
                    $msg .= "\n\t<label>" . $key . ":</label>" . $val;
                $msg .= "\n\t</div>\n</div>";
            }
            elseif ($this->errorMsgFormat == "text") {
                $msg .= "SQL Error\n" . str_repeat("-", 50);
                foreach ($error as $key => $val)
                    $msg .= "\n\n$key:\n$val";
            }

            $func = $this->errorCallbackFunction;
            $func($msg);
        }
    }

    public function delete($table, $where = "", $bind = "", $debug = false) {
        $sql = "DELETE FROM " . $table . " WHERE " . $where . ";";
        return $this->query($sql, $bind, $debug);
    }

    private function filter($table, $info) {
        $driver = $this->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver == 'sqlite') {
            $sql = "PRAGMA table_info('" . $table . "');";
            $key = "name";
        } elseif ($driver == 'mysql') {
            $sql = "DESCRIBE " . $table . ";";
            $key = "Field";
        } else {
            $sql = "SELECT column_name FROM information_schema.columns WHERE table_name = '" . $table . "';";
            $key = "column_name";
        }

        if (false !== ($list = $this->query($sql))) {
            $fields = array();
            foreach ($list as $record)
                $fields[] = $record[$key];
            return array_values(array_intersect($fields, array_keys($info)));
        }
        return array();
    }

    private function cleanup($bind) {
        if (!is_array($bind)) {
            if (!empty($bind))
                $bind = array($bind);
            else
                $bind = array();
        }
        return $bind;
    }

    public function insert($table, $info, $debug = false) {
        $bind = array();
        $cols = $fields = $this->filter($table, $info);

        if ($cols) {
            foreach ($cols as $i => $f)
                $cols[$i] = "`" . $f . "`";
        }

        $sql = "INSERT INTO " . $table . " (" . implode($cols, ", ") . ") VALUES (:" . implode($fields, ", :") . ");";
        foreach ($fields as $field)
            $bind[":$field"] = $info[$field];
        return $this->query($sql, $bind, $debug);
    }

    public function multipleInsert($table, $info, $debug = false) {
        $bind = array();
        $cols = $fields = $this-filter($table, $info[0]);
        
        if($cols){
            foreach ($cols as $i => $f)
                $cols[$i] = "`" . $f . "`";
        }
    }

    public function placeholder($text, $count = 0, $separator = ',') {
        $result = array();

        if ($count > 0) {
            for ($x = 0; $x < $count; $x++) {
                $result[] = $text;
            }
        }

        return implode($separator, $result);
    }

    public function query($sql, $bind = "", $debug = false, $fetchNum = false) {
        $this->sql = trim($sql);

        $this->bind = $this->cleanup($bind);
        $this->error = "";

        try {
            $pdostmt = $this->prepare($this->sql);

            if ($pdostmt->execute($this->bind) !== false) {
                if ($debug)
                    echo $pdostmt->queryString . '<br><br>';

                $q = strtolower(substr($this->sql, 0, 6));
                //descri means describe
                if ($q == "select" || $q == "descri" || $q == "pragma") {
                    if ($fetchNum)
                        return $pdostmt->fetchAll(PDO::FETCH_NUM);
                    else
                        return $pdostmt->fetchAll(PDO::FETCH_ASSOC);
                }
                else if ($q == "delete" || $q == "insert" || $q == "update") {
                    $id = $this->lastInsertId();
                    if ($id)
                        return $id;
                    else
                        return $pdostmt->rowCount();
                }
            }
        } catch (PDOException $e) {
            $this->error = $e->getMessage();

            if ($debug) {
                echo $pdostmt->queryString . '<br><br>';
                echo ( $this->error . '<br>' );

                $this->debug();
            }

            return false;
        }
    }

    public function pagedQuery($sql, $pageno = 1, $pagesize = 50, $bind = "", $debug = false, $fetchNum = false) {
        try {
            $pdostmt = $this->prepare($sql);
            $pdostmt->execute();
            $total_records = $pdostmt->rowCount();
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            if ($debug)
                echo $this->error;
            return array();
        }

        $start = 0;
        if ($pageno) {
            $lastchar = substr($sql, -1, 1);

            if ($lastchar == ';')
                $sql = substr($sql, 0, -1);

            $start = $pagesize * ($pageno - 1);
            $sql = $sql . " LIMIT " . $start . ", " . $pagesize . ";";
        }

        $list['result'] = $this->query($sql, $bind, $debug);

        $total = count($list['result']);
        if ($total) {
            $list['page']['cur_page'] = $pageno;
            $list['page']['total_records'] = $total_records;
            $list['page']['total'] = $total;
            $list['page']['total_pages'] = ceil((float) $total_records / (float) $pagesize);
            $list['page']['start'] = $start;

            return $list;
        } else
            return false;
    }

    public function select($table, $where = "", $fields = "*", $bind = "", $debug = false, $fetchNum = false) {
        if ($fields == "")
            $fields = "*";

        $sql = "SELECT " . $fields . " FROM " . $table;
        if (!empty($where))
            $sql .= " WHERE " . $where;
        $sql .= ";";
        return $this->query($sql, $bind, $debug, $fetchNum);
    }

    public function singleRow($table, $where = "", $fields = "*", $bind = "", $debug = false, $fetchNum = false) {
        if ($fields == "")
            $fields = "*";

        $sql = "SELECT " . $fields . " FROM " . $table;
        if (!empty($where))
            $sql .= " WHERE " . $where;
        $sql .= " LIMIT 0,1;";
        $rows = $this->query($sql, $bind, $debug, $fetchNum);
        if ($rows && isset($rows[0]))
            return $rows[0];
    }

    public function singleVal($table, $where = "", $fields = "*", $bind = "", $debug = false, $fetchNum = false) {
        if ($fields == "")
            $fields = "*";

        $sql = "SELECT " . $fields . " FROM " . $table;
        if (!empty($where)){
           if(empty($bind))
            $sql .= " WHERE " . $where;
           else{
             echo $bind;
             echo $sql .= " WHERE " . $where;exit;
           }  
        }   
        $sql .= " LIMIT 0,1;";
        $rows = $this->query($sql, $bind, $debug, $fetchNum);
        if ($rows && isset($rows[0][$fields]))
            return $rows[0][$fields];
    }

    public function setErrorCallbackFunction($errorCallbackFunction, $errorMsgFormat = "html") {
        //Variable functions for won't work with language constructs such as echo and print, so these are replaced with print_r.
        if (in_array(strtolower($errorCallbackFunction), array("echo", "print")))
            $errorCallbackFunction = "print_r";

        if (function_exists($errorCallbackFunction)) {
            $this->errorCallbackFunction = $errorCallbackFunction;
            if (!in_array(strtolower($errorMsgFormat), array("html", "text")))
                $errorMsgFormat = "html";
            $this->errorMsgFormat = $errorMsgFormat;
        }
    }

    public function update($table, $info, $where, $bind = "", $debug = false) {
        $cols = $fields = $this->filter($table, $info);
        $fieldSize = sizeof($fields);

        if ($cols) {
            foreach ($cols as $i => $f)
                $cols[$i] = "`" . $f . "`";
        }

        $sql = "UPDATE " . $table . " SET ";
        for ($f = 0; $f < $fieldSize; ++$f) {
            if ($f > 0)
                $sql .= ", ";
            $sql .= $cols[$f] . " = :update_" . $fields[$f];
        }
        $sql .= " WHERE " . $where . ";";

        $bind = $this->cleanup($bind);
        foreach ($fields as $field)
            $bind[":update_$field"] = $info[$field];

        return $this->query($sql, $bind, $debug);
    }

}

?>
