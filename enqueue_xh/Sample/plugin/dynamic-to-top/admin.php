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
 * @core author  Matt <https://wordpress.org/plugins/dynamic_to_top/> 3.4.2
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   
 * @link      http://cmsimple-xh.org/
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
 * Handle the plugin administration.
 */

if (isset($dynamic_to_top_xh) && ($dynamic_to_top_xh == 'true')) {
	$o .= print_plugin_admin('on');

    switch ($admin) {
    case '':
    case 'plugin_main':
	$o .= dynamic_to_top_xh_version() . DYNAMIC_TO_TOP_XH_systemCheck() ;
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
function dynamic_to_top_xh_version()
{
    global $pth;

    return '<h1>Dynamic-to-top_xh(Enqueue_Xh Sample plugin)</h1>'."\n"
	. tag('img src="'.$pth['folder']['plugins'].'dynamic_to_top_xh/images/dynamic2top_xh.png" style="float: left; margin: 0 20px 20px 0"')
	. '<p>Enqueue_Xh Sample: Adds an automatic and dynamic "To Top" button in CMSimple_XH.</p>'
	. '<p>Version: '.DYNAMIC_TO_TOP_XH_VERSION.'</p>'."\n"
	. '<p>Copyright &copy; 2015 <a href="http://cmsimple-jp.org" target="_blank">cmsimple-jp.org</a></p>'."\n"
	. '<p>Original <a href="https://wordpress.org/plugins/dynamic-to-top/" target="_blank">Wordpress dynamic to top Ver.3.4.2</a></p>'
	. '<p style="text-align: justify">'
	. '<b>License</b>'. tag('br') . "\n"
	. ' Detail <a href="https://wordpress.org/plugins/dynamic-to-top/" target="_blank">dynamic to top</a>'. tag('br')."\n"
	. ' Software License terms : <a href="http://www.gnu.org/licenses/" target="_blank">GPLv3.</a>';
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
function DYNAMIC_TO_TOP_XH_systemCheck()
{
    global $pth, $plugin_cf, $tx, $plugin_tx ,$xh_scripts,$xh_styles;

    define('DYNAMIC_TO_TOP_XH_PHP_VERSION', '5.3');
//    $ptx = $plugin_tx['dynamic_to_top_xh'];
    $imgdir = $pth['folder']['plugins'] . 'dynamic_to_top_xh/images/';
    $ok = tag('img src="' . $imgdir . 'ok.png" alt="ok"');
    $warn = tag('img src="' . $imgdir . 'warn.png" alt="warning"');
    $fail = tag('img src="' . $imgdir . 'fail.png" alt="failure"');
    $o = tag('hr') . '<h4>' . "System check" . '</h4>'
        . (version_compare(PHP_VERSION, DYNAMIC_TO_TOP_XH_PHP_VERSION) >= 0 ? $ok : $fail)
        . '&nbsp;&nbsp;' . sprintf("PHP version >= %s" , DYNAMIC_TO_TOP_XH_PHP_VERSION)
        . PHP_EOL;

	if( function_exists('enqueue_style') && function_exists('enqueue_script') ){
	    $o .= tag('br') .  $ok ;
	}else{
	    $o .= tag('br') .  $warn ;
	}
    $o .= '&nbsp;&nbsp;' . 'Enqueue_Xh ' . PHP_EOL;

	$list = 'registered';//
    $o .= tag('br') . (script_is('jquery',$list) ? $ok : $warn)
        . '&nbsp;&nbsp;' . "jquery registered by Enqueue_Xh" . PHP_EOL;

	


    $o .= tag('br') . (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
        . '&nbsp;&nbsp;' . "Encoding 'UTF-8' configured" . PHP_EOL;

    return $o;
}

?>
