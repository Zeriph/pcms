 <?php
    include_once 'common.php';
    include_once 'logger.php';
    include_once 'mysql.php';
    
    /**
     *  The ProjectSortType class is an abstract class used to facilitate the sorting
     *  of projects by name, type and other properties.
     *  
     *  This class is essentially an enum wrapper with extended capabilites.
     */
    abstract class ProjectSortType {
        /**
         * Sort by ID
         */
        public static $ID = 0;
        /**
         * Sort by full name
         */
        public static $NAME = 1;
        /**
         * Sort by project start date
         */
        public static $STARTED = 2;
        /**
         * Sort by project creation date
         */
        public static $CREATED = 3;
        
        /**
         * Gets the sort type from an integer value. (Default NAME)
         * 
         * @param [in] $ival    The value to parse
         * 
         * @return A ProjectSortType::$VALUE, default $NAME
         */
        public static function FromValue($ival) {
            if (ProjectSortType::IsValid($ival)) { return $ival; }
            return ProjectSortType::$NAME;
        }
        
        /**
         * Validates a Project sort type value
         * 
         * @param [in] $ustype      The Project sort type value to check
         * 
         * @return True if the value passed in is a valid Project sort type type
         */
        public static function IsValid($ustype) {
            if (is_numeric($ustype)) {
                switch ($ustype) {
                    case UserSortType::$ID:
                    case UserSortType::$NAME:
                    case UserSortType::$STARTED:
                    case UserSortType::$CREATED:
                        return true;
                    default: break;
                }
            }
            return false;
        }
        
        /**
         * Gets the string representation of the project sort type value
         * 
         * @param [in] $ustype    The value to parse
         * 
         * @return A string representation of the project sort type value parsed.
         */
        public static function ToString($ustype) {
            switch ($ustype) {
                case ProjectSortType::$ID: return "ID";
                case ProjectSortType::$CREATED: return "Creation Date";
                case ProjectSortType::$STARTED: return "Start Date";
                case ProjectSortType::$NAME: default: break;
            }
            return "Project Name";
        }
    }

    class Project {
        public $id;
        public $name;
        public $started;
        public $created;
        public $description;
        public $user_ids;
        
        public function Project($i, $n, $c, $s, $d, $do_decode = false) {
            $this->id = $i;
            $this->name = trim($n);
            $this->created = trim($c);
            $this->started = trim($s);
            $this->description = trim($d);
            $this->user_ids = array();
            if ($do_decode) {
                $this->name = html_entity_decode($this->name);
                $this->created = html_entity_decode($this->created);
                $this->started = html_entity_decode($this->started);
                $this->description = html_entity_decode($this->description);
            }
        }
        
        public function getID() {
            return $this->id;
        }

        public function getName() {
            return $this->name;
        }

        public function log($val) {
            Log::WriteLine($this->name.' (id:'.$this->id.') - '.$val);
        }
        
        public function updateInfo() {
            $pname = trim($this->name);
            if (!Project::IsValidName($pname)) { return 'Invalid project name specified'; }
            $pdesc = trim($this->description);
            if (!Project::IsValidDesc($pdesc)) { return 'Invalid project name specified'; }
            $pstart = trim($this->started);
            if (!Project::IsValidDate($pstart)) { return 'Invalid project start date specified'; }
            $pcreate = trim($this->created);
            if (!Project::IsValidDate($pcreate)) { return 'Invalid project created date specified'; }
            $err = 'Unspecified error updating the project.';
            $sql = new MySql();
            $pname = $sql->Escape($pname);
            $pstart = $sql->Escape($pstart);
            $pcreate = $sql->Escape($pcreate);
            $pdesc = $sql->Escape($pdesc);
            $sqlstr = "UPDATE projects SET pname='".$pname."', pstart='".$pstart."', pcreate=".$pcreate.", pdesc='".$pdesc."' WHERE id='".$this->id."'";
            if ($sql->Execute($sqlstr)) {
                if ($sql->Execute("DELETE FROM project_user_map WHERE pid='".$this->id."'")) {
                    foreach ($this->user_ids as $uid) {
                        $sqlstr = 'INSERT IGNORE INTO project_user_map (pid,uid) VALUES ('.$this->id.','.$uid.')';
                        $sql->Execute($sqlstr);
                    }
                    $err = '';
                }
            }
            $sql->Close();
            return $err;
        }
        
        public function __toString() {
            return $this->name;
        }
        
        // ----- static functions ----- //
        
        public static function AddProject($proj)
        {
            if ($proj == null) { return 'Invalid project object.'; }
            $pname = trim($proj->name);
            if (!Project::IsValidName($pname)) { return 'Invalid project name specified'; }
            $pdesc = trim($proj->description);
            if (!Project::IsValidDesc($pdesc)) { return 'Invalid project name specified'; }
            $pstart = trim($proj->started);
            if (!Project::IsValidDate($pstart)) { return 'Invalid project start date specified'; }
            $pcreate = trim($proj->created);
            if (!Project::IsValidDate($pcreate)) { return 'Invalid project created date specified'; }
            $err = 'Unspecified error adding project information to the database.';
            $sql = new MySql();
            $pname = $sql->Escape($pname);
            $pdesc = $sql->Escape($pdesc);
            $pstart = $sql->Escape($pstart);
            $pcreate = $sql->Escape($pcreate);
            $sqlstr = 'INSERT INTO projects (pname,pstart,pcreate,pdesc) VALUES ("'.$pname.'","'.$pstart.'","'.$pcreate.'","'.$pdesc.'")';
            if ($sql->Execute($sqlstr)) {
                $ui = $sql->Query('SELECT id FROM projects WHERE (pname="'.$pname.'" AND pstart="'.$pstart.'" AND pcreate="'.$pcreate.'" AND pdesc="'.$pdesc.'")');
                if (count($ui) > 0) {
                    $proj->id = $ui[0]['id'];
                    foreach ($proj->user_ids as $usr) {
                        if ($usr != null && $usr >= 0) {
                            $sql->Execute('INSERT IGNORE INTO project_user_map (pid,uid) VALUES ('.$proj->id.','.$usr.')');
                        }
                    }
                    $err = '';
                }
            }
            $sql->Close();
            return $err;
        }
        
        public static function Compare($a, $b)
        {
            return strcmp($a->name, $b->name);
        }
        
        public static function IsValidDate($pdate)
        {
            if ($pdate == null) { return true; }
            $p = trim($pdate);
            if (strlen($p) > 22) { return false; }
            // TODO: htmlentities/html_entity_decode
            // TODO: override alert with "pretty" JS popup
            
            // TODO: search&replace for alert() and do something else with CSS (for <noscrit> etc.)
            // TODO: where else would <noscript> stuff need to be? </noscript>
            // TODO: <META http-equiv="refresh" content="0;URL=/"> 
            
            $re = "/^([0-9]{4}[-\/\s]?[0-9]{2}[-\/\s]?[0-9]{2}[\s]+([0|1][0-9])[:\s]([0-5][0-9])[:\s]([0-5][0-9s])([\s]+([AaPp][Mm]))?)$/";
            return Common::ValidateDateAllFormats($p) || preg_match($re, $p);
        }
        
        public static function IsValidName($pname)
        {
            if ($pname == null) { return false; }
            $p = trim($pname);
            $tsr = strtolower($p);
            if ($tsr == '[add project]' || $tsr == '[new project]') { return false; }
            return (strlen($p) <= 255);
        }
        
        public static function IsValidDesc($pdesc)
        {
            if ($pdesc == null) { return true; }
            $p = trim($pdesc);
            return strlen($p) <= 4096;
        }
        
        public static function IsValidInfo($proj)
        {
            return Project::IsValidName($proj->name) &&
                   Project::IsValidDate($proj->started) &&
                   Project::IsValidDate($proj->created) &&
                   Project::IsValidDesc($proj->description);
        }
        
        public static function GetNewProject()
        {
            $new_proj = new Project(-1, "[New Project]", date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), "Enter a description");
            //$day = date("d");
            //$mth = date("m");
            //easter egg: if ($day == 25 && $mth == 12) { $new_proj->description = "Happy Holiday"; }
            return $new_proj;
        }
        
        public static function GetProject($pid)
        {
            $proj = null;
            if (is_numeric($pid)) {
                if ($pid == -1) { return Project::GetNewProject(); }
                $sql = new MySql();
                $ui = $sql->Query("SELECT id,pname,pstart,pcreate,pdesc FROM projects WHERE id='$pid'");
                if (count($ui) > 0) {
                    $proj = new Project($ui[0]['id'], $ui[0]['pname'], $ui[0]['pcreate'], $ui[0]['pstart'], $ui[0]['pdesc']);
                    $uids = $sql->Query("SELECT uid FROM project_user_map WHERE pid='".$proj->id."'");
                    if (count($uids) > 0) {
                        foreach ($uids as $uid) { $proj->user_ids[] = $uid['uid']; }
                    }
                }
                $sql->Close();
            }
            return $proj;
        }
        
        /*CREATE TABLE pcms.projects (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT, -- unsigned bigint (never know how many projects come/go)
    name VARCHAR(255),                             -- project name (max 255 for size [varchar is +1 so 255==256])
    started VARCHAR(14),                           -- project start date in the format of yyyymmddHHmmss (PHP YmdHis)
    created VARCHAR(14),                           -- project created date in the format of yyyymmddHHmmss (PHP YmdHis)
    description VARCHAR(4096)                      -- the project description; 4096 letters is about 2 paragraphs of text
) CHARACTER SET utf8;*/
        
        public static function GetProjects($include_new = false)
        {
            $projs = array();
            $sql = new MySql();
            $ui = $sql->Query("SELECT id,name,started,created,description FROM projects");
            if (count($ui) > 0) {
                foreach ($ui as $u) {
                    $tproj = new Project($u['id'], $u['name'], $u['created'], $u['started'], $u['description']);
                    $sqlstr = "SELECT id FROM project_user_map WHERE prid='".$u['id']."'";
                    $uids = $sql->Query($sqlstr);
                    if (count($uids) > 0) {
                        foreach ($uids as $uuid) { $tproj->user_ids[] = $uuid['usid']; }
                    }
                    $projs[] = $tproj;
                }
                usort($projs, "Project::Compare");
            }
            $sql->Close();
            if ($include_new) {
                $tprojs[] = Project::GetNewProject();
                $projs = array_merge($tprojs, $projs);
            }
            return $projs;
        }
        
        public static function RemoveProject($pid)
        {
            $err = 'Unspecified error adding project information to the database.';
            if (is_numeric($pid)) {
                $sql = new MySql();
                if ($sql->Execute("DELETE FROM projects WHERE id='$pid'")) {
                    if ($sql->Execute("DELETE FROM project_user_map WHERE pid='$pid'")) {
                        $err = '';
                    }
                }
                $sql->Close();
            } else {
                $err = 'Invalid project specified';
            }
            return $err;
        }
        
        public static function Exists($pname)
        {
            $ret = false;
            $sql = new MySql();
            $ui = $sql->Query("SELECT id,pname,pstart,pcreate,pdesc FROM projects WHERE pname='$pname'");
            if (count($ui) > 0) { $ret = true; }
            $sql->Close();
            return $ret;
        }
	}
?>
