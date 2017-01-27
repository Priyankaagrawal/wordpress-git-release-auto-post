<?php

function gits_link_list() {
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/git-link/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>List</h2>
        <div class="tablenav top">
            <div class="alignleft actions">
                <a href="<?php echo admin_url('admin.php?page=git_link_create'); ?>">Add New</a>
            </div>
            <br class="clear">
        </div>
        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . "git_link";

        $rows = $wpdb->get_results("SELECT id,link, repository, username, added_date,last_modified from $table_name");
        ?>
        <table class='wp-list-table widefat fixed striped posts'>
            <tr>
                <th class="manage-column ss-list-width">ID</th>
                <th class="manage-column ss-list-width">Link</th>
				<th class="manage-column ss-list-width">Repo</th>
				<th class="manage-column ss-list-width">Username</th>
				<th class="manage-column ss-list-width">Created</th>
				<th class="manage-column ss-list-width">Updated</th>
                <th>&nbsp;</th>
            </tr>
            <?php foreach ($rows as $row) { ?>
                <tr>
                    <td class="manage-column ss-list-width"><?php echo $row->id; ?></td>
                    <td class="manage-column ss-list-width"><?php echo $row->link; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->repository; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->username; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->added_date; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->last_modified; ?></td>
                    <td><a href="<?php echo admin_url('admin.php?page=git_link_update&id=' . $row->id); ?>">Update</a></td>
					 <!--<td><a href="<!?php echo admin_url('admin.php?page=get_links_api'); ?>">Api</a></td>-->
                </tr>
            <?php } ?>
        </table>
    </div>
    <?php
}