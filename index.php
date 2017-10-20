<?php
    include_once 'scripts/session.php';
    $err = ''; $u = null; $p = ''; $alert = ''; $nsalert = '';
    $redir = ''; $unm = ''; $dolog = true; $logd = false;
    if (Session::Validate()) {
        // already logged in, so redirect to pcms.php        
        $u = Session::CurrentUser();
        if ($u != null) {
            if ($u->isNoAccess()) {
                $u->log("user with previous login and NO_ACCESS permissions accessing main page");
                $err = 'You are not authorized on this system.';
                $dolog = false;
                Session::Logout();
            }
            else { $redir = ($u->isFirstLogin() ? 'chpw.php' : 'pcms.php'); }
        }
        else { Session::Logout(); } // logout on invalid session
    }
    else {
        if (isset($_POST['uname'])) {
            $unm = trim(html_entity_decode($_POST['uname']));
            if ($unm == '') { $err = 'Must enter a user name.'; $dolog = false; }
        }
        if (isset($_POST['upass'])) { $p = $_POST['upass']; }
        if ($unm != '') {
            // check login credentials here
            if (Session::Login($unm, $p)) {
                $u = Session::CurrentUser();
                if ($u != null) {
                    if ($u->isNoAccess()) {
                        $u->log("user with NO_ACCESS permissions attempted login");
                        $err = 'You are not authorized on this system.';
                        $dolog = false;
                        Session::Logout();
                    }
                    else {
                        $u->updateLastLogin();
                        $u->log("logged in");
                        $logd = true;
                        $redir = 'pcms.php';
                        if ($u->isFirstLogin()) {
                            $dolog = false;
                            $err = 'You must change your password.';
                            $redir = 'chpw.php';
                        }
                    }
                }
                else {
                    $err = "Login session has become invalid: ".Session::GetLastError();
                    Session::Logout();
                }
            }
            else { $err = "Invalid credentials for user '$unm'."; }
        }
    }
    if ($err != '') {
        if ($dolog) {
            if ($u != null) {
                $u->log($err);
            } else {
                Log::WriteLine($err);
            }
        }
        $nsalert = '<noscript>'.$err.'</noscript>';
        $alert = '<script type="text/javascript">alert("'.$err.'");</script>';
    }
    $redir = Session::GetRedirect($redir);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head><title>PCMS - Login</title><link rel="stylesheet" type="text/css" href="css/main.css" />
<link rel="stylesheet" type="text/css" href="css/dark.css" /><style>
.login{height:auto;width:auto;}.login img{position:relative;top:0.15em;height:3em;width:3em;}.login .text{height:2em;width:15em;font-family:Consolas;}</style>
<?php if ($redir != '' && $redir != null) { echo $redir->getHead(); } ?>
<script type="text/javascript">
function loadLogin() {
    document.getElementById("flog").style.display = "initial";
    document.getElementById("nojs").style.display = "none";
}
</script>
</head><body style="height:100%;width:100%;" onload="loadLogin()">
<table style="height:100%;width:100%;"><tr><td><center>
<div id="nojs" name="nojs" style="font-size:18px;font-weight:bold;">
<font style="color:red;">JavaScript is not enabled.</font><br><br>
While PCMS does not make extensive use of<br>
<a href="help/javascript.html">JavaScript</a>,
it does require it to work properly.
</div>
<form method="POST" action="index.php" id="flog" name="flog" style="display:none;"><table class="login">
<tr style="height:8em;">
    <td></td>
    <td><font style="font-size:3em;">PCMS Login</font></td>
</tr>
<tr>
    <td><img src="img/login_u.gif" class="icon" alt="User Name" title="User Name" /></td>
    <td>
    <?php
        echo '<input id="uname" name="uname" type="text" class="text" maxlength="128" value="'.htmlentities($unm).'" title="User Name" />';
    ?>
    </td>
</tr>
<tr>
    <td><img src="img/login_p.gif" class="icon" alt="Password" title="Password" /></td>
    <td><input id="upass" name="upass" type="password" class="text" maxlength="255" title="Password" /></td>
</tr>
<tr style="height:4em;">
    <td colspan="2" style="text-align:right;"><input type="submit" class="button" style="width:7em;" value="Login" /></td>
</tr>
<tr style="height:5em;">
    <td colspan="2"><div style="height:4em;position:absolute;left:0;width:100%;text-align:center;">
    <?php
        echo $nsalert;
        if ($logd) { echo 'Logged in! If you are not automatically redirected, please <a href="'.$redir->getLink().'">click here</a>'; }
    ?>
    </div></td>
</tr>
</table></form>

</center></td></tr><tr style="height:4em;text-align:center;"><td>
<?php echo '<a href="help/">version: '.PCMS::$VERSION.'</a>'; ?><br>
<font style="font-size: 10px;">(C) Zeriph Enterprises LLC</font><br><br></td></tr></table><?php echo $alert ?></body></html>