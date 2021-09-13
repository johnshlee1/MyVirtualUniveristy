<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //

if (strstr($_SERVER['SERVER_NAME'], 'my-virtual-university.local')) {
    define( 'DB_NAME', 'local' );
    define( 'DB_USER', 'root' );
    define( 'DB_PASSWORD', 'root' );
    define( 'DB_HOST', 'localhost' );
} else {
    define( 'DB_NAME', 'johnshlee_demyvirtualuniversity' );
    define( 'DB_USER', 'johnshlee_demyvirtualuniversity' );
    define( 'DB_PASSWORD', 'tmd1987&Hwan2002' );
    define( 'DB_HOST', 'johnshlee.de.mysql' );
}



/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'PbY0K2kfAlMVBW1S3/pYwORbFG//lkXLkK8Dxklr7H3j8pdtbtHl1hn+EeLXi0vzZ49P59Qz5uZ6kcLwf4wU6g==');
define('SECURE_AUTH_KEY',  'lUX/owAVD2LhSoc42tEEdVa26Fg5wwH203DI83g300EyxSolvqcyeVoC1hsLp8FS//nJFq97kT0r4sFi9Q0hZA==');
define('LOGGED_IN_KEY',    '5Bi7HvbGLPmGDtQH82DHpx65JS//4dgf9cdPzWLZJz8IeZL1255IT8vdebSfXw0Q1eO/ViUHkx2+wuJHi7PVMQ==');
define('NONCE_KEY',        'EQvM7tC6ddd+N5sHWCZrOcvQfVzd0inovjPiYFatjW8ddciUbzaw8P08H9zSkJIeVHCnuNvcT3sJMAyE23do8g==');
define('AUTH_SALT',        'uZBE6XssFq9+3MDfOmSsZnTkQj2aM4mKzRRKw0vSIEZWhR6sAg5f9b9h2pZqNcJGmWORRySe5KfiLzDfR0wBdA==');
define('SECURE_AUTH_SALT', 'uBH/OjHJcPC8SiEAsZSUhOJQjvW5JOiirFx1/jm6fuwI/9Vq065F/tbuh4vGqbjCxRi6fNYjexkiMdb+H20hkQ==');
define('LOGGED_IN_SALT',   'WgDHUlhnHjPll7u2nXCVbHIJAn+pSlrDa9T2ktCSufDa6nBpzi+zP1hxR2TwS4baTs0SsV+3BkU1lreGROim2Q==');
define('NONCE_SALT',       'Frvtf5MVxEtbEG/44ZuKp/z3GNeQnyECcTE9J8Dyhe2nB2lPX2qLs3LAKT5x3TmkgY5VwaxAJhSOc1aV02ZTZw==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
