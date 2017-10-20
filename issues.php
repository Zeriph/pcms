<?php
    include_once 'scripts/session.php';
    $cpage = ''; $u = null; $redir = 'index.php';
    $task = null; $tasks = null; $proj = null; $projs = null; $fopt = ''; $doadd = false; $domod = false;
    if (Session::Validate()) {
        $u = Session::CurrentUser();
        $cpage = str_replace('.php', '', basename($_SERVER['PHP_SELF']));
        if (Session::ValidateUser($u, $cpage)) {
            $redir = '';
            
            /*if (isset($_GET['id'])) {
                $domod = true;
                $proj = Project::GetProject($_GET['id']);
                if ($proj == null) { $err = '<script type="text/javascript">alert("Invalid project specified");</script>'; }
            } else if (isset($_GET['name'])) {
                $domod = true;
                $tpn = trim($_GET['name']);
                if ($tpn != '') {
                    $tprojs = Project::GetProjects();
                    foreach ($tprojs as $pr) {
                        if ($tpn == $pr->name) {
                            $proj = Project::GetProject($pr->id);
                            break;
                        }
                    }
                }
                if ($proj == null) { $err = '<script type="text/javascript">alert("Invalid project specified");</script>'; }
            } else if (isset($_POST['add_task'])) {
                $doadd = true;
            } else if (isset($_POST['do_add_task'])) {
                $proj = new Project(0, $_POST['pname'], $_POST['pcreate'], $_POST['pstart'], $_POST['pdesc']);
                if (isset($_POST['usrlist'])) {
                    foreach ($_POST['usrlist'] as $usr) {
                        $proj->user_ids[] = $usr;
                    }
                }
                $err = Project::AddProject($proj);
                if ($err != '') { $err = '<script type="text/javascript">alert("'.$err.'");</script>'; }
            } else if (isset($_POST['mod_task'])) {
                if (isset($_POST['val_task']) && is_numeric($_POST['val_task'])) {
                    $domod = true;
                    $proj = Project::GetProject($_POST['val_task']);
                    if ($proj == null) { $err = '<script type="text/javascript">alert("Invalid project specified");</script>'; }
                } else {
                    $err = '<script type="text/javascript">alert("Invalid project specified");</script>';
                }
            } else if (isset($_POST['do_mod_task'])) {
                if (isset($_POST['val_mod_task']) && is_numeric($_POST['val_mod_task'])) {
                    $proj = new Project($_POST['val_mod_task'], $_POST['pname'], $_POST['pcreate'], $_POST['pstart'], $_POST['pdesc']);
                    if (isset($_POST['usrlist'])) {
                        foreach ($_POST['usrlist'] as $usr) {
                            $proj->user_ids[] = $usr;
                        }
                    }
                    $domod = true;
                    $err = $proj->updateInfo();
                    if ($err != '') { $err = '<script type="text/javascript">alert("'.$err.'");</script>'; }
                } else {
                    $err = '<script type="text/javascript">alert("Invalid project specified");</script>';
                }
            } else if (isset($_POST['do_del_task'])) {
                if (isset($_POST['val_del_task']) && is_numeric($_POST['val_del_task'])) {
                    $err = Project::RemoveProject($_POST['val_del_task']);
                    if ($err != '') { $err = '<script type="text/javascript">alert("'.$err.'");</script>'; }
                } else {
                    $err = '<script type="text/javascript">alert("Invalid project specified");</script>';
                }
            }
            $projs = Project::GetProjects();
            foreach ($projs as $pr) {
                if ($proj != null && $proj->id == $pr->id) {
                    $fopt .= '<option value="'.$pr->id.'" selected>'.htmlentities($pr->name)."</option>\n";
                } else {
                    $fopt .= '<option value="'.$pr->id.'">'.htmlentities($pr->name)."</option>\n";
                }
            }*/
        }
    }
    $redir = Session::GetRedirect($redir);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head><title>PCMS - Issue Management</title><link rel="stylesheet" type="text/css" href="css/main.css" />
<?php Session::UserCssRedirOrDie($u, $redir); ?><script type="text/javascript" src="js/common.js"></script>
</head><body><div class="main"><div class="header"><div class="ltext">Issue Management</div><div class="rtext">
<?php $u->printPLink(); ?><a href="logout.php"><img src="img/logout.gif" />Logout</a></div></div>
<div class="cbody"><div class="umenu"><?php $u->printMenu($cpage); ?></div><div class="content">
<!-- start content -->
<table style="height:100%;width:100%;">
<tr valign="top">
<?php
    if (count($projs) > 0) {
        echo '<td class="listview"><b>Projects:</b><br><div><form method="GET" action="issues.php">
              <select name="id" id="id" class="users" size="15" onchange="this.form.submit()">'.$fopt.'</select><br>
              <div style="width:100%;text-align:right;position:relative;top:0.6em;right:0.4em;">
              <input type="submit" class="button" style="width:4em;position:relative;left:0.25em;" value="View" /></div>
              </form></div></td><td class="blc" valign="top"><div class="profile">';
        if ($doadd || $domod) {
            echo '<font style="position:relative;top:0.25em;left:0.5em;">Tasks for '.htmlentities($proj->name).'</font>';
        }
        echo '</div>';
    } else {
        echo '<td><font>There are no '.($u->isManager() ? '<a href="projects.php">projects</a>' : 'projects').' to display any tasks for.</font></td>';
    }
?>
</tr></table>

<!-- end content -->
</div></div><div class="footer"><div class="ltext">(C) <a href="http://zeriph.com/">Zeriph Enterprises LLC</a>
</div><div class="rtext"><?php $u->printFoot(); ?></div><div class="ctext"><div id="jstime" name="jstime">
<div class="noscript"><noscript><a href="help/javascript.html">JavaScript</a> disabled.</noscript></div></div>
<script type="text/javascript">StartTimers();</script></div></div></div></body></html>