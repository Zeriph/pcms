<?php
    /**
     * The UserType class is an abstract class used to facilitate the parsing
     * and setting of user type.
     *
     * This class is essentially an enum wrapper with extended capabilities.
     */
    abstract class UserType {
        /**
         * The user has no access to the system.
         * If a user with access is changed to NO_ACCESS during an active
         * session, they will be logged off and won't be able to log back
         * in until given access.
         */
        public static $NO_ACCESS = 0;
        /**
         * The user has basic access to the system which means they can view
         * certain portions of the system (projects, etc.) and edit their own
         * information (theme, user info, and items created by them).
         */
        public static $BASIC = 1;
        /**
         * The user has the same access level as a BASIC user plus they can add/edit
         * user information and have access to the invoicing and time sheet services.
         */
        public static $HR = 2;
        /**
         * The user has the same access level as an HR user plus they can add/edit
         * project information and inventory/asset control.
         */
        public static $MANAGER = 3;
        /**
         * The user has full access to all areas of the system plus administrative
         * access to certain server/service functions. This is not a SYSTEM admin,
         * that is to say that this user will not be given local OS admin privileges.
         */
        public static $ADMIN = 255;
        
        /**
         * Validates the user access level specified against the check value.
         * Does not determine if a user has access to a specific area, merely
         * if the user type is a valid type and is not NO_ACCESS.
         * 
         * @param [in] $user_val        The user type value to check
         * @param [in] $check_val       The UserType value to check against
         * 
         * @return True if the user access level specified is greater than
         * the check value specified
         */
        public static function AccessAllowed($user_val, $check_val) {
            if (is_numeric($user_val) && is_numeric($check_val)) {
                if ($user_val <= UserType::$NO_ACCESS) { return false; }
                if ($user_val >= $check_val) { return true; }
            }
            return false;
        }
        
        /**
         * Gets the Type from an integer value. (Default User::Type::$NO_ACCESS)
         * 
         * @param [in] $ival    The value to parse
         * 
         * @return A User::Type::$VALUE, default User::Type::$NO_ACCESS
         */
        public static function FromValue($ival) {
            if (UserType::IsValid($ival)) {
                return $ival;
            }
            return UserType::$NO_ACCESS;
        }
        
        /**
         *  Checks if a type value is a valid type
         *  
         *  @param [in] $type   The type value to validate
         * 
         *  @return True if a valid value
         */
        public static function IsValid($type) {
            if (is_numeric($type)) {
                switch ($type) {
                    case UserType::$BASIC: case UserType::$HR:
                    case UserType::$MANAGER: case UserType::$ADMIN:
                        return true;
                    default: break;
                }
            }
            return false;
        }
        
        /**
         * Gets the string representation of the Type value
         * 
         * @param [in] $type    The value to parse
         * 
         * @return A string representation of the Type value parsed.
         */
        public static function ToString($type) {
            switch ($type) {
                case UserType::$BASIC: return "Basic";
                case UserType::$HR: return "HR";
                case UserType::$MANAGER: return "Manager";
                case UserType::$ADMIN: return "Administrator";
                default: break;
            }
            return "No Access";
        }
    }
    
    /**
     * The theme class is an abstract class used to facilitate
     * the getting/setting of the User's theme settings
     */
    abstract class UserTheme {
        /**
         * A dark user theme specified
         */
        public static $DARK = 0;
        /**
         * A light user theme specified
         */
        public static $LIGHT = 1;
        /**
         * A user defined theme specified
         */
        public static $USER_DEFINED = 255;
        
        /**
         * Gets the theme from an integer value. (Default DARK)
         * 
         * @param [in] $ival    The value to parse
         * 
         * @return A UserTheme::$VALUE, default $DARK
         */
        public static function FromValue($ival) {
            if (UserTheme::IsValid($ival)) { return $ival; }
            return UserTheme::$DARK;
        }
        
        /**
         * Validates a theme value
         * 
         * @param [in] $theme      The theme value to check
         * 
         * @return True if the value passed in is a valid theme type
         */
        public static function IsValid($theme) {
            if (is_numeric($theme)) {
                switch ($theme) {
                    case UserTheme::$DARK:
                    case UserTheme::$LIGHT:
                    case UserTheme::$USER_DEFINED:
                        return true;
                    default: break;
                }
            }
            return false;
        }
        
        /**
         * Gets the string representation of the theme value
         * 
         * @param [in] $theme    The value to parse
         * 
         * @return A string representation of the theme value parsed.
         */
        public static function ToString($theme) {
            switch ($theme) {
                case UserTheme::$LIGHT: return "Light";
                case UserTheme::$DARK: return "Dark";
                default; break;
            }
            return "User Defined";
        }
    }
    
    /**
     *  The UserSortType class is an abstract class used to facilitate the sorting
     *  of users by name, type and other properties.
     *  
     *  This class is essentially an enum wrapper with extended capabilites.
     */
    abstract class UserSortType {
        /**
         * Sort by ID
         */
        public static $ID = 0;
        /**
         * Sort by login name
         */
        public static $LOGIN = 1;
        /**
         * Sort by full name (same as first name)
         */
        public static $NAME = 2;
        /**
         * Sort by user type
         */
        public static $TYPE = 3;
        /**
         * Sort by email address
         */
        public static $EMAIL = 4;
        /**
         * Sort by phone number
         */
        public static $PHONE = 5;
        /**
         * Sort by first name
         */
        public static $FNAME = 6;
        /**
         * Sort by last name
         */
        public static $LNAME = 7;
        /**
         * Sort by theme
         */
        public static $THEME = 8;
        /**
         * Sort by is first login
         */
        public static $FIRSTLOG = 9;
        /**
         * Sort by users last login
         */
        public static $LASTLOG = 10;
        /**
         * Sort by if the user has an image (i.e. != img/login_u.gif)
         */
        public static $HAS_IMAGE = 11;
        
        /**
         * Gets the sort type from an integer value. (Default NAME)
         * 
         * @param [in] $ival    The value to parse
         * 
         * @return A UserSortType::$VALUE, default $NAME
         */
        public static function FromValue($ival) {
            if (UserSortType::IsValid($ival)) { return $ival; }
            return UserSortType::$NAME;
        }
        
        /**
         * Validates a user sort type value
         * 
         * @param [in] $ustype      The user sort type value to check
         * 
         * @return True if the value passed in is a valid user sort type type
         */
        public static function IsValid($ustype) {
            if (is_numeric($ustype)) {
                switch ($ustype) {
                    case UserSortType::$ID:
                    case UserSortType::$NAME:
                    case UserSortType::$TYPE:
                    case UserSortType::$EMAIL:
                    case UserSortType::$PHONE:
                    case UserSortType::$FNAME:
                    case UserSortType::$LNAME:
                    case UserSortType::$THEME:
                    case UserSortType::$FIRSTLOG:
                    case UserSortType::$LASTLOG:
                    case UserSortType::$HAS_IMAGE:
                        return true;
                    default: break;
                }
            }
            return false;
        }
        
        /**
         * Gets the string representation of the user sort type value
         * 
         * @param [in] $ustype    The value to parse
         * 
         * @return A string representation of the user sort type value parsed.
         */
        public static function ToString($ustype) {
            switch ($ustype) {
                case UserSortType::$ID: return "ID";
                case UserSortType::$TYPE: return "Type";
                case UserSortType::$EMAIL: return "E-mail";
                case UserSortType::$PHONE: return "Phone Number";
                case UserSortType::$FNAME: return "First Name";
                case UserSortType::$LNAME: return "Last Name";
                case UserSortType::$THEME: return "Theme Type";
                case UserSortType::$FIRSTLOG: return "Never Logged In";
                case UserSortType::$LASTLOG: return "Last Login Time";
                case UserSortType::$HAS_IMAGE: return "Have An Image";
                case UserSortType::$NAME: default: break;
            }
            return "Full Name";
        }
    }
?>