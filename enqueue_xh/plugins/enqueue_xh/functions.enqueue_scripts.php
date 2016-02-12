<?php
/**
 * BackPress Scripts Procedural API
 *
 *
 * @package CMSimple
 * @subpackage BackPress
 */

/**
 * Initialize $xh_scripts if it has not been set.
 *
 * @global XH_Script $xh_scripts
 *
 *
 * @return XH_Script XH_Script instance.
 */
function xh_scripts() {
	global $xh_scripts;
	if ( ! ( $xh_scripts instanceof XH_Script ) ) {
		$xh_scripts = new XH_Script();

		$xh_scripts -> base_url = 'http://' . $_SERVER['HTTP_HOST'] . CMSIMPLE_ROOT;
		$xh_scripts -> content_url = $xh_scripts -> base_url;
	}
	return $xh_scripts;
}


/**
 * Print scripts in document head that are in the $handles queue.
 *
 * Called by admin-header.php and wp_head hook. Since it is called by wp_head on every page load,
 * the function does not instantiate the XH_Script object unless script names are explicitly passed.
 * Makes use of already-instantiated $xh_scripts global if present. Use provided wp_print_scripts
 * hook to register/enqueue new scripts.
 *
 * @see XH_Script::do_items()
 * @global XH_Script $xh_scripts The XH_Script object for printing scripts.
 *
 *
 * @param string|bool|array $handles Optional. Scripts to be printed. Default 'false'.
 * @return array On success, a processed array of Dependencies items; otherwise, an empty array.
 */
function print_scripts( $handles = false ) {
	/**
	 * Fires before scripts in the $handles queue are printed.
	 *
	 */
	do_action( 'print_scripts' );
	if ( '' === $handles ) { // for wp_head
		$handles = false;
	}

	global $xh_scripts;
	if ( ! ( $xh_scripts instanceof XH_Script ) ) {
		if ( ! $handles ) {
			return array(); // No need to instantiate if nothing is there.
		}
	}

	return xh_scripts()->do_items( $handles );
}

/**
 * Register a new script.
 *
 * Registers a script to be linked later using the enqueue_script() function.
 *
 * @see Dependencies::add(), Dependencies::add_data()
 *
 *
 * @param string      $handle    Name of the script. Should be unique.
 * @param string      $src       Path to the script from the CMSimple root directory. Example: '/js/myscript.js'.
 * @param array       $deps      Optional. An array of registered script handles this script depends on. Set to false if there
 *                               are no dependencies. Default empty array.
 * @param string|bool $ver       Optional. String specifying script version number, if it has one, which is concatenated
 *                               to end of path as a query string. If no version is specified or set to false, a version
 *                               number is automatically added equal to current installed CMSimple version.
 *                               If set to null, no version is added. Default 'false'. Accepts 'false', 'null', or 'string'.
 * @param bool        $in_footer Optional. Whether to enqueue the script before </head> or before </body>.
 *                               Default 'false'. Accepts 'false' or 'true'.
 * @return bool Whether the script has been registered. True on success, false on failure.
 */
function register_script( $handle, $src, $deps = array(), $ver = false, $in_footer = false ) {
	$xh_scripts = xh_scripts();

	$registered = $xh_scripts->add( $handle, $src, $deps, $ver );
	if ( $in_footer ) {
		$xh_scripts->add_data( $handle, 'group', 1 );
	}

	return $registered;
}

/**
 * Localize a script.
 *
 * Works only if the script has already been added.
 *
 * Accepts an associative array $l10n and creates a JavaScript object:
 *
 *     "$object_name" = {
 *         key: value,
 *         key: value,
 *         ...
 *     }
 *
 *
 * @see Dependencies::localize()
 * @link https://core.trac.wordpress.org/ticket/11520
 * @global XH_Script $xh_scripts The XH_Script object for printing scripts.
 *
 *
 * @todo Documentation cleanup
 *
 * @param string $handle      Script handle the data will be attached to.
 * @param string $object_name Name for the JavaScript object. Passed directly, so it should be qualified JS variable.
 *                            Example: '/[a-zA-Z0-9_]+/'.
 * @param array $l10n         The data itself. The data can be either a single or multi-dimensional array.
 * @return bool True if the script was successfully localized, false otherwise.
 */
function localize_script( $handle, $object_name, $l10n ) {
	global $xh_scripts;
	if ( ! ( $xh_scripts instanceof XH_Script ) ) {
		return false;
	}

	return $xh_scripts->localize( $handle, $object_name, $l10n );
}

/**
 * Remove a registered script.
 *
 * Note: there are intentional safeguards in place to prevent critical admin scripts,
 * such as jQuery core, from being unregistered.
 *
 * @see Dependencies::remove()
 *
 *
 * @param string $handle Name of the script to be removed.
 */
function deregister_script( $handle ) {

	/**
	 * Do not allow accidental or negligent de-registering of critical scripts in the admin.
	 * Show minimal remorse if the correct hook is used.
	 */
	$current_filter = current_filter();
//	if ( ( is_admin() && 'admin_enqueue_scripts' !== $current_filter ) ||
//		( 'wp-login.php' === $GLOBALS['pagenow'] && 'login_enqueue_scripts' !== $current_filter )
//	) {
	if ( ( XH_ADM && 'admin_enqueue_scripts' !== $current_filter ) ||
		( 'wp-login.php' === $GLOBALS['pagenow'] && 'login_enqueue_scripts' !== $current_filter )
	) {

		$no = array(
			'jquery', 'jquery-core', 'jquery-migrate', 'jquery-ui-core', 'jquery-ui-accordion',
			'jquery-ui-autocomplete', 'jquery-ui-button', 'jquery-ui-datepicker', 'jquery-ui-dialog',
			'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-menu', 'jquery-ui-mouse',
			'jquery-ui-position', 'jquery-ui-progressbar', 'jquery-ui-resizable', 'jquery-ui-selectable',
			'jquery-ui-slider', 'jquery-ui-sortable', 'jquery-ui-spinner', 'jquery-ui-tabs',
			'jquery-ui-tooltip', 'jquery-ui-widget', 'underscore', 'backbone',
		);

		if ( in_array( $handle, $no ) ) {
			$message = sprintf( __( 'Do not deregister the %1$s script in the administration area. To target the frontend theme, use the %2$s hook.' ),
				"<code>$handle</code>", '<code>enqueue_scripts</code>' );
//			_doing_it_wrong( __FUNCTION__, $message, '3.6' );
			return;
		}
	}

	xh_scripts()->remove( $handle );
}

/**
 * Enqueue a script.
 *
 * Registers the script if $src provided (does NOT overwrite), and enqueues it.
 *
 * @see Dependencies::add(), Dependencies::add_data(), Dependencies::enqueue()
 *
 *
 * @param string      $handle    Name of the script.
 * @param string|bool $src       Path to the script from the root directory of CMSimple. Example: '/js/myscript.js'.
 * @param array       $deps      An array of registered handles this script depends on. Default empty array.
 * @param string|bool $ver       Optional. String specifying the script version number, if it has one. This parameter
 *                               is used to ensure that the correct version is sent to the client regardless of caching,
 *                               and so should be included if a version number is available and makes sense for the script.
 * @param bool        $in_footer Optional. Whether to enqueue the script before </head> or before </body>.
 *                               Default 'false'. Accepts 'false' or 'true'.
 */
function enqueue_script( $handle, $src = false, $deps = array(), $ver = false, $in_footer = false ) {
	$xh_scripts = xh_scripts();


	if ( $src || $in_footer ) {
		$_handle = explode( '?', $handle );

		if ( $src ) {
			$xh_scripts->add( $_handle[0], $src, $deps, $ver );
		}

		if ( $in_footer ) {
			$xh_scripts->add_data( $_handle[0], 'group', 1 );
		}
	}

	$xh_scripts->enqueue( $handle );
}

/**
 * Remove a previously enqueued script.
 *
 * @see Dependencies::dequeue()
 *
 * @param string $handle Name of the script to be removed.
 */
function dequeue_script( $handle ) {

	xh_scripts()->dequeue( $handle );
}

/**
 * Check whether a script has been added to the queue.
 *
 * @since 1.0.1 'enqueued' added as an alias of the 'queue' list.
 *
 * @param string $handle Name of the script.
 * @param string $list   Optional. Status of the script to check. Default 'enqueued'.
 *                       Accepts 'enqueued', 'registered', 'queue', 'to_do', and 'done'.
 * @return bool Whether the script script is queued.
 */
function script_is( $handle, $list = 'enqueued' ) {

	return (bool) xh_scripts()->query( $handle, $list );
}

/**
 * Add metadata to a script.
 *
 * Works only if the script has already been added.
 *
 * Possible values for $key and $value:
 * 'conditional' string Comments for IE 6, lte IE 7, etc.
 *
 * @see XH_Dependency::add_data()
 *
 * @param string $handle Name of the script.
 * @param string $key    Name of data point for which we're storing a value.
 * @param mixed  $value  String containing the data to be added.
 * @return bool True on success, false on failure.
 */
function script_add_data( $handle, $key, $value ){
	return xh_scripts()->add_data( $handle, $key, $value );
}

/**
 * Prints the script queue in the HTML head on admin pages.
 *
 * Postpones the scripts that were queued for the footer.
 * print_footer_scripts() is called in the footer to print these scripts.
 *
 * @see print_scripts()
 *
 * @global bool $concatenate_scripts
 *
 * @return array
 */
function print_head_scripts() {
	global $concatenate_scripts, $bjs;

	$xh_scripts = xh_scripts();

	$xh_scripts->do_head_items();

	print_footer_scripts();

	$xh_scripts->reset();

	return $xh_scripts->done;
}


/**
 * Private, for use in *_footer_scripts hooks
 *
 */
//function _wp_footer_scripts() {
//	print_late_styles();
//	print_footer_scripts();
//}

/**
 * Prints the scripts that were queued for the footer or too late for the HTML head.
 *
 */
function print_footer_scripts() {
	global $xh_scripts ,$bjs;

	ob_start();

//	if ( ! ( $xh_scripts instanceof XH_Script ) ) {
//		return array(); // No need to run if not instantiated.
//	}

	$xh_scripts->do_footer_items();

	$print_footer_scripts = ob_get_contents();
	ob_end_clean();
	$bjs .= $print_footer_scripts;

	$xh_scripts->reset();
	return $xh_scripts->done;
}
