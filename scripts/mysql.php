<?php
	class MySql {
        
        #region "Protected Members/Functions"
        
		protected $_db;
		protected $_result;
        protected $_connected;
        
        protected function _doConnect($type, $table) {
            $user = 'pcms_'.$type.'_'.$table;
            $pass = 'pcms_'.$type.'_'.$table.'_long_password_for_entropy';
            return $this->reconnect($user, $pass);
        }
        
        protected function _delete($table, $whereColumns = null, $whereValues = null) {
			if ((($whereColumns != null) && ((is_array($whereColumns) && !is_array($whereValues)) ||
                (is_array($whereValues) && !is_array($whereColumns))))) {
                die('Programming error: where clause values passed are not both arrays.');
            }
			if ((($whereColumns != null) && is_array($whereColumns)) && (count($whereColumns) != count($whereValues))) {
                die('Programming error: where clause value count does not match column count.');
			}
			if ($whereValues != null) {
				if (is_array($whereValues)) {
					$cnt = count($whereValues);
					for($i = 0; $i < $cnt; $i++) {
						$whereValues[$i] = $this->escape($whereValues[$i]);
					}
				} else {
					$whereValues = $this->escape($whereValues);
				}
			}
            $query = 'DELETE FROM '.$table;
			if ($whereColumns) {
				$query .= ' WHERE ';
				if (is_array($whereColumns)) {
					$cnt = count($whereColumns);
					for ($i = 0; $i < $cnt; $i++) {
						$query .= $whereColumns[$i]."='".$whereValues[$i]."'";
						if ($i < $cnt - 1) { $query .= " AND "; }
					}
				} else {
					$query .= $whereColumns."='".$whereValues."'";
				}
			}
            return $this->rawExecute($query);
		}
        
        protected function _insert($table, $columns, $values, $checkExists = false) {
			if (!$this->_connected) { return 'Database not connected.'; }
			if (((is_array($columns) && !is_array($values)) || (is_array($values) && !is_array($columns)))) {
				die('Programming error: values passed are not both arrays.');
			}
			if (is_array($columns) && (count($columns) != count($values))) {
                die('Programming error: value count does not match column count.');
            }
			if (is_array($values)) {
				$cnt = count($values);
				for($i = 0; $i < $cnt; $i++) {
                    $values[$i] = $this->escape($values[$i]);
				}
			} else {
				$values = $this->escape($values);
			}
			if ($checkExists && $this->rowExists($table, $columns, $values)) { return 'Value exists.'; }
			$query = 'INSERT INTO '.$table.' ('.(is_array($columns) ? (implode(",", $columns)) : ($columns)).') ';
			$query .= 'VALUES ('.(is_array($values) ? ("'".implode("','", $values)."'") : ("'".$values."'")).')';
			$rcnt = (($this->_db->query($query)) ? $this->_db->affected_rows : 0);
			return (($rcnt > 0) ? '' : $this->_db->error);
		}
		
		protected function _update($table, $columns, $values, $whereColumns = null, $whereValues = null) {
			if (((is_array($columns) && !is_array($values)) || (is_array($values) && !is_array($columns))) ||
				($whereColumns && ((is_array($whereColumns) && !is_array($whereValues)) ||
                (is_array($whereValues) && !is_array($whereColumns))))) {
                die('Programming error: values passed are not both arrays.');
            }
			if (is_array($columns) && (count($columns) != count($values))) {
                die('Programming error: value count does not match column count.');
			}
			if (($whereColumns != null && is_array($whereColumns)) && (count($whereColumns) != count($whereValues))) {
                die('Programming error: where clause value count does not match column count.');
			}
			if (is_array($values)) {
				$cnt = count($values);
				for($i = 0; $i < $cnt; $i++) {
					$values[$i] = $this->escape($values[$i]);
				}
			} else {
				$values = $this->escape($values);
			}
			if ($whereValues != null) {
				if (is_array($whereValues)) {
					$cnt = count($whereValues);
					for($i = 0; $i < $cnt; $i++) {
						$whereValues[$i] = $this->escape($whereValues[$i]);
					}
				} else {
					$whereValues = $this->escape($whereValues);
				}
			}
			$query = 'UPDATE '.$table.' SET ';
			if (is_array($columns)) {
				$cnt = count($columns);
				for ($i = 0; $i < $cnt; $i++) {
					$query .= $columns[$i]."='".$values[$i]."'";
					if ($i < $cnt - 1) { $query .= ","; }
				}
			} else {
				$query .= $columns."='".$values."'";
			}
			if ($whereColumns) {
				$query .= ' WHERE ';
				if (is_array($whereColumns)) {
					$cnt = count($whereColumns);
					for ($i = 0; $i < $cnt; $i++) {
						$query .= $whereColumns[$i]."='".$whereValues[$i]."'";
						if ($i < $cnt - 1) { $query .= " AND "; }
					}
				} else {
					$query .= $whereColumns."='".$whereValues."'";
				}
			}            
			$rcnt = (($this->_db->query($query)) ? $this->_db->affected_rows : 0);
			return (($rcnt > 0) ? '' : $this->_db->error);
		}
		
        protected function _select($table, $columns, $whereColumns = null, $whereValues = null) {
			if ((($whereColumns != null) && ((is_array($whereColumns) && !is_array($whereValues)) ||
                (is_array($whereValues) && !is_array($whereColumns))))) {
                die('Programming error: where clause values passed are not both arrays.');
            }
			if ((($whereColumns != null) && is_array($whereColumns)) && (count($whereColumns) != count($whereValues))) {
                die('Programming error: where clause value count does not match column count.');
			}
			if ($whereValues != null) {
				if (is_array($whereValues)) {
					$cnt = count($whereValues);
					for($i = 0; $i < $cnt; $i++) {
						$whereValues[$i] = $this->escape($whereValues[$i]);
					}
				} else {
					$whereValues = $this->escape($whereValues);
				}
			}
			$query = 'SELECT ';
			if (is_array($columns)) {
				$cnt = count($columns);
				for ($i = 0; $i < $cnt; $i++) {
					$query .= $columns[$i];
					if ($i < $cnt - 1) { $query .= ","; }
				}
			} else {
				$query .= $columns;
			}
            $query .= ' FROM '.$table;
			if ($whereColumns) {
				$query .= ' WHERE ';
				if (is_array($whereColumns)) {
					$cnt = count($whereColumns);
					for ($i = 0; $i < $cnt; $i++) {
						$query .= $whereColumns[$i]."='".$whereValues[$i]."'";
						if ($i < $cnt - 1) { $query .= " AND "; }
					}
				} else {
					$query .= $whereColumns."='".$whereValues."'";
				}
			}
            return $this->query($query);
		}
        
		#endregion
        
        #region "Public Members/Functions"
        
		function __construct() {
        }
        
        function __destruct() {
            if ($this->_connected) { $this->close(); }
        }
		
		public function close() {
            if ($this->_connected) { $this->_db->close(); }
			$this->_connected = false;
		}
		
        public function escape($str) {
            return ($this->_connected ? $this->_db->real_escape_string($str) : $str);
		}
        
        public function isConnected() {
            return $this->_connected;
        }
        
        public function lastError() {
            return ($this->_connected ? $this->_db->error : '');
        }
        
        public function query($query) {
            if (!$this->_connected) { return null; }
			$this->_result = $this->_db->query($query);
			if ($this->_result) {
                $ret = array();
                $r = (($this->_result) ? $this->_result->num_rows : 0);
                for ($i = 0; $i < $r; $i++) {
                    // fetch_assoc() returns an array and moves the underlying stream pointer
                    // to the next row in the query's dataset, so we only need to do a for..loop
                    // just to loop through the objects
                    $row = $this->_result->fetch_assoc();
                    $ret[] = $row;
                }
                return $ret;
            }
			return null;
        }
        
        public function reconnect($user, $pw) {
			if ($this->_connected) { $this->close(); }
			$this->_db = new mysqli('localhost', $user, $pw, 'pcms');
			if ($this->_db->connect_errno) { return 'Could not connect to the database, error number '.$this->_db->connect_errno.'<br>'; }
            if (!$this->_db->set_charset('utf8')) {
                $err = 'Could not set default character set to UFT8, error: '.$this->_db->error;
                $this->close();
                return $err;
            };
			$this->_connected = true;
            return '';
		}
        
        /**
		 * Submit an executable query to the currently connected database (delete, insert, etc).
		 *
		 * Submits a query to the currently connected database. This function is different
		 * from Query in that it does not return the result set. This function is intended
		 * to submit executable query's, in other words, 'INSERT', 'UPDATE', 'DROP'. This
		 * function will submit the query and return true on a successful execution of the query
		 *
		 * @param	$query	string	Submit an executable query to the currently connected database
		 * 
		 * @returns 	True on sucess, false otherwise
		 */
		public function rawExecute($query) {
			if (!$this->_connected) { return 'Database not connected.'; }
			$this->_result = $this->_db->query($query);
			if ($this->_result) { return ''; }
			return $this->_db->error;
		}
        
		/**
		 * Submit a query to the currently connected database.
		 *
		 * Submits a query to the currently connected database. What is returned is a
		 * mysqli result set, which can be called by doing a couple of things
		 * Example:
		 *   $Result = $db->Query("SELECT * FROM dbname");
		 *   $Rows = (($Result) ? $Result->num_rows : 0);
		 *   for ($i = 0; $i < $Rows; $i++) {
		 *      // fetch_assoc() returns an array and moves the underlying stream pointer
		 *      // to the next row in the query's dataset, so we only need to do a for..loop
		 *      // just to loop through the objects
		 *   	$Row = $Result->fetch_assoc();
		 *      echo $Row['col1_name'];
		 *      echo $Row['col2_name'];
		 *   }
		 * 
		 * @param	$query	string	Submit a query to the currently connected database
		 * 
		 * @returns 	A mysqli result set
		 */
		public function rawQuery($query) {
			if (!$this->_connected) { return null; }
			$this->_result = $this->_db->query($query);
			if ($this->_result) { return $this->_result; }
			return null;
		}

        #endregion
	}
    
    class SqlDelete extends MySql {
        private $_table;
        
        public function __construct($table='') {
            $this->_table = $table;
            parent::__construct();
        }
        
        public function __destruct() {
            parent::__destruct();
        }
        
        public function connect() {
            return ($this->_connected ? '' : $this->_doConnect('delete', 'users'));
        }
        
        public function delete_on($table, $whereColumns, $whereValues) {
            return $this->_delete($table, $whereColumns, $whereValues);
        }
        
        public function delete($whereColumns, $whereValues) {
            return $this->_delete($this->_table, $whereColumns, $whereValues);
        }
    }
    
    class SqlInsert extends MySql {
        private $_table;
        
        public function __construct($table='') {
            $this->_table = $table;
            parent::__construct();
        }
        
        public function __destruct() {
            parent::__destruct();
        }
        
        public function connect() {
            return ($this->_connected ? '' : $this->_doConnect('insert', 'users'));
        }
        
        public function insert_on($table, $columns, $values, $checkExists = false) {
            return $this->_insert($table, $columns, $values, $checkExists);
        }
        
        public function insert($columns, $values, $checkExists = false) {
            return $this->_insert($this->_table, $columns, $values, $checkExists);
        }
    }
    
    class SqlSelect extends MySql {
        private $_table;
        
        public function __construct($table='') {
            $this->_table = $table;
            parent::__construct();
        }
        
        public function __destruct() {
            parent::__destruct();
        }
        
        public function connect() {
            return ($this->_connected ? '' : $this->_doConnect('select', 'users'));
        }
        
        public function select_on($table, $columns, $whereColumns = null, $whereValues = null) {
            return $this->_select($table, $columns, $whereColumns, $whereValues);
        }
        
        public function select($columns, $whereColumns = null, $whereValues = null) {
            return $this->_select($this->_table, $columns, $whereColumns, $whereValues);
        }
        
        #region "Static Members/Functions"
        
        public static function Password($plain_pass, $table) {
            $ret = null;
            $sqlp = new SqlSelect($table);
            if ($sqlp->connect() == '') {
                $res = $sqlp->query("SELECT PASSWORD('$plain_pass')");
                $ret = (($res != null && is_array($res)) ? $res[0]["PASSWORD('$plain_pass')"] : null);
            }
            $sqlp->close();
            return $ret;
        }
        
        #endregion
    }
    
    class SqlUpdate extends MySql {
        private $_table;
        
        public function __construct($table='') {
            $this->_table = $table;
            parent::__construct();
        }
        
        public function __destruct() {
            parent::__destruct();
        }
        
        public function connect() {
            return ($this->_connected ? '' : $this->_doConnect('update', 'users'));
        }
        
        public function update_on($table, $columns, $values, $whereColumns = null, $whereValues = null) {
            return $this->_update($table, $columns, $values, $whereColumns, $whereValues);
        }
        
        public function update($columns, $values, $whereColumns = null, $whereValues = null) {
            return $this->_update($this->_table, $columns, $values, $whereColumns, $whereValues);
        }
    }
?>
