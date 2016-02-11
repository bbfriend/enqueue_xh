# enqueue_xh
This plugin is to use the familiar <a href="https://www.google.co.jp/search?q=+Add+JavaScripts+and+Styles++WordPress&ie=utf-8&oe=utf-8&hl=ja#hl=ja&q=Add+JavaScripts+and+Styles++WordPress+enqueue" target="_blank">WordPress Enqueue System</a> in CMSimple_XH.  

Requirements  
 First to the installation   
  hook_xh https://github.com/bbfriend/hook_xh   　
  
## Function List  
 print_scripts( $handles = false )  
 register_script( $handle, $src, $deps = array(), $ver = false, $in_footer = false )  
 localize_script( $handle, $object_name, $l10n )  
 deregister_script( $handle )  
 enqueue_script( $handle, $src = false, $deps = array(), $ver = false, $in_footer = false )  
 dequeue_script( $handle )  
 script_is( $handle, $list = 'enqueued' )  
 script_add_data( $handle, $key, $value )  
 print_head_scripts()  
 print_footer_scripts()  
  
 print_styles( $handles = false )  
 add_inline_style( $handle, $data )  
 register_style( $handle, $src, $deps = array(), $ver = false, $media = 'all' )  
 deregister_style( $handle )  
 enqueue_style( $handle, $src = false, $deps = array(), $ver = false, $media = 'all' )  
 dequeue_style( $handle )  
 style_is( $handle, $list = 'enqueued' )  
 style_add_data( $handle, $key, $value )  
 
Forum:http://cmsimpleforum.com/viewtopic.php?f=29&t=9878
 
