<?php
require __DIR__ . '/../config/auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = (string)($_POST['password'] ?? '');
    if (password_verify($password, ADMIN_PASSWORD_HASH)) {
        session_regenerate_id(true);
        $_SESSION['is_admin'] = true;
        header('Location: admin.php');
        exit;
    }
    $error = 'Incorrect password.';
}
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin sign-in — Kerala Founders</title>
<link rel="stylesheet" href="assets/style.css"></head>
<body><header class="topbar"><div class="wrap nav">
<a class="brand" href="index.html"><span class="brand-mark">K</span>Kerala Founders</a>
</div></header><main>
<section class="page-head"><div class="wrap" style="max-width:420px">
<div class="eyebrow">Admin</div>
<h1>Sign in.</h1>
<form method="post" class="form-card" style="margin-top:20px">
  <?php if ($error): ?><div class="notice" style="margin-bottom:16px"><?= htmlspecialchars($error, ENT_QUOTES) ?></div><?php endif; ?>
  <label class="label">Password</label>
  <input class="field" type="password" name="password" required autofocus>
  <button class="pill coral" type="submit" style="margin-top:16px">Sign in →</button>
</form>
</div></section></main></body></html>
