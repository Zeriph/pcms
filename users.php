<?php
    include_once 'scripts/session.php';
    $user = null; $doadd = false; $domod = false;
    
    function add_user($u) {
        global $user, $doadd, $domod;
        $user = User::GetNewUser(-1, $_POST['ulog'], $_POST['uname'], $_POST['utype'], $_POST['umail'], $_POST['uphone'], $_POST['utheme']);
        if (User::Exists($user->getLogin())) {
            $doadd = true;
            return ('Login name \''.htmlentities($user->getLogin()).'\' already exists.');
        } else {
            $domod = true;
            $err = User::AddUser($user);
            if ($err == '') { $u->log('added user '.$user->getFullName().' (id:'.$user->getID().')'); }
            else { return (htmlentities($err)); }
        }
        return '';
    }
    
    function del_user($u, $uid) {
        global $user, $doadd, $domod;
        $oldu = User::GetUser($uid);
        $dorem = true;
        if ($oldu->isAdmin()) {
            $dorem = false;
            foreach (User::GetUsers() as $usr) {
                if ($usr->getID() == $oldu->getID()) { continue; }
                if ($usr->isAdmin()) { $dorem = true; break; }
            }
        }
        if ($dorem) {
            $err = User::RemoveUser($uid);
            if ($err == '' && $oldu != null) { $u->log('deleted '.$oldu->getFullName().' (id:'.$oldu->getID().')'); }
            else { $err = (htmlentities($err)); }
            return $err;
        }
        return ('Cannot delete the only admin account.');
    }
    
    function mod_user($u, $uid, $flog) {
        global $user, $doadd, $domod;
        $oldu = User::GetUser($uid);
        if ($oldu == null) {
            return ('User id not found.');
        } else {
            $user = new User($_POST['val_mod_user'], $_POST['ulog'], $_POST['uname'], $_POST['utype'], $_POST['umail'], $_POST['uphone'], $_POST['utheme'], $flog, $oldu->getLastLogin());
            $login = $user->getLogin();
            if (($oldu->getLogin() != $login) && User::Exists($login)) {
                $domod = true;
                return ('Login name \''.htmlentities($login).'\' already exists.');
            } else {
                if (!User::IsValidInfo($user)) {
                    $domod = true;
                    return ('Invalid user information specified.');
                } else {
                    $err == '';
                    if ($oldu->isAdmin() && ($oldu->getUserType() != $user->getUserType())) {
                        $fadm = false;
                        foreach (User::GetUsers() as $usr) {
                            if ($usr->getID() == $oldu->getID()) { continue; }
                            if ($usr->isAdmin()) { $fadm = true; break; }
                        }
                        if (!$fadm) { $err = 'Cannot remove admin privileges of the only administrator.'; }
                    }
                    $domod = true;
                    if ($err == '') { $err = User::UpdateInfo($user); }
                    if ($err == '') {
                        $u->log('modified user '.$user->getFullName().' (id:'. $user->getID().') with: '.User::GetModDiffStr($oldu, $user));
                        if (isset($_POST['new_pass']) && isset($_POST['con_pass'])) {
                            $pw = $_POST['new_pass'];
                            $cw = $_POST['con_pass'];
                            if ($pw != '' || $cw != '') {
                                if ($pw == $cw) {
                                    // update user password
                                    $err = $user->updatePass($pw);
                                    if ($err == '') { $u->log('modified password for user '.$user->getFullName().' (id:'. $user->getID().')'); }
                                    else { $err = (htmlentities($err)); }
                                } else {
                                    $err = ('Password validation failed.');
                                }
                            }
                        }
                    }
                    else {
                        $u->log('error modifying user: '.$err);
                        $err = (htmlentities($err));
                    }
                    return $err;
                }
            }
        }
        return '';
    }
    
    function print_users($u, $user, $doadd, $sort_type, $rev) {
        if ($u != null) {
            $stypes = array(UserSortType::$NAME, UserSortType::$FNAME, UserSortType::$LNAME, UserSortType::$EMAIL, UserSortType::$PHONE);
            $sadmt = array(UserSortType::$ID, UserSortType::$TYPE, UserSortType::$LASTLOG);
            $sfmt = ''; $safmt = '';
            $cur = ($doadd ? '_current' : '');
            if ($u->isHR()) { // add user item
                echo '
                <div class="citem'.$cur.' b_bottom">
                    <form id="uid_n1" name="uid_n1" method="POST" action="users.php">
                        <input type="hidden" id="val_user" name="val_user" value="-1" />
                        <div class="uinfo" style="cursor:pointer;" onclick="document.getElementById(\'uid_n1\').submit()">
                            <img src="img/login_uadd.gif" />
                            <div style="display:inline-block;font-size:18px;font-weight:bold;"><br>Add a user</div>
                        </div>
                    </form>
                </div>';
                foreach ($sadmt as $st) { $safmt .= '<option value="'.$st.'"'.(($sort_type == $st) ? ' selected' : '').'>'.UserSortType::ToString($st).'</option>'; }
            }
            foreach ($stypes as $st) { $sfmt .= '<option value="'.$st.'"'.(($sort_type == $st) ? ' selected' : '').'>'.UserSortType::ToString($st).'</option>'; }
            echo '<div class="uinfo_search b_bottom">
                <form id="user_sort" name="user_sort" method="POST" action="users.php">
                    Sort by:&nbsp;&nbsp;<select class="dropdown" name="sort_user" id="sort_user">'.$safmt.$sfmt.'</select><br>
                    <div class="ltext" style="margin-left:0.5em;">
                        <div class="checkbox uis_c">
                            <input type="checkbox" name="rev" id="rev" '.(($rev) ? 'checked' : '').' onchange="this.form.submit()" >
                            <label for="rev" onclick="">Reverse Results</label>
                        </div>
                    </div>
                    <div style="width:95%;text-align:right;"><input type="submit" class="button uis_b" value="Sort" /></div>
                </form>
            </div>';
            
            /*<b>&lt;A&gt;</b><br>
            <b>&lt;B&gt;</b><br>
            <b>&lt;C&gt;</b><br>*/
            
            $fmt = '<div class="citem%s b_bottom">
                <form id="uid_%s" name="uid_%s" method="POST" action="users.php">
                    <input type="hidden" id="val_user" name="val_user" value="%s" />
                    <div class="uinfo" style="cursor:pointer;" onclick="document.getElementById(\'uid_%s\').submit()">
                        <img src="%s" /><div style="display:inline-block;"><u>%s</u></div><br>
                        Type: %s<br>Login: %s<br>Email: %s<br>Phone: %s
                    </div>
                </form>
            </div>';
            $users = User::GetUsers(false, $sort_type);
            if ($rev) { $users = array_reverse($users); }
            foreach ($users as $usr) {
                $uid = $usr->getID();
                if ($uid > 0) {
                    $is_cur = (($user != null && $user->getID() == $uid) ? '_current' : '');
                    $usr_email = $usr->getEmail();
                    $usr_email = (($usr_email != '') ? $usr_email : 'No E-mail');
                    $usr_phone = $usr->getPhone();
                    $usr_phone = (($usr_phone != '') ? $usr_phone : 'No Phone');
                    echo sprintf($fmt, $is_cur, $uid, $uid, $uid, $uid, $usr->getImage(), $usr->getFullName(), $usr->getUserType(), $usr->getLastLogin('m/d/Y @ h:i:s A'), $usr_email, $usr_phone)."\n";
                }
            }
        }
    }
    
    function view_user($uid) {
        global $user, $doadd, $domod;
        if ($uid == -1) {
            $doadd = true;
            $user = User::GetNewUser();
        } else {
            $domod = true;
            $user = User::GetUser($uid);
            if ($user == null) { return ('Invalid user specified.'); }
        }
        return '';
    }
    
    $cpage = ''; $u = null; $redir = 'index.php';
    $sort_type = UserSortType::$NAME; $rev = false; $err = '';
    if (Session::Validate()) {
        $u = Session::CurrentUser();
        $cpage = str_replace('.php', '', basename($_SERVER['PHP_SELF']));
        if (Session::ValidateUser($u, $cpage)) {
            $redir = '';
            if ($u->isHR()) { // HR/managers can edit info and admins can edit pass/remove
                if (isset($_POST['add_user'])) {
                    $doadd = true;
                    $user = User::GetNewUser();
                }
                else if (isset($_POST['do_add_user'])) {
                    $err = add_user($u);
                }
                else if (isset($_POST['val_user'])) {
                    if (is_numeric($_POST['val_user'])) {
                        $err = view_user($_POST['val_user']);
                    } else {
                        $err = 'Invalid user specified.';
                    }
                }
                else if (isset($_POST['sort_user'])) {
                    if (is_numeric($_POST['sort_user'])) {
                        $sort_type = UserSortType::FromValue($_POST['sort_user']);
                        $rev = isset($_POST['rev']);
                    } else {
                        $err = 'Invalid user sort specified.';
                    }
                }
                else if (isset($_POST['do_mod_user'])) {
                    if (isset($_POST['val_mod_user']) && is_numeric($_POST['val_mod_user'])) {
                        $err = mod_user($u, $_POST['val_mod_user'], (isset($_POST['uflog']) ? $_POST['uflog'] : 0));
                    } else {
                        $err = 'Invalid user specified.';
                    }
                }
                else if (isset($_POST['do_del_user'])) {
                    if (isset($_POST['val_del_user']) && is_numeric($_POST['val_del_user'])) {
                        $err = del_user($u, $_POST['val_del_user']);
                    } else {
                        $err = 'Invalid user specified.';
                    }
                }
            }
        }
    }
    $redir = Session::GetRedirect($redir);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head><title>PCMS - Personnel Management</title><link rel="stylesheet" type="text/css" href="css/main.css" />
<?php Session::UserCssRedirOrDie($u, $redir); ?><script type="text/javascript" src="js/common.js"></script>
</head><body><div class="main"><div class="header"><div class="ltext">Personnel Management</div><div class="rtext">
<?php $u->printPLink(); ?><a href="logout.php"><img src="img/logout.gif" />Logout</a></div></div>
<div class="cbody"><div class="umenu"><?php $u->printMenu($cpage); ?></div><div class="content">
<!-- start content -->
<div class="clist b_right" style="width:21.5em;overflow-y:auto;">
<?php print_users($u, $user, $doadd, $sort_type, $rev); ?>
</div>
<div style="margin: auto 0; height: 100%; vertical-align:top; text-align: left; overflow: auto;">
<?php
    if ($err != '') { echo Common::JSAlert($err); }
    if ($doadd || $domod) {
        echo '<div class="profile"><center><form method="POST" action="users.php" onsubmit="return validateForm();">';
        $usrname = substr($user->getFirstName(), 0, 1).'. '.$user->getLastName();
        if ($doadd) {
            echo'<input type="hidden" name="do_add_user" id="do_add_user" />';
            $usrname = 'New User';
        } else { // domod
            echo '<input type="hidden" name="do_mod_user" id="do_mod_user" /><input type="hidden" name="val_mod_user" id="val_mod_user" value="'.$user->getID().'" />';
        }
        echo '<font>Edit information for '.htmlentities($usrname).':</font>'.($doadd ? '' : ('<br>Last logged in: '.$user->getLastLogin('h:i:s A m/d/Y'))).'
            <table>
            <tr><td style="width:4em;">Name:</td><td><input id="uname" name="uname" type="text" class="text" maxlength="128" onkeyup="CheckUserName(\'uname\')" value=\''.htmlentities($user->getFullName()).'\' /></td></tr>
            <tr><td>Type:</td><td>
            <select id="utype" name="utype">
                <option value="0"'.($user->isNoAccess() ? ' selected' : '').'>No Access</option>
                <option value="1"'.($user->isBasic() ? ' selected' : '').'>Basic</option>'.
                //<option value="2"'.($user->isHR() ? ' selected' : '').'>HR</option>
                '<option value="3"'.($user->isManager() ? ' selected' : '').'>Manager</option>
                <option value="255"'.($user->isAdmin() ? ' selected' : '').'>Admin</option>
            </select>
            </td></tr>
            <tr><td>Login:</td><td><input id="ulog" name="ulog" type="text" class="text" maxlength="128" onkeyup="CheckLogin(\'ulog\')" value="'.htmlentities($user->getLogin()).'" /></td></tr>
            <tr><td>E-Mail:</td><td><input id="umail" name="umail" type="text" class="text" maxlength="255" onkeyup="CheckEmail(\'umail\')" value="'.htmlentities($user->getEmail()).'" /></td></tr>
            <tr><td>Phone:</td><td><input id="uphone" name="uphone" type="text" class="text" maxlength="40" onkeyup="CheckPhone(\'uphone\')" value="'.htmlentities($user->getPhone()).'" /></td></tr>
            <tr><td>Theme:</td><td><table style="border:0;"><tr>
            <td><div class="radio"><input id="utheme" name="utheme" type="radio" value="0" '.(($user->getTheme() == UserTheme::$DARK) ? 'checked' : '').'><label for="utheme" onclick="">Dark</label></div></td>
            <td><div class="radio"><input id="utheme" name="utheme" type="radio" value="1" '.(($user->getTheme() == UserTheme::$LIGHT) ? 'checked' : '').'><label for="utheme" onclick="">Light</label></div></td>
            </tr></table></td></tr>';
            if (!$doadd) {
                echo '<tr><td>Password:</td><td><div class="checkbox" style="position:relative;top:0.25em;left:1.8em;">
                <input type="checkbox" name="uflog" id="uflog" value="1" '.(($user->isFirstLogin()) ? 'checked' : '').' />
                <label for="uflog" onclick="">User must reset password</label></div></td></tr>';
            }
            echo '</table>';
        if ($domod && $u->isAdmin()) {
            echo '<font><div style="padding-top:0.5em;">Change Password</div></font>
            <table>
                <tr><td style="width:4em;">New:</td><td><input id="new_pass" name="new_pass" type="password" class="text" maxlength="255" onkeyup="CheckPass(\'con_pass\')" value="" /></td></tr>
                <tr><td>Confirm:</td><td><input id="con_pass" name="con_pass" type="password" class="text" maxlength="255" onkeyup="CheckPass(\'new_pass\')" value="" /></td></tr>
                <tr><td colspan="2" style="font-size:10px;text-align:center">All characters allowed, max of 255</td></tr>
            </table>';
        } else if ($doadd) {
            echo '<font><div style="padding-top:0.5em;">Defualt password for users is lowercase <i>password</i></div></font>';
        }
        echo '<input type="submit" class="button" value="Submit" style="width:96px;position:relative;top:0.5em;" /></form></center></div>';
        if ($domod && $u->isAdmin()) {
            echo '
                <center><form method="POST" action="users.php" onsubmit="return confirm(\'Are you sure you wish to delete the user '.htmlentities($user->getFullName()).' (login: '.$user->getLogin().')\')">
                <input type="hidden" name="val_del_user" id="val_del_user" value="'.$user->getID().'" />
                <input type="hidden" name="do_del_user" id="do_del_user" />
                <input type="submit" class="button_rem" value="Remove" style="width:96px;position:relative;top:0.2em;" />
                </form></center>';
        }
    }
?>
</div>
<script type="text/javascript">
function validateForm() {
    <?php if ($domod && $u->isAdmin()) { echo '
    var np = document.getElementById("new_pass").value;
    var cp = document.getElementById("con_pass").value;
    if (np != cp) { alert("Passwords do not match"); return false; }
    '; } ?>
    var l = document.getElementById("ulog").value;
    if (!IsValidLogin(l)) { alert("User login name is invalid"); return false; }
    var n = document.getElementById("uname").value;
    if (!IsValidUserName(n)) { alert("User name is invalid"); return false; }
    var e = document.getElementById("umail").value;
    if (!IsValidEmail(e)) { alert("Email is invalid"); return false; }
    var p = document.getElementById("uphone").value;
    if (!IsValidPhone(p)) { alert("Phone is invalid"); return false; }
    return true;
}
</script>
<!-- end content -->
</div></div><div class="footer"><div class="ltext">(C) <a href="http://zeriph.com/">Zeriph Enterprises LLC</a>
</div><div class="rtext"><?php $u->printFoot(); ?></div><div class="ctext"><div id="jstime" name="jstime">
<div class="noscript"><noscript><a href="help/javascript.html">JavaScript</a> disabled.</noscript></div></div>
<script type="text/javascript">StartTimers();</script></div></div></div></body></html>