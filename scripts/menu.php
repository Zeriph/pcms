<?php
    abstract class Menu {
        public static $Items = array();
        
        public static $TEXT = 0;
        public static $ACL = 1;
        public static $HEADER = 2;
        
        public static function GetItems() {
            $ret = array();
            foreach (Menu::$Items as $item => $vals) {
                $ret[] = new MenuItem($item);
            }
            return $ret;
        }
        
        public static function GetACL($cpage) {
            return (array_key_exists($cpage, Menu::$Items) ? Menu::$Items[$cpage][Menu::$ACL] : '');
        }
        
        public static function GetText($cpage) {
            return (array_key_exists($cpage, Menu::$Items) ? Menu::$Items[$cpage][Menu::$TEXT] : '');
        }
        
        public static function GetHeader($cpage) {
            return (array_key_exists($cpage, Menu::$Items) ? Menu::$Items[$cpage][Menu::$HEADER] : '');
        }
    }
    
    class MenuItem {
        private $_val = '';
        private $_acl = 0;
        private $_text = '';
        private $_head = '';
        
        public function __construct($cpage = '') {
            if ($cpage != '' && array_key_exists($cpage, Menu::$Items)) {
                $this->_val = $cpage;
                $this->_acl = Menu::GetACL($cpage);
                $this->_text = Menu::GetText($cpage);
                $this->_head = Menu::GetHeader($cpage);
            }
        }
        
        public function getACL() { return $this->_acl; }
        public function getText() { return $this->_text; }
        public function getHeader() { return $this->_head; }
        public function getValue() { return $this->_val; }
        
        public function __toString() {
            return $this->getValue();
        }
    }
?>