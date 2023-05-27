<?php
session_start();
require_once(__DIR__  . '/../models/Database.php');
require_once(__DIR__  . '/configuration.php');
$css_time = filemtime(__DIR__  . '/../css/style.css');
$js_time = filemtime(__DIR__  . '/../js/script.js');
