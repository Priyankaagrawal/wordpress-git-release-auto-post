<?php

include "common.php";

function git_link_create() {
  global $wpdb;
    $link = $_POST["link"];
	$tags = $_POST["tags"];
	$title = $_POST["title"];
	

	if (isset($_POST['insert'])) {
			$category = -1;
	foreach($_POST['category_list'] as $selected){
		if($category==-1)
		{
			$category=$selected;
		}else{
$category= $category.",".$selected;
		}
}

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
           
        $table_name = $wpdb->prefix . "git_link";
		 $rowcount  = $wpdb->get_var("SELECT COUNT(*) from $table_name where link='".$link."'");
		if($rowcount){
		$message.="Link already exist";	
		}
else{
        $wpdb->insert(
                $table_name, //table
                array('link' => $link, 'repository' => $repo, 'username' => $username, 'last_modified' =>current_time( 'mysql' ), 'added_date' =>current_time( 'mysql' ),
				'title' => $title, 'tags' => $tags, 'category' => $category), //data
                array('%s', '%s','%s','%s','%s','%s','%s','%s','%s') //data format			
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
				
				
              <tr>
                    <th class="ss-th-width">Post Title</th>
                    <td><input type="text" name="title" value="<?php echo $title; ?>" class="ss-field-width" required/></td>
                </tr>
				
                <tr>
                    <th class="ss-th-width">Tags</th>
                    <td><input type="text" name="tags" value="<?php echo $tags; ?>" class="ss-field-width" required/></td>
                </tr>
				<tr>
                    <th class="ss-th-width">Categories</th>
                    <td><div style="height:300px; overflow-y:auto;"><?php 
					
					$rows = $wpdb->get_results("select wt.term_taxonomy_id id,  w.name from wp_term_taxonomy  wt , wp_terms w where wt.term_id=w.term_id and wt.taxonomy='category'");
					
					foreach($rows as $row)
					{ 
					
					?>
						<input type="checkbox" name="category_list[]" value="<?php echo $row->id; ?>"><label><?php echo $row->name; ?></label><br/>
					<?php }
					
?></div></td>
                </tr>
				
            </table>
            <input type='submit' name="insert" value='Save' class='button'>
        </form>
    </div>
    <?php
}

