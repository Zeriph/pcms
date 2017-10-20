<?php
    include_once 'menu.php';
    include_once 'user_type.php';
    
    // TODO: these might be overkill for PCMS ...
    // instead, maybe make some sort of "server setting" that connects to a chat/email backend,
    // same thing for "file repo" (ftp/smb/nfs all serve this purpose). "assets" is kind of
    // point-less (realistically, "large" amounts of assets need a full AMS, otherwise, XLSX
    // could serve that purpose). PCMS is more for simplistic project management with invoicing
    
    Menu::$Items = [
        "issues" => ["Issues", UserType::$BASIC, "Issue Management"],
        "projects" => ["Projects", UserType::$MANAGER, "Project Management"],
        //"messages" => ["Messages", UserType::$BASIC, "User Messages"],
        //"chat" => ["Chat", UserType::$BASIC, "Local Chat"],
        //"assets" => ["Assets", UserType::$MANAGER, "Asset Management"],
        //"repo" => ["Files", UserType::$BASIC, "File Repository"],
        "time" => ["Time Sheet", UserType::$BASIC, "Time Sheet"],
        //"reports" => ["Reports", UserType::$HR, "Report Generation"],
        "invoice" => ["Invoicing", UserType::$HR, "Invoice Management"],
        "users" => ["Users", UserType::$BASIC, "User Management"],
        //"logs" => ["Logs", UserType::$ADMIN, "Log Viewer"],
        //"server" => ["Server", UserType::$ADMIN, "Server Management"],
    ];
?>
