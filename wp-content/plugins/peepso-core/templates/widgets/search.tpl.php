<?php
if(get_current_user_id()) {
    echo $args['before_widget'];

    ?>
    <div class="ps-widget__wrapper- ps-widget- ps-js-widget-search">

    <div class="ps-widget__header-">
        <?php
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        ?>
    </div>
    <div class="ps-widget__body-">
        <div class="ps-widget--members">
            <?php PeepSoTemplate::exec_template('search','search');?>
        </div>
    </div>
    </div><?php

    echo $args['after_widget'];
// EOF

}
