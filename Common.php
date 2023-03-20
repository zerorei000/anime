<?php
session_start();
if(empty($_SESSION['admin']['userid']) && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
    header('Location:/zero');
    exit;
}
require_once 'config.php';
require_once 'NotionAPI.class.php';
require_once 'Tools.class.php';