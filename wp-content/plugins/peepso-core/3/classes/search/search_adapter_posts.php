<?php

if(!class_exists('PeepSo3_Search_Adapter')) {
    require_once(dirname(__FILE__) . '/search_adapter.php');
    new PeepSoError('Autoload issue: PeepSo3_Search_Adapter not found ' . __FILE__);
}

class PeepSo3_Search_Adapter_Posts extends PeepSo3_Search_Adapter {

    private $results;
    private $ids;

    public function __construct()
    {
        $this->section = 'posts';
        $this->title = __('Community posts', 'peepso-core');
        $this->url = PeepSo::get_page('activity').'?filter=';

        $this->ids = $this->results = [];


        parent::__construct();
    }

    public function results() {

        $results = [];

        // We trick the old AJAX based member search into giving us some raw data
        $_GET['no_html'] = 1;
        $_GET['limit'] = $this->config['items_per_section']+1; // because of pinned posts, we might run into duplicates
        $_GET['search'] = $this->query;


        $this->add_results(TRUE);
        $this->add_results(FALSE);

        return $this->results;
    }

    private function add_results($pinned) {

        if(isset($_GET['pinned'])) {
            unset($_GET['pinned']);
        }

        if($pinned) {
            $_GET['pinned'] = 1;
        }

        $PeepSoActivity = new PeepSoActivity();

        $resp = new PeepSoAjaxResponse();
        $PeepSoActivity->show_posts_per_page($resp);

        if(isset($resp->data['no_html_data']) && count($resp->data['no_html_data'])) {

            foreach($resp->data['no_html_data'] as $post_data) {

                global $post;
                $group_id = FALSE;
                $group = FALSE;

                $post = get_post($post_data['ID']);

                if(count($this->results) >= $this->config['items_per_section']) {
                    break;
                }

                // avoid duplicates (generated by pinned endpoint returning the un-pinned item on the bottom)
                if(in_array($post_data['ID'], $this->ids)) {
                    continue;
                }

                $meta = [];

                $PeepSoUser = PeepSoUser::get_instance($post_data['post_author']);

                if($group_id = get_post_meta($post_data['ID'],'peepso_group_id', TRUE)) {
                    $group = new PeepSoGroup($group_id);
                    $level = [
                        'title' => $group->privacy->name,
                        'icon' => $group->privacy->icon,
                    ];
                } else {
                    $privacy = PeepSoPrivacy::get_instance();
                    $level = $privacy->get_access_setting($post_data['act_access']);
                }


                $PeepSoActivity = PeepSoActivity::get_instance();



                $meta = [];

                if($group) {
                    $level['icon'] = 'gcis '.$group->privacy['icon'];
                    $level['label'] = sprintf(__('%s Group','groupso'), $group->privacy['name']);
                }

                $meta[] = [
                    'context' => 'privacy',
                    'icon' => $level['icon'],
                    'title' => $level['label'],
                ];

                if($group) {
                    $meta[] = [
                        'context' => 'group',
                        'icon' => 'gcis gci-users',
                        'title'=> $group->get('name'),
                    ];
                }


                $meta[]= [
                        'context' => 'time',
                        'icon' => 'gcis gci-clock',
                        'title' => $PeepSoActivity->post_age(FALSE),
                        //sprintf(__('%s ago', 'peepso-core'), human_time_diff_round_alt(strtotime($post_data['post_date']))),
                    ];




                $pinned = intval(isset($post_data['pinned']) && 0 < $post_data['pinned']);
                if($pinned) {
                    $meta[] = [
                        'context' => 'pinned',
                        'icon' => 'gcis gci-map-pin',
                        'title' => 'Pinned',
                    ];
                }

                $extras = [
                    'mine' => -1,
                ];

                if(get_current_user_id()) {
                    $extras['mine'] = intval($post_data['post_author'] == get_current_user_id());
                }

                //$extras['post_data'] = $post_data;

                $image = $PeepSoUser->get_avatar();

                // Photos
                if(class_exists('PeepSoSharePhotos') && PeepSoSharePhotos::MODULE_ID == $post_data['act_module_id']) {
                    $photo_type = get_post_meta($post_data['ID'], PeepSoSharePhotos::POST_META_KEY_PHOTO_TYPE, true);

                    // if type is photo album, show latest photos as first ones.
                    if($photo_type === PeepSoSharePhotos::POST_META_KEY_PHOTO_TYPE_ALBUM) {
                        $order = 'desc';
                    }

                    $PeepSoPhotosModel = new PeepSoPhotosModel();
                    $photos = $PeepSoPhotosModel->get_post_photos($post_data['ID'], $order);
                    if(count($photos)) {
                        $photo = $photos[0];
                        if (isset($photo->pho_thumbs['l'])) {
                            $image = $photo->pho_thumbs['l'];
                        } else {
                            $image = $photo->pho_thumbs['m'];
                        }
                    }
                }

                // Videos
                if(class_exists('PeepSoVideos') && PeepSoVideos::MODULE_ID == $post_data['act_module_id']) {
                    $PeepSoVideos = PeepSoVideos::get_instance();
                    $videos = $PeepSoVideos->get_post_video($post_data['ID']);
                    if(count($videos)) {
                        $video = $videos[0];
                        $image = isset($video->vid_thumbnail) ? $video->vid_thumbnail : $image;
                    }

                }

                $this->ids[]=$post_data['ID'];
                $this->results[]= $this->map_item(
                    [
                        'id' => $post_data['ID'],
                        'title' => sprintf(__('Post by %s','peepso-core'), $PeepSoUser->get_fullname()),
                        'text' => $post_data['human_friendly'],
                        'url' => PeepSo::get_page('activity_status') . $post_data['post_title'] . '/',
                        'image' => $image,
                        'meta' => $meta,
                        'extras' => $extras,
                    ]
                );
            }
        }
    }

}

new PeepSo3_Search_Adapter_Posts();