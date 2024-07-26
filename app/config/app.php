''
<?php

/**
 * Configuration
 *
 * For more info about constants please @see http://php.net/manual/en/function.define.php
 */

/**
 * Configuration for: Error reporting
 * Useful to show every little problem during development, but only show hard errors in production
 */
define('ENV', 'dev');

if (ENV == 'dev' || ENV == 'maint') {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}

// Datetime format
define('TIMESTAMP_FORMAT', 'Y-m-d H:i:s');

// set a constant that holds the project's "application" folder, like "/var/www/application".
define('APP', ROOT . 'app' . DIRECTORY_SEPARATOR);
define('APP_CONTROLLER', APP . 'controllers' . DIRECTORY_SEPARATOR);
define('APP_MODEL', APP . 'models' . DIRECTORY_SEPARATOR);
define('APP_STORAGE', APP . 'storage' . DIRECTORY_SEPARATOR);
define('APP_CACHE', APP_STORAGE . 'cache' . DIRECTORY_SEPARATOR);

// Define the home directory
define('HOME_DIRECTORY', ROOT . "/public/uploads/");

// Define default server node
define('DEFAULT_NODE', '0');

define('SPECIAL_CHARS', "!?,;.'[]={}@#$%^*()-_\/|");

/**
 * Configuration for: URL
 * Here we auto-detect your applications URL and the potential sub-folder. Works perfectly on most servers and in local
 * development environments (like WAMP, MAMP, etc.). Don't touch this unless you know what you do.
 *
 * URL_PUBLIC_FOLDER:
 * The folder that is visible to public, users will only have access to that folder so nobody can have a look into
 * "/application" or other folder inside your application or call any other .php file than index.php inside "/public".
 *
 * URL_PROTOCOL:
 * The protocol. Don't change unless you know exactly what you do. This defines the protocol part of the URL, in older
 * versions of MINI it was 'http://' for normal HTTP and 'https://' if you have a HTTPS site for sure. Now the
 * protocol-independent '//' is used, which auto-recognized the protocol.
 *
 * URL_DOMAIN:
 * The domain. Don't change unless you know exactly what you do.
 * If your project runs with http and https, change to '//'
 *
 * URL_SUB_FOLDER:
 * The sub-folder. Leave it like it is, even if you don't use a sub-folder (then this will be just "/").
 *
 * URL:
 * The final, auto-detected URL (build via the segments above). If you don't want to use auto-detection,
 * then replace this line with full URL (and sub-folder) and a trailing slash.
 */

define('URL_PUBLIC_FOLDER', '');
define('URL_PROTOCOL', '//');
define('URL_DOMAIN', $_SERVER['HTTP_HOST']);
define('URL_SUB_FOLDER', str_replace(URL_PUBLIC_FOLDER, '', dirname($_SERVER['SCRIPT_NAME'])));
define('URL', URL_PROTOCOL . URL_DOMAIN . URL_SUB_FOLDER);

/**
 * Configuration for: Database
 * This is the place where you define your database credentials, database type etc.
 */
define('DB_TYPE', 'mysql');
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'terminal');
define('DB_USER', 'root');
define('DB_PASS', 'mysql');
define('DB_CHARSET', 'utf8');

/**
* Configuration for: Email server credentials
*
* Here you can define how you want to send emails.
* If you have successfully set up a mail server on your linux server and you know
* what you do, then you can skip this section. Otherwise please set EMAIL_USE_SMTP to true
* and fill in your SMTP provider account data.
*
* EMAIL_USED_MAILER: Check Mail class for alternatives
* EMAIL_USE_SMTP: Use SMTP or not
* EMAIL_SMTP_AUTH: leave this true unless your SMTP service does not need authentication
*/
define('EMAIL_USED_MAILER' , 'phpmailer');
define('EMAIL_USE_SMTP', false);
define('EMAIL_SMTP_HOST', 'yourhost');
define('EMAIL_SMTP_AUTH', true);
define('EMAIL_SMTP_USERNAME', 'yourusername');
define('EMAIL_SMTP_PASSWORD', 'yourpassword');
define('EMAIL_SMTP_PORT', 465);
define('EMAIL_SMTP_ENCRYPTION', 'ssl');
