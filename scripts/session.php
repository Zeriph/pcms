<?php
    include_once 'common.php';
    include_once 'logger.php';
    include_once 'redirect.php';
    include_once 'mysql.php';
    include_once 'user.php';
    include_once 'project.php';
    
    class Session {
        
        #region "Private Static Members"
        
        private static $_err = "";
        private static $_cookie = 'zeriph_pcms_cookie';
        
        public static $Vars = [
            'id' => 'sess_zeriph_pcms_id',
            'login' => 'sess_zeriph_pcms_login',
            'name' => 'sess_zeriph_pcms_name',
            'acl' => 'sess_zeriph_pcms_acl',
            'email' => 'sess_zeriph_pcms_email',
            'phone' => 'sess_zeriph_pcms_phone',
            'theme' => 'sess_zeriph_pcms_theme',
            'firstlog' => 'sess_zeriph_pcms_firstlog',
            'lastlog' => 'sess_zeriph_pcms_lastlog',
        ];
        
        #endregion
        
        public static function CurrentUser() {
            $ac = 0;
            foreach (Session::$Vars as $var=>$sv) {
                if (isset($_SESSION[$sv])) { $ac += 1; }
            }
            if ($ac == count(Session::$Vars)) {
                // each page hits the DB to check if user should be allowed access as well
                // this is for refreshing the theme/user info/etc. from the 'admin' side
                $user = User::GetUser($_SESSION[Session::$Vars['id']]);
                if ($user == null) {
                    Session::$_err = 'Error retrieving user ['.$_SESSION[Session::$Vars['id']].' - '.$_SESSION[Session::$Vars['login']].'] from database.';
                    Log::WriteLine(Session::$_err);
                }
                return $user;
            }
            Session::$_err = "No session active.";
            return null;
        }
        
        public static function Get($name) {
            if (isset($_SESSION['sess_zeriph_pcms_'.$name])) {
                return $_SESSION['sess_zeriph_pcms_'.$name];
            }
            return null;
        }
        
        public static function Set($name, $val) {
            if (isset($_SESSION['sess_zeriph_pcms_'.$name])) {
                $_SESSION['sess_zeriph_pcms_'.$name] = $val;
            }
        }
        
        public static function GetLastError() {
            return Session::$_err;
        }
        
        public static function GetRedirect($redir = 'index.php', $doheader = true) {
            if ($redir == '') { return null; } // if passed in value is empty, then don't redirect
            if ($doheader) { header("Location:$redir"); }
            return new Redirect($redir);
        }
        
        public static function Logout() {
            session_name(Session::$_cookie);
            if (Session::Validate()) {
                foreach (Session::$Vars as $var=>$sv) {
                    if (isset($_SESSION[$sv])) { unset($_SESSION[$sv]); }
                }
            }
            session_destroy();
        }
    
        public static function Login($u, $p) {
            $ret = false;
            $sql = new SqlSelect('users');
            if (($err = $sql->connect()) == '') {
                $cols = array('pass');
                $cols = array_merge(User::$COLUMNS, $cols);
                $ui = $sql->select($cols, 'login', strtolower(trim($u)));
                if (count($ui) > 0) {
                    $pw = SqlSelect::Password($p, 'users');
                    if ($ui[0]['pass'] == $pw) {
                        $ret = true;
                        if (Session::Start()) {
                            foreach (Session::$Vars as $var => $sv) {
                                $_SESSION[$sv] = $ui[0][$var];
                                if (!isset($_SESSION[$sv])) {
                                    $_SESSION[$sv] = 0;
                                }
                            }
                        } else {
                            $ret = false;
                        }
                    }
                }
            }
            $sql->close();
            return $ret;
        }
        
        public static function Start() {
            session_name(Session::$_cookie);
            if (!isset($_SESSION[Session::$Vars['login']])) { return session_start(); }
            return false;
        }
        
        public static function UserCssRedirOrDie($user, $redir) {
            // if the user != null then they're logged in
            // if the redir != '' then they are not authorized
            if ($user == null) {
                // default of 'dark' theme
                echo '<link rel="stylesheet" type="text/css" href="css/dark.css" />'.$redir;
            } else {
                echo '<link rel="stylesheet" type="text/css" href="css/'.$user->getThemeType(true).'.css" />'.$redir;
            }
            if ($redir != null) {
                echo $redir;
                $str = '</head><body>If you are not automatically redirected, please <a href="'.$redir->getLink().'">click here</a>.</body></html>';
                /* if both of these are true and we have gotten here, then chances are the browser has auto-redirect and JS turned off;
                in this case, we don't want to continue parsing the php/html so the user doesn't see what they are not supposed to, so die..
                not pretty, but the most 'compatible' way to avoid invalid access without a lot of markup in the PHP */
                die($str);
            }
        }
        
        public static function Validate() {
            session_name(Session::$_cookie);
            if (!session_start()) {
                Session::$m_err = "Error validating session.";
                return false;
            }
            return isset($_SESSION[Session::$Vars['login']]);
        }
        
        public static function ValidateUser($user, $cpage) {
            return ($user != null && $cpage != '' && !$user->isNoAccess() && !$user->isFirstLogin() && UserType::AccessAllowed($user->getACL(), Menu::GetACL($cpage)));
        }
    }
    
    
?>
