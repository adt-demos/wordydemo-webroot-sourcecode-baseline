<?php

class PeepSo3_Shortcode_Search {
    public function __construct()
    {
        add_shortcode('peepso_search', array(&$this, 'shortcode'));
    }

    public function shortcode() {
        PeepSo::set_current_shortcode('peepso_search');

        if (FALSE == apply_filters('peepso_access_content', TRUE, 'peepso_search', PeepSo::MODULE_ID)) {
            return PeepSoTemplate::do_404();
        }

        if(!isset($this->url) || !($this->url instanceof PeepSoUrlSegments)) {
            $this->url = PeepSoUrlSegments::get_instance();
        }

        wp_enqueue_script('peepso-search', PeepSo::get_asset('js/page-search.min.js'), array('peepso'), PeepSo::PLUGIN_VERSION, TRUE);

        ob_start();

        PeepSoTemplate::exec_template('search','search',['context'=>'shortcode']);

        ?>
        <style type="text/css">
            #peepso-search li {
                list-style-type: none;
            }
        </style>
        <?php
        PeepSo::reset_query();

        return (ob_get_clean());
    }

    public static function get_search_query() {
        $search = '';

        if(isset($_REQUEST['filter']) || isset($_REQUEST['s'])) {
            $PeepSoInput = new PeepSo3_Input();
            $search = $PeepSoInput->value('filter', $PeepSoInput->value('s'));
        } else {
            $PeepSoUrl = new PeepSoUrlSegments();
            if('peepso_search' == $PeepSoUrl->get(0)) {
                $search = $PeepSoUrl->get(1);
            }
        }

        return $search;
    }

    public static function description() {
        return 'Early Access search page.';
    }

    public static function post_state() {
        return _x('PeepSo', 'Page listing', 'peepso-core') . ' - ' . __('Search', 'peepso-core');
    }


}

new PeepSo3_Shortcode_Search();
