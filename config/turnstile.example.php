<?php
// Copy this file to turnstile.php (on the server, outside the web root, same
// place as db.php/auth.php) and fill in your real secret key. turnstile.php
// itself is gitignored — never commit a real secret key.
//
// Get both keys from the Cloudflare dashboard: Turnstile -> your widget.
// The Site Key isn't secret (it's already in config/reference.php, which
// deploys automatically) — only the Secret Key needs to stay private.
//
// Leave this blank/unset and the add-company form simply skips Turnstile
// verification (falls back to the existing honeypot + rate limit) — so it's
// safe to deploy this code before you've created the real file.
define('TURNSTILE_SECRET_KEY', '');
