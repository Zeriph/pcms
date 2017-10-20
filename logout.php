<?php
    include_once 'scripts/session.php';
    if (Session::Validate()) {
        $u = Session::CurrentUser();
        if ($u != null) { $u->log('logged out'); }
    }
    Session::Logout();
    header("Location: index.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head><title>PCMS - Logout</title><link rel="stylesheet" type="text/css" href="css/main.css" /><link rel="stylesheet" type="text/css" href="css/dark.css" />
<META http-equiv="refresh" content="0;URL=/index.php"><script type="text/javascript">window.location.assign("index.php");</script></head><body style="height:100%;">
<center><br><br><br>Logged out.<br>If you are not automatically redirected <a href="index.php">home</a>, please <a href="index.php">click here</a>.</center></body></html>