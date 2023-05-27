<?php
switch ($_SERVER['DOCUMENT_ROOT']) {
    case 'C:/xampp/htdocs':
        define('LINKS_DB_HOST', 'localhost');
        define('LINKS_DB_USERNAME', 'root');
        define('LINKS_DB_PASSWORD', '');
        define('LINKS_DB_NAME', 'links');
        break;
}
