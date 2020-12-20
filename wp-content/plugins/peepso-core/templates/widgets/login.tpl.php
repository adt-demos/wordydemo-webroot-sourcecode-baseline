<?php

echo $args['before_widget'];

$PeepSoProfile=PeepSoProfile::get_instance();
$PeepSoUser = $PeepSoProfile->user;

$disable_registration = intval(PeepSo::get_option('site_registration_disabled', 0));
// PeepSo/peepso#2906 hide "resend activation" until really necessary
$hide_resend_activation = TRUE;

$view_class = $instance['view_option'];

?>

<?php
  if(!$instance['user_id'] > 0)
  {
?>

  <div class="psw-login psw-login--<?php echo $view_class; ?>">
    <!-- Title of Profile Widget -->
    <?php
    if ( ! empty( $instance['title'] ) ) {
      echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
    }
    ?>

    <div class="psf-login">
        <form class="ps-form ps-form--login ps-js-form-login-widget" action="" onsubmit="return false;" method="post" name="login" id="ps-form-login-me">
            <!-- Login -->
            <div class="ps-form__row ps-form__row--username ps-js-username-field">
                <div class="ps-form__field ps-form__field--icon">
                    <input class="ps-input ps-input--sm ps-input--icon" type="text" name="username" placeholder="<?php echo __('Username', 'peepso-core'); ?>" mouseev="true"
                           autocomplete="off" keyev="true" clickev="true" />
                    <i class="gcis gci-user"></i>
                </div>
            </div>

            <!-- Password -->
            <div class="ps-form__row ps-form__row--password ps-js-password-field">
                <div class="ps-form__field ps-form__field--icon">
                    <input class="ps-input ps-input--sm ps-input--icon" type="password" name="password" placeholder="<?php echo __('Password', 'peepso-core'); ?>" mouseev="true"
                           autocomplete="off" keyev="true" clickev="true" />
                    <i class="gcis gci-key"></i>
                </div>
            </div>

            <?php include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); ?>
            <?php if( PeepSo::two_factor_plugin_enabled() /* is_plugin_active('two-factor-authentication/two-factor-login.php') */ ) { ?>
                <!-- Two Factor authentication -->
                <div class="ps-form__row ps-form__row--password ps-js-password-field">
                    <div class="ps-form__field ps-form__field--icon">
                        <input class="ps-input ps-input--sm ps-input--icon" type="password" name="password" placeholder="<?php echo __('Password', 'peepso-core'); ?>" mouseev="true"
                               autocomplete="off" keyev="true" clickev="true" />
                        <i class="gcis gci-fingerprint"></i>
                    </div>
                </div>
            <?php } ?>

            <!-- Remember password -->
            <div class="ps-form__row ps-form__row--remember ps-js-password-field">
                <div class="ps-form__field ps-form__field--checkbox">
                    <div class="ps-checkbox ps-checkbox--login">
                        <input class="ps-checkbox__input" type="checkbox" alt="<?php echo __('Remember Me', 'peepso-core'); ?>" value="yes" name="remember" id="ps-form-login-me-remember" <?php echo PeepSo::get_option('site_frontpage_rememberme_default', 0) ? ' checked':'';?>>
                        <label class="ps-checkbox__label" for="ps-form-login-me-remember"><?php echo __('Remember Me', 'peepso-core'); ?></label>
                    </div>
                </div>
            </div>

            <!-- Submit form -->
            <div class="ps-form__row ps-form__row--submit ps-js-password-field">
                <div class="ps-form__field ps-form__field--submit">
                    <button type="submit" class="ps-btn ps-btn--sm ps-btn--action ps-btn--login ps-btn--loading">
                        <span><?php echo __('Login', 'peepso-core'); ?></span>
                        <img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>">
                    </button>
                </div>
            </div>

            <input type="hidden" name="option" value="ps_users">
            <input type="hidden" name="task" value="-user-login">
            <input type="hidden" name="redirect_to" value="<?php echo PeepSo::get_page('redirectlogin'); ?>" />
            <?php
            // Remove ID attribute from nonce field.
            $nonce = wp_nonce_field('ajax-login-nonce', 'security', true, false);
            $nonce = preg_replace( '/\sid="[^"]+"/', '', $nonce );
            echo $nonce;
            ?>

            <?php do_action('peepso_action_render_login_form_after'); ?>
        </form>

        <?php do_action('peepso_after_login_form'); ?>

        <div class="psf-login__links">
            <?php
            $disable_registration = intval(PeepSo::get_option('site_registration_disabled', 0));

            // PeepSo/peepso#2906 hide "resend activation" until really necessary
            $hide_resend_activation = TRUE;
            ?>

            <?php if(0 === $disable_registration) { ?>
                <a class="psf-login__link psf-login__link--register" href="<?php echo PeepSo::get_page('register'); ?>"><?php echo __('Register', 'peepso-core'); ?></a>
            <?php } ?>

            <a class="psf-login__link psf-login__link--recover" href="<?php echo PeepSo::get_page('recover'); ?>"><?php echo __('Forgot Password', 'peepso-core'); ?></a>

            <?php if(0 === $disable_registration) { ?>
                <a class="psf-login__link psf-login__link--activation ps-js-register-activation" href="<?php echo PeepSo::get_page('register'); ?>?resend"><?php echo __('Resend activation code', 'peepso-core'); ?></a>
            <?php } ?>
        </div>
    </div>

    <script>
        (function() {
            function initLoginForm( $ ) {
                $('.ps-js-form-login-widget').off('submit').on('submit', function( e ) {
                    e.preventDefault();
                    e.stopPropagation();
                    peepso.login.submit( e.target );
                });
            }

            // naively check if jQuery exist to prevent error
            var timer = setInterval(function() {
                if ( window.jQuery ) {
                    clearInterval( timer );
                    initLoginForm( window.jQuery );
                }
            }, 1000 );
        })();
    </script>
  </div>
<?php
  }
?>

<?php
echo $args['after_widget'];
// EOF
