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
        $table_name = $wpdb->prefix . "git_link";

        		
	     $rows = $wpdb->get_results("SELECT id,link, repository, username, last_tag_version from $table_name");
       $i=0; ?>
	   <div class="wrap">
        <h2>Git APi</h2>
		<?php foreach ($rows as $row) { 
		 
		 $output= httpGet("https://api.github.com/repos/".$row->username."/".$row->repository."/releases/latest");
   	$json_a = json_decode($output, true);

    $url=$json_a['url'];
    $tag=$json_a['tag_name'];
    $body=$json_a['body'];
	  $name=$json_a['name'];
	  ?>
	  
	   
<?php $Parsedown = new Parsedown(); ?>

  
	
	<?php 
            if ($row->last_tag_version != $tag) { 	
            
            $post_id= postCreator($row->username."-".$row->repository."-".$tag, $row->username."-".$row->repository."- ".$name, $Parsedown->text($body));
			
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


function postCreator($slug, $title, $content) {

	// Initialize the page ID to -1. This indicates no action has been taken.
	$post_id = -1;

	// Setup the author, slug, and title for the post
	$author_id = 1;
	

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
			)
		);

	// Otherwise, we'll stop
	} else {

    		// Arbitrarily use -2 to indicate that the page with the title already exists
    		$post_id = -2;

	} // end if

} 


