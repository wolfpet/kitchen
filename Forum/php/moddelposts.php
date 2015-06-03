<?php
/*$Id: moddelposts.php 378 2009-11-02 19:36:24Z dmitriy $*/

require_once('head_inc.php');
    if ( !is_null( $moder ) && $moder > 0 ) {
        $cur_page = $page_m_delposts;
require('modposts.php');
    } else {
        print( "<HTML><BODY>" );
        print( "You have no access to this page.");
        print( "</BODY></HTML>" );
    }

?>

