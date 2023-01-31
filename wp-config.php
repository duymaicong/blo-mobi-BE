<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress3' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '123456789' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );


define('JWT_AUTH_SECRET_KEY', 'your-top-secret-key');
define('JWT_AUTH_CORS_ENABLE', true);

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'ic- N4:bVU+#O8Eu_n#7H4.EI^AJLRg-OMpnh@O)-Nz}F$xVxU,J64#[s=]+q!:k' );
define( 'SECURE_AUTH_KEY',  'p~RODD{p^nu+q@Y1MpF9f,(.4-6}g{vcZBRAK7!Vga HsMG<[!otTS4>;TpxY!Oq' );
define( 'LOGGED_IN_KEY',    '_TP48ZCZf=cCEyy%/-fI8%DiA]n+vSlX-+(%U&+^&{CF)P)Q:;iV _?GQ?M2P7Bn' );
define( 'NONCE_KEY',        '4(|qi@yx{0)d:5;hOB2<tVt@s%qe9K6^V;CJ. i|g3{/<*DZ%S`rF:cTZmkZqq0u' );
define( 'AUTH_SALT',        'M:a#0+rt/gRPWyc!nB5YJ*2N2w&xTY.a G8;*,X~.CTQ-mohm$p7b v<Rg/_c>lG' );
define( 'SECURE_AUTH_SALT', 'A7*`uUtJXcM{+yfPoWbY7yBZC0..lwAz/9c6*<Op^c9;,N&(XNqUUjS*p74FZXZ)' );
define( 'LOGGED_IN_SALT',   'JVS@.hJ9Cy3jUGLUQ%nYWTI/:BK6XB!qhiUL: x1j;#U^Hh+G::d!=Jq%.-v3O[.' );
define( 'NONCE_SALT',       'CUS@?SLy|xThIRYB6=Dl4x?O3O)Xl=do3LF&1ugfXAW0D/>7_<aa}ywfM>,!?XpP' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
