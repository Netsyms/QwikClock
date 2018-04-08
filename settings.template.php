<?php

// Whether to show debugging data in output.
// DO NOT SET TO TRUE IN PRODUCTION!!!
define("DEBUG", false);

// Database connection settings
// See http://medoo.in/api/new for info
define("DB_TYPE", "mysql");
define("DB_NAME", "qwikclock");
define("DB_SERVER", "localhost");
define("DB_USER", "qwikclock");
define("DB_PASS", "");
define("DB_CHARSET", "utf8");

// Name of the app.
define("SITE_TITLE", "QwikClock");

// Which pages to show the app icon on:
// index, app, both, none
define("SHOW_ICON", "both");
// Where to put the icon: top or menu
// Overridden to 'menu' if MENU_BAR_STYLE is 'fixed'.
define("ICON_POSITION", "menu");
// App menu bar style: fixed or static
define("MENU_BAR_STYLE", "fixed");

// URL of the Business Portal API endpoint
define("PORTAL_API", "http://localhost/accounthub/api.php");
// URL of the Portal home page
define("PORTAL_URL", "http://localhost/accounthub/home.php");
// Business Portal API Key
define("PORTAL_KEY", "123");

// For supported values, see http://php.net/manual/en/timezones.php
define("TIMEZONE", "America/Denver");

// See http://php.net/manual/en/function.date.php
define("TIME_FORMAT", "g:i A"); // 12 hour time
#define("TIME_FORMAT", "G:i"); // 24 hour time

// Used in many places
define("DATETIME_FORMAT", "M j Y g:i:s A"); // 12 hour time
#define("DATETIME_FORMAT", "M j Y G:i:s"); // 24 hour time

// Used for reports
define("DATE_FORMAT", "M j, Y");

// Used on the clock widget
define("LONG_DATE_FORMAT", "l F j");

// Base URL for site links.
define('URL', 'http://localhost/qwikclock');

// Use reCAPTCHA on login screen
// https://www.google.com/recaptcha/
define("RECAPTCHA_ENABLED", FALSE);
define('RECAPTCHA_SITE_KEY', '');
define('RECAPTCHA_SECRET_KEY', '');

// See lang folder for language options
define('LANGUAGE', "en_us");



define("FOOTER_TEXT", "");
define("COPYRIGHT_NAME", "Netsyms Technologies");
