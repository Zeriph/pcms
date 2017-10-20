<?php
    include_once 'scripts/session.php';
    $cpage = ''; $u = null; $redir = 'index.php';
    if (Session::Validate()) {
        $u = Session::CurrentUser();
        $cpage = str_replace('.php', '', basename($_SERVER['PHP_SELF']));
        if ($u != null && !$u->isNoAccess()) { // have to do manual session checking for access here
            $redir = ''; $ph_err = ''; $pw_err = ''; $sc_err = '';
            if (isset($_POST['utheme']) || isset($_POST['uphone']) || (isset($_POST['new_pass']) && isset($_POST['con_pass']))) {
                $th = $_POST['utheme'];
                $ph = $_POST['uphone'];
                $pw = $_POST['new_pass'];
                $cw = $_POST['con_pass'];
                if ($u->getThemeType() != $th) { $sc_err = $u->updateTheme($th); }
                if ($u->getPhone() != $ph) {
                    $ph_err = $u->updatePhone(html_entity_decode($ph));
                    if ($ph_err == '') { $u->log('User updated phone number'); }
                }
                if ($pw != '' || $cw != '') {
                    if ($pw == $cw) {
                        $pw_err = $u->updatePass($pw);
                    } else {
                        $pw_err = 'Password validation failed.';
                    }
                }
                if ($pw_err == '' && $u->isFirstLogin()) {
                    $pw_err = $u->updateFirstLogin(0);
                }
            }
        }
    }
    $redir = Session::GetRedirect($redir);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head><title>PCMS - User Profile</title><link rel="stylesheet" type="text/css" href="css/main.css" />
<?php Session::UserCssRedirOrDie($u, $redir); ?><script type="text/javascript" src="js/common.js"></script>
</head><body><div class="main"><div class="header"><div class="ltext">User Profile</div><div class="rtext">
<?php $u->printPLink(); ?><a href="logout.php"><img src="img/logout.gif" />Logout</a></div></div>
<div class="cbody"><div class="umenu"><?php $u->printMenu($cpage); ?></div><div class="content">
<!-- start content -->
<div class="profile" style="padding-top:0.5em;"><center>
    <?php
    if ($sc_err != '') { echo '<font color="red">'.$sc_err.'</font><br>'; }
    if ($ph_err != '') { echo '<font color="red">'.$ph_err.'</font><br>'; }
    if ($pw_err != '') { echo '<font color="red">'.$pw_err.'</font><br>'; }
    echo '<font>Profile Details for '.$u->getFullName();
    echo '<br></font>Last logged in: '.$u->getLastLogin('m/d/Y @ h:i:s A'); ?>
<br><form method="POST" onsubmit="return validateForm()" action="profile.php">
<table><tr><td style="width:4em;">Name:</td><td>
<input id="uname" name="uname" type="text" class="text" maxlength="128" readonly value="<?php echo htmlentities($u->getFullName()); ?>" /></td></tr>
<tr><td>Type:</td><td><input id="utype" name="utype" type="text" class="text" readonly value="<?php echo $u->getUserType(); ?>" /></td></tr>
<tr><td>Login:</td><td><input id="ulog" name="ulog" type="text" class="text" maxlength="128" readonly value="<?php echo htmlentities($u->getLogin()); ?>" /></td></tr>
<tr><td>E-Mail:</td><td><input id="umail" name="umail" type="text" class="text" maxlength="255" readonly value="<?php echo htmlentities($u->getEmail()); ?>" /></td></tr>
<tr><td>Phone:</td><td><input id="uphone" name="uphone" type="text" class="text" maxlength="40" onkeyup="CheckPhone('uphone')" value="<?php echo htmlentities($u->getPhone()); ?>" /></td></tr>
<tr><td>Theme:</td><td><table style="border:0;"><tr><td><div class="radio"><input id="utheme" name="utheme" type="radio" value="0" <?php if ($u->getTheme() == UserTheme::$DARK) { echo 'checked'; } ?> >
<label for="utheme" onclick="">Dark</label></div></td><td><div class="radio"><input id="utheme" name="utheme" type="radio" value="1" <?php if ($u->getTheme() == UserTheme::$LIGHT) { echo 'checked'; } ?>  >
<label for="utheme" onclick="">Light</label></div></td></tr></table></td></tr></table><font><div style="padding-top:0.5em;">Change Password</div></font><table>
<tr><td style="width:4em;">New:</td><td><input id="new_pass" name="new_pass" type="password" class="text" maxlength="255" onkeyup="CheckPass('con_pass')" value="" /></td></tr>
<tr><td>Confirm:</td><td><input id="con_pass" name="con_pass" type="password" class="text" maxlength="255" onkeyup="CheckPass('new_pass')" value="" /></td></tr>
<tr><td colspan="2" style="font-size:10px;text-align:center">All characters allowed, max of 255</td></tr>
</table><br><input type="submit" class="button" style="width:96px;" value="Submit" style="position:relative; top:0.5em;" />
</form></center></div>
<script type="text/javascript">
function validateForm() {
    var n = document.getElementById("new_pass");
    var c = document.getElementById("con_pass");
    if (n.value != c.value) { alert("Passwords do not match."); return false; }
    var p = document.getElementById("uphone").value;
    if (!IsValidPhone(p)) { alert("Phone is invalid."); return false; }
    return true;
}
</script>
<!-- end content -->
</div></div><div class="footer"><div class="ltext">(C) <a href="http://zeriph.com/">Zeriph Enterprises LLC</a>
</div><div class="rtext"><?php $u->printFoot(); ?></div><div class="ctext"><div id="jstime" name="jstime">
<div class="noscript"><noscript><a href="help/javascript.html">JavaScript</a> disabled.</noscript></div></div>
<script type="text/javascript">StartTimers();</script></div></div></div></body></html>