<?php
    include_once 'scripts/session.php';
    $cpage = ''; $u = null; $redir = 'index.php';
    if (Session::Validate()) {
        $u = Session::CurrentUser();
        $cpage = str_replace('.php', '', basename($_SERVER['PHP_SELF']));
        if (Session::ValidateUser($u, $cpage)) {
            $redir = '';
        }
    }
    $redir = Session::GetRedirect($redir);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head><title>PCMS - Report Generation</title><link rel="stylesheet" type="text/css" href="css/main.css" />
<?php Session::UserCssRedirOrDie($u, $redir); ?><script type="text/javascript" src="js/common.js"></script>
</head><body><div class="main"><div class="header"><div class="ltext">Report Generation</div><div class="rtext">
<?php $u->printPLink(); ?><a href="logout.php"><img src="img/logout.gif" />Logout</a></div></div>
<div class="cbody"><div class="umenu"><?php $u->printMenu($cpage); ?></div><div class="content">
<!-- start content -->
This section will contain report generation capabilities.

<!-- end content -->
</div></div><div class="footer"><div class="ltext">(C) <a href="http://zeriph.com/">Zeriph Enterprises LLC</a>
</div><div class="rtext"><?php $u->printFoot(); ?></div><div class="ctext"><div id="jstime" name="jstime">
<div class="noscript"><noscript><a href="help/javascript.html">JavaScript</a> disabled.</noscript></div></div>
<script type="text/javascript">StartTimers();</script></div></div></div></body></html>