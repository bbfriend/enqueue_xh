
1:Unzip
2:UP 
  plugins/enqueue_xh/* 	---> plugins/enqueue_xh/*
3:UP2 
  cmsimple/add_to_userfuncs.php  	---> cmsimple/userfuncs.php
  * if you have already have a userfuncs.php, please copy the source code. Very simple code.

4:EDIT
##### OPEN ##############
## 
   cmsimple/tplfuncs.php

##### FIND & Change #### 
## About Line78
## function head()

function head()
{
    global $title, $cf, $pth, $tx, $hjs;

    $t = XH_title($cf['site']['title'], $title);
    $t = '<title>' . strip_tags($t) . '</title>' . "\n";
    foreach (array_merge($cf['meta'], $tx['meta']) as $i => $k) {
        $t .= meta($i);
    }
    $t = tag('meta http-equiv="content-type" content="text/html;charset=UTF-8"')
        . "\n" . $t;
    $plugins = implode(', ', XH_plugins());

 /** Add Enqueue_xh St.******/
//	This is Use Hook_XH .
	do_action( 'enqueue_scripts' );

	ob_start();
		print_styles();
		$print_styles = ob_get_contents();
	  ob_clean();
		print_head_scripts();
		$print_head_scripts = ob_get_contents();
	ob_end_clean();
 /** Add Enqueue_xh END.******/

    return $t
        . tag(
            'meta name="generator" content="' . CMSIMPLE_XH_VERSION . ' '
            . CMSIMPLE_XH_BUILD . ' - www.cmsimple-xh.org"'
        ) . "\n"
        . '<!-- plugins: ' . $plugins . ' -->' . "\n"
        . XH_renderPrevLink() . XH_renderNextLink()
        . tag(
            'link rel="stylesheet" href="' . $pth['file']['corestyle']
            . '" type="text/css"'
        ) . "\n"
        . tag(
            'link rel="stylesheet" href="' . $pth['file']['stylesheet']
            . '" type="text/css"'
        ) . "\n"
        . $print_styles . $print_head_scripts . $hjs ; // <--- Add Enqueue_xh
}