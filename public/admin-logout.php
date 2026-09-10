<?php
require __DIR__ . '/../config/auth.php';
kf_session_start();
session_destroy();
header('Location: admin-login.php');
