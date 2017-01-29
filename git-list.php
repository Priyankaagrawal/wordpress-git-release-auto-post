<?php

function gits_link_list() {
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/git-link/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>List</h2>
		<?php	
		global $wpdb;
        $table_name1 = $wpdb->prefix . "git_config";
		
		$links = $wpdb->get_results($wpdb->prepare("SELECT last_updated from $table_name1 where id=%s", 1));
        foreach ($links as $s) {
            $last_update = $s->last_updated;
			
        } ?>
        <div class="tablenav top">
            <div class="alignleft actions">
				
                <a href="<?php echo admin_url('admin.php?page=git_link_create'); ?>" class="button button-primary">Add New Link</a>
				
				
            </div>
			<div class="alignleft actions">
				
                <span class="button button-primary">Last Updated: <?php echo $last_update ?></span>
				
            </div>
            <br class="clear">
			<br/>
        </div>
        <?php
        
        $table_name = $wpdb->prefix . "git_link";

        $rows = $wpdb->get_results("SELECT id,link, repository, username,title, tags, added_date,last_modified from $table_name");
        ?>
        <table class='wp-list-table widefat fixed striped posts'>
            <tr>
                <th class="manage-column">ID</th>				
				<th class="manage-column">Title</th>
                <th class="manage-column" style="width:35%">Link</th>
				<th class="manage-column">Repo</th>
				<th class="manage-column">Username</th>
				<th class="manage-column">Tags</th>
				<th class="manage-column">Created</th>
				<th class="manage-column">Updated</th>
                <th>&nbsp;</th>
            </tr>
            <?php foreach ($rows as $row) { ?>
                <tr>
                    <td class="manage-column "><?php echo $row->id; ?></td>
					<td class="manage-column "><?php echo $row->title; ?></td>
                    <td class="manage-column "><a href="<?php echo $row->link; ?>" target="_blank"><?php echo $row->link; ?></a></td>
					<td class="manage-column "><?php echo $row->repository; ?></td>
					<td class="manage-column "><?php echo $row->username; ?></td>
					<td class="manage-column "><?php echo $row->tags; ?></td>
					<td class="manage-column "><?php echo $row->added_date; ?></td>
					<td class="manage-column "><?php echo $row->last_modified; ?></td>
                    <td><a href="<?php echo admin_url('admin.php?page=git_link_update&id=' . $row->id); ?>">Edit</a></td>
										 <!--<td><a href="<!?php echo admin_url('admin.php?page=get_links_api'); ?>">Api</a></td>-->
                </tr>
            <?php } ?>
        </table>
    </div>
    <?php
}