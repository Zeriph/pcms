<?php
    class Redirect {
        private $_link = '';
        private $_head = '';
        
        public function __construct($redir = 'index.php') {
            if ($redir != '') {
                $this->_link = $redir;
                $this->_head = '<META http-equiv="refresh" content="0;URL='.$redir.'"><script type="text/javascript">window.location.assign("'.$redir.'");</script>';
            }
        }
        
        public function getLink() {
            return $this->_link;
        }
        
        public function getHead() {
            return $this->_head;
        }
        
        public function __toString() {
            return $this->_head;
        }
    }
?>