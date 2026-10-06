<?php

// Copy this file to social-upload-auth.php (on the server, outside the web
// root, next to db.php) and set your own token. social-upload-auth.php itself
// is gitignored — never commit a real token. Generate one with:
//   php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
//
// To rotate: generate a new token, replace the value below in File Manager,
// then give the new token to the automation. The old one stops working the
// moment the file is saved.
define('SOCIAL_UPLOAD_TOKEN', 'paste-your-generated-token-here');
