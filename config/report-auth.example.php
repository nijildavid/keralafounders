<?php

// Copy this file to report-auth.php (outside the web root) and set your own
// token. report-auth.php itself is gitignored — never commit a real token.
// Generate one with:
//   php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
define('REPORT_API_TOKEN', 'paste-your-generated-token-here');
