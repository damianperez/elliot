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
define( 'DB_NAME', 'elliot' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',          'a!Mv*A*# n)N`OMxO`=yL&0<Y#F*2>4h1?{oObJ*ir3t&APCFS =aFKH&l{i$$ly' );
define( 'SECURE_AUTH_KEY',   'p6l tixP@a$Sm6?A][_N9fXL@#;^n4JSUxQ-,xfVq6M)qIAu/Fx<& .`i-EhMD:&' );
define( 'LOGGED_IN_KEY',     'E;8Ts*71(!3f?aNKJSK `XeRfr2hje3HQTjj5CL&FDv8`UrLs.HI[GCr5= odye6' );
define( 'NONCE_KEY',         'dSF#QTMlUq;s#WuZloVHUmeJvB<yOq(qK[AnWgUJyXu)OhO;C&Fr*G+BT6B?i0>_' );
define( 'AUTH_SALT',         'Uc%Q,9|8L({Tw:0B)s_[Z7j|)Z9p8hOoSHjVf^}>Hzi>3rpnqW$ZS >q^Ds6yscj' );
define( 'SECURE_AUTH_SALT',  '#_r<!JeE;S]h[uB_n{T[?MAiycfFMjx_^e/cM^LGi$`<1)Z^b.DQ4X;/sRb=LLS0' );
define( 'LOGGED_IN_SALT',    'xKZL8SLO,d2YUuJCAx6^xbW3>i5T`cnzki5E]rzX|O}A%I}}mG5t~}T?0(:!S0*v' );
define( 'NONCE_SALT',        '`aYsw#i,FIms 6uowja4k*C[Q-E#DWAM2M}!4ji09f>tXsr_u7%F9G$R|(>)gFHp' );
define( 'WP_CACHE_KEY_SALT', '>RYX3%o.P]&> zNU0Q8ahZEkcn-l:*r1HB>*H0x _lpQ~E{D40DN3jq+oy7Uy3CC' );


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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
