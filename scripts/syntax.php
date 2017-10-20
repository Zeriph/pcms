<?php
	class Syntax {	
		function Highlight($code, $lang) {			
			/*$code = str_replace("<br>", "", $code);
			$code = str_replace("<br />", "", $code);
			$code = str_replace("&gt;", ">", $code);
			$code = str_replace("&lt;", "<", $code);
			$code = str_replace("&quot;", "\"", $code);
			$code = str_replace('$', '\$', $code);
			$code = str_replace("&amp;", "&", $code);
			$code = str_replace('\n', '\\\\n', $code);
			$code = str_replace('\r', '\\\\r', $code);
			$code = str_replace('\t', '\\\\t', $code);*/
			$code = str_replace("\\", "\\\\", $code);
			$code = str_replace("\r\n", "\n", $code);
			$code = str_replace("\n", "\r\n", $code);
			$code = stripslashes($code);
			$code = htmlentities($code);
			$code = str_replace('&iuml;&raquo;&iquest;', '', $code);
			$Keywords = array(
				'abstract', 'event', 'new', 'struct', 'as', 'explicit', 'null', 
				'switch', 'base', 'extern', 'object', 'this', 'bool', 'false', 
				'operator', 'throw', 'break', 'finally', 'out', 'true', 'byte', 
				'fixed', 'override', 'try', 'case', 'float', 'params', 'typeof', 
				'catch', 'for', 'private', 'uint', 'char', 'foreach', 'protected', 
				'ulong', 'checked', 'goto', 'public', 'unchecked', 'class', 'if', 
				'readonly', 'unsafe', 'const', 'implicit', 'ref', 'ushort', 'continue', 
				'in', 'return', 'using', 'decimal', 'int', 'sbyte', 'virtual', 'default', 
				'interface', 'sealed', 'volatile', 'delegate', 'internal', 'short', 'void', 
				'do', 'is', 'sizeof', 'while', 'double', 'lock', 'stackalloc', 'else', 
				'long', 'static', 'enum', 'namespace', 'string', 'get', 'set', '#region', '#endregion'
				);
			if ($lang == 'cpp' || $lang == 'cxx' || $lang == 'hpp' || $lang == 'hxx' || $lang == 'c' || $lang == 'h') {
				$CppKeys = array('#include', '#if', '#ifdef', '#else', '#endif', '#ifndef', '#define', 'template', 'typename', 'unsigned');
				$Result = array_merge($Keywords, $CppKeys);
				$Keywords = $Result;
			}
			// keywords: #0000FF;
			$Count = count($Keywords);
			for ($x = 0; $x < $Count; $x++) { 
				$Pattern = '/(^|[^a-zA-Z0-9#@_\'\"])('.$Keywords[$x].')([^a-zA-Z0-9#_\'\"]|$)/'; 
				$code = preg_replace($Pattern, '\\1<font color="#0000FF">\\2</font>\\3', $code); 
			}
			// string text: #A31515
			$Pattern = '/(\&quot;(.*?)\&quot;)/is';
			$code = preg_replace($Pattern, '<font color="#A31515">&quot;\\2&quot;</font>', $code); 
			// regular comment: #008200
			$SingleComment = '/(\/\/\/(.*?))([\r\n]|$)/is';
			if (preg_match_all($SingleComment, $code, $Match)) { 
				$Tmp = preg_replace('/<font color=(.*?)>(.*?)<\/font>/','\\2', $Match[0]);
				$Tmp = preg_replace($SingleComment, '<font color="#008200">\\1</font>\\4', $Tmp);
				$code = str_replace($Match[0], $Tmp, $code);
			}
			$MultiComment = '/(\/\*(.*?)(\*\/|$))/is'; 
			if (preg_match_all($MultiComment, $code, $Match)) { 
				$Tmp = preg_replace('/<font color=(.*?)>(.*?)<\/font>/','\\2', $Match[0]);
				$Tmp = preg_replace($MultiComment, '<font color="#008200">\\1</font>', $Tmp); 
				$code = str_replace($Match[0],$Tmp,$code); 
			}
			// xml comment: #808080
			$Pattern = '/(\&lt;(param)(.*?)\&gt;)/is';
			$code = preg_replace($Pattern, '<font color="#808080">\\1</font>', $code); 
			$code = str_replace('///', '<font color="#808080">///</font>', $code);
			$code = str_replace("&quot;", "\"", $code);
			$code = str_replace("<br>", "", str_replace("<br />", "", $code));
			$xmlcomments = array('&lt;summary&gt;', '&lt;/summary&gt;', '&lt;returns&gt;', '&lt;/returns&gt;', '&lt;/param&gt;');
			$Count = count($xmlcomments);
			for ($x = 0; $x < $Count; $x++) { 
				$code = str_replace($xmlcomments[$x], '<font color="#808080">'.$xmlcomments[$x].'</font>', $code);
			}
			return $code;
		}
		
		function HighlightFile($fpath, $fs = '', $backto = '', $dobt = false) {
			$suplang = array('cs', 'cpp', 'hpp', 'cxx', 'hxx', 'c', 'h', 'png');
			$ext = strtolower(end(explode('.', $fpath)));
			if (in_array($ext, $suplang) && file_exists($fpath)) {
				$fname = (end(explode('/', $fpath)));
				$lang = $ext; $code = ''; $fsstr = '';
				if ($fs != '') {
					$fsstr = '<a href="'.$fpath.'" onclick="return ShowFullScreen(\''.$fs.'\', \''.$backto.'\')">View Full Frame</a>';
				}
				if ($backto != '' && $dobt) {
					$fsstr .= '&nbsp;<a href="'.$backto.'">Back</a>';
				}
				if ($ext == 'png') {
					return '
					<table border="0" width="100%" height="100%" align="center" cellspacing="0" cellpadding="0" class="main">
						<tr height="25" valign="middle">
							<td colspan="5">
								<table border="0" width="100%" align="center" cellspacing="0" cellpadding="0" class="main">
									<tr>
										<td valign="middle">
											<font style="font-family:Courier New,Consolas,Calibri,Arial,serif; font-size:12px; font-weight:bold;">
												<a href="'.$fpath.'" onclick="return ShowCode(\''.$fpath.'\')">'.$fname.'</a></b>
											</font>
										</td>
										<td width="160" align="right" valign="middle">
											<font style="font-family:Courier New,Consolas,Calibri,Arial,serif; font-size:12px;">
												'.$fsstr.'
											</font>
										</td>
									</tr>
									<tr height="5"><td colspan="2"></td></tr>
								</table>
							</td>
						</tr>
						<tr height="1" class="dev_frame"><td></td></tr>
						<tr height="5"><td></td></tr>
						<tr valign="top">
							<td align="left"><img src="'.$fpath.'"></td>
						</tr>
					</table>';
				} else { // It's on our supported 'languages' array, but it's not a png, so it must be a code file
					$code = file_get_contents($fpath);
					$code = Syntax::Highlight($code, $lang);
					$code = str_replace("    ", "\t", $code);
					$code = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $code);
					$splits = split("\n", $code);
					$count = count($splits) + 1;
					$clen = strlen("$count");
					$code = '';
					for ($i = 0; $i < $count; $i++) {
						$ival = $i+1;
						while (strlen("$ival") < $clen) {
							$ival = '0'.$ival;
						}
						$val = ((isset($splits[$i])) ? $splits[$i] : '');
						$code .= '<div class="dev_ln" value="'.$ival.'" '.(($i % 2 == 0) ? 'style="background-color: #D7D7D7;"' : '').'>'."$val</div>\r\n";
					}
					return '
			<table border="0" width="100%" height="100%" align="center" cellspacing="0" cellpadding="0" class="main">
				<tr height="25">
					<td colspan="5" valign="middle">
						<table border="0" width="100%" align="center" cellspacing="0" cellpadding="0" class="main">
							<tr>
								<td valign="middle">
									<font style="font-family:Courier New,Consolas,Calibri,Arial,serif; font-size:12px;">
										<b><a href="'.$fpath.'" onclick="return ShowCode(\''.$fpath.'\')">'.$fname.'</a></b>
										<font style="font-size:10px">(Right click and \'Save As...\' to save this file)</font>
									</font>
								</td>
								<td width="160" align="right" valign="middle">
									<font style="font-family:Courier New,Consolas,Calibri,Arial,serif; font-size:12px;">
										'.$fsstr.'
									</font>
								</td>
							</tr>
							<tr height="5"><td colspan="2"></td></tr>
						</table>
					</td>
				</tr>
				<tr height="1" bgcolor="#FFFFFF"><td colspan="5"></td></tr>
				<tr>
					<td valign="top" align="left" style="font-family:Courier New,Consolas,Calibri,Arial,serif; font-size:11px;">
						'.$code.'
					</td>
				</tr>
			</table>';
				}
			}
			return '<br>The file you requested does not exist ('.$fpath.'). Please go <a href="'.$backto.'">back</a> and try again.<br>We apologize for any inconvenience.<br>';
		}
	}	
?>
