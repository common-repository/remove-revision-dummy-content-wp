<?php
/**
 Plugin Name: Remove Revision Dummy Content WP
 Plugin URI: http://wordpress.org/plugins/remove-revision-dummy-content-wp/
 Description: Remove revision history wp table  term_relationships,postmeta,term_taxonomy,posts
 Version: 1.1.0
 Author: Satish
 Author URI:
 Text Domain: remove-revision-dummy-content-wp
 License: GPL3
 
 You should have received a copy  of the GNU General Public License
 along with Remove revision history. If not, see http://wordpress.org/plugins/remove-revision-dummy-content-wp/
 */
// add admin notice success
function rrdcw_admin_notice__success() {
    if (!isset($_SESSION)) {
        @session_start();
    }
	
    if (isset($_SESSION['notices']) && !empty($_SESSION['notices']) && ($_SESSION['notices'] != '')) {
        if ($_SESSION['notices']['type'] == 'success') {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e($_SESSION['notices']['msg'], 'remove-revision-dummy-content-wp'); ?></p>
            </div>
            <?php
            $_SESSION['notices']['type'] = '';
        }
    }
}
add_action('admin_notices', 'rrdcw_admin_notice__success');
// add admin error notice
function rrdcw_my_error_notice() {
	
    if (!isset($_SESSION)) {
        @session_start();
    }
    if (isset($_SESSION['notices']) && !empty($_SESSION['notices']) && ($_SESSION['notices'] != '')) {
        if ($_SESSION['notices']['type'] == 'error') {
            ?>
            <div class="error notice">
                <p><?php _e($_SESSION['notices']['msg'], 'remove-revision-dummy-content-wp'); ?></p>
            </div>
            <?php
            $_SESSION['notices']['type'] = '';
        }
    }
}
add_action('admin_notices', 'rrdcw_my_error_notice');
add_action('admin_menu', 'rrdcw_register_submenu_page');
function rrdcw_register_submenu_page() {
add_options_page('RRH Size of Table', 'RRH Size of Table', 'manage_options', 'rrdcw-options', 'rrdcw_remove_revision_history_option');
add_options_page('RRH Revision list', 'RRH Revision list', 'manage_options', 'rrdcw-options-revision_list', 'rrdcw_revision_list');
add_options_page('RRH Dummy list', 'RRH Dummy list', 'manage_options', 'rrdcw-options-dummy_list', 'rrdcw_dummy_list');
}
// All option
function rrdcw_remove_revision_history_option() {
	?>
    <div id="rrhw" class="wrap"> 
        <div id="icon-settings" class="icon32"></div>
        <h2><?php _e('Size of Table', 'remove-revision-dummy-content-wp') ?></h2>
		
		<?php 
		
		global $wpdb;
			 $query = "SELECT table_name AS 'Table', ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size' FROM information_schema.TABLES WHERE table_schema = '".DB_NAME."' ORDER BY (data_length + index_length) DESC ";
			
				 $database_size = $wpdb->get_results($query, OBJECT);
				 $total_db_size = 0;
				 $tbl_posts = 0;
				 $tbl_postmeta = 0;
				 foreach($database_size as $key => $val)
				 {
					$total_db_size +=$val->Size; 
					if($wpdb->prefix.'posts' == $val->Table)
					{
						$tbl_posts = $val->Size;
					}
					if($wpdb->prefix.'postmeta' == $val->Table)
					{
						$tbl_postmeta = $val->Size;
					}
				 } ?>
	<h2><?php _e('Database Size : '.$total_db_size.' (MB)', 'remove-revision-dummy-content-wp') ?></h2>	
	
	<p><?php _e('Posts Size : '.$tbl_posts.' (MB)', 'remove-revision-dummy-content-wp') ?></p>	
	<p><?php _e('Postmeta Size : '.$tbl_postmeta.' (MB)', 'remove-revision-dummy-content-wp') ?></p>	
	<?php 
}

function rrdcw_revision_list() {
	?>
    <div id="rrhw" class="wrap"> 
        <div id="icon-settings" class="icon32"></div>
        <h2><?php _e('RRH Revision list', 'remove-revision-dummy-content-wp') ?></h2>
	
		
	<?php    
	global $wpdb;
	$query = "SELECT *
						FROM ".$wpdb->prefix ."posts a
						LEFT JOIN ".$wpdb->prefix ."term_relationships b ON ( a.ID = b.object_id)
						LEFT JOIN ".$wpdb->prefix ."postmeta c ON ( a.ID = c.post_id )
						LEFT JOIN ".$wpdb->prefix ."term_taxonomy d ON ( b.term_taxonomy_id = d.term_taxonomy_id)
						WHERE a.post_type = 'revision'";
			
				 $pageposts = $wpdb->get_results($query, OBJECT);
				 ?>
				 <h2><?php  _e('Select Revision Total Row : '.count($pageposts), 'remove-revision-dummy-content-wp');?> </h2>
				 <?php 
				 if(!empty($pageposts) && ($pageposts != ''))
				 {
					 ?>
					 
					 <div class="rrdcw_snv">
							<table class="widefat">
								<tr>
									<th>	<?php  _e('No.', 'remove-revision-dummy-content-wp');?></th>
									<th>	<?php  _e('Post ID', 'remove-revision-dummy-content-wp');?></th>
									<th>	<?php  _e('Post Title', 'remove-revision-dummy-content-wp');?></th>
									<td>	<?php  _e('Meta ID', 'remove-revision-dummy-content-wp');?></td>
									<td>	<?php  _e('Meta Key', 'remove-revision-dummy-content-wp');?></td>
									
								</tr>
								 <?php 
								 foreach($pageposts as $key => $val)
								 {?>
								  
											<tr class="querycode" >
												<td>	<?php  _e($key, 'remove-revision-dummy-content-wp');?></td>
												<td>	<?php  _e($val->ID, 'remove-revision-dummy-content-wp');?></td>
												<td>	<?php  _e($val->post_title, 'remove-revision-dummy-content-wp');?></td>
												<td>	<?php  _e($val->meta_id, 'remove-revision-dummy-content-wp');?></td>
												<td>	<?php  _e($val->meta_key, 'remove-revision-dummy-content-wp');?></td>											
											</tr>
											
										
								 <?php 
								 }
								 ?>
								 </table>
										
							</div>
								 <?php 
				 }
				 ?>
        <form id="form_data" name="form" method="post">   
            <p class="submit">
                <button onclick="fn_option_save();" class="button button-primary button-large" type="button" name="Save-Settings"><?php 
                            _e('Remove revision', 'remove-revision-dummy-content-wp') ?></button>
            </p>
			<?php wp_nonce_field('rrhw_form_submit','rrhw_form_nonce'); ?>
			<input type="hidden" name="form_submit" value="true" />
			<input type="hidden" name="action" value="rrdcw_remove_revision_query_run"/>
        </form>
		
        <script type="text/javascript">
            function fn_option_save() {
				if (confirm("Are you soure want dummy data remove and had you backup database ?") == true) {
					var form_data = jQuery('#form_data').serialize();
					jQuery.ajax({
						type: "POST",
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						data: form_data,
						success: function (data) {
						  window.location.reload(true);
						}
					});
				}
                return false;
            }
		</script>
    </div>        
    <?php
}

function rrdcw_dummy_list() {
	?>
    <div id="rrhw" class="wrap"> 
        <div id="icon-settings" class="icon32"></div>
        <h2><?php _e('RRH Dummy list', 'remove-revision-dummy-content-wp') ?></h2>
		<?php 
		global $wpdb;
			 $query_rb = "SELECT ".$wpdb->prefix ."postmeta.* FROM ".$wpdb->prefix ."postmeta WHERE ".$wpdb->prefix ."postmeta.meta_id NOT IN (SELECT `meta_id` FROM ".$wpdb->prefix ."postmeta INNER JOIN ".$wpdb->prefix ."posts ON ".$wpdb->prefix ."postmeta.post_id = ".$wpdb->prefix ."posts.ID ) ORDER BY `".$wpdb->prefix ."postmeta`.`meta_id` ASC ";
			
				 $pageposts_rb = $wpdb->get_results($query_rb, OBJECT);
				 ?>
				 <h2><?php  _e('Select Recycle Bin Total Row : '.count($pageposts_rb), 'remove-revision-dummy-content-wp');?> </h2>
				 <?php 
				 if(!empty($pageposts_rb) && ($pageposts_rb != ''))
				 {
					 ?>
					 
					 <div class="rrdcw_snv">
							<table class="widefat">
								<tr>
									<td>	<?php  _e('No.', 'remove-revision-dummy-content-wp');?></td>
									<td>	<?php  _e('Meta ID', 'remove-revision-dummy-content-wp');?></td>
									<th>	<?php  _e('Post ID', 'remove-revision-dummy-content-wp');?></th>
									<td>	<?php  _e('Meta Key', 'remove-revision-dummy-content-wp');?></td>
									<th>	<?php  _e('Meta value', 'remove-revision-dummy-content-wp');?></th>
								</tr>
								 <?php 
								 foreach($pageposts_rb as $key => $val)
								 {?>
								  
											<tr class="querycode" >
												<td>	<?php  _e($key, 'remove-revision-dummy-content-wp');?></td>
												<td>	<?php  _e($val->meta_id, 'remove-revision-dummy-content-wp');?></td>
												<td>	<?php  _e($val->post_id, 'remove-revision-dummy-content-wp');?></td>
												<td>	<?php  _e($val->meta_key, 'remove-revision-dummy-content-wp');?></td>
												<td>	<?php  _e($val->meta_value, 'remove-revision-dummy-content-wp');?></td>											
											</tr>
											
										
								 <?php 
								 }
								 ?>
								 </table>
										
							</div>
								 <?php 
				 }
				 ?>
        <form id="form_data_rb" name="form_rb" method="post">   
            <p class="submit">
                <button onclick="fn_option_save_rb();" class="button button-primary button-large" type="button" name="Save-Settings_rb"><?php 
                            _e('Remove Recycle Bin', 'remove-revision-dummy-content-wp') ?></button>
            </p>
			<?php wp_nonce_field('rrhw_form_submit','rrhw_form_nonce'); ?>
			<input type="hidden" name="form_submit_rb" value="true" />
			<input type="hidden" name="action" value="rrdcw_remove_revision_query_run_rb"/>
        </form>
        <br />
        <script type="text/javascript">
            function fn_option_save_rb() {
				if (confirm("Are you soure want dummy data remove and had you backup database ?") == true) {
				  var form_data = jQuery('#form_data_rb').serialize();
					jQuery.ajax({
						type: "POST",
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						data: form_data,
						success: function (data) {
						  window.location.reload(true);
						}
					});
				}                 
                return false;
            }
        </script>
    </div>        
    <?php
}
/*  Message store in session and remove revision*/
add_action('wp_ajax_rrdcw_remove_revision_query_run', 'rrdcw_remove_revision_query_run');
function rrdcw_remove_revision_query_run() {
    session_start();
    if (isset($_POST['form_submit']) &&  wp_verify_nonce($_POST['rrhw_form_nonce'],'rrhw_form_submit')) {
	
	global $wpdb;
	 $query = "DELETE a,b,c
				FROM ".$wpdb->prefix ."posts a
				LEFT JOIN ".$wpdb->prefix ."term_relationships b ON ( a.ID = b.object_id)
				LEFT JOIN ".$wpdb->prefix ."postmeta c ON ( a.ID = c.post_id )
				LEFT JOIN ".$wpdb->prefix ."term_taxonomy d ON ( b.term_taxonomy_id = d.term_taxonomy_id)
				WHERE a.post_type = 'revision'";
	
		 $results = $wpdb->query($query);
		if($results == 0)
		{
    	$_SESSION['notices'] = array('type' => 'error', 'msg' => $results .' Data not found.');
		}else{
    	$_SESSION['notices'] = array('type' => 'success', 'msg' => $results .' row  Remove revision successfully.');
		}
		
		exit;
	}
}
/*  Message store in session remove Recycle Bin */
add_action('wp_ajax_rrdcw_remove_revision_query_run_rb', 'rrdcw_remove_revision_query_run_rb');
function rrdcw_remove_revision_query_run_rb() {
    session_start();
    if (isset($_POST['form_submit_rb']) &&  wp_verify_nonce($_POST['rrhw_form_nonce'],'rrhw_form_submit')) {
	
	global $wpdb;
	 $query_rb = "DELETE FROM ".$wpdb->prefix ."postmeta WHERE ".$wpdb->prefix ."postmeta.meta_id NOT IN (SELECT `meta_id` FROM ".$wpdb->prefix ."postmeta INNER JOIN ".$wpdb->prefix ."posts ON ".$wpdb->prefix ."postmeta.post_id = ".$wpdb->prefix ."posts.ID ) ORDER BY `".$wpdb->prefix ."postmeta`.`meta_id` ASC ";
	
	$results = $wpdb->query($query_rb);
	 if($results == 0)
		{
    	$_SESSION['notices'] = array('type' => 'error', 'msg' => $results .' Data not found.');
		}else{
    	$_SESSION['notices'] = array('type' => 'success', 'msg' => $results .' row  Remove recycle bin successfully.');
		}
		
		exit;
	}
}
?>