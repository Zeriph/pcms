<?php
    include_once 'scripts/session.php';
    $cpage = ''; $u = null; $redir = 'index.php';
    $file = ''; $logre = false; $revc = ''; $linec = 'checked'; $wrapc = 'checked';
    if (Session::Validate()) {
        $u = Session::CurrentUser();
        $cpage = str_replace('.php', '', basename($_SERVER['PHP_SELF']));
        $redir = 'pcms.php';
        if (Session::ValidateUser($u, $cpage)) {
            if ($u->isAdmin()) {
                $redir = '';
                if (isset($_POST['logre'])) {
                    $logre = true;
                    if (!isset($_POST['nolines'])) { $linec = ''; }
                    if (!isset($_POST['nowrap'])) { $wrapc = ''; }
                    if (isset($_POST['rev'])) { $revc = 'checked'; }
                }
                if (isset($_GET['nolines'])) { $linec = ''; }
                if (isset($_GET['nowrap'])) { $wrapc = ''; }
                if (isset($_GET['rev'])) { $revc = 'checked'; }
                if (isset($_POST['log']) || isset($_GET['log'])) {
                    $file = Log::GetValidatedLogName((isset($_POST['log']) ? $_POST['log'] : $_GET['log']));
                }
                if (isset($_POST['log_clear'])) {
                    $file = Log::GetValidatedLogName($_POST['log_clear']);
                    if (Log::Clear($file)) { $u->log("cleared log $file"); }
                }
            }
        }
    }
    $redir = Session::GetRedirect($redir);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head><title>PCMS - Log Viewer</title><link rel="stylesheet" type="text/css" href="css/main.css" />
<?php Session::UserCssRedirOrDie($u, $redir); ?><script type="text/javascript" src="js/common.js"></script>
</head><body><div class="main"><div class="header"><div class="ltext">Log Viewer</div><div class="rtext">
<?php $u->printPLink(); ?><a href="logout.php"><img src="img/logout.gif" />Logout</a></div></div>
<div class="cbody"><div class="umenu"><?php $u->printMenu($cpage); ?></div><div class="content">
<!-- start content -->
<div id="log_items" name="log_items" class="clist b_right" style="overflow:auto;"><form method="POST" action="logs.php">
<?php
    $el = null;
    $fmt = '<div class="citem%s litem b_bottom" id="logitm%s" name="logitm%s" ><label>%s<br>Size: %s&nbsp;&nbsp;<font>(%s)</font><br><font>Last Modified: %s</font></label><input type="submit" name="log" id="log" value="%s"></div>'."\n";
    foreach (Log::GetLogs(($revc != '')) as $l) {
        $cur = '';
        $n = $l->getName();
        if ($file != null && $file == $n) {
            $el = $l;
            $cur = '_current';
        }
        echo sprintf($fmt, $cur, $cur, $cur, $n, $l->getFileSizeString(), $l->getEntryCountString(), $l->getLastModified(), $n);
    }
?>
</form></div><div class="log_content">
<?php
    // TODO: is this really necessary?  .. MUST it be done in PHP before JS? ... ugh ....
    $jsln = (($linec == '') ? "'table-cell'" : "'none'");
    $jsnw = (($wrapc == '') ? "'normal'" : "'nowrap'");
    $logops = '<div class="checkbox"><input type="checkbox" name="nolines" id="nolines" '.$linec.' /><label for="nolines" title="Show line numbers" onclick="doLines()">Lines</label></div><div class="checkbox"><input type="checkbox" name="nowrap" id="nowrap" '.$wrapc.' /><label for="nowrap" title="Word wrap" onclick="doWrap()">Wrap</label></div><div class="checkbox"><input type="checkbox" name="rev" id="rev" '.$revc.' /><label for="rev" title="Show first entry last" onclick="doRev()">Reverse</label></div>';
    if ($el != null) {
        $ecnt = $el->getEntryCount();
        // we echo the javascript here to scroll the log menu so we don't have to do another check of ($el != null) above
        // NOTE: the line '<style>.logclear > ... { visibility: ... }</style>' is there for a FF bug that starts the opacity transition on page load (flash-fade)
        echo '<script type="text/javascript">ScrollTo(\'logitm_current\', \'log_items\');</script>
        <div class="loghdr">
        <table>
            <tr>
                <td class="loginf">
                    '.$file.'<br>
                    Size: '.$el->getFileSizeString().'&nbsp;&nbsp;<font style="font-size:12px;">('.$ecnt.' line'.(($ecnt > 1 || $ecnt == 0) ? 's' : '').')</font><br>
                    Last Modified: '.$el->getLastModified().'
                </td>
                <td class="logbtns">
                    <div>
                    <div class="logrbtn">
                        <div id="logops" name="logops">
                            <noscript>
                            <form method="POST" action="logs.php">
                                '.$logops.'
                                <input type="submit" id="logre" name="logre" class="button" value="Refresh" />
                                <input type="hidden" id="log" name="log" value="'.$file.'" />
                            </form>
                            </noscript>
                            <script type="text/javascript">document.write(\''.$logops.'\');</script>
                        </div>
                    </div>
                    <div class="logcbtn">
                        <style>.logclear > input+label + form > div { visibility: hidden; }</style>
                        <div class="logclear">
                            <input type="checkbox" name="clear" id="clear" />
                            <label for="clear" class="button_rem" title="Clear '.$file.'"><font>Clear</font></label>
                            <form method="POST" action="logs.php">
                                <input type="hidden" name="log_clear" id="log_clear" value="'.$file.'" />
                                <div>Clear \''.$file.'\' file? <input type="submit" class="button_rem" value="OK" title="Clear '.$file.'" /></div>
                            </form>
                        </div>
                    </div>
                    </div>
                </td>
            </tr>
        </table></div>
        
        <div class="logview b_top"><div>';
                $el->printTable(($linec != ''), ($wrapc == ''));
                echo '</div></div>';
            } else {
                echo '<font style="font-size:20px;position:relative;top:0.25em;">&nbsp;Please select a file</font>';
            }
?>
    </div>
    <script type="text/javascript">
        function doLines(){
            var elms = document.getElementsByClassName('lcnt');
            if (elms !== null) { for (var i = 0; i < elms.length; i++) { elms[i].style.display = ((elms[i].style.display == '') ? <?php echo $jsln; ?> : ''); } }
        }
        function doWrap(){
            var elms = document.getElementsByClassName('linfo');
            if (elms !== null) { for (var i = 0; i < elms.length; i++) { elms[i].style.whiteSpace = ((elms[i].style.whiteSpace == '') ? <?php echo $jsnw; ?> : ''); } }
        }
        function doRev(){
            var elm = document.getElementById('logentry');
            if (elm != null) {
                var elms = elm.getElementsByTagName('div'); // rows
                if (elms !== null) {
                    var rows = [];
                    while (elm.hasChildNodes()) {
                        rows.push(elm.firstChild);
                        elm.removeChild(elm.firstChild);
                    }
                    elm.appendChild(rows[0]);
                    for (var i = (rows.length-1); i > 0; i--) {
                        elm.appendChild(rows[i]);
                    }
                }
            }
        }
        function doCallback() {
            //alert("Starting logs monitor");
        }
    </script>
<!-- end content -->
</div></div><div class="footer"><div class="ltext">(C) <a href="http://zeriph.com/">Zeriph Enterprises LLC</a>
</div><div class="rtext"><?php $u->printFoot(); ?></div><div class="ctext"><div id="jstime" name="jstime">
<div class="noscript"><noscript><a href="help/javascript.html">JavaScript</a> disabled.</noscript></div></div>
<script type="text/javascript">StartTimers(doCallback);</script></div></div></div></body></html>