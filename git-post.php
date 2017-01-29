<?php

/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 1/26/2017
 * Time: 11:53 AM
 */
//$client = new HttpClient('https://api.github.com');
//$client->setDebug(true);
//if (!$client->get('/repos/ethcore/parity/releases/latest')) {
//    echo '<p>Request failed!</p>';
//} else {
//    echo '<p>Amazon home page is '.strlen($client->getContent()).' bytes.</p>';
//}
include "Parsedown.php";

function get_links_api()
{

        global $wpdb;
		
		$table_name = $wpdb->prefix . "git_config";
		 $wpdb->update(
                $table_name, //table
                array('last_updated' =>current_time( 'mysql' )), //data
				
                array('ID' => 1), //where
                array('%s'), //data format
                array('%s') //where format
				
        );
		
        $table_name = $wpdb->prefix . "git_link";

        		
	     $rows = $wpdb->get_results("SELECT id,link, repository, username, last_tag_version, title, tags, category from $table_name");
       $i=0; ?>
	   <div class="wrap">
        <h2>Git APi</h2>
		<?php foreach ($rows as $row) { 
		 
		 $output= httpGet("https://api.github.com/repos/".$row->username."/".$row->repository."/releases/latest");
   	$json_a = json_decode($output, true);
    $message_url=$json_a['message'];
    $url=$json_a['url'];
    $tag=$json_a['tag_name'];
    $body=$json_a['body'];
	  $name=$json_a['name'];
	  
	  if($tag=="" || $body=="")
	  {
		  continue;
	  }
	  $download="##Downloads\n";
	  
	  foreach ($json_a['assets'] as $item) {
		  
   $download=$download."[".$item['name']."](".$item['browser_download_url'] .")\n";
}
if($json_a['tarball_url']!="")
{
$download=$download."[Source code (tar.gz)](".$json_a['tarball_url'].")\n";
}
if($json_a['zipball_url']!="")
{
$download=$download."[Source code (zip)](".$json_a['zipball_url'].")\n";
}
	  ?>
	  
	   
<?php $Parsedown = new Parsedown(); ?>

  
	
	<?php 
            if ($row->last_tag_version != $tag) { 	
            
            $post_id= postCreator($row->username."-".$row->repository."- ".$name,$row->title."-".$tag,  $Parsedown->text($body."".$download), $row->category, $row->tags);
			
			 $wpdb->update(
                $table_name, //table
                array('last_tag_version' => $tag, 'body' => $body, 'last_modified' =>current_time( 'mysql' )), //data
				
                array('ID' => $row->id), //where
                array('%s', '%s', '%s'), //data format
                array('%s') //where format
				
        );
		echo $url." - ".$tag."<br/>";
		$i++;
           } else{
			   $wpdb->update(
                $table_name, //table
                array( 'last_modified' =>current_time( 'mysql' )), //data
				
                array('ID' => $row->id), //where
                array('%s'), //data format
                array('%s') //where format
			   );
		   }
		}	?>
		  </div>
		
			<?php if($i==0)
			{
				echo "No Updation";
			}
}



function httpGet($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

    $output = curl_exec($ch);
    if ($output === false) {
        echo "Error Number:" . curl_errno($ch) . "<br>";
        echo "Error String:" . curl_error($ch);
    }
    curl_close($ch);

    

 return $output;
//    return $url." ".$tag." ".$body;
}


function postCreator($slug, $title, $content, $category, $tags) {

	// Initialize the page ID to -1. This indicates no action has been taken.
	$post_id = -1;

	// Setup the author, slug, and title for the post
	$author_id = 1;
	
$categoryArray=split ("\,", $category);
$tagsArray=split ("\,", $tags);

	// If the page doesn't already exist, then create it
	if( null == get_page_by_title( $title ) ) {

		// Set the post ID so that we know the post was created successfully
		$post_id = wp_insert_post(
			array(
				'comment_status'	=>	'closed',
				'ping_status'		=>	'closed',
				'post_author'		=>	$author_id,
				'post_name'		=>	$slug,
				'post_title'		=>	$title,
				'post_status'		=>	'publish',
				'post_type'		=>	'post',
				'post_content'  => $content,
				'post_category' =>  $categoryArray,
				'tags_input'    => $tagsArray
			)
		);

	// Otherwise, we'll stop
	} else {

    		// Arbitrarily use -2 to indicate that the page with the title already exists
    		$post_id = -2;

	} // end if

} 


