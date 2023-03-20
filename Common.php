<?php
session_start();
if(empty($_SESSION['admin']['userid']) && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
    header('Location:/zero');
    exit;
}
require_once 'config.php';
require_once 'classes/NotionAPI.class.php';
require_once 'classes/Tools.class.php';

const SITE_URL = 'https://zerorei.top/anime';