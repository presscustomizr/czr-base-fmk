<?php
////////////////////////////////////////////////////////////////
// CZR_Fmk_Base
if ( ! class_exists( 'CZR_Fmk_Base_Load_Resources' ) ) :
    class CZR_Fmk_Base_Load_Resources extends CZR_Fmk_Base_Construct {

        // fired in the constructor
        function czr_enqueue_fmk_resources() {
            // Enqueue the fmk control js
            add_action ( 'customize_controls_enqueue_scripts' , array( $this, 'ac_load_additional_controls_js' ) );
            add_action ( 'customize_controls_enqueue_scripts' , array( $this, 'ac_load_additional_controls_css' ) );

            // Enqueue the base preview js
            //hook : customize_preview_init
            add_action ( 'customize_preview_init' , array( $this, 'ac_customize_load_preview_js' ) );

            // adds specific js templates for the czr_module control
            add_action( 'customize_controls_print_footer_scripts', array( $this, 'ac_print_module_control_templates' ) , 1 );
        }


        // hook : 'customize_controls_enqueue_scripts'
        function ac_load_additional_controls_js() {
            //'czr-customizer-fmk' will be enqueued as a dependency of 'font-customizer-control' only in plugin mode
            wp_enqueue_script(
                'czr-customizer-fmk',
                //dev / debug mode mode?
                sprintf(
                    '%1$s/assets/js/%2$s',
                    FMK_BASE_URL,
                    defined('CZR_DEV') && true === CZR_DEV ? '_0_ccat_czr-base-fmk.js' : '_0_ccat_czr-base-fmk.min.js'
                ),
                array('customize-controls' , 'jquery', 'underscore'),
                ( defined('WP_DEBUG') && true === WP_DEBUG ) ? time() : FMK_BASE_VERSION,
                $in_footer = true
            );

            // When used with Customizr or Hueman, free and pro, we also need to load the theme js part
            if ( false !== strpos( czr_get_parent_theme_slug(), 'customizr' ) || false !== strpos( czr_get_parent_theme_slug(), 'hueman' ) ) {
                wp_enqueue_script(
                    'czr-theme-customizer-fmk',
                    //dev / debug mode mode?
                    sprintf(
                        '%1$s/assets/js/%2$s',
                        FMK_BASE_URL,
                        defined('CZR_DEV') && true === CZR_DEV ? '_1_ccat_czr-theme-fmk.js' : '_1_ccat_czr-theme-fmk.min.js'
                    ),
                    array( 'czr-customizer-fmk' ),
                    ( defined('WP_DEBUG') && true === WP_DEBUG ) ? time() : FMK_BASE_VERSION,
                    $in_footer = true
                );
            }


            //additional localized param when standalone plugin mode
            wp_localize_script(
                'czr-customizer-fmk',
                'serverControlParams',
                apply_filters( 'czr_js_customizer_control_params' ,
                  array(
                      'css_attr' => $this -> czr_css_attr,
                      'isDevMode' => ( defined('WP_DEBUG') && true === WP_DEBUG ) || ( defined('CZR_DEV') && true === CZR_DEV ),
                      'docURL'          => esc_url('docs.presscustomizr.com/'),
                      'i18n' => array(
                            'edit' => __('Edit', 'text_domain_to_be_replaced'),
                            'close' => __('Close', 'text_domain_to_be_replaced'),
                            'notset' => __('Not set', 'text_domain_to_be_replaced'),
                            'successMessage' => __('Done !', 'text_domain_to_be_replaced'),

                            'readDocumentation' => __('Learn more about this in the documentation', 'text_domain_to_be_replaced'),
                            'Settings' => __('Settings', 'text_domain_to_be_replaced'),
                            'Options for' => __('Options for', 'text_domain_to_be_replaced'),

                            // img upload translation
                            'select_image'        => __( 'Select Image', 'text_domain_to_be_replaced' ),
                            'change_image'        => __( 'Change Image', 'text_domain_to_be_replaced' ),
                            'remove_image'        => __( 'Remove', 'text_domain_to_be_replaced' ),
                            'default_image'       => __( 'Default', 'text_domain_to_be_replaced'  ),
                            'placeholder_image'   => __( 'No image selected', 'text_domain_to_be_replaced' ),
                            'frame_title_image'   => __( 'Select Image', 'text_domain_to_be_replaced' ),
                            'frame_button_image'  => __( 'Choose Image', 'text_domain_to_be_replaced' ),
                            'isThemeSwitchOn' => true
                      ),
                      'paramsForDynamicRegistration' => apply_filters( 'czr_fmk_dynamic_setting_js_params', array() )
                  )
                )
            );
        }



        // Enqueue the fmk css when standalone plugin
        // hook : 'customize_controls_enqueue_scripts'
        function ac_load_additional_controls_css() {
            wp_enqueue_style(
                'czr-fmk-controls-style',
                sprintf('%1$s/assets/css/czr-ccat-control-base%2$s.css', FMK_BASE_URL, ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min'),
                array( 'customize-controls' ),
                ( defined('WP_DEBUG') && true === WP_DEBUG ) ? time() : FMK_BASE_VERSION,
                $media = 'all'
            );

            //select2 stylesheet
            //overriden by some specific style in czr-control-base.css
            wp_enqueue_style(
                'select2-css',
                 sprintf('%1$s/assets/css/lib/select2.min.css', FMK_BASE_URL, ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min'),
                array( 'customize-controls' ),
                ( defined('WP_DEBUG') && true === WP_DEBUG ) ? time() : FMK_BASE_VERSION,
                $media = 'all'
            );

            wp_enqueue_style(
                'font-awesome',
                sprintf('%1$s/assets/fonts/css/fontawesome-all.min.css', FMK_BASE_URL, ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min'),
                array(),
                ( defined('WP_DEBUG') && true === WP_DEBUG ) ? time() : FMK_BASE_VERSION,
                $media = 'all'
            );
        }


        //hook : customize_preview_init
        function ac_customize_load_preview_js() {
            global $wp_version;

            wp_enqueue_script(
                'czr-customizer-preview' ,
                  sprintf(
                      '%1$s/assets/js/%2$s',
                      FMK_BASE_URL,
                      defined('CZR_DEV') && true === CZR_DEV ? 'czr-preview-base.js' : 'czr-preview-base.min.js'
                  ),
                  array( 'customize-preview', 'underscore'),
                  ( defined('WP_DEBUG') && true === WP_DEBUG ) ? time() : FMK_BASE_VERSION,
                  true
            );

            //localizes
            wp_localize_script(
                  'czr-customizer-preview',
                  'serverPreviewParams',
                  apply_filters('czr_base_fmk_customizer_preview_params' ,
                      array(
                          'themeFolder'     => get_template_directory_uri(),
                          //patch for old wp versions which don't trigger preview-ready signal => since WP 4.1
                          'preview_ready_event_exists'   => version_compare( $wp_version, '4.1' , '>=' ),
                          'blogname' => get_bloginfo('name'),
                          'isRTL'    => is_rtl()
                      )
                  )
            );
        }

        // DO WE STILL NEED TO PRINT THIS TMPL ?
        /////////////////////////////////////////////////////
        /// WHEN EMBEDDED IN A CONTROL //////////////////////
        /////////////////////////////////////////////////////
        //add specific js templates for the czr_module control
        //this is usually called in the manager for "registered" controls that need to be rendered with js
        //for this control, we'll do it another way because we need several js templates
        //=> that's why this control has not been "registered" and js templates are printed with the following action
        function ac_print_module_control_templates() {
            //Render the control wrapper for the CRUD types modules
            ?>
              <?php //Render the control wrapper for the CRUD types modules ?>
              <script type="text/html" id="tmpl-customize-control-czr_module-content">
                <label for="{{ data.settings['default'] }}-button">

                  <# if ( data.label ) { #>
                    <span class="customize-control-title">{{ data.label }}</span>
                  <# } #>
                  <# if ( data.description ) { #>
                    <span class="description customize-control-description">{{{ data.description }}}</span>
                  <# } #>
                </label>
              </script>
            <?php
        }
    }//class
endif;

?>