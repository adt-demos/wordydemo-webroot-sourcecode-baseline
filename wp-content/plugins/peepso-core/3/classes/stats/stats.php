<?php

class PeepSo3_Stats {

	private $optin_stats = 0;

	private static $_instance = NULL;

	private $mayfly = 'peepso_stats_last_run';
	private $mayfly_expire = 3600;

	private $debug = FALSE;

	public static $desc = '
Help us improve PeepSo by sending some important statistical information we can use to understand our users better. 
<br/><br/>When ON, our servers will receive and store your environment information (PHP version, WordPress version, locale used), some basic information about your set-up (how many users there are, what plugins and themes are used) and a few key PeepSo configuration options.
<br/><br/>This data will help us focus our efforts better based on real world scenarios.';

	private function __construct()
	{
		add_action('admin_init',function() {
			if(isset($_GET['peepso_stats_details'])) {
				$stats = $this->get_stats(TRUE);
				?>
				<h1>PeepSo Usage Tracking</h1>

				<p><?php echo self::$desc;?></p>

				<h2>What is being tracked?</h2>


				<h3>PeepSo Product Data</h3>
				<ul>
                    <li>Current <b><?php
							echo $stats['ver_peepso_desc'];
							unset($stats['ver_peepso']);
							unset($stats['ver_peepso_desc']);?></b></li>

                    <li><b><?php
							echo $stats['peepso_install_date_desc'];
							unset($stats['peepso_install_date']);
							unset($stats['peepso_install_date_desc']);?></b></li>
					<?php
					foreach($stats as $k=>$v) {
						if(stristr($k, 'prd_') && stristr($k, '_desc')) {
							unset($stats[$k]);
							unset($stats[str_replace('_desc','',$k)]);

							echo "<li>Is <b>$v</b> active?</li>";
						}
					}
					?>
				</ul>

				<h3>Selected PeepSo Configuration Options</h3>
				<ul>
					<?php
					$c = $stats['peepso_config_desc'];
					$desc = array(
						'videos_upload_enable' => 'Are <b>video uploads</b> enabled?',
					);
					unset($stats['peepso_config']);
					unset($stats['peepso_config_desc']);
					foreach($c as $k=>$v) {
						$k = isset($desc[$k]) ? $desc[$k] : $k ." (missing description)";
						echo "<li>$k</li>";
					}

					?>
				</ul>

				<h3>Basic Environment & Website Data</h3>
				<ul>
					<?php
					foreach($stats as $k=>$v) {
						if(stristr($k, 'ver_') && stristr($k, '_desc')) {
							unset($stats[$k]);
							unset($stats[str_replace('_desc','',$k)]);

							echo "<li>Current <b>$v</b></li>";
						}
					}
					$keys = array('url_desc','count_users_desc','count_plugins_desc');
					foreach($stats as $k=>$v) {
						if(in_array($k, $keys)) {
							unset($stats[$k]);
							unset($stats[str_replace('_desc','',$k)]);

							echo "<li>$v</li>";
						}
					}
					?>
				</ul>


				<?php
				if(count($stats)) {
					?>
					<h3>Other</h3>
					<p>Variables with a missing description</p>
					<ul>
					<?php
					foreach ($stats as $k => $v) {
						echo "<li>$k => $v</li>";
					}
					echo "</ul>";
				}
				?>


				<h2>Raw data send to PeepSo</h2>
				<pre><?php
					$stats = $this->get_stats();
					print_r($stats);
					?></pre>



				<?php
				die();
			}
		});

		$this->debug = isset($_GET['peepso_stats_debug']);

		if(isset($_GET['peepso_enable_tracking_nudge'])) {
			PeepSoConfigSettings::get_instance()->set_option('optin_stats', 1);
		}

		$this->optin_stats = PeepSo::get_option('optin_stats', 0);
		if(!$this->optin_stats) {

			if(PeepSo::is_admin() && is_admin() && isset($_GET['page']) && 'peepso_config'==$_GET['page']) {

				add_action('admin_notices', function() {

				    // reset logic
				    if(isset($_GET['reset_hide_tracking_nudge'])) {
					    delete_user_option(get_current_user_id(), 'peepso_tracking_last_nudge');
				    }

					// dismiss logic
					if(isset($_GET['peepso_hide_tracking_nudge'])) {
						update_user_option(get_current_user_id(), 'peepso_tracking_last_nudge', date('Y-m-d'));
					}

					$was_asked = TRUE;
					$last_nudge = get_user_option('peepso_tracking_last_nudge');

					// If last nudge is not found, user was never asked, use the install date instead
					if(!$last_nudge) {
						$was_asked = FALSE;
						$last_nudge = get_option('peepso_install_date');
					}

					if(time() - strtotime($last_nudge) > 90 * 24 * 3600) {
						PeepSoTemplate::exec_template('admin','tracking_nudge',array('was_asked'=>$was_asked, 'last_nudge'=>$last_nudge));
					}

				});

			}

			return FALSE;
		}

		$this->run();
	}

	public static function get_instance()
	{
		if (NULL === self::$_instance)
			self::$_instance = new self();
		return (self::$_instance);
	}

	private function get_stats($desc=FALSE) {
		global $wp_version;

		$peepso_config = array(
			'videos_upload_enable' => (int) PeepSo::get_option('videos_upload_enable'),
		);

		$count_users = count_users('memory');

		$stats = array(

			'url'               => trim(str_replace(array('http://','https://','www.'),'',get_option( 'siteurl' )),'/'),
			'url_desc'          => 'The URL of your site',

			'ver_peepso'        => PeepSo::PLUGIN_VERSION,
			'ver_peepso_desc'   => 'PeepSo version',

			'ver_wp'           =>  $wp_version,
			'ver_wp_desc'       => 'WordPress version',

			'ver_php'           => PHP_VERSION,
			'ver_php_desc'       => 'PHP version',

			'ver_locale'        =>  get_locale(),
			'ver_locale_desc'       => 'site language',

			'count_users'       => $count_users['total_users'],
			'count_users_desc'  => 'Number of registered users',

			'count_plugins'     => (int) count(get_option( 'active_plugins' )),
			'count_plugins_desc'  => 'Number of active plugins',

			'peepso_install_date'     => get_option('peepso_install_date'),
			'peepso_install_date_desc'  => 'PeepSo install date (community age)',

			'peepso_config'     => json_encode($peepso_config),
			'peepso_config_desc' => $peepso_config,

			'prd_gecko'         => (int) class_exists('GeckoConfigSettings'),
			'prd_gecko_desc'    => 'Gecko Theme',

			'prd_photos'        => (int) class_exists('PeepSoSharePhotos'),
			'prd_photos_desc'    => 'Photos Plugin',

			'prd_media'         => (int) class_exists('PeepSoVideos'),
			'prd_media_desc'    => 'Audio & Video Plugin',

			'prd_chat'          => (int) class_exists('PeepSoMessagesPlugin'),
			'prd_chat_desc'    => 'Chat Plugin',

			'prd_groups'        => (int) class_exists('PeepSoGroupsPlugin'),
			'prd_groups_desc'    => 'Groups Plugin',

			'prd_friends'       => (int) class_exists('PeepSoFriendsPlugin'),
			'prd_friends_desc'    => 'Friends Plugin',

			'prd_polls'         => (int) class_exists('PeepSoPolls'),
			'prd_polls_desc'    => 'Polls Plugin',

			'prd_userlimits'    => (int) class_exists('peepsolimitusers'),
			'prd_userlimits_desc'    => 'User Limits Plugin',

			'prd_vip'           => (int) class_exists('PeepSoVIPPlugin'),
			'prd_vip_desc'    => 'VIP Plugin',

			'prd_giphy'         => (int) class_exists('PeepSoGiphyPlugin'),
			'prd_giphy_desc'    => 'GIPHY Integration Plugin',

			'prd_emaildigest'   => (int) class_exists('PeepSoEmailDigest'),
			'prd_emaildigest_desc'    => 'Email Digest Plugin',

			'prd_automator'     => (int) class_exists('PeepSoAutoFriendsPlugin'),
			'prd_automator_desc'    => 'AutoFriends Plugin',

			'prd_wordfilter'    => (int) class_exists('PeepSoWordFilterPlugin'),
			'prd_wordfilter_desc'    => 'WordFilter Plugin',

			'prd_advancedads'    => (int) class_exists('PeepSoAdvancedAdsPlugin'),
			'prd_advancedads_desc'    => 'Advanced Ads Integration Plugin',

			'prd_badgeos'       => (int) class_exists('BadgeOS_PeepSo'),
			'prd_badgeos_desc'    => 'BadgeOS Integration Plugin',

			'prd_edd'           => (int) class_exists('PeepSoEDD'),
			'prd_edd_desc'    => 'EDD Integration Plugin',

			'prd_learndash'     => (int) class_exists('PeepSoLearnDash'),
			'prd_learndash_desc'    => 'LearnDash Integration Plugin',

			'prd_pmp'           => (int) class_exists('PeepSoPMP'),
			'prd_pmp_desc'    => 'PMP Integration Plugin',

			'prd_mycred'        => (int) class_exists('PeepSoMyCreds'),
			'prd_mycred_desc'    => 'myCRED Integration Plugin',

			'prd_social_login' => (int) (class_exists('TwistPress_Social_Login') ||class_exists('PeepSo_Social_Login')),
            'prd_social_login_desc'    => 'Social Login Integration Plugin',

            'prd_wpadverts'     => (int) class_exists('PeepSoWPAdverts'),
			'prd_wpadverts_desc'    => 'WPAdverts Integration Plugin',

            'prd_woocommerce'   => (int) class_exists('WBPWI_PeepSo_Woo_Integration'),
            'prd_woocommerce_desc'    => 'WooCommerce Integration Plugin',

            'prd_earlyaccess'   => (int) class_exists('PeepSoEarlyAccessPlugin'),
            'prd_earlyaccess_desc'    => 'Early Access Plugin',
		);

		if(!$desc) {
			foreach($stats as $k=>$v) {
				if(stristr($k,'_desc')) {
					unset($stats[$k]);
				}
			}
		}

		return $stats;
	}

	private function run() {

		if($this->debug) {
			PeepSo3_Mayfly::del($this->mayfly);
		}

		if(!PeepSo::is_api_request() && !strlen(PeepSo3_Mayfly::get($this->mayfly))) {

			$stats = $this->get_stats();

			foreach($stats as &$stat) {
				$stat = urlencode($stat);
			}

			//array_walk($stats, 'urlencode');

			$url = 'https://stats.peep.so?action=insert';

			foreach($stats as $k => $v) {
				$url .= "&$k=$v";
			}

			$request = wp_safe_remote_get($url);

			PeepSo3_Mayfly::set($this->mayfly, $url, $this->mayfly_expire);
		}
	}


}