<?php

/**
 *  enqueue_xh 
 *
 * @package	enqueue_xh
 * @copyright	Copyright (c) 2015 T.Uchiyama <http://cmsimple-jp.org/>
 * @license	http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version 1.0.1
 * @link	http://cmsimple-jp.org
 */

if (!XH_ADM ) {
    return;
}

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}
/*
 * Register the plugin menu items.
 */
if (function_exists('XH_registerStandardPluginMenuItems')) {
//    XH_registerStandardPluginMenuItems(false);
}

//	}


/*
 * Handle the plugin administration.
 */

if (isset($enqueue_xh) && $enqueue_xh == 'true') {
    if ($admin == 'plugin_language') {
        $o .= print_plugin_admin('on');
    } else {
        $o .= print_plugin_admin('off');
    }

    switch ($admin) {
    case '':
    case 'plugin_main':
	$o .= enqueue_xh_version() .Enqueue_Xh_systemCheck() ;
	break;
    default://Handles reading and writing of plugin files
	$o .= plugin_admin_common($action, $admin, $plugin);
    }
}


/**
 * Returns the plugin version information view.
 *
 * @return string  The (X)HTML.
 */
function enqueue_xh_version()
{
    global $pth;

    return '<h1>Enqueue_Xh</h1>'."\n"
	. tag('img src="'.$pth['folder']['plugins'].'enqueue_xh/help/Enqueue_xh.png" style="float: left; margin: 0 20px 20px 0"')
	. '<p>The Enqueue_Xh prevents script and style conflicts.' . tag('br') . 'To CMSimple_XH provide <a href="https://www.google.co.jp/search?q=Enqueue+wordpress"  target="_blank">WordPress Enqueue system</a> and equivalent function</p>'
	. '<p>Version: '.ENQUEUE_XH_VERSION.'</p>'."\n"
	. '<p>Copyright &copy; 2015 <a href="http://cmsimple-jp.org" target="_blank">cmsimple-jp.org</a></p>'."\n"
	. '<p>Original <a href="https://github.com/WordPress/WordPress" target="_blank">WordPress Ver4.4</a></p>'
	. '<p style="text-align: justify">'
	. '<b>License</b>'. tag('br') . "\n"
	. ' Detail <a href="https://github.com/WordPress/WordPress" target="_blank">https://github.com/WordPress/WordPress</a>'. tag('br')."\n"
	. ' Software License terms : <a href="http://www.gnu.org/licenses/" target="_blank">GPLv2.</a>';
}

/**
 * Returns requirements information.
 *
 * @return string (X)HTML
 *
 * @global array The paths of system files and folders.
 * @global array The configuration of the plugins.
 * @global array The localization of the core.
 * @global array The localization of the plugins.
 */
function Enqueue_Xh_systemCheck()
{
    global $pth, $plugin_cf, $tx, $plugin_tx ,$xh_scripts,$xh_styles,$hooks;

    define('ENQUEUE_XH_PHP_VERSION', '5.3');
    $ptx = $plugin_tx['shortcodes_xh'];
    $imgdir = $pth['folder']['plugins'] . 'enqueue_xh/images/';
    $ok = tag('img src="' . $imgdir . 'ok.png" alt="ok"');
    $warn = tag('img src="' . $imgdir . 'warn.png" alt="warning"');
    $fail = tag('img src="' . $imgdir . 'fail.png" alt="failure"');
    $o = tag('hr') . '<h4>' . "System check" . '</h4>'
        . (version_compare(PHP_VERSION, ENQUEUE_XH_PHP_VERSION) >= 0 ? $ok : $fail)
        . '&nbsp;&nbsp;' . sprintf("PHP version >= %s" , ENQUEUE_XH_PHP_VERSION)
        . tag('br') . PHP_EOL;
    $o .= tag('br') . (@isset($xh_scripts)  ? $ok : $warn)
        . '&nbsp;&nbsp;' . 'BackPress Scripts Procedural API' . PHP_EOL;
    $o .= tag('br') . (@isset($xh_styles)  ? $ok : $warn)
        . '&nbsp;&nbsp;' . 'BackPress Styles Procedural API' . PHP_EOL;
    $o .= tag('br') . (@isset($hooks)  ? $ok : $warn)
        . '&nbsp;&nbsp;' . 'Hook_XH ( Required.)' . PHP_EOL;


    $o .= tag('br') . (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
        . '&nbsp;&nbsp;' . "Encoding 'UTF-8' configured" . PHP_EOL;

    return $o;
}

?>
