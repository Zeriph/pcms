<?php
    // TODO: abstract and public static, etc. etc.
	class Common {
        #region Date Formats
        
        public static $DateFormats = array(
            //"[Y|y][m|n][d|j][H|h|G|g][i][s][A|a]",
            "YmdHis",
            
            // 2-4 digit month/day
            "m/d/Y H:i:s",   "m-d-Y H:i:s",   "m/d/y H:i:s",   "m-d-y H:i:s",
            "m/d/Y h:i:s",   "m-d-Y h:i:s",   "m/d/y h:i:s",   "m-d-y h:i:s",
            "m/d/Y H:i:s A", "m-d-Y H:i:s A", "m/d/y H:i:s A", "m-d-y H:i:s A",
            "m/d/Y h:i:s A", "m-d-Y h:i:s A", "m/d/y h:i:s A", "m-d-y h:i:s A",
            "m/d/Y H:i:s a", "m-d-Y H:i:s a", "m/d/y H:i:s a", "m-d-y H:i:s a",
            "m/d/Y h:i:s a", "m-d-Y h:i:s a", "m/d/y h:i:s a", "m-d-y h:i:s a",
            
            "Y/m/d H:i:s",   "Y-m-d H:i:s",   "y/m/d H:i:s",   "y-m-d H:i:s",
            "Y/m/d h:i:s",   "Y-m-d h:i:s",   "y/m/d h:i:s",   "y-m-d h:i:s",
            "Y/m/d H:i:s A", "Y-m-d H:i:s A", "y/m/d H:i:s A", "y-m-d H:i:s A",
            "Y/m/d h:i:s A", "Y-m-d h:i:s A", "y/m/d h:i:s A", "y-m-d h:i:s A",
            "Y/m/d H:i:s a", "Y-m-d H:i:s a", "y/m/d H:i:s a", "y-m-d H:i:s a",
            "Y/m/d h:i:s a", "Y-m-d h:i:s a", "y/m/d h:i:s a", "y-m-d h:i:s a",
            
            "m/d/Y G:i:s",   "m-d-Y G:i:s",   "m/d/y G:i:s",   "m-d-y G:i:s",
            "m/d/Y g:i:s",   "m-d-Y g:i:s",   "m/d/y g:i:s",   "m-d-y g:i:s",
            "m/d/Y G:i:s A", "m-d-Y G:i:s A", "m/d/y G:i:s A", "m-d-y G:i:s A",
            "m/d/Y g:i:s A", "m-d-Y g:i:s A", "m/d/y g:i:s A", "m-d-y g:i:s A",
            "m/d/Y G:i:s a", "m-d-Y G:i:s a", "m/d/y G:i:s a", "m-d-y G:i:s a",
            "m/d/Y g:i:s a", "m-d-Y g:i:s a", "m/d/y g:i:s a", "m-d-y g:i:s a",
            
            "Y/m/d G:i:s",   "Y-m-d G:i:s",   "y/m/d G:i:s",   "y-m-d G:i:s",
            "Y/m/d g:i:s",   "Y-m-d g:i:s",   "y/m/d g:i:s",   "y-m-d g:i:s",
            "Y/m/d G:i:s A", "Y-m-d G:i:s A", "y/m/d G:i:s A", "y-m-d G:i:s A",
            "Y/m/d g:i:s A", "Y-m-d g:i:s A", "y/m/d g:i:s A", "y-m-d g:i:s A",
            "Y/m/d G:i:s a", "Y-m-d G:i:s a", "y/m/d G:i:s a", "y-m-d G:i:s a",
            "Y/m/d g:i:s a", "Y-m-d g:i:s a", "y/m/d g:i:s a", "y-m-d g:i:s a",
            
            "m d Y H i s",   "m d y H i s",   "m d Y h i s",   "m d y h i s",
            "m d Y H i s A", "m d y H i s A", "m d Y h i s A", "m d y h i s A", 
            "m d Y H i s a", "m d y H i s a", "m d Y h i s a", "m d y h i s a", 
            
            "Y m d H i s",   "y m d H i s",   "Y m d h i s",   "y m d h i s",
            "Y m d H i s A", "y m d H i s A", "Y m d h i s A", "y m d h i s A", 
            "Y m d H i s a", "y m d H i s a", "Y m d h i s a", "y m d h i s a", 
            
            "m d Y G i s",   "m d y G i s",   "m d Y g i s",   "m d y g i s",   
            "m d Y G i s A", "m d y G i s A", "m d Y g i s A", "m d y g i s A", 
            "m d Y G i s a", "m d y G i s a", "m d Y g i s a", "m d y g i s a", 
            
            "Y m d G i s",   "y m d G i s",   "Y m d g i s",   "y m d g i s",   
            "Y m d G i s A", "y m d G i s A", "Y m d g i s A", "y m d g i s A", 
            "Y m d G i s a", "y m d G i s a", "Y m d g i s a", "y m d g i s a", 
            
            
            // 1-2 digit month/day
            "n/j/Y H:i:s",   "n-j-Y H:i:s",   "n/j/y H:i:s",   "n-j-y H:i:s",
            "n/j/Y h:i:s",   "n-j-Y h:i:s",   "n/j/y h:i:s",   "n-j-y h:i:s",
            "n/j/Y H:i:s A", "n-j-Y H:i:s A", "n/j/y H:i:s A", "n-j-y H:i:s A",
            "n/j/Y h:i:s A", "n-j-Y h:i:s A", "n/j/y h:i:s A", "n-j-y h:i:s A",
            "n/j/Y H:i:s a", "n-j-Y H:i:s a", "n/j/y H:i:s a", "n-j-y H:i:s a",
            "n/j/Y h:i:s a", "n-j-Y h:i:s a", "n/j/y h:i:s a", "n-j-y h:i:s a",
            
            "Y/n/j H:i:s",   "Y-n-j H:i:s",   "y/n/j H:i:s",   "y-n-j H:i:s",
            "Y/n/j h:i:s",   "Y-n-j h:i:s",   "y/n/j h:i:s",   "y-n-j h:i:s",
            "Y/n/j H:i:s A", "Y-n-j H:i:s A", "y/n/j H:i:s A", "y-n-j H:i:s A",
            "Y/n/j h:i:s A", "Y-n-j h:i:s A", "y/n/j h:i:s A", "y-n-j h:i:s A",
            "Y/n/j H:i:s a", "Y-n-j H:i:s a", "y/n/j H:i:s a", "y-n-j H:i:s a",
            "Y/n/j h:i:s a", "Y-n-j h:i:s a", "y/n/j h:i:s a", "y-n-j h:i:s a",
            
            "n/j/Y G:i:s",   "n-j-Y G:i:s",   "n/j/y G:i:s",   "n-j-y G:i:s",
            "n/j/Y g:i:s",   "n-j-Y g:i:s",   "n/j/y g:i:s",   "n-j-y g:i:s",
            "n/j/Y G:i:s A", "n-j-Y G:i:s A", "n/j/y G:i:s A", "n-j-y G:i:s A",
            "n/j/Y g:i:s A", "n-j-Y g:i:s A", "n/j/y g:i:s A", "n-j-y g:i:s A",
            "n/j/Y G:i:s a", "n-j-Y G:i:s a", "n/j/y G:i:s a", "n-j-y G:i:s a",
            "n/j/Y g:i:s a", "n-j-Y g:i:s a", "n/j/y g:i:s a", "n-j-y g:i:s a",
            
            "Y/n/j G:i:s",   "Y-n-j G:i:s",   "y/n/j G:i:s",   "y-n-j G:i:s",
            "Y/n/j g:i:s",   "Y-n-j g:i:s",   "y/n/j g:i:s",   "y-n-j g:i:s",
            "Y/n/j G:i:s A", "Y-n-j G:i:s A", "y/n/j G:i:s A", "y-n-j G:i:s A",
            "Y/n/j g:i:s A", "Y-n-j g:i:s A", "y/n/j g:i:s A", "y-n-j g:i:s A",
            "Y/n/j G:i:s a", "Y-n-j G:i:s a", "y/n/j G:i:s a", "y-n-j G:i:s a",
            "Y/n/j g:i:s a", "Y-n-j g:i:s a", "y/n/j g:i:s a", "y-n-j g:i:s a",
            
            "n j Y H i s",   "n j y H i s",   "n j Y h i s",   "n j y h i s",  
            "n j Y H i s A", "n j y H i s A", "n j Y h i s A", "n j y h i s A",
            "n j Y H i s a", "n j y H i s a", "n j Y h i s a", "n j y h i s a",

            "Y n j H i s",   "y n j H i s",   "Y n j h i s",   "y n j h i s",  
            "Y n j H i s A", "y n j H i s A", "Y n j h i s A", "y n j h i s A",
            "Y n j H i s a", "y n j H i s a", "Y n j h i s a", "y n j h i s a",

            "n j Y G i s",   "n j y G i s",   "n j Y g i s",   "n j y g i s",  
            "n j Y G i s A", "n j y G i s A", "n j Y g i s A", "n j y g i s A",
            "n j Y G i s a", "n j y G i s a", "n j Y g i s a", "n j y g i s a",

            "Y n j G i s",   "y n j G i s",   "Y n j g i s",   "y n j g i s",  
            "Y n j G i s A", "y n j G i s A", "Y n j g i s A", "y n j g i s A",
            "Y n j G i s a", "y n j G i s a", "Y n j g i s a", "y n j g i s a"
        );
    
        #endregion
    
		function ConvertSizeToString($size) {
			$lvl = 0;
			$endings = array("B", "KB", "MB", "GB", "TB", "PB", "EB");
			while ($size >= 1024) {
				$size = $size / 1024;
				$lvl += 1;
			}
			if (strpos($size, ".") === false) { // Don't trim
				return $size." ".$endings[$lvl];
			} else {
				return substr($size, 0, strpos($size, ".") + 3)." ".$endings[$lvl]; // Trim
			}
		}
		
		function DateFormat($date, $format = 'YmdHis') {
            $d = date_create($date);
            if (!$d) {
                $e = date_get_last_errors();
                if (!empty($e)) { return $e; }
            }
            return date_format($d, $format);
        }
        
        function GetFiles($dir, $type = 'file', $ext = '') {
			if (trim($dir) == '') { $dir = '.'; }
			//if (substr($dir, (strlen($dir) - 1), 1) != "/") { $dir .= '/'; }
            $isdir = ($type == 'dir');
            $doext = ($type == 'file' && $ext != '');
            if ($doext && substr($ext, 0, 1) == '.') { $ext = substr($ext, 1); }
			if (is_dir($dir) || is_link($dir)) {
				$files = array();
				if ($handle = opendir($dir)) {
					while (($file = readdir($handle)) !== false) {
                        $path = $dir.$file;
                        $ftype = filetype($path);
                        if ($ftype == 'link') { $ftype = filetype(realpath($path)); }
						if ($ftype == $type) {
							if ($isdir && ($file == '.' || $file == '..')) { continue; }
                            if ($doext && ($ext != pathinfo($file, PATHINFO_EXTENSION))) { continue; }
							$files[] = ($dir.$file);
                        }
					}
				}
				return $files;
			}
			return array(); // return an empty array
		}
		
		function GetAllFiles($dir, $ext = '') {
			$dirs = Common::GetAllDirs($dir);
            $files = array();
			foreach ($dirs as $subdir) {
                $tmp = Common::GetFiles($subdir, 'file', $ext);
				$files = array_merge($files, $tmp);
			}
            $tmp = Common::GetFiles($dir, 'file', $ext);;
            $files = array_merge($files, $tmp);
			return $files;
		}
		
		function GetDirs($dir) {
			return Common::GetFiles($dir, 'dir');
		}
		
		function GetAllDirs($top, $blacklist = array()) {
			$tmptop = Common::GetDirs($top);
			$tops = array(); $all = array();
            foreach ($tmptop as $top) {
                $tname = substr($top, 2);
                if (in_array($tname, $blacklist)) { continue; }
                $name = strtolower(end(explode('/', $top)));
                if ($name == '.' || $name == '..') { continue; }
                $tops[] = $top;
            }
			$all = array_merge($all, $tops);
			foreach ($tops as $dir) {
				$subs = Common::GetAllDirs($dir, $blacklist);
				$all = array_merge($all, $subs);
			}
			return $all;
		}
		
		function GetNewFileName($dir, $oldFileName, $ext) {
			$Idx = 0;
			if (substr($dir, (strlen($dir) - 1), 1) != "/") { $dir .= '/'; }
			if ($ext != '' && substr($ext, 0, 1) != ".") { $ext = ".".$ext; }
			if (!file_exists(($dir.$oldFileName.$ext))) { return ($dir.$oldFileName.$ext); }
			do {
				$FullName = $dir.$oldFileName.'.'.($Idx++).$ext;
			} while(file_exists($FullName));
			$Idx--;
			return $dir.$oldFileName.'.'.$Idx.$ext;
		}
		
		function GetFileContents($file, $split = false) {
            if (!is_file($file)) { return ''; }
            $ret = '';
			$h = fopen($file, "r");
            if ($split) {
                $ret = array();
                while(!feof($h)) {
                    $ret[] = fgets($h);
                }
            } else {
                $ret = fread($h, filesize($file));
            }
			fclose($h);
			return $ret;
        }
        
        function JSAlert($alert) {
            return '<script type="text/javascript">alert("'.$alert.'");</script><noscript>'.$alert.'</noscript>';
        }
        
        function QuickSort(&$array, $propertyName, $dir = "SORT_ASC") {
			$cur = 1;
			$stack[1]['l'] = 0;
			$stack[1]['r'] = count($array)-1;
			do {
				$l = $stack[$cur]['l'];
				$r = $stack[$cur]['r'];
				$cur--;
				do {
					$i = $l;
					$j = $r;
					$tmp = $array[(int)( ($l+$r)/2 )];
					do {
						while( $array[$i]->$propertyName < $tmp->$propertyName ) { $i++; }
						while( $tmp->$propertyName < $array[$j]->$propertyName ) { $j--; }
						// swap elements from the two sides
						if( $i <= $j) {
							$w = $array[$i];
							$array[$i] = $array[$j];
							$array[$j] = $w;
							$i++;
							$j--;
						}
					} while($i <= $j);
					if($i < $r) {
						$cur++;
						$stack[$cur]['l'] = $i;
						$stack[$cur]['r'] = $r;
					}
					$r = $j;
				} while($l < $r);
			} while($cur != 0);
			if ($dir == "SORT_DESC") {
				$array = array_reverse($array);
			}
		}
		
		function SplitString(&$Item) {
			$Item = trim($Item, " #\t");
			$Item = trim($Item, "\x00..\x1F");
			$NewItem = array();
			$Items = explode(" ", $Item);
			$Count = count($Items);
			$TmpValue = "";
			$FoundEnd = false; $AddValue = false;
			for ($i = 0; $i < $Count; $i++) {
				if ($Items[$i] == "") { continue; }
				if (substr($Items[$i], 0, 1) == '"') { $AddValue = true; $FoundEnd = false; }
				if (substr($Items[$i], -1) == '"') { $FoundEnd = true; }
				if ($AddValue) {
					if ($TmpValue == "") {
						$TmpValue = $Items[$i];
						if (substr($TmpValue, -1) == '"') { // It's a whole value
							$TmpValue = trim($TmpValue, '"');
							$NewItem[] = $TmpValue;
							$TmpValue = "";
							$FoundEnd = false;
							$AddValue = false;
						}
					} else {
						$TmpValue .= " ".$Items[$i];
						if ($FoundEnd) {
							$TmpValue = trim($TmpValue, '"');
							$NewItem[] = $TmpValue;
							$TmpValue = "";
							$FoundEnd = false;
							$AddValue = false;
						}
					}
				} else {
					$NewItem[] = $Items[$i];
				}
			}
			return $NewItem;
		}

        function StringStartsWith($haystack, $needle) {
            return (($needle === "") || (strrpos($haystack, $needle, -strlen($haystack)) !== FALSE));
        }
        
        function StringEndsWith($haystack, $needle) {
            return (($needle === "") || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE));
        }
        
        function ValidateDate($date, $format = 'YmdHis') {
            $d = Common::DateFormat($date, $format);
            return ($d != null && $d == $date);
        }
        
        function ValidateDateAllFormats($date) {
            foreach (Common::$DateFormats as $fmt) {
                if (Common::ValidateDate($date, $fmt)) { return true; }
            }
            return false;
        }
        
        function ValidateEmail($email) {
            $em = trim($email);
            // FILTER_VALIDATE_EMAIL seems to give false positives e.g. 'smaller_emails@seem_to_work.com' fails but passes with regex
            return (strlen($em) <= 255) &&
                    (filter_var($em, FILTER_VALIDATE_EMAIL) ||
                    preg_match("/^(([^<>()[\]\.,;:\s@\\\"]+(\.[^<>()[\]\.,;:\s@\\\"]+)*)|(\\\".+\\\"))@(([^<>()[\]\.,;:\s@\\\"]+\.)+[^<>()[\]\.,;:\s@\\\"]{2,})$/i", $em));
        }
        
        function WriteFile($file, $val = '', $mode = "w") {
            $f = fopen($file, $mode);
            fwrite($f, $val);
            fflush($f);
            fclose($f);
        }
        
	}
    
    abstract class PCMS {
        public static $VERSION = '1.0a';
        
        public static function GetVersion() {
            return PCMS::$VERSION;
        }
    }
?>
