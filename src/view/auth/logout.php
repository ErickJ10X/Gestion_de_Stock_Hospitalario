<?php
session_start();
require_once(__DIR__ . '/../../controller/authController.php');
$authController = new authController();
$authController->logout();
