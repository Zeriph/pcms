<?php
    
    // TODO: need to add a JS hook in the logs.php that then calls this file? (or something)
    // that can grab the info of the logs and update the HTML form elements to reflect it
    // TODO: need to add functionality to allow 'downloading' of the log file currently viewing
    
    class Log {
        public static $DIR = 'logs/';
        
        public static $RAW = 0;
        public static $ERROR = 1;
        public static $ACCESS = 2;
        public static $USER = 4;
        public static $SMBD = 8; // TODO: finish this
        public static $SQL = 16; // TODO: finish this
        
        private static $_file = 'logs/users.log';
        
        #region static functions
        
        public static function Clear($file) {
            if ($file != null && $file != '') {
                $lfile = Log::$DIR.$file;
                if (file_exists($lfile)) {
                    Common::WriteFile($lfile, '');
                    return true;
                }
            }
            return false;
        }
        
        public static function GetLogs($rev) {
            $logs = array();
            foreach (Common::GetAllFiles(Log::$DIR, '.log') as $f) {
                $n = substr($f, strlen(Log::$DIR));
                $logs[] = new LogReader($n, $rev);
            }
            return $logs;
        }
        
        public static function GetValidatedLogName($file_var) {
            $file = trim(htmlentities($file_var));
            if (!Common::StringEndsWith($file, '.log')) { $file .= '.log'; }
            if (!file_exists(Log::$DIR.$file)) { $file = ''; }
            return $file;
        }
        
        public static function WriteRaw($val) {
            Common::WriteFile(Log::$_file, $val."\n", "a");
        }
        
        public static function WriteLine($val) {
            Log::WriteRaw(date("Y/m/d H:i:s").' - '.$val);
        }
        
        #end region
    }

    class LogEntry {
        private $_info1;
        private $_info2;
        private $_info3;
        private $_type = 0;
        private $_line = 0;
        
        public function __construct($etype, $line = 0, $i1 = null, $i2 = null, $i3 = null) {
            $this->_type = $etype;
            $this->_line = $line;
            $this->_info1 = $i1;
            $this->_info2 = $i2;
            $this->_info3 = $i3;
        }
        
        public function isEmpty() {
            return ((($this->_info1 == null || $this->_info1 == '') &&
                     ($this->_info2 == null || $this->_info2 == '') &&
                     ($this->_info3 == null || $this->_info3 == ''))
                     || ($this->_type == 0));
        }
        
        public function toHtml($padlen = 0) {
            $lnstr = '<div class="lcnt">'.str_repeat('0', $padlen - strlen($this->_line)).$this->_line.'</div>';
            $i1 = (($this->_info1 == null || $this->_info1 == '') ? '&nbsp;' : htmlentities($this->_info1));
            $i2 = (($this->_info2 == null || $this->_info2 == '') ? '&nbsp;' : htmlentities($this->_info2));
            $i3 = (($this->_info3 == null || $this->_info3 == '') ? '&nbsp;' : htmlentities($this->_info3));
            switch ($this->_type) {
                case Log::$RAW:
                    return '<div>'.$lnstr.'<div class="linfo">'.$i1."</div></div>\n"; break;
                case Log::$ERROR:
                case Log::$ACCESS:
                case Log::$USER:
                    return '<div>'.$lnstr.'<div>'.$i1.'</div><div>'.$i2.'</div><div class="linfo">'.$i3."</div></div>\n";
                    break; // pedant
                default: break;
            }
            return '';
        }
    }
    
    class LogReader {
        private $_file = '';
        private $_name = '';
        private $_fsize = 0;
        private $_fmtime = 0;
        private $_entries = array();
        private $_type = 0;
        
        public function __construct($file, $rev) {
            $file = Log::$DIR.htmlentities($file);
            $this->_file = $file;
            if (file_exists($file)) {
                $this->_name = basename($file);
                $this->_fsize = filesize($file);
                $this->_fmtime = filemtime($file);
                if ($this->_name == "error.log") {
                    $this->_type = Log::$ERROR;
                } else if ($this->_name == "access.log") { // access.log
                    $this->_type = Log::$ACCESS;
                } else if ($this->_name == "users.log") { // users.log
                    $this->_type = Log::$USER;
                } else {
                    $this->_type = Log::$RAW;
                }
                $data = Common::GetFileContents($file, true);
                if (is_array($data)) {
                    $i = 0;
                    foreach ($data as $val) {
                        $d = trim($val);
                        if ($d == '') { continue; }
                        $this->_entries[] = $this->_parseLine($d, ++$i);
                    }
                } else if ($data != '') {
                    $this->_entries[] = $this->_parseLine($data, 1);
                }
                if (!$rev) { $this->_entries = array_reverse($this->_entries); }
            }
        }
        
        public function getEntryCount(){
            return count($this->_entries);
        }
        
        public function getEntryCountString(){
            $e = count($this->_entries);
            return $e.' line'.(($e > 1 || $e == 0) ? 's' : '');
        }
        
        public function getFileSize() {
            return $this->_fsize;
        }
        
        public function getFileSizeString() {
            return Common::ConvertSizeToString($this->_fsize);
        }
        
        public function getLastModifiedTime() {
            return $this->_fmtime;
        }
        
        public function getLastModified($fmt = 'm/d/Y @ H:i:s') {
            if ($this->_fmtime == 0) { return ''; }
            return date($fmt, $this->_fmtime);
        }
        
        public function getName() {
            return $this->_name;
        }
        
        public function printTable($showLines, $wordWrap) {
            $mcnt = count($this->_entries);
            if ($mcnt == 0) {
                echo '<div><div class="noent">No entries.</div></div>';
            } else {
                $mpad = strlen($mcnt);
                $em = (($mpad > 0) ? ($mpad *10) : 10);
                $linec = ($showLines ? 'table-cell' : 'none');
                $wrapc = ($wordWrap ? ' .linfo { white-space: nowrap; }' : '');
                // print the header
                if ($this->_type == Log::$RAW) {
                    echo '<style>.logentry > div > div:first-child { width:'.$em.'px; display:'.$linec.'; }'.$wrapc.'</style><div class="logentry" id="logentry" name="logentry"><div><div class="lcnt">#</div><div class="linfo">Info</div></div>';
                }
                else {
                $fmt = '<style>.logentry > div > div:first-child { width:'.$em.'px; display:'.$linec.'; }'.$wrapc.'</style><div class="logentry %s" id="logentry" name="logentry"><div><div class="lcnt">#</div><div>Date</div><div>%s</div><div class="linfo">Info</div></div>';
                    switch ($this->_type) {
                        case Log::$ERROR: echo sprintf($fmt, 'log_error', 'Type'); break;
                        case Log::$ACCESS: echo sprintf($fmt, 'log_access', 'Client'); break;
                        case Log::$USER: echo sprintf($fmt, 'log_user', 'User'); break;
                        // this shouldn't happen
                        case Log::$RAW: default: die("A Software Engineering error occurred: invalid log type specified"); break;
                    }
                }
                foreach ($this->_entries as $ent) {
                    echo $ent->toHtml($mpad);
                }
                echo "</div>\n";
            }
        }
        
        private function _parseError($d, &$i1, &$i2, &$i3) {
            $phpm = "PHP message:";
            // PHP message: PHP Fatal error:  
            if (substr($d, 0, strlen($phpm)) == $phpm) {
                // date
                $i1 = "PHP message:";
                $d = substr($d, strlen($phpm));
                // type
                $fpos = strpos($d, ':');
                $i2 = substr($d, 0, $fpos);
                // info
                $i3 = substr($d, $fpos+1);
            } else {
                // date
                $fpos = strpos($d, ']');
                $spos = strpos($d, '[') + 1;
                $i1 = substr($d, $spos, $fpos - $spos);

                $d = substr($d, $fpos + 1);
                // type [error,etc.]
                $fpos = strpos($d, 'PHP ');
                $tmp = substr($d, $fpos);
                $spos = strpos($tmp, ':');
                //$spos = strpos($d, '[') + 1;
                $i2 = substr($d, $fpos, $spos);

                //$d = substr($d, $fpos + 1);
                // errno 853#0:
                //$fpos = strpos($d, ':');
                //$i2 = $i2.':'.substr($d, 0, $fpos);
                
                // info
                $i3 = substr($tmp, $spos + 2);
            }
            return Log::$ERROR;
        }

        private function _parseAccess($d, &$i1, &$i2, &$i3) {
            // client
            $fpos = strpos($d, '-');
            $i2 = trim(substr($d, 0, $fpos));
            $fpos = strpos($d, '[');
            $d = substr($d, $fpos+1);
            // date
            $fpos = strpos($d, ']');
            $i1 = trim(substr($d, 0, $fpos));
            // info
            $i3 = trim(substr($d, $fpos+1));
            return Log::$ACCESS;
        }

        private function _parseUser($d, &$i1, &$i2, &$i3) {
            //2015/07/15 04:48:30 - zmperez (id:1) - modified user information
            // date
            $fpos = strpos($d, '-');
            $i1 = trim(substr($d, 0, $fpos));
            $d = substr($d, $fpos+1);
            // client
            $fpos = strpos($d, '-');
            $i2 = trim(substr($d, 0, $fpos));
            // info
            $i3 = trim(substr($d, $fpos+1));
            return Log::$USER;
        }

        private function _parseLine($d, $line = 0) {
            $type = 0; $i1 = null; $i2 = null; $i3 = null;
            switch ($this->_type) {
                case Log::$ERROR: $type = $this->_parseError($d, $i1, $i2, $i3); break;
                case Log::$ACCESS: $type = $this->_parseAccess($d, $i1, $i2, $i3); break;
                case Log::$USER: $type = $this->_parseUser($d, $i1, $i2, $i3); break;
                case Log::$RAW: default: {
                    $type = Log::$RAW;
                    $i1 = trim($d);
                } break;
            }
            return new LogEntry($type, $line, $i1, $i2, $i3);
        }
    }
?>
