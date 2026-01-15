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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

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
define( 'AUTH_KEY',          'saz)l9M<k25PhS1Np2Uk`^)VCDyHjJ!e-V]kO=~/p5|<daSwEma!=!loiZ7X[Rq-' );
define( 'SECURE_AUTH_KEY',   't|<1;5DK_I8*VhB5pcF_n;@d&GYK([jC~[^$dH8~p9<@C(4M>rcI*(:YN4MDrDuo' );
define( 'LOGGED_IN_KEY',     'e/R{.fBzl8j[/ZPedtPhX RJ>?]| xNz;%`--c7Re5ek[bBy2X=4En2p[`_o{>3Y' );
define( 'NONCE_KEY',         'wDYp??]1pFACrue3=!ypqe.$BLp(o}G oR,maTQtO^~3.|vobHh]8o!68swol/Hw' );
define( 'AUTH_SALT',         'VWpc:O0q@_b*X]O#A&O,}e&teRCGvkiIOp?{]),LKi8]4:kq7A+YHk9~#hJgqZ[<' );
define( 'SECURE_AUTH_SALT',  'J+z~EeRJ$#y*Z5&M@BeQ4b{@2NZ,O&pU@:Qy&lp|QSR;Z:kd{[z;T_^8h&RvZ|n1' );
define( 'LOGGED_IN_SALT',    ' hD0?svAG&cB8_X6>yqLdDaO;:P`HP?o{i2a#)&t_md.ytJLB79hT%INlhV?P?`*' );
define( 'NONCE_SALT',        '-_C[]5]BI&<{?m~Grm3p6vA0=3DCY/1cGyl)h*WP4Yf=m]WwpwMobQw.;$[]}yCB' );
define( 'WP_CACHE_KEY_SALT', '51kD!jD`%W^P :?&>n00O<_M:63;~sZJs|~;iHGq12Q,{TA$9{:/micBHu`oAm9x' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
