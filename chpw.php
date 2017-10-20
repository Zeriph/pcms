<?php
    include_once 'scripts/session.php';
    $cpage = ''; $u = null; $redir = 'index.php';
    if (Session::Validate()) {
        $u = Session::CurrentUser();
        $cpage = str_replace('.php', '', basename($_SERVER['PHP_SELF']));
        if ($u != null && !$u->isNoAccess()) { // have to do manual session checking for access here
            $redir = ''; $pw_err = '';
            if (isset($_POST['new_pass']) && isset($_POST['con_pass'])) {
                $pw = $_POST['new_pass']; $cw = $_POST['con_pass'];
                if ($pw != '' || $cw != '') {
                    if ($pw == $cw) {
                        $pw_err = $u->updatePass($pw);
                    } else {
                        $pw_err = 'Password validation failed.';
                    }
                }
                if ($pw_err == '') {
                    $u->log('modified user information');
                    if ($u->isFirstLogin()) {
                        $pw_err = $u->updateFirstLogin(0);
                    }
                }
            }
        }
    }
    $redir = Session::GetRedirect($redir);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head><title>PCMS - Change Password</title><link rel="stylesheet" type="text/css" href="css/main.css" />
<?php Session::UserCssRedirOrDie($u, $redir); ?><script type="text/javascript" src="js/common.js"></script>
</head><body><div class="main"><div class="header"><div class="ltext">Change Password</div><div class="rtext">
<?php $u->printPLink(); ?><a href="logout.php"><img src="img/logout.gif" />Logout</a></div></div>
<div class="cbody"><div class="umenu"><?php $u->printMenu($cpage); ?></div><div class="content">
<!-- start content -->
<div class="profile" style="padding-top:0.5em;">
    <center>
    <?php
        // TODO: after password changed and firstlogin, send to 'welcome' (i.e. pcms.php/pcms.php)
    
        if ($pw_err != '') { echo '<font style="color:red;font-size:12px;">Error: '.$pw_err.'</font><br>'; }
        echo '<font>Reset Password for '.$u->getFullName().'</font><br>Login name: '.$u->getLogin().'<br><br>';
    ?>
    <form method="POST" action="chpw.php" onsubmit="return validateForm()"><table>
        <tr><td style="width:4em;">New:</td><td><input id="new_pass" name="new_pass" type="password" maxlength="255" onkeyup="CheckPass('con_pass')" value="" /></td></tr>
        <tr><td>Confirm:</td><td><input id="con_pass" name="con_pass" type="password" maxlength="255" onkeyup="CheckPass('new_pass')" value="" /></td></tr>
        <tr><td colspan="2" style="font-size:10px;text-align:center">All characters allowed, max of 255</td></tr>
    </table>
    <input type="submit" class="button" value="Submit" style="width:7em;position:relative;top:0.5em;" />
    </form></center>
</div>
<script type="text/javascript">
function validateForm() {
    var n = document.getElementById("new_pass").value;
    var c = document.getElementById("con_pass").value;
    if (n != c) { alert("Passwords do not match"); return false; }
    if (n == "" && c == "") { alert("Password cannot be empty"); return false; }
    return true;
}
</script>
<!-- end content -->
</div></div><div class="footer"><div class="ltext">(C) <a href="http://zeriph.com/">Zeriph Enterprises LLC</a>
</div><div class="rtext"><?php $u->printFoot(); ?></div><div class="ctext"><div id="jstime" name="jstime">
<div class="noscript"><noscript><a href="help/javascript.html">JavaScript</a> disabled.</noscript></div></div>
<script type="text/javascript">StartTimers();</script></div></div></div></body></html>