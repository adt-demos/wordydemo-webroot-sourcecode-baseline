<?php

class PeepSoConfigSections extends PeepSoConfigSectionAbstract {
    const SITE_ALERTS_SECTION = 'site_alerts_';

    public function register_config_groups() {
        $this->set_context( 'left' );
        $this->activity();
        $this->reporting();
        $this->login();
        $this->usernames();
        $this->registration();

        $this->set_context( 'right' );

        // Don't show licenses box on our demo / d3mo site
        if ( ! isset( $_SERVER['HTTP_HOST'] ) || ! in_array( $_SERVER['HTTP_HOST'], [
                'demo.peepso.com',
                'd3mo.peepso.com',
                //'three.peepso.com'
            ] ) ) {
            $this->license();
        }
    }

    private function login() {

        # Always remember me
        $this->args( 'default', 0 );

        $this->set_field(
            'site_frontpage_rememberme_default',
            __( 'Always check "remember me" by default', 'peepso-core' ),
            'yesno_switch'
        );

        $this->set_group(
            'login_logout',
            __( 'Login & Logout', 'peepso-core' )
        );
    }

    private function usernames() {
        // Allow Username changes
        $this->set_field(
            'system_allow_username_changes',
            __( 'Allow username changes', 'peepso-core' ),
            'yesno_switch'
        );

        // Allow Username changes
        $this->args( 'descript', __( 'Some plugins (like WooCommerce, EDD and Learndash) create user accounts where the usename is an e-mail address. PeepSo uses the usernames to build profile URLs, which can lead to accidental e-mail exposure through site URLs. Enabling this feature will cause PeepSo to step in during third party user registration and automatically generate a safe username for the new user.', 'peepso-core' ) );
        $this->set_field(
            'thirdparty_username_cleanup',
            __( 'Clean up third party registrations', 'peepso-core' ),
            'yesno_switch'
        );

        $this->set_group(
            'usernames',
            __( 'Usernames', 'peepso-core' )
        );
    }

    private function registration() {
        /** GENERAL **/

        // disabled registration
        $this->args( 'descript', __( 'ON: registration through PeepSo becomes impossible and is not shown anywhere in the front-end. Use only if your site is a closed community or registrations are coming in through another plugin.', 'peepso-core' ) );
        $this->args( 'default', 0 );
        $this->set_field(
            'site_registration_disabled',
            __( 'Disable registration', 'peepso-core' ),
            'yesno_switch'
        );

        // Re-enter e-mail
        $this->args( 'default', 1 );
        $this->args( 'descript', __( 'ON: users need to type their e-mail twice, which improves the chance of it being valid and the verification e-mail reaching them.', 'peepso-core' ) );
        $this->set_field(
            'registration_confirm_email_field',
            __( 'Repeat e-mail field', 'peepso-core' ),
            'yesno_switch'
        );

        // Enable Account Verification
        $this->args( 'descript',
            __( 'ON: users register, confirm their e-mail (optional) and must be accepted by an Admin. Users are notified by email when they\'re approved.<br/>OFF: users register, confirm their email (optional) and can immediately participate in your community.', 'peepso-core' )
            . "<br/>"
            . __( 'PeepSo will not apply this step to third party registrations (WooCommerce, EDD, Social Login etc.)', 'peepso-core' )
        );

        $this->set_field(
            'site_registration_enableverification',
            __( 'Admin account verification', 'peepso-core' ),
            'yesno_switch'
        );

        // # Force profile completion
        $this->args( 'descript',
            __( 'ON: users have to fill in all required fields before being able to participate in the community.', 'peepso-core' )
            . "<br/>"
            . __( 'Applies to all users.', 'peepso-core' )
        );
        $this->set_field(
            'force_required_profile_fields',
            __( 'Force profile completion', 'peepso-core' ),
            'yesno_switch'
        );


        // Enable Secure Mode For Registration
        $this->args( 'descript', __( 'Requires a valid SSL certificate.<br/>Enabling this option without a valid certificate might break your site.', 'peepso-core' ) );
        $this->set_field(
            'site_registration_enable_ssl',
            __( 'Force SSL on registration page', 'peepso-core' ),
            'yesno_switch'
        );


        /** EMAIL CONFIRMATION **/
        // # Separator E-mail Confirmation
        $this->set_field(
            'separator_emil_confirmation',
            __( 'Activation & Redirect', 'peepso-core' ),
            'separator'
        );


        // Disable e-mail confirmation
        $this->args( 'descript',
            __( 'ON: users don\'t need to confirm their e-mail.', 'peepso-core' )
            . "<br/>"
            . __( 'PeepSo will not apply this step to third party registrations (WooCommerce, EDD, Social Login etc.)', 'peepso-core' )
        );
        $this->set_field(
            'registration_disable_email_verification',
            __( 'Skip e-mail verification', 'peepso-core' ),
            'yesno_switch'
        );

        $options = array(
            0 => __( 'No', 'peepso-core' ),
        );

        for ( $i = 1; $i <= 5; $i ++ ) {
            $options[ $i ] = sprintf( _n( '%d time', '%d times', $i, 'peepso-core' ), $i );
        }

//        $this->args('options', $options);
//        $this->args('descript', __('If enabled, unconfirmed users will receive a repeated activation link', 'msgso'));
//        $this->set_field(
//            'registration_email_verification_resend',
//            __('Resend activation e-mail', 'msgso'),
//            'select'
//        );


//        $options = array();
//
//        for($i=1; $i<=14; $i++) {
//            $options[$i] = sprintf(_n('%d day', '%d days', $i, 'peepso-core'), $i);
//        }
//
//        $this->args('options', $options);
//
//        $this->set_field(
//            'registration_email_verification_resend_period',
//            __('Resend every', 'msgso'),
//            'select'
//        );

        // # Redirect After activations

        $args    = array(
            'sort_order'   => 'asc',
            'sort_column'  => 'post_title',
            'hierarchical' => 1,
            'exclude'      => '',
            'include'      => '',
            'meta_key'     => '',
            'meta_value'   => '',
            'authors'      => '',
            'child_of'     => 0,
            'parent'       => - 1,
            'exclude_tree' => '',
            'number'       => '',
            'offset'       => 0,
            'post_type'    => 'page',
            'post_status'  => 'publish'
        );
        $pages   = get_pages( $args );
        $options = array(
            - 1 => __( 'First known visit', 'peepso-core' ),
            0   => __( 'Home page', 'peepso-core' ) . ': ' . home_url( '/' ),
        );

        $pageredirect = PeepSo::get_option( 'site_activation_redirect' );
        $settings     = PeepSoConfigSettings::get_instance();
        foreach ( $pages as $page ) {
            // handling selected old value (activity/profile)
            if ( $page->post_name == $pageredirect ) {
                //$this->args('default', $page->ID);
                // update option to selected ID
                $settings->set_option( 'site_activation_redirect', $page->ID );
            }

            $options[ $page->ID ] = __( 'Page:', 'peepso-core' ) . ' ' . ( $page->post_parent > 0 ? '&nbsp;&nbsp;' : '' ) . $page->post_title;
        }

        $this->args( 'options', $options );

        $this->set_field(
            'site_activation_redirect',
            __( 'Activation redirect', 'peepso-core' ),
            'select'
        );
        /** RECAPTCHA **/
        // # Separator Recaptcha
        $this->set_field(
            'separator_recaptcha',
            __( 'ReCaptcha', 'peepso-core' ),
            'separator'
        );

        // # Enable ReCaptcha
        $this->set_field(
            'site_registration_recaptcha_enable',
            __( 'Enable ReCaptcha', 'peepso-core' ),
            'yesno_switch'
        );

        // # ReCaptcha Site Key
        $this->set_field(
            'site_registration_recaptcha_sitekey',
            __( 'Site Key', 'peepso-core' ),
            'text'
        );

        // # ReCaptcha Secret Key
        $this->args( 'descript',
            __( 'Get INVISIBLE ReCaptcha keys <a href="https://www.google.com/recaptcha/admin" target="_blank">here</a></strong>.<br/>Having issues or questions? Please refer to the <a href="http://peep.so/recaptcha" target="_blank">documentation</a> or contact PeepSo Support.', 'peepso-core' ) );
        $this->set_field(
            'site_registration_recaptcha_secretkey',
            __( 'Secret Key', 'peepso-core' ),
            'text'
        );

        // # Use ReCaptcha Globally
        $this->args( 'descript',
            __( 'ON: will use "www.recaptcha.net" in circumstances when "www.google.com" is not accessible.', 'peepso-core' ) );
        $this->set_field(
            'site_registration_recaptcha_use_globally',
            __( 'Use ReCaptcha Globally', 'peepso-core' ),
            'yesno_switch'
        );


        /** T&C **/

        // # Separator Terms & Conditions
        $this->set_field(
            'separator_terms',
            __( 'Terms & Conditions', 'peepso-core' ),
            'separator'
        );

        // # Enable Terms & Conditions
        $this->set_field(
            'site_registration_enableterms',
            __( 'Enable Terms &amp; Conditions', 'peepso-core' ),
            'yesno_switch'
        );

        // # Terms & Conditions Text
        $this->args( 'raw', true );

        $this->set_field(
            'site_registration_terms',
            __( 'Terms &amp; Conditions', 'peepso-core' ),
            'textarea'
        );

        /** Privacy Policy **/

        // # Separator Terms & Conditions
        $this->set_field(
            'separator_privacy',
            __( 'Privacy Policy', 'peepso-core' ),
            'separator'
        );

        // # Enable Privacy
        $this->set_field(
            'site_registration_enableprivacy',
            __( 'Enable Privacy Policy', 'peepso-core' ),
            'yesno_switch'
        );

        // # Privacy Text
        $this->args( 'raw', true );

        $this->set_field(
            'site_registration_privacy',
            __( 'Privacy Policy', 'peepso-core' ),
            'textarea'
        );

        /** RESEND **/
        // # Separator Advanced
        $this->set_field(
            'separator_resend_confirmation',
            __( 'Advanced', 'peepso-core' ),
            'separator'
        );

        // Resend ON/OFF
        $this->args( 'descript', __( 'PeepSo will resend the activation e-mail a defined amount of times to any users who did not activate their account.', 'peepso-core' ) );
        $this->set_field(
            'resend_activation',
            __( 'Automatically resend activation', 'peepso-core' ),
            'yesno_switch'
        );


        // Resend DELAY
        $options = array();

        if(isset($_GET['resend_activation_debug'])) {
            $options[60] = "DEBUG: ONE MINUTE";
        }

        for ( $i = 1; $i <= 72; $i ++ ) {
            $options[ $i * 3600 ] = sprintf( _n( '%d hour', '%d hours', $i ), $i );
        }



        $this->args( 'options', $options );
        $this->set_field(
            'resend_activation_interval',
            __( 'Every', 'peepso-core' ),
            'select'
        );

        // Resend MAX TRY
        $options = array();

        if(isset($_GET['resend_activation_debug'])) {
            $options[50] = "DEBUG: 50 TIMES";
            $options[100] = "DEBUG: 100 TIMES";
        }

        for ( $i = 1; $i <= 10; $i ++ ) {
            $options[ $i ] = sprintf( _n( '%d attempt', '%d attempts', $i ), $i );
        }

        $this->args( 'options', $options );
        $this->set_field(
            'resend_activation_max_attempts',
            __( 'Maximum', 'peepso-core' ),
            'select'
        );




        // Build Group

        #$this->args('summary', $summary);

        $this->set_group(
            'registration',
            __( 'Registration', 'peepso-core' )
        );
    }

    private function activity() {
        // # Separator Callout
        $this->set_field(
            'separator_general',
            __( 'General', 'peepso-core' ),
            'separator'
        );



        // # Hide Activity Stream From Guests
        $this->set_field(
            'site_activity_hide_stream_from_guest',
            __( 'Hide Activity Stream from Non-logged in Users', 'peepso-core' ),
            'yesno_switch'
        );

        // # Enable Repost
        $this->set_field(
            'site_repost_enable',
            __( 'Enable Repost', 'peepso-core' ),
            'yesno_switch'
        );

        $stream_config = apply_filters( 'peepso_activity_stream_config', array() );

        if ( count( $stream_config ) > 0 ) {

            foreach ( $stream_config as $option ) {
                if ( isset( $option['descript'] ) ) {
                    $this->args( 'descript', $option['descript'] );
                }
                if ( isset( $option['int'] ) ) {
                    $this->args( 'int', $option['int'] );
                }
                if ( isset( $option['default'] ) ) {
                    $this->args( 'default', $option['default'] );
                }

                $this->set_field( $option['name'], $option['label'], $option['type'] );
            }
        }

        // # Separator Post Save
        $this->set_field(
            'separator_post_save',
            __( 'Saved Posts', 'peepso-core' ),
            'separator'
        );

        // # Message Post Save
        $this->set_field(
            'save_post_message',
            'This feature <b>requires the WP REST API</b>. Please make sure your server allows REST (wp-json) connections and that no security software is blocking GET, POST, PUT, DELETE and PATCH calls. <br/><br/>If anything blocks connections to URLs in the <b>/wp-json/peepso/</b> namespace, this and many future PeepSo features will not work.',
            'message'
        );


        // # Enable Post Save
        $this->args( 'descript', __( 'Allows users to save (favorite / bookmark) posts into a private "saved posts" collection.', 'peepso-core' ) );
        $this->set_field(
            'post_save_enable',
            __( 'Enable Saved Posts', 'peepso-core' ),
            'yesno_switch'
        );


        // # Separator Comments
        $this->set_field(
            'separator_comments',
            __( 'Comments', 'peepso-core' ),
            'separator'
        );

        // # Number Of Comments To Display
        $this->args( 'validation', array( 'numeric' ) );

        $this->set_field(
            'site_activity_comments',
            __( 'Number of Comments to display', 'peepso-core' ),
            'text'
        );

        // Show comments in batches
        $this->args( 'validation', array( 'numeric' ) );
        $this->args( 'int', true );
        $this->set_field(
            'activity_comments_batch',
            __( 'Show X more comments', 'peepso-core' ),
            'text'
        );

        /* READMORE */

        // # Separator Readmore
        $this->set_field(
            'separator_readmore',
            __( 'Read more', 'peepso-core' ),
            'separator'
        );

        // # Show Read More After N Characters
        $this->args( 'default', 1000 );
        $this->args( 'validation', array( 'numeric' ) );

        $this->set_field(
            'site_activity_readmore',
            __( "Show 'read more' after: [n] characters", 'peepso-core' ),
            'text'
        );


        // # Redirect To Single Post View
        $this->args( 'default', 2000 );
        $this->args( 'validation', array( 'numeric' ) );

        $this->set_field(
            'site_activity_readmore_single',
            __( 'Redirect to single post view when post is longer than: [n] characters', 'peepso-core' ),
            'text'
        );

        // Build Group
        $this->set_group(
            'activity',
            __( 'Activity', 'peepso-core' )
        );
    }

    private function reporting() {

        // # Enable Reporting
        $this->args( 'children', array( 'site_reporting_types', 'reporting_notify' ) );
        $this->set_field(
            'site_reporting_enable',
            __( 'Enabled', 'peepso-core' ),
            'yesno_switch'
        );

        // # Automatically unpublish reported posts after X reports

        $this->options = array();

        for ( $i = 0; $i <= 50; $i ++ ) {
            $options[ $i ] = $i;
        }

        $this->args( 'options', $options );
        $this->args( 'default', 0 );
        $this->args( 'validation', array( 'numeric' ) );
        $this->args( 'descript', __( 'If a post is reported enough times, it will be automatically unpublished. Set to 0 to disable.', 'peepso-core' ) );

        $this->set_field(
            'site_reporting_num_unpublish_post',
            __( "Automatically unpublish posts after: [n] reports", 'peepso-core' ),
            'select'
        );

        // # Predefined  Text
        $this->args( 'raw', true );
        $this->args( 'multiple', true );
        $this->args( 'descript', __( 'One per line.', 'peepso-core' ) );
        $this->set_field(
            'site_reporting_types',
            __( 'Predefined report reasons', 'peepso-core' ),
            'textarea'
        );

        // # E-mail alerts
        $this->args( 'descript', __( 'ON: Administrators and Community Administrators will receive e-mails about new reports' ) );
        $this->set_field(
            'reporting_notify_email',
            __( 'E-mail alerts', 'peepso-core' ),
            'yesno_switch'
        );

        // # E-mail alerts
        $this->args( 'descript', __( 'One per line.', 'peepso-core' ) . ' ' . __( 'Additional e-mails to receive notifications about new reports.' ) );
        $this->set_field(
            'reporting_notify_email_list',
            __( 'Additional recipients', 'peepso-core' ),
            'textarea'
        );


        // Build Group
        $this->set_group(
            'reporting',
            __( 'Reporting', 'peepso-core' )
        );
    }

    private function license() {
        $this->set_field(
            'bundle',
            __( 'Use a PeepSo Bundle license', 'peepso-core' ),
            'yesno_switch'
        );

        $this->set_field(
            'bundle_license',
            __( 'PeepSo Bundle License Key', 'peepso-core' ),
            'text'
        );

        if ( isset( $_GET['peepso_debug'] ) ) {
            PeepSo3_Mayfly::del( 'peepso_config_licenses_bundle' );
        }

        $bundle = PeepSo3_Mayfly::get( 'peepso_config_licenses_bundle' );

        if ( ! strlen( $bundle ) ) {
            $url = PeepSoAdmin::PEEPSO_URL . '/peepsotools-integration-json/peepso_config_licenses_bundle.html';

            // Attempt contact with PeepSo.com without sslverify
            $resp = wp_remote_get( add_query_arg( array(), $url ), array( 'timeout' => 10, 'sslverify' => false ) );

            // In some cases sslverify is needed
            if ( is_wp_error( $resp ) ) {
                $resp = wp_remote_get( add_query_arg( array(), $url ), array( 'timeout' => 10, 'sslverify' => true ) );
            }

            if ( is_wp_error( $resp ) ) {

            } else {
                $bundle = $resp['body'];
                PeepSo3_Mayfly::set( 'peepso_config_licenses_bundle', $bundle, 3600 * 24 );
            }
        }

        $this->set_field(
            'bundle_message',
            $bundle,
            'message'
        );

        // Get all licensed PeepSo products
        $products = apply_filters( 'peepso_license_config', array() );

        if ( count( $products ) ) {

            $new_products = array();
            foreach ( $products as $prod ) {

                $key = $prod['plugin_name'];

                if ( strstr( $prod['plugin_name'], ':' ) ) {
                    $name                = explode( ':', $prod['plugin_name'] );
                    $prod['cat']         = $name[0];
                    $prod['plugin_name'] = $name[1];
                }

                if ( !isset($prod['cat']) || !strlen($prod['cat']) ) {
                    $prod['cat'] = $prod['plugin_name'];
                }

                $new_products[ $key ] = $prod;
            }

            ksort( $new_products );

            // Loop through the list and build fields
            $prev_cat = null;
            foreach ( $new_products as $prod ) {

                if ( isset( $prod['cat'] ) && $prev_cat != $prod['cat'] ) {
                    $this->set_field(
                        'cat_' . $prod['cat'],
                        $prod['cat'],
                        'separator'
                    );

                    $prev_cat = $prod['cat'];
                }
                // label contains some extra HTML for  license checking AJAX to hook into
                $label = $prod['plugin_name'];
                $label .= ' <small style=color:#cccccc>';
                $label .= $prod['plugin_version'] . '</small>';
                $label .= ' <span class="license_status_check" id="' . $prod['plugin_slug'] . '" data-plugin-name="' . $prod['plugin_edd'] . '"><img src="images/loading.gif"></span>';

                $this->set_field(
                    'site_license_' . $prod['plugin_slug'],
                    $label,
                    'text'
                );
            }
        }

        // Build Group
        $this->set_group(
            'license',
            __( 'License Key Configuration', 'peepso-core' ),
            __( 'This is where you configure the license keys for each PeepSo add-on. You can find your license numbers <a target="_blank" href="https://www.peepso.com/my-licenses/">here</a>. Please copy them here and click SAVE at the bottom of this page.', 'peepso-core' )
            . ' ' . sprintf( __( 'We are detecting %s as your install URL. Please make sure your "supported domain" is configured properly.', 'peepso-core' ), str_ireplace( array(
                'http://',
                'https://'
            ), '', home_url() ) )
            . '<br><br><b>'
            . __( 'If some licenses are not validating, please make sure to click the SAVE button.', 'peepso-core' )
            . ' </b><br/>'
            . __( 'If that does not help, please <a target="_blank" href="https://www.peepso.com/contact/">Contact Support</a>.', 'peepso-core' )

        );
    }

}

// EOF
