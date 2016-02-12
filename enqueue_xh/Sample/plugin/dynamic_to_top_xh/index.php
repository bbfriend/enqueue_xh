<?php
/**
 * dynamic_to_top_xh - main index.php
 *
 * Adds an automatic and dynamic "To Top" button to easily scroll long pages back to the top.  
 *
 * PHP versions 5
 *
 * @category  CMSimple_XH
 * @package   dynamic_to_top_xh
 * @author    Takashi Uchiyama <http://cmsimple-xh.org/>
 * @core author  Matt <https://wordpress.org/plugins/dynamic-to-top/> 3.4.2
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   
 * @link      http://cmsimple-xh.org/
 */


/* utf8-marker = äöüß */

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/**
 * The plugin version.
 */
define('DYNAMIC_TO_TOP_XH_VERSION', '3.4.2.01');

/*
 * Add a tab for admin-menu.
 */
/*if($plugin_cf['dynamic_to_top_xh']['tab_show'] =='true'){
	$pd_router->add_tab(
	    $plugin_tx['shortcodes_xh']['tab'],
	    $pth['folder']['plugins'] . 'shortcodes_xh/Shortcodes_view.php'
	);
}
*/

	/* A safe way to register a CSS style file for later.
	See . https://codex.wordpress.org/Function_Reference/wp_register_style 
	*/
	register_style(
		'dynamic_to_top_css',
		$pth['folder']['plugins'] . 'dynamic_to_top_xh/css/dynamic_to_top.css',
		'',
		'3.4.2',
		 false
	);


	/* Registers a script file in CMSimple to be linked , which safely handles the script dependencies. 
		See . https://codex.wordpress.org/Function_Reference/wp_register_script 
	*/
	register_script(
		'jquery.easing',
		$pth['folder']['plugins'] . 'dynamic_to_top_xh/js/libs/jquery.easing.js',
		array( 'jquery','jquery-migrate'),
		'1.3',
		false		
	);

	register_script(
		'dynamic.to.top',
		$pth['folder']['plugins'] . 'dynamic_to_top_xh/js/dynamic.to.top.min.js',
		array( 'jquery'),
		'3.4.2',
		true		//add to bottom
	);



 /*** Out put *****/ 
//if(!$adm or !$edit){
//if( !$adm && ( ( !$xhpages || !$userfiles) && $normal ) ){
if(!$adm ){
	$bjs .= <<<EOM
<script type='text/javascript'>
/* <![CDATA[ */
var mv_dynamic_to_top = {"text":"0","version":"0","min":"200","speed":"1000","easing":"easeInOutExpo","margin":"20"};
/* ]]> */
</script>
EOM;

	/* enqueues Registed style
	See . https://developer.wordpress.org/reference/functions/wp_enqueue_style/ 
	*/
	enqueue_style( 'dynamic_to_top_css');

	/* enqueues Registed script
	See . https://developer.wordpress.org/reference/functions/wp_enqueue_script/ 
	*/
	enqueue_script( 'jquery.easing');
	enqueue_script( 'dynamic.to.top');
}
?>
