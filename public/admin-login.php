<?php
require __DIR__ . '/../config/auth.php';
kf_session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (login_is_locked_out()) {
        $error = 'Too many failed attempts. Please try again in a few minutes.';
    } else {
        $password = (string)($_POST['password'] ?? '');
        if (password_verify($password, ADMIN_PASSWORD_HASH)) {
            login_reset_attempts();
            session_regenerate_id(true);
            $_SESSION['is_admin'] = true;
            header('Location: admin-dashboard.php');
            exit;
        }
        login_register_failure();
        $error = 'Incorrect password.';
    }
}
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin sign-in — Kerala Founders</title>
<link rel="stylesheet" href="assets/style.css"></head>
<body><a class="skip-link" href="#main">Skip to content</a><header class="topbar"><div class="wrap nav">
<a class="brand" href="index.php"><img class="brand-mark" src="assets/logo.png" alt="Kerala Founders">Kerala Founders</a>
</div></header><main id="main">
<section class="page-head"><div class="wrap" style="max-width:420px">
<div class="eyebrow">Admin</div>
<h1>Sign in.</h1>
<form method="post" class="form-card" style="margin-top:20px">
  <?php if ($error): ?><div class="notice" role="alert" style="margin-bottom:16px"><?= htmlspecialchars($error, ENT_QUOTES) ?></div><?php endif; ?>
  <label class="label" for="admin-password">Password</label>
  <input class="field" id="admin-password" type="password" name="password" required autofocus>
  <button class="pill coral" type="submit" style="margin-top:16px">Sign in →</button>
</form>
</div></section></main></body></html>
