<?php
// This is a template. On the server, create a NEW file named turnstile.php
// (outside the web root, same place as db.php/auth.php) with these lines and
// your real secret key. The deploy only copies reference.php into that folder,
// so this example file is not there to copy. turnstile.php itself is
// gitignored — never commit a real secret key.
//
// Get both keys from the Cloudflare dashboard: Turnstile -> your widget.
// The Site Key isn't secret (it's already in config/reference.php, which
// deploys automatically) — only the Secret Key needs to stay private.
//
// Leave this blank/unset and the add-company form simply skips Turnstile
// verification (falls back to the existing honeypot + rate limit) — so it's
// safe to deploy this code before you've created the real file.
define('TURNSTILE_SECRET_KEY', '');
