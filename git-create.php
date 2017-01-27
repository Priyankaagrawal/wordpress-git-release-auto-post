<?php

include "common.php";

function git_link_create() {
  
    $link = $_POST["link"];
	
	if (isset($_POST['insert'])) {
	if(!(startsWith($link, "https://github.com") && (endsWith($link, "releases") || endsWith($link, "releases/"))))
	{
		$message.="Invalid Url";
	}else{
	 
   $pathArray = split ("\/", $link); 
   
if(count($pathArray)<6)
{
	 $message.="Invalid Url";
}else{
	$username = $pathArray[3];
	$repo = $pathArray[4];

	
    //insert
    
        global $wpdb;
        $table_name = $wpdb->prefix . "git_link";
		 $rowcount  = $wpdb->get_var("SELECT COUNT(*) from $table_name where link='".$link."'");
		if($rowcount){
		$message.="Link already exist";	
		}
else{
        $wpdb->insert(
                $table_name, //table
                array('link' => $link, 'repository' => $repo, 'username' => $username, 'last_modified' =>current_time( 'mysql' ), 'added_date' =>current_time( 'mysql' )), //data
                array('%s', '%s','%s','%s','%s','%s') //data format			
        );
        $message.="Link inserted";
}
    }
}
	}
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/git-link/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Add New Link</h2>
        <?php if (isset($message)): ?><div class="updated"><p><?php echo $message; ?></p></div><?php endif; ?>
        <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
            
            <table class='wp-list-table widefat fixed'>
                
                <tr>
                    <th class="ss-th-width">Link</th>
                    <td><input type="text" name="link" value="<?php echo $link; ?>" class="ss-field-width" required/></td>
                </tr>
				
            <!--    <tr>
                    <th class="ss-th-width">Repo</th>
                    <td><input type="text" name="repo" value="<?php echo $repo; ?>" class="ss-field-width" required/></td>
                </tr>
				
                <tr>
                    <th class="ss-th-width">Username</th>
                    <td><input type="text" name="username" value="<?php echo $username; ?>" class="ss-field-width" required/></td>
                </tr>-->
            </table>
            <input type='submit' name="insert" value='Save' class='button'>
        </form>
    </div>
    <?php
}

