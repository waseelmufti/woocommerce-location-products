 <!-- Trigger/Open The Modal -->
<!-- <button id="myBtn">Open Modal</button> -->

<!-- The Modal -->
<div id="myModal" class="modal">
 <!-- Modal content -->
<div class="modal-content">
  <div class="modal-header">
    <!-- <span class="close">&times;</span> -->
    <?php $upload_path = wp_upload_dir(); ?>
    <h2><img src="<?php  echo $upload_path['baseurl'].'/2018/11/logo-1.png';?>" alt=""></h2>
  </div>
  <div class="modal-body">
    <form class="wpcf7-form cf7_custom_style_1">
    	<span class="wpcf7-form-control-wrap">
    	<select class="wpcf7-form-control wpcf7-select wlp-select-control" name="wp_location" id="wc_location_product_007">
        <option value="">Select Your Location</option>
    		<option value="all">All Locations</option>
    		<?php 
    		foreach ($locations_posts as $loc_post) { ?>
    			<option value="<?php echo $loc_post->ID; ?>"><?php echo __($loc_post->post_title); ?></option>
			<?php }
    		 ?>
    	</select>
    	</span>
    </form>
  </div>
  <div class="modal-footer">
    <p>Copyright &copy; <?php echo date('Y') ?> Organic Jiyo. All Rights Reserved.</p>
  </div>
</div> 
</div>