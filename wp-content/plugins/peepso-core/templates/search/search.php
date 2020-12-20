<?php

    $search = '';

    if( isset($context) && 'shortcode' == $context ) {
        $search = PeepSo3_Shortcode_Search::get_search_query();
    }
?>
<div class="ps-js-page-search">
	<input type="text" value="<?php echo $search; ?>" class="ps-input ps-full ps-js-query"
		placeholder="<?php echo __('Type to search...', 'peepso-core'); ?>"
		style="margin-bottom:15px" />
	<div class="ps-js-loading" style="display:none">
		<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>">
	</div>
    <div class="ps-js-result" style="display:none"></div>
</div>
