<?php



function git_link_update() {
    global $wpdb;
    $table_name = $wpdb->prefix . "git_link";
    $id = $_GET["id"];
    $link = $_POST["link"];	
	
   $pathArray = split ("\/", $link); 
  
	
//update
    if (isset($_POST['update'])) {
		if(!(startsWith($link, "https://github.com") && (endsWith($link, "releases") || endsWith($link, "releases/"))))
	{
		$message.="Invalid Url";
	}else{
		 if(count($pathArray)<6)
{
	 $message.="Invalid Url";
}else{
	$username = $pathArray[3];
	$repo = $pathArray[4];
		 $rowcount  = $wpdb->get_var("SELECT COUNT(*) from $table_name where link='".$link."' and id!=".$id);
		if($rowcount){
		$message.="Link already exist";	
		}
else{
        $wpdb->update(
                $table_name, //table
                array('link' => $link, 'repository' => $repo, 'username' => $username), //data
				
                array('ID' => $id), //where
                array('%s', '%s', '%s'), //data format
                array('%s') //where format
				
        );
		 $message.="List Updated";
}
}
	}

    }
//delete
    else if (isset($_POST['delete'])) {
        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id = %s", $id));
    } else {//selecting value to update	
        $links = $wpdb->get_results($wpdb->prepare("SELECT id,link, repository, username from $table_name where id=%s", $id));
        foreach ($links as $s) {
            $link = $s->link;
			$repo = $s->repository;
			$username = $s->username;
        }
    }
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/git-link/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Git Links</h2>

        <?php if ($_POST['delete']) { ?>
            <div class="updated"><p>Link deleted</p></div>
            <a href="<?php echo admin_url('admin.php?page=gits_link_list') ?>">&laquo; Back to list</a>

        <?php } else if ($_POST['update']) { ?>
            <div class="updated"><p><?php echo $message ?></p></div>
			            <a href="<?php echo admin_url('admin.php?page=gits_link_list') ?>">&laquo; Back to list</a>

        <?php } else { ?>
            <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <table class='wp-list-table widefat fixed'>
                    <tr><th>Link</th><td><input type="text" name="link" value="<?php echo $link; ?>" required/></td></tr>
					<!--<tr><th>Repo</th><td><input type="text" name="repo" value="<!?php echo $repo; ?>" required/></td></tr>
					<tr><th>Username</th><td><input type="text" name="username" value="<!?php echo $username; ?>" required/></td></tr>-->
                </table>
                <input type='submit' name="update" value='Save' class='button'> &nbsp;&nbsp;
                <input type='submit' name="delete" value='Delete' class='button' onclick="return confirm('Are you sure you want to delete?')">
            </form>
        <?php } ?>

    </div>
    <?php
}