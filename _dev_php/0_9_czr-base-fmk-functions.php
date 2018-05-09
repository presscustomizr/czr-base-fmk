<?php
/**
* @uses  wp_get_theme() the optional stylesheet parameter value takes into account the possible preview of a theme different than the one activated
*/
function czr_get_parent_theme_slug() {
    $theme_slug = get_option( 'stylesheet' );
    // $_REQUEST['theme'] is set both in live preview and when we're customizing a non active theme
    $theme_slug = isset($_REQUEST['theme']) ? $_REQUEST['theme'] : $theme_slug; //old wp versions
    $theme_slug = isset($_REQUEST['customize_theme']) ? $_REQUEST['customize_theme'] : $theme_slug;

    //gets the theme name (or parent if child)
    $theme_data = wp_get_theme( $theme_slug );
    if ( $theme_data -> parent() ) {
        $theme_slug = $theme_data -> parent() -> Name;
    }

    return sanitize_file_name( strtolower( $theme_slug ) );
}

//Creates a new instance
//@params ex :
//array(
//    'base_url' => PC_AC_BASE_URL . '/inc/czr-base-fmk'
// )
function CZR_Fmk_Base( $params = array() ) {
    return CZR_Fmk_Base::czr_fmk_get_instance( $params );
}

?>