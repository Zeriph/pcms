<?php
    include_once 'common.php';
    include_once 'logger.php';
    include_once 'mysql.php';
    include_once 'user_type.php';
    include_once 'menu_layout.php';
    
    /**
     *  The User class facilitates the user management portion of the PCMS.
     *  A User object contains the information needed to display the proper page
     *  and layout for the User specified in the class.
     */
    class User {
        
        #region "Private Members/Functions"
        
        private $_id = -1; // New User
        private $_login = ''; // new.user
        private $_name = ''; // full name
        private $_type = 0; // UserType::$NO_ACCESS
        private $_email = ''; // office email
        private $_phone = ''; // office phone
        private $_fname = ''; // This is derived from $_name
        private $_lname = ''; // This is derived from $_name
        private $_theme = 0; // UserTheme::$DARK
        private $_firstlog = 1; // users first login?
        private $_lastlog = ''; // last time successfully logged in
        
        // TODO: add these to DB/finish
        private $_img = 'img/login_u.gif';
        private $_altphone = ''; // cell/home phone
        private $_altemail = ''; // home email
        private $_office = ''; // building/room/etc.
        private $_title = ''; // users title (like Engineer/Clerk/etc.)
        
        private function _updateFirstLast() {
            $nvals = explode(' ', $this->_name);
            $cnt = count($nvals);
            $this->_fname = $nvals[0];
            if ($cnt > 1) { $this->_lname = $nvals[$cnt-1]; }
        }
        
        #endregion
        
        #region "static Members"
        
        // note: this array matches the columns in the db
        public static $COLUMNS = array('id','login','name','acl','email','phone','theme','firstlog','lastlog');
        
        public static $SORT_TYPE = -1; // default of UserSortType::$NAME;
        
        #endregion
        
        #region "Public Members/Functions"
        
        /**
         * The constructor for a User object
         * 
         * @param [in] $id      The user ID
         * @param [in] $login   The users login name
         * @param [in] $name    The users full name
         * @param [in] $type    The users type, of type User::Type
         * @param [in] $email   The users E-mail address
         * @param [in] $phone   The users phone number
         * @param [in] $theme   The users theme type
         * @param [in] $flog    True if this is the users first time logging into the system
         */
        public function __construct($id, $login, $name, $type, $email, $phone, $theme, $fl, $llog) {
            $this->_id = (is_numeric($id) ? $id : -1);
            $this->_login = html_entity_decode(trim($login));
            $this->_name = html_entity_decode(trim($name));
            $this->_email = html_entity_decode(trim($email));
            $this->_phone = html_entity_decode(trim($phone));
            
            $this->_type = UserType::FromValue($type);
            $this->_theme = UserTheme::FromValue($theme);
            $this->_firstlog = ((is_numeric($fl) && $fl == 0) ? 0 : 1);
            $this->_lastlog = (Common::ValidateDate($llog) ? $llog : '');
            $this->_updateFirstLast();
        }
        
        public function getACL() {
            return $this->_type;
        }
        
        public function getID() {
            return $this->_id;
        }
        
        public function getImage() {
            return $this->_img;
        }
        
        public function getLogin() {
            return $this->_login;
        }
        
        public function getFullName() {
            return $this->_name;
        }
        
        public function getFirstName() {
            return $this->_fname;
        }
        
        public function getLastName() {
            return $this->_lname;
        }
        
        public function getLastLogin($format = '') {
            if ($this->isFirstLogin()) { return ''; }
            return (($format != '') ? Common::DateFormat($this->_lastlog, $format) : $this->_lastlog);
        }
        
        public function getUserType() {
            return UserType::ToString($this->_type);
        }
        
        public function getEmail() {
            return $this->_email;
        }
        
        public function getPhone() {
            return $this->_phone;
        }
        
        public function getTheme() {
            return $this->_theme;
        }
        
        public function getThemeType($tolower = false) {
            return ($tolower ? strtolower(UserTheme::ToString($this->_theme)) : UserTheme::ToString($this->_theme));
        }
        
        public function isFirstLogin() {
            return ($this->_firstlog == 1);
        }
        
        public function isNoAccess() {
            if ($this->isBasic()) { return false; } // we do have access
            return true; // we don't have access
        }
        
        public function isBasic() {
            return ($this->_type == UserType::$BASIC) || $this->isHR();
        }
        
        public function isHR() {
            return ($this->_type == UserType::$HR) || $this->isManager();
        }
        
        public function isManager() {
            return ($this->_type == UserType::$MANAGER) || $this->isAdmin();
        }
        
        public function isAdmin() {
            return ($this->_type == UserType::$ADMIN);
        }
        
        public function isNewUser() {
            return ($this->_id == -1);
        }
        
        public function log($val) {
            // TODO: add the IP of the computer to the log or maybe just on login?
            Log::WriteLine($this->_login.' (id:'.$this->_id.') - '.$val);
        }
        
        public function printFoot() {
            // TODO: possibly put in for 'system messages' to 'user' or something?
            echo '<a href="pcms.php">version: '.PCMS::$VERSION.'</a>';
        }
        
        public function printMenu($cur='', $rel='', $imgp='') {
            $scto = '';
            $fmt = '<div id="umenu_%s" name="umenu_%s"%s><a href="'.$rel.'%s.php"><img src="'.$imgp.'img/%s.gif" class="icon" /><font>%s</font></a></div>'."\n";
            foreach (Menu::$Items as $item => $vals) {
                if (UserType::AccessAllowed($this->_type, $vals[Menu::$ACL])) {
                    $c = '';
                    if ($cur == $item) {
                        $scto = '<script type="text/javascript">ScrollTo(\'umenu_'.$item.'\');</script>';
                        $c = ' class="current"';
                    }
                    echo sprintf($fmt, $item, $item, $c, $item, $item, $vals[Menu::$TEXT]);
                }
            }
            echo $scto;
        }
        
        public function printPLink() {
            $uname = htmlentities($this->_name);
            echo '<a href="profile.php" title="Profile Details for '.$uname.'"><img src="img/profile.gif" />'.$uname.'</a>&nbsp;|&nbsp;';
        }
        
        public function updateEmail($email) {
            $email = trim($email);
            if (!User::IsValidEmail($email)) { return 'Invalid email specified.'; }
            $sql = new SqlUpdate('users');
            if (($err = $sql->connect()) == '') {
                if (($err = $sql->update("email", $email, "id", $this->_id)) == '') {
                    $this->_email = $email;
                    Session::Set('email', $this->_email);
                }
            }
            $sql->close();
            return $err;
        }
        
        public function updateFirstLogin($flog) {
            if (is_numeric($flog) || is_bool($flog)) {
                $firstlog = 1;
                if (is_numeric($flog)) {
                    $firstlog = (($flog != 0) ? 1 : 0);
                } else {
                    $firstlog = ($flog ? 1 : 0);
                }
                $sql = new SqlUpdate('users');
                if (($err = $sql->connect()) == '') {
                    if (($err = $sql->update("firstlog", $firstlog, "id", $this->_id)) == '') {
                        $this->_firstlog = $firstlog;
                        Session::Set('firstlog', $this->_firstlog);
                    }
                }
                $sql->close();
                return $err;
            }
            return 'Invalid first login value specified.';
        }
        
        public function updateLastLogin() {
            $sql = new SqlUpdate('users');
            if (($err = $sql->connect()) == '') {
                $llog = date("YmdHis");
                if (($err = $sql->update("lastlog", $llog, "id", $this->_id)) == '') {
                    $this->_lastlog = $llog;
                    Session::Set('lastlog', $this->_lastlog);
                }
            }
            $sql->close();
            return $err;
        }
        
        public function updateLogin($login) {
            $login = trim($login);
            if (!User::IsValidLogin($login)) { return 'Invalid user login specified'; }
            $sql = new SqlUpdate('users');
            if (($err = $sql->connect()) == '') {
                if (($err = $sql->update("login", $login, "id", $this->_id)) == '') {
                    $this->_login = $login;
                    Session::Set('login', $this->_login);
                }
            }
            $sql->close();
            return $err;
        }
        
        public function updateName($name) {
            $name = trim($name);
            if (!User::IsValidName($name)) { return 'Invalid user name specified'; }
            $sql = new SqlUpdate('users');
            if (($err = $sql->connect()) == '') {
                if (($err = $sql->update("name", $name, "id", $this->_id)) == '') {
                    $this->_name = $name;
                    Session::Set('name', $this->_name);
                    $this->_updateFirstLast();
                }
            }
            $sql->close();
            return $err;
        }
        
        public function updatePhone($new_phone) {
            $new_phone = trim($new_phone);
            if (!User::IsValidPhone($new_phone)) { return 'Invalid phone number specified'; }
            $sql = new SqlUpdate('users');
            if (($err = $sql->connect()) == '') {
                if (($err = $sql->update("phone", html_entity_decode($new_phone), "id", $this->_id)) == '') {
                    $this->_phone = $new_phone;
                    Session::Set('phone', $this->_phone);
                }
            }
            $sql->close();
            return $err;
        }
        
        public function updatePass($new_pass) {
            $sqlu = new SqlUpdate('users');
            if (($err = $sqlu->connect()) == '') {
                $pw = SqlSelect::Password($new_pass, 'users');
                if ($pw != null) {
                    $err = $sqlu->update('pass', $pw, 'id', $this->_id);
                } else {
                    $err = 'Could not get verified hashed password.';
                }
            }
            $sqlu->close();
            // TODO: does this make sense?
            //if ($err == '') { $this->log('modified password for user '.$this->_name.' (id:'. $this->_id.')'); }
            return $err;
        }
        
        public function updateTheme($theme) {
            if (!is_numeric($theme)) { return 'Invalid theme specified.'; }
            $sql = new SqlUpdate('users');
            if (($err = $sql->connect()) == '') {
                if (($err = $sql->update("theme", $theme, "id", $this->_id)) == '') {
                    $this->_theme = $theme;
                    Session::Set('theme', $this->_theme);
                }
            }
            $sql->close();
            return $err;
        }
        
        public function updateType($type) {
            if (!is_numeric($type)) { return 'Invalid user type specified.'; }
            $sql = new SqlUpdate('users');
            if (($err = $sql->connect()) == '') {
                if (($err = $sql->update("acl", $type, "id", $this->_id)) == '') {
                    $this->_type = $type;
                    Session::Set('acl', $this->_type);
                }
            }
            $sql->close();
            return $err;
        }
        
        public function __toString() {
            return $this->_login.' (id:'.$this->_id.')';
        }
        
        #endregion
        
        #region "Static Members/Functions"
        
        public static function AddUser($user) {
            if ($user == null) { return 'Invalid user object.'; }
            $ulog = strtolower(trim($user->_login));
            if (!User::IsValidLogin($ulog)) { return 'Invalid user login specified'; }
            $uname = trim($user->_name);
            if (!User::IsValidName($uname)) { return 'Invalid user name specified'; }
            $uemail = trim($user->_email);
            if (!User::IsValidEmail($uemail)) { return 'Invalid email specified'; }
            $uphone = trim($user->_phone);
            if (!User::IsValidPhone($uphone)) { return 'Invalid phone number specified'; }
            $vals = null;
            $cols = array('login','name','pass','acl','email','phone','theme','firstlog');
            $sql = new SqlInsert('users');
            if (($err = $sql->connect()) == '') {
                $pw = SqlSelect::Password('password', 'users');
                if ($pw != null) {
                    $vals = array($ulog,$uname,$pw,$user->_type,$uemail,$uphone,0,1);
                    $err = $sql->insert($cols, $vals);
                } else {
                    $err = 'Could not get verified hashed password.';
                }
            }
            $sql->close();
            if ($err == '' && $vals != null) {
                $sql = new SqlSelect('users');
                if (($err = $sql->connect()) == '') {
                    $retarr = $sql->select('id', $cols, $vals);
                    if (count($retarr) > 0) {
                        $user->_id = $retarr[0]['id'];
                        $err = '';
                    } else {
                        $err = 'Could not get new user ID.';
                    }
                }
                $sql->close();
            }
            return $err;
        }
        
        public static function Compare($a, $b) {
            switch (User::$SORT_TYPE) {
                case UserSortType::$ID:
                    return $a->_id > $b->_id;
                case UserSortType::$TYPE:
                    return $a->_type > $b->_type;
                case UserSortType::$EMAIL:
                    return strcmp($a->_email, $b->_email);
                case UserSortType::$PHONE:
                    return strcmp($a->_phone, $b->_phone);
                case UserSortType::$FNAME:
                    return strcmp($a->_fname, $b->_fname);
                case UserSortType::$LNAME:
                    return strcmp($a->_lname, $b->_lname);
                case UserSortType::$LASTLOG:
                    return strcmp($a->_lastlog, $b->_lastlog);
                case UserSortType::$FIRSTLOG: // not valid for compare
                case UserSortType::$THEME: // not valid for compare
                case UserSortType::$HAS_IMAGE: // not valid for compare
                case UserSortType::$NAME: default: break;
            }
            return strcmp($a->_name, $b->_name);
        }
        
        public static function Exists($ulog) {
            $ret = false;
            $sql = new SqlSelect('users');
            if (($err = $sql->connect()) == '') {
                $ui = $sql->select(array('id','login'), 'login', strtolower($ulog));
                if (count($ui) > 0) { $ret = true; }
            }
            $sql->close();
            return $ret;
        }
        
        public static function GetModDiffStr($oldu, $newu) {
            $ret = '';
            if ($oldu->_login != $newu->_login) { $ret .= ' (login:'.$oldu->_login.' -> '.$newu->_login.')'; } 
            if ($oldu->_name != $newu->_name) { $ret .= ' (name:'.$oldu->_name.' -> '.$newu->_name.')'; } 
            if ($oldu->_type != $newu->_type) { $ret .= ' (type:'.$oldu->getUserType().' -> '.$newu->getUserType().')'; } 
            if ($oldu->_email != $newu->_email) { $ret .= ' (email:'.$oldu->email.' -> '.$newu->_email.')'; } 
            if ($oldu->_phone != $newu->_phone) { $ret .= ' (phone:'.$oldu->_phone.' -> '.$newu->_phone.')'; }
            if ($oldu->_theme != $newu->_theme) { $ret .= ' (theme:'.$oldu->getThemeType(true).' -> '.$newu->getThemeType(true).')'; }
            if ($oldu->_firstlog != $newu->_firstlog) { $ret .= ' (firstlog:'.$oldu->_firstlog.' -> '.$newu->_firstlog.')'; } 
            if ($ret == '') { $ret = ' empty changes detected.'; }
            return $ret;
        }
        
        public static function GetNewUser($id = -1, $login = "new.user", $name = "[New User]", $type = 0, $email = "new.user@email.com", $phone = "1 (555) 867-5309", $theme = 0, $is_flog = 1, $last_log = '') {
            $new_user = new User($id, $login, $name, $type, $email, $phone, $theme, $is_flog, $last_log);
            // id, login, name, type, email, phone, theme, fl
            //$day = date("d");
            //$mth = date("m");
            //easter egg: if ($day == 25 && $mth == 12) { $new_user->name = "Chris Kringle"; }
            return $new_user;
        }
        
        public static function GetUser($uid) {
            $user = null;
            if (is_numeric($uid) && $uid >= -1) {
                if ($uid == -1) { return User::GetNewUser(); }
                $sql = new SqlSelect('users');
                if (($err = $sql->connect()) == '') {
                    $ui = $sql->select(User::$COLUMNS, 'id', $uid);
                    if (count($ui) > 0) {
                        $user = new User($ui[0]['id'], $ui[0]['login'], $ui[0]['name'],
                                     $ui[0]['acl'], $ui[0]['email'], $ui[0]['phone'],
                                     $ui[0]['theme'], $ui[0]['firstlog'], $ui[0]['lastlog']);
                    }
                }
                $sql->close();
            }
            return $user;
        }
        
        public static function GetUsers($include_new = false, $sort_type = -1) {
            $users = array();
            $sql = new SqlSelect('users');
            if (($err = $sql->connect()) == '') {
                $ui = $sql->select(User::$COLUMNS);
                if (count($ui) > 0) {
                    foreach ($ui as $u) {
                        $users[] = new User($u['id'], $u['login'], $u['name'], $u['acl'], $u['email'],
                                            $u['phone'], $u['theme'], $u['firstlog'], $u['lastlog']);
                    }
                    User::$SORT_TYPE = UserSortType::FromValue($sort_type);
                    usort($users, "User::Compare");
                }
            }
            $sql->Close();
            if ($include_new) {
                $tu[] = User::GetNewUser();
                $users = array_merge($tu, $users);
            }
            return $users;
        }
        
        public static function IsValidName($uname) {
            if ($uname == null) { return false; }
            $u = trim($uname);
            $tsr = strtolower($u);
            if ($tsr == '[add user]' || $tsr == '[new user]') { return false; }
            return (strlen($u) <= 128) && preg_match("/^[0-9a-zA-Z\\\"\'\.\s]+$/", $u);
        }
        
        public static function IsValidLogin($ulog) {
            if ($ulog == null || trim($ulog) == '') { return false; }
            $l = trim($ulog);
            return (strlen($l) <= 128) && preg_match("/^[0-9a-zA-Z\.\_]+$/", $l);
        }
        
        public static function IsValidEmail($umail) {
            // empty emails ok
            if ($umail == null) { return true; }
            return Common::ValidateEmail($umail);
        }
        
        public static function IsValidPhone($uphone) {
            // empty phone's ok
            if ($uphone == null) { return true; }
            $u = trim($uphone);
            return (strlen($u) <= 40) && preg_match("/^[xX0-9\~\`\!\@\\#\\\$\%\^\&\*\(\)\-\_\+\|\}\{\\\"\:\?\>\<\\\\\]\[\'\;\/\.\,\s]+$/", $u);
        }
        
        public static function IsValidInfo($user) {
            return User::IsValidName($user->_name) &&
                   User::IsValidLogin($user->_login) &&
                   User::IsValidEmail($user->_email) &&
                   User::IsValidPhone($user->_phone) &&
                   UserType::IsValid($user->_type) &&
                   UserTheme::IsValid($user->_theme);
        }
        
        public static function RemoveUser($uid) {
            $err = 'Unspecified error adding user information to the database.';
            if (is_numeric($uid)) {
                $sql = new SqlDelete('users');
                if (($err = $sql->connect()) == '') {
                    if (($err = $sql->delete('id', $uid)) == '') {
                        $err = $sql->delete_on('project_user_map', 'usid', $uid);
                    }
                }
                $sql->close();
            } else {
                $err = 'Invalid user specified.';
            }
            return $err;
        }
        
        public static function UpdateInfo($user) {
            $ulog = strtolower(trim($user->_login));
            if (!User::IsValidLogin($ulog)) { return 'Invalid user login specified'; }
            $uname = trim($user->_name);
            if (!User::IsValidName($uname)) { return 'Invalid user name specified'; }
            $uemail = trim($user->_email);
            if (!User::IsValidEmail($uemail)) { return 'Invalid email specified'; }
            $uphone = trim($user->_phone);
            if (!User::IsValidPhone($uphone)) { return 'Invalid phone number specified'; }
            $err = 'Unspecified error updating the user information.';
            $sql = new SqlUpdate('users');
            if (($err = $sql->connect()) == '') {
                $cols = array('login','name','acl','email','phone','theme','firstlog');
                $vals = array($ulog, $uname, $user->_type, $uemail, $uphone, $user->_theme, $user->_firstlog);
                $err = $sql->update($cols, $vals, "id", $user->_id);
            }
            $sql->close();
            return $err;
        }
        
        #endregion
	}
?>
