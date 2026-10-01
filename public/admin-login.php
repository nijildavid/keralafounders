<?php
require __DIR__ . '/../config/auth.php';
kf_session_start();

if (($_GET['restart'] ?? '') === '1') {
    unset($_SESSION['pending_2fa']);
}

$step = !empty($_SESSION['pending_2fa']) ? 'code' : 'password';

$error = '';
if (($_GET['timeout'] ?? '') === '1') {
    $error = 'You were signed out after a period of inactivity. Please sign in again.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (login_is_locked_out()) {
        $error = 'Too many failed attempts. Please try again in a few minutes.';
    } elseif ($step === 'password') {
        $password = (string)($_POST['password'] ?? '');
        if (password_verify($password, ADMIN_PASSWORD_HASH)) {
            login_reset_attempts();
            session_regenerate_id(true);
            if (admin_2fa_enabled()) {
                $_SESSION['pending_2fa'] = true;
                $step = 'code';
            } else {
                $_SESSION['is_admin'] = true;
                $_SESSION['last_activity'] = time();
                header('Location: admin-dashboard.php');
                exit;
            }
        } else {
            login_register_failure();
            $error = 'Incorrect password.';
        }
    } else {
        $code = (string)($_POST['code'] ?? '');
        if (totp_verify(ADMIN_TOTP_SECRET, $code) || totp_backup_code_verify($code)) {
            login_reset_attempts();
            unset($_SESSION['pending_2fa']);
            session_regenerate_id(true);
            $_SESSION['is_admin'] = true;
            $_SESSION['last_activity'] = time();
            header('Location: admin-dashboard.php');
            exit;
        }
        login_register_failure();
        $error = 'Incorrect code.';
    }
}
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin sign-in — Kerala Founders</title><meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" href="assets/style.css?v=<?= (int)@filemtime(__DIR__ . "/assets/style.css") ?>"></head>
<body><a class="skip-link" href="#main">Skip to content</a><header class="topbar"><div class="wrap nav">
<a class="brand" href="index.php"><img class="brand-mark" src="assets/logo.svg" alt="Kerala Founders">Kerala Founders</a>
</div></header><main id="main">
<section class="page-head"><div class="wrap" style="max-width:420px">
<div class="eyebrow">Admin</div>
<?php if ($step === 'code'): ?>
<h1>Enter your code.</h1>
<form method="post" class="form-card" style="margin-top:20px">
  <?php if ($error): ?><div class="error" role="alert" style="margin-bottom:16px"><?= htmlspecialchars($error, ENT_QUOTES) ?></div><?php endif; ?>
  <label class="label" for="admin-code">6-digit code from your authenticator app</label>
  <input class="field" id="admin-code" type="text" inputmode="numeric" autocomplete="one-time-code" name="code" required autofocus>
  <button class="pill" type="submit" style="margin-top:16px">Verify →</button>
  <p style="margin-top:12px"><a href="admin-login.php?restart=1">Start over</a></p>
</form>
<?php else: ?>
<h1>Sign in.</h1>
<form method="post" class="form-card" style="margin-top:20px">
  <?php if ($error): ?><div class="error" role="alert" style="margin-bottom:16px"><?= htmlspecialchars($error, ENT_QUOTES) ?></div><?php endif; ?>
  <label class="label" for="admin-password">Password</label>
  <input class="field" id="admin-password" type="password" name="password" required autofocus>
  <button class="pill" type="submit" style="margin-top:16px">Sign in →</button>
</form>
<?php endif; ?>
</div></section></main></body></html>
