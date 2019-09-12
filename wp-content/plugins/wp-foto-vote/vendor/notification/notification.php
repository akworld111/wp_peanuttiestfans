<?php
/**
 * Plugin Name: Notification
 * Description: Customisable email and webhook notifications with powerful developer friendly API for custom triggers and notifications. Send alerts easily.
 * Author: BracketSpace
 * Author URI: https://bracketspace.com
 * Version: 5.3.2
 * License: GPL3
 * Text Domain: notification
 * Domain Path: /languages
 *
 * @package notification
 */

define( 'NOTIFICATION_VERSION', '5.3.1' );

/**
 * Plugin's autoload function
 *
 * @param  string $class class name.
 * @return mixed         false if not plugin's class or void
 */
function notification_autoload( $class ) {

	$parts      = explode( '\\', $class );
	$namespaces = array( 'BracketSpace', 'Notification' );

	foreach ( $namespaces as $namespace ) {
		if ( array_shift( $parts ) !== $namespace ) {
			return false;
		}
	}

	$file = trailingslashit( dirname( __FILE__ ) ) . trailingslashit( 'class' ) . implode( '/', $parts ) . '.php';

	if ( file_exists( $file ) ) {
		require_once $file;
	}

}
spl_autoload_register( 'notification_autoload' );

/**
 * Requirements check
 */
$requirements = new BracketSpace\Notification\Utils\Requirements(
	__( 'Notification', 'notification' ),
	array(
		'php'                => '5.6',
		'wp'                 => '4.9',
		'function_collision' => array( 'register_trigger', 'register_notification' ),
		'dochooks'           => true,
	)
);

/**
 * Check if ReflectionObject returns proper docblock comments for methods.
 */
if ( method_exists( $requirements, 'add_check' ) ) {
	$requirements->add_check(
		'dochooks',
		function( $comparsion, $r ) {
			if ( true !== $comparsion ) {
				return;
			}

			/**
			 * NotificationDocHookTest class
			 */
			class NotificationDocHookTest {
				/**
				 * Test method
				 *
				 * @action test 10
				 * @return void
				 */
				public function test_method() {}
			}

			$reflector = new \ReflectionObject( new NotificationDocHookTest() );
			foreach ( $reflector->getMethods() as $method ) {
				$doc = $method->getDocComment();
				if ( false === strpos( $doc, '@action' ) ) {
					$r->add_error( __( 'PHP OP Cache to be disabled', 'notification' ) );
				}
			}

		}
	);
}

if ( ! $requirements->satisfied() ) {
	add_action( 'admin_notices', array( $requirements, 'notice' ) );
	return;
}

global $notification_runtime;

/**
 * Gets the plugin runtime.
 *
 * @param string $property Optional property to get.
 * @return object Runtime class instance
 */
function notification_runtime( $property = null ) {

	global $notification_runtime;

	if ( empty( $notification_runtime ) ) {
		$notification_runtime = new BracketSpace\Notification\Runtime( __FILE__ );
	}

	if ( null !== $property && isset( $notification_runtime->{ $property } ) ) {
		return $notification_runtime->{ $property };
	}

	return $notification_runtime;

}

$runtime = notification_runtime();
$runtime->boot();
