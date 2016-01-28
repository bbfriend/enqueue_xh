<?php
/**
 * Plugin Name: enqueue_xh
 * Description: Enqueue System
 * Version:    0.2
 * Build:      20151225
 * Copyright:  Takashi Uchiyama
 * Website:    http://cmsimple-jp.org
 * */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}
/**
 * The plugin version.
 */
define('ENQUEUE_XH_VERSION', '0.2');



$pcf= $plugin_cf['enqueue_xh'];


/*** Default enqueue  ***/
if($pcf['enqueue_jqurey'] == "true"){

	register_script( 'jquery', Enqueue_jQuery() );
	enqueue_script( 'jquery' );

	if ($plugin_cf['jquery']['load_migrate'] == 'true') {
		register_script( 'jquery-migrate', Enqueue_jQueryMigrate(),array( 'jquery') );
		enqueue_script( 'jquery-migrate' );
	}
	define('JQUERY', TRUE);
}



function Enqueue_jQuery($path = '') {
    global $pth, $plugin_cf;

        if ($path == '') {
            $path = $pth['folder']['plugins'] . 'jquery/lib/jquery/' . $plugin_cf['jquery']['version_core'] . '/jquery.min.js';
            if (!is_file($path)) {
                e('missing', 'file', $path);
                return;
            }
        }
        $js =  $path;
         return $js ;
}
function Enqueue_jQueryMigrate($path = '') {
    global $pth, $plugin_cf;
    $migrate = $pth['folder']['plugins'] . 'jquery/lib/migrate/' . $plugin_cf['jquery']['version_migrate'];
    if (is_file($migrate)) {
        $js =  $migrate ;
    } else {
        e('missing', 'file', $migrate);
        return;
    }
    return $js ;
}