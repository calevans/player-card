<?php

$_tests_dir = getenv('WP_TESTS_DIR');
if ( !$_tests_dir ) $_tests_dir = '/tmp/wordpress-tests-lib';

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	$_tests_dir = '/tmp/wordpress-tests-lib';
	require dirname( __FILE__ ) . '/../player-card.php';
	require '/tmp/wordpress/wp-content/plugins/powerpress/powerpress.php';

// Update array with plugins to include ...
	$plugins_to_active = array(
		'powerpress/powerpress.php',
		'player-card/player-card.php'
	);

	update_option( 'active_plugins', $plugins_to_active );

}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';

