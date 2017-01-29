<?php



function git_link_update() {
    global $wpdb;
    $table_name = $wpdb->prefix . "git_link";
    $id = $_GET["id"];
    $link = $_POST["link"];	
	$tags = $_POST["tags"];
	$title = $_POST["title"];
	
   $pathArray = split ("\/", $link); 
  
	
//update
    if (isset($_POST['update'])) {
		
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
                array('link' => $link, 'repository' => $repo, 'username' => $username, 'title' => $title, 'tags' => $tags, 'category' => $category), //data
				
                array('ID' => $id), //where
                array('%s', '%s', '%s','%s', '%s', '%s'), //data format
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
        $links = $wpdb->get_results($wpdb->prepare("SELECT id,link, repository, username, category, title, tags from $table_name where id=%s", $id));
        foreach ($links as $s) {
            $link = $s->link;
			$repo = $s->repository;
			$username = $s->username;
			$title = $s->title;
			$tags = $s->tags;
			$category = $s->category;
			$categoryArray = split ("\,", $category); 
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
                    <tr><th class="ss-th-width">Link</th><td><input type="text" name="link" value="<?php echo $link; ?>" class="ss-field-width" required/></td></tr>
					
					
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
                    <td>
					<div style="height:300px; overflow-y:auto;"><?php 
					
					$rows = $wpdb->get_results("select wt.term_taxonomy_id id,  w.name from wp_term_taxonomy  wt , wp_terms w where wt.term_id=w.term_id and wt.taxonomy='category'");
					
					foreach($rows as $row)
					{ 
					
					if (in_array($row->id, $categoryArray)) {
						?>
						<input type="checkbox" name="category_list[]" value="<?php echo $row->id; ?>" checked><label><?php echo $row->name; ?></label><br/>
					<?php 
					}else{
					?>
						<input type="checkbox" name="category_list[]" value="<?php echo $row->id; ?>"><label><?php echo $row->name; ?></label><br/>
					<?php }
					}
					
?></div></td>
                </tr>
				
                </table>
                <input type='submit' name="update" value='Save' class='button'> &nbsp;&nbsp;
                <input type='submit' name="delete" value='Delete' class='button' onclick="return confirm('Are you sure you want to delete?')">
            </form>
        <?php } ?>

    </div>
    <?php
}