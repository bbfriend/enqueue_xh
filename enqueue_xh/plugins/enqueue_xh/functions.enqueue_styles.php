<?php
/**
 * BackPress Styles Procedural API
 *
 * @package CMSimple
 * @subpackage BackPress
 */

/**
 * Initialize $xh_styles if it has not been set.
 *
 * @global XH_Style $xh_styles
 *
 * @return XH_Style XH_Style instance.
 */
function xh_styles() {
	global $xh_styles;
	if ( ! ( $xh_styles instanceof XH_Style ) ) {
		$xh_styles = new XH_Style();

		$xh_styles -> base_url = 'http://' . $_SERVER['HTTP_HOST'] . CMSIMPLE_ROOT;
		$xh_styles -> content_url = $xh_styles -> base_url;
	}
	return $xh_styles;
}

/**
 * Display styles that are in the $handles queue.
 *
 * Passing an empty array to $handles prints the queue,
 * passing an array with one string prints that style,
 * and passing an array of strings prints those styles.
 *
 * @global XH_Style $xh_styles The XH_Style object for printing styles.
 *
 * @param string|bool|array $handles Styles to be printed. Default 'false'.
 * @return array On success, a processed array of Dependencies items; otherwise, an empty array.
 */
function print_styles( $handles = false ) {
	if ( '' === $handles ) { // for wp_head
		$handles = false;
	}
	/**
	 * Fires before styles in the $handles queue are printed.
	 *
	 */
	if ( ! $handles ) {
		do_action( 'print_styles' );
	}

	global $xh_styles;
	if ( ! ( $xh_styles instanceof XH_Style ) ) {
		if ( ! $handles ) {
			return array(); // No need to instantiate if nothing is there.
		}
	}

	return xh_styles()->do_items( $handles );
}

/**
 * Add extra CSS styles to a registered stylesheet.
 *
 * Styles will only be added if the stylesheet in already in the queue.
 * Accepts a string $data containing the CSS. If two or more CSS code blocks
 * are added to the same stylesheet $handle, they will be printed in the order
 * they were added, i.e. the latter added styles can redeclare the previous.
 *
 * @see XH_Style::add_inline_style()
 *
 * @param string $handle Name of the stylesheet to add the extra styles to. Must be lowercase.
 * @param string $data   String containing the CSS styles to be added.
 * @return bool True on success, false on failure.
 */
function add_inline_style( $handle, $data ) {

	if ( false !== stripos( $data, '</style>' ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Do not pass style tags to add_inline_style().' ), '3.7' );
		$data = trim( preg_replace( '#<style[^>]*>(.*)</style>#is', '$1', $data ) );
	}

	return xh_styles()->add_inline_style( $handle, $data );
}

/**
 * Register a CSS stylesheet.
 *
 * @see Dependencies::add()
 * @link http://www.w3.org/TR/CSS2/media.html#media-types List of CSS media types.
 *
 * @param string      $handle Name of the stylesheet.
 * @param string|bool $src    Path to the stylesheet from the CMSimple root directory. Example: '/css/mystyle.css'.
 * @param array       $deps   An array of registered style handles this stylesheet depends on. Default empty array.
 * @param string|bool $ver    String specifying the stylesheet version number. Used to ensure that the correct version
 *                            is sent to the client regardless of caching. Default 'false'. Accepts 'false', 'null', or 'string'.
 * @param string      $media  Optional. The media for which this stylesheet has been defined.
 *                            Default 'all'. Accepts 'all', 'aural', 'braille', 'handheld', 'projection', 'print',
 *                            'screen', 'tty', or 'tv'.
 * @return bool Whether the style has been registered. True on success, false on failure.
 */
function register_style( $handle, $src, $deps = array(), $ver = false, $media = 'all' ) {

	return xh_styles()->add( $handle, $src, $deps, $ver, $media );
}

/**
 * Remove a registered stylesheet.
 *
 * @see Dependencies::remove()
 *
 *
 * @param string $handle Name of the stylesheet to be removed.
 */
function deregister_style( $handle ) {

	xh_styles()->remove( $handle );
}

/**
 * Enqueue a CSS stylesheet.
 *
 * Registers the style if source provided (does NOT overwrite) and enqueues.
 *
 * @see Dependencies::add(), Dependencies::enqueue()
 * @link http://www.w3.org/TR/CSS2/media.html#media-types List of CSS media types.
 *
 * @param string      $handle Name of the stylesheet.
 * @param string|bool $src    Path to the stylesheet from the root directory of CMSimple. Example: '/css/mystyle.css'.
 * @param array       $deps   An array of registered style handles this stylesheet depends on. Default empty array.
 * @param string|bool $ver    String specifying the stylesheet version number, if it has one. This parameter is used
 *                            to ensure that the correct version is sent to the client regardless of caching, and so
 *                            should be included if a version number is available and makes sense for the stylesheet.
 * @param string      $media  Optional. The media for which this stylesheet has been defined.
 *                            Default 'all'. Accepts 'all', 'aural', 'braille', 'handheld', 'projection', 'print',
 *                            'screen', 'tty', or 'tv'.
 */
function enqueue_style( $handle, $src = false, $deps = array(), $ver = false, $media = 'all' ) {

	$xh_styles = xh_styles();

	if ( $src ) {
		$_handle = explode('?', $handle);
		$xh_styles->add( $_handle[0], $src, $deps, $ver, $media );
	}
	$xh_styles->enqueue( $handle );
}

/**
 * Remove a previously enqueued CSS stylesheet.
 *
 * @see Dependencies::dequeue()
 *
 * @param string $handle Name of the stylesheet to be removed.
 */
function dequeue_style( $handle ) {

	xh_styles()->dequeue( $handle );
}

/**
 * Check whether a CSS stylesheet has been added to the queue.
 *
 * @param string $handle Name of the stylesheet.
 * @param string $list   Optional. Status of the stylesheet to check. Default 'enqueued'.
 *                       Accepts 'enqueued', 'registered', 'queue', 'to_do', and 'done'.
 * @return bool Whether style is queued.
 */
function style_is( $handle, $list = 'enqueued' ) {

	return (bool) xh_styles()->query( $handle, $list );
}

/**
 * Add metadata to a CSS stylesheet.
 *
 * Works only if the stylesheet has already been added.
 *
 * Possible values for $key and $value:
 * 'conditional' string      Comments for IE 6, lte IE 7 etc.
 * 'rtl'         bool|string To declare an RTL stylesheet.
 * 'suffix'      string      Optional suffix, used in combination with RTL.
 * 'alt'         bool        For rel="alternate stylesheet".
 * 'title'       string      For preferred/alternate stylesheets.
 *
 * @see XH_Dependency::add_data()
 *
 * @param string $handle Name of the stylesheet.
 * @param string $key    Name of data point for which we're storing a value.
 *                       Accepts 'conditional', 'rtl' and 'suffix', 'alt' and 'title'.
 * @param mixed  $value  String containing the CSS data to be added.
 * @return bool True on success, false on failure.
 */
function style_add_data( $handle, $key, $value ) {
	return xh_styles()->add_data( $handle, $key, $value );
}
