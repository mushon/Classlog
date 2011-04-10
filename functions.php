<?php

//
//  Trying to simulate grandchild theme

// Enter the name of the theme you are trying to extend:

// ---------------------------------------------------------------- Log-in
function classlog_login() {
  ?>  
      <div id="log">
      	<div>
      		<?php
      			$user = wp_get_current_user();
      			//user is logged in, $user->ID will be their ID, etc..			
      			if ($user_ID) {
      				$username = $user->display_name . " | ";
      			} else {
      				$username = "";
      			}
      		?>
      		<span class="log-name"><?php echo $username; wp_register('', ''); ?></span>
      		<span class="log-in-out"><?php wp_loginout(); ?></span>
      	</div>
      </div>     
  <?php
  }

add_filter('thematic_before','classlog_login');

// ---------------------------------------------------------------- Header

// Remove the "access" from the header
function remove_thematic_branding() {
	remove_action('thematic_header','thematic_access',9);
}
add_action('init','remove_thematic_branding');

// Add search & the custom menu under the header
function childtheme_header_extension() { ?>

  <div id="search">
		<h3><label for="s"><?php _e( 'Search', 'sandbox' ) ?></label></h3>
		<form id="searchform" class="blog-search" method="get" action="<?php bloginfo('home') ?>">
			<div>
				<input id="s" name="s" type="text" class="text" value="<?php the_search_query() ?>" size="10" tabindex="1" />
				<input type="submit" class="button" value="" tabindex="2" />
			</div>
		</form>
	</div>
  
  <?php
  if ( function_exists( 'add_theme_support' ) ) {
  
  	// This theme uses wp_nav_menu()
  	add_theme_support( 'nav-menus' );
  	?>
  	
    <div id="access">
		  <?php wp_nav_menu( 'sort_column=menu_order&container_class=menu&menu_class=sf-menu' ); ?>
		</div>
		<!-- #access -->	
  
	<?php
	}
}	
add_action('thematic_belowheader','childtheme_header_extension');

// ---------------------------------------------------------------- Post Structure

// Make the default index loop show excerpts
function classlog_content() {
  if(is_home()){
    //return 'excerpt';
	} elseif (is_author()) {
		return 'full';
	}
}
add_filter('thematic_content','classlog_content');


// Removing Thematic's Postmeta
function classlog_postheader_postmeta(){
  //nothing
}
add_filter('thematic_postheader_postmeta', 'classlog_postheader_postmeta');

// Functions to change order of Post Meta

function classlog_postheader($old){
  $new  = '<div class="post-head">';
  $new .= $old;
  
  //Get a prompt from the Conversation starter plugin:
  global $post;
	setup_postdata($post);
  $prompt = get_post_meta($post->ID, prompt, true);
  //$prompt="test";
  //embed the quote as a "?" link
  if($prompt){
    $new .= '<a href="' . get_comments_link() . '" class="please-respond" title="' . $prompt . '">?</a>';
  }
  
  $new .= '</div>';
  
  return $new;
}

add_filter('thematic_postheader','classlog_postheader');

// Set the post footer:
function classlog_postfooter() {?>
	<div class="entry-utility meta">
		<a class="author-img" href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>" title="View all posts by <?php echo $authordata->display_name ?>"><?php echo get_avatar( get_the_author_email(), '41' ); ?></a>
		<span class="author vcard"><?php printf( __( 'By %s', 'sandbox' ), '<a class="url fn n" href="' . get_author_posts_url(get_the_author_meta('ID')) . '" title="' . sprintf( __( 'View all posts by %s', 'sandbox' ), $authordata->display_name ) . '">' . get_the_author() . '</a>' ) ?></span>
		<span class="date"><?php the_time('H:i, M jS, y'); ?></span>
		<span class="cat-links"><?php printf( __( '%s', 'sandbox' ), get_the_category_list(' ') ) ?></span>
		<?php the_tags( __( '<span class="tag-links">', 'sandbox' ), " ", "</span>" ) ?>
		<span class="comments-link"><?php comments_popup_link( __( 'No Comments', 'sandbox' ), __( '1 Comment', 'sandbox' ), __( '% Comments', 'sandbox' ) ) ?></span>
		<?php edit_post_link( __( 'Edit', 'sandbox' ), "<span class='edit-link'>", "</span>" ) ?>
	</div>
	<?php
}
add_filter ('thematic_postfooter', 'classlog_postfooter');


// ---------------------------------------------------------------- Comments


function my_comments($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    $GLOBALS['comment_depth'] = $depth;
    ?>
    	<li id="comment-<?php comment_ID() ?>" class="<?php thematic_comment_class() ?>">
        <div class="comment-meta">
          <div class="comment-author vcard">
          	<?php thematic_commenter_link() ?>
          	<span class="date"><?php comment_time('H:i, '); comment_date('M jS, y') ?></span>
          	<?php edit_comment_link('Edit', '<span class="edit-comment meta">', '</span>'); ?> 
          </div>
        </div>
        
        <?php if ($comment->comment_approved == '0') _e("\t\t\t\t\t<span class='unapproved'>Your comment is awaiting moderation.</span>\n", 'sandbox') ?>
        <div class="comment-body"><?php comment_text() ?></div>

						
			<?php // echo the comment reply link with help from Justin Tadlock http://justintadlock.com/ and Will Norris http://willnorris.com/
				if($args['type'] == 'all' || get_comment_type() == 'comment') :
					comment_reply_link(array_merge($args, array(
						'reply_text' => __('Reply','thematic'),
						'login_text' => __('Log in to reply.','thematic'),
						'depth' => $depth,
						'before' => '<div class="comment-reply-link">',
						'after' => '</div>'
					)));
				endif;
			?>
<?php }

function my_callback() {
	$content = 'type=comment&callback=my_comments';
	return $content;
}
add_filter('list_comments_arg', 'my_callback');

// ---------------------------------------------------------------- Third Aside

// Register my widget Areas
function third_widgets_init() {
	if ( !function_exists('register_sidebars') )
		return;
	  // Register Widgetized areas.
			   // Area 1
            register_sidebar(array(
       	'name' => 'Thirdly Aside',
       	'id' => 'thirdly-aside',
       	'before_widget' => '<li id="%1$s" class="widgetcontainer %2$s">',
       	'after_widget' => '</li>',
		    'before_title' => "<h3 class=\"widgettitle\">",
		    'after_title' => "</h3>\n",
    ));
  }
add_action( 'init', 'third_widgets_init' );
  
// adds widget areas to the thematic_belowmainasides hook
function insert_third_aside_widget() {
      // Area 1
  if ( function_exists('dynamic_sidebar') && is_sidebar_active('thirdly-aside') ) {
      echo '<div id="thirdly" class="aside main-aside">'. "\n" . '<ul class="xoxo">' . "\n";
      dynamic_sidebar('thirdly-aside');
      echo "\n" . '</ul>' . "\n" . '</div><!-- #thirdly-aside .aside -->'. "\n";
  }
}
add_filter('thematic_belowmainasides', 'insert_third_aside_widget');

// ---------------------------------------------------------------- Preset Widgets

/*

update_option( 'widget_recent_entries', array( 2 => array( 'title' => '' ), '_multiwidget' => 1 ) );

update_option( 'sidebars_widgets', array(
	'sidebar-1' => array(
		'widget_recent_entries-2',
	),
	'wp_inactive_widgets' => array(),
	'array_version' => 3
));
*/

// ---------------------------------------------------------------- Adding theme options

// ----------------------------------------------------------------- OEmbed

// Add Slideshare oEmbed
function add_oembed_slideshare(){
    wp_oembed_add_provider( 'http://www.slideshare.net/*', 'http://api.embed.ly/v1/api/oembed'); //http://www.slideshare.net/api/oembed/1
}
add_action('init','add_oembed_slideshare');

// ----------------------------------------------------------------- Plugins

function script_folding(){
	//For codebox expansion based on:
	//http://bavotasan.com/tutorials/using-jquery-to-make-an-expandable-code-box-for-wp-syntax/:
	 wp_enqueue_script("jquery"); ?>
	<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery(".wp_syntax").hover(function() {
		var width = jQuery("table", this).width();
		var pad = width + 5;
		if (width > 475) {
			jQuery(this)
				.stop(true, false)
				.css({
					zIndex: "100",
					position: "relative"
				})
				.animate({
					width: pad + "px"
				});
			}
		}, function() {
				jQuery(this).stop(true, false).animate({
					width: 475
			});
		});
	});
	</script>
<?php }

add_filter('thematic_belowmainasides', 'script_folding');

// -------------------------------------------------------- Gravatared Posts

/*
Plugin Name: Gravatared Posts Widget
Plugin URI: http://www.mushon.com
Description: Latest posts with Gravatars and comment count
Author: Mushon Zer-Aviv
Version: 0.1
Author URI: http://www.mushon.com

	My Widget is released under the GNU General Public License (GPL)
	http://www.gnu.org/licenses/gpl.txt

	This is a WordPress plugin (http://wordpress.org) and widget
	(http://automattic.com/code/widgets/).
*/

// We're putting the plugin's functions in one big function we then
// call at 'init' (add_action() at bottom) to ensure the
// required Sidebar Widget functions are available.
function widget_gravataredPosts_init() {

	// Check to see required Widget API functions are defined...
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
		return; // ...and if not, exit gracefully from the script.

	// This function prints the sidebar widget--the cool stuff!
	function widget_gravataredPosts($args) {

		// $args is an array of strings which help your widget
		// conform to the active theme: before_widget, before_title,
		// after_widget, and after_title are the array keys.
		extract($args);

		// Collect our widget's options, or define their defaults.
		$options = get_option('widget_gravataredPosts');
		$title = empty($options['title']) ? 'Latest Posts' : $options['title'];
		$count = empty($options['count']) ? '5' : $options['count'];
		
		global $post;
		$myposts = get_posts('numberposts='.$count);
		foreach($myposts as $post) :
			setup_postdata($post);
			$list .= "<li><a href='".get_permalink($post)."' title='a post by ".get_the_author()."'>".get_avatar( get_the_author_email(), '20' )."<span class='side-title'>".get_the_title()." <span class='side-meta'>(".get_comments_number($post->ID).")</span></span></a></li>";
		endforeach;
				
		$before_widget = "<li id='latest-posts'><a class='feed-icon' href='".get_bloginfo('rdf_url')."' title='blog rss feed'></a>";
		$before_title = "<h3>";
		$after_title = "</h3><ul>";
		$after_widget = "</ul></li>";
		
 		// It's important to use the $before_widget, $before_title,
 		// $after_title and $after_widget variables in your output.
		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo $list;
		echo $after_widget;
	}

	// This is the function that outputs the form to let users edit
	// the widget's title and so on. It's an optional feature, but
	// we'll use it because we can!
	function widget_gravataredPosts_control() {

		// Collect our widget's options.
		$options = get_option('widget_gravataredPosts');

		// This is for handing the control form submission.
		if ( $_POST['gravataredPosts-submit'] ) {
			// Clean up control form submission options
			$newoptions['title'] = strip_tags(stripslashes($_POST['gravataredPosts-title']));
			$newoptions['count'] = strip_tags(stripslashes($_POST['gravataredPosts-count']));
		}

		// If original widget options do not match control form
		// submission options, update them.
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_gravataredPosts', $options);
		}

		// Format options as valid HTML. Hey, why not.
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$count = htmlspecialchars($options['count'], ENT_QUOTES);

// The HTML below is the control form for editing options.
?>
		<div>
		<label for="gravataredPosts-title" style="line-height:35px;display:block;">Widget title: <input type="text" id="gravataredPosts-title" name="gravataredPosts-title" value="<?php echo $title; ?>" /></label>
		<label for="gravataredPosts-count" style="line-height:35px;display:block;">Number of posts: <input style="width: 25px;" type="text" id="gravataredPosts-count" name="gravataredPosts-count" value="<?php echo $count; ?>" /></label>
		<input type="hidden" name="gravataredPosts-submit" id="gravataredPosts-submit" value="1" />
		</div>
	<?php
	// end of widget_gravataredPosts_control()
	}

	// This registers the widget. About time.
	register_sidebar_widget('Gravatared Latest Posts', 'widget_gravataredPosts');

	// This registers the (optional!) widget control form.
	register_widget_control('Gravatared Latest Posts', 'widget_gravataredPosts_control');
}

// Delays plugin execution until Dynamic Sidebar has loaded first.
add_action('init', 'widget_gravataredPosts_init');


// -------------------------------------------------------- Gravatared Comments
/*
Plugin Name: Gravatared Comments Widget
Plugin URI: http://www.mushon.com
Description: Latest Comments with Gravatars and post title
Author: Mushon Zer-Aviv
Version: 0.1
Author URI: http://www.mushon.com

	My Widget is released under the GNU General Public License (GPL)
	http://www.gnu.org/licenses/gpl.txt

	This is a WordPress plugin (http://wordpress.org) and widget
	(http://automattic.com/code/widgets/).
*/

// We're putting the plugin's functions in one big function we then
// call at 'init' (add_action() at bottom) to ensure the
// required Sidebar Widget functions are available.
function widget_gComments_init() {

	// Check to see required Widget API functions are defined...
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
		return; // ...and if not, exit gracefully from the script.

	// This function prints the sidebar widget--the cool stuff!
	function widget_gComments($args) {

		// $args is an array of strings which help your widget
		// conform to the active theme: before_widget, before_title,
		// after_widget, and after_title are the array keys.
		extract($args);

		// Collect our widget's options, or define their defaults.
		$options = get_option('widget_gComments');
		$title = empty($options['title']) ? 'Latest Comments' : $options['title'];
		$count = empty($options['count']) ? '5' : $options['count'];
		
    $arguments = array(
    	'number' => $count,
    	'status' => 'approve'
    );
    $comments = get_comments($arguments);
    //$list = $pre_HTML;
    //$list .= "\n<ul>";
    foreach ($comments as $comment) {
      $list .= "\n<li>
      <a href=\"" . get_permalink($comment->comment_post_ID) .
      "#comment-" . $comment->comment_ID . "\" title=\"&mdash; " .
      substr(strip_tags($comment->comment_content), 0, 50) . "\">" . get_avatar( $comment->comment_author_email, 20 ) .
      "<span class=\"side-title\">" . strip_tags($comment->comment_author) . "</span><span class=\"side-meta\">&laquo; &raquo;</span> " . get_the_title($comment->comment_post_ID) .
      "<span class=\"side-meta\"> (" . get_comments_number($comment->comment_post_ID) . ")</span></span></a></li>";
      
    }
    //$list .= "\n</ul>";
    //$list .= $post_HTML;
				
		$before_widget = "<li id='latest-comments'><a class='feed-icon' href='".get_bloginfo('comments_rss2_url')."' title='comments rss feed'></a>";
		$before_title = "<h3>";
		$after_title = "</h3><ul>";
		$after_widget = "</ul></li>";
		
 		// It's important to use the $before_widget, $before_title,
 		// $after_title and $after_widget variables in your output.
		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo $list;
		echo $after_widget;
	}

	// This is the function that outputs the form to let users edit
	// the widget's title and so on. It's an optional feature, but
	// we'll use it because we can!
	function widget_gComments_control() {

		// Collect our widget's options.
		$options = get_option('widget_gComments');

		// This is for handing the control form submission.
		if ( $_POST['gComments-submit'] ) {
			// Clean up control form submission options
			$newoptions['title'] = strip_tags(stripslashes($_POST['gComments-title']));
			$newoptions['count'] = strip_tags(stripslashes($_POST['gComments-count']));
		}

		// If original widget options do not match control form
		// submission options, update them.
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_gComments', $options);
		}

		// Format options as valid HTML. Hey, why not.
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$count = htmlspecialchars($options['count'], ENT_QUOTES);

// The HTML below is the control form for editing options.
?>
		<div>
		<label for="gComments-title" style="line-height:35px;display:block;">Widget title: <input type="text" id="gComments-title" name="gComments-title" value="<?php echo $title; ?>" /></label>
		<label for="gComments-count" style="line-height:35px;display:block;">Number of comments: <input style="width: 25px;" type="text" id="gComments-count" name="gComments-count" value="<?php echo $count; ?>" /></label>
		<input type="hidden" name="gComments-submit" id="gComments-submit" value="1" />
		</div>
	<?php
	// end of widget_gComments_control()
	}
	
	// This registers the widget. About time.
	register_sidebar_widget('Gravatared Comments', 'widget_gComments');

	// This registers the (optional!) widget control form.
	register_widget_control('Gravatared Comments', 'widget_gComments_control');
}

add_action('init', 'widget_gComments_init');

// -------------------------------------------------------- Gravatared Authors
/*
Plugin Name: Gravatared Authors Widget
Plugin URI: http://www.mushon.com
Description: Author list with Gravatars and comment count
Author: Mushon Zer-Aviv
Version: 0.1
Author URI: http://www.mushon.com

	My Widget is released under the GNU General Public License (GPL)
	http://www.gnu.org/licenses/gpl.txt

	This is a WordPress plugin (http://wordpress.org) and widget
	(http://automattic.com/code/widgets/).
*/

// We're putting the plugin's functions in one big function we then
// call at 'init' (add_action() at bottom) to ensure the
// required Sidebar Widget functions are available.
function widget_mushon_list_authors_init() {

	// Check to see required Widget API functions are defined...
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
		return; // ...and if not, exit gracefully from the script.

	// This function prints the sidebar widget--the cool stuff!
	function widget_mushon_list_authors($args) {

		// $args is an array of strings which help your widget
		// conform to the active theme: before_widget, before_title,
		// after_widget, and after_title are the array keys.
		extract($args);

		// Collect our widget's options, or define their defaults.
		$options = get_option('widget_mushon_list_authors');
		$title = empty($options['title']) ? 'Authors' : $options['title'];
		$gravatar = empty($options['count']) ? false : $options['count'];
		
		// Improve wp_list_authors:

		function mushon_list_authors($args = '') {
			global $wpdb;
		
			$defaults = array(
				'optioncount' => false, 'exclude_admin' => true,
				'show_fullname' => false, 'hide_empty' => true,
				'feed' => '', 'feed_image' => '', 'feed_type' => '', 'gravatar' => false, 'echo' => true
			);
		
			$r = wp_parse_args( $args, $defaults );
			extract($r, EXTR_SKIP);
		
			$return = '';
		
			/** @todo Move select to get_authors(). */
			$authors = $wpdb->get_results("SELECT ID, user_nicename, user_email from $wpdb->users " . ($exclude_admin ? "WHERE user_login <> 'admin' " : '') . "ORDER BY display_name");
		
			$author_count = array();
			foreach ((array) $wpdb->get_results("SELECT DISTINCT post_author, COUNT(ID) AS count FROM $wpdb->posts WHERE post_type = 'post' AND " . get_private_posts_cap_sql( 'post' ) . " GROUP BY post_author") as $row) {
				$author_count[$row->post_author] = $row->count;
			}
		
			foreach ( (array) $authors as $author ) {
				$author = get_userdata( $author->ID );
				$posts = (isset($author_count[$author->ID])) ? $author_count[$author->ID] : 0;
				$name = $author->display_name;
		
				if ( $show_fullname && ($author->first_name != '' && $author->last_name != '') )
					$name = "$author->first_name $author->last_name";
					
				if ( $gravatar )
					$image = get_avatar( $author->user_email, $gravatar );
		
				if ( !($posts == 0 && $hide_empty) )
					$return .= '<li>';
				if ( $posts == 0 ) {
					if ( !$hide_empty )
						$link = $name;
				} else {
					
					// I want the count inside the link:
					if ( $optioncount )
						$count = ' <span class="side-meta">('. $posts . ')</span>';
					
					// I want to be able to better select things
					$link = '<a href="' . get_author_posts_url($author->ID, $author->user_nicename) . '" title="' . sprintf(__("Posts by %s"), attribute_escape($author->display_name)) . '"> ' . $image . ' <span class="side-title">' . $name . $count . '</span></a>';
					
					// Add a link to the user's comments (requires wp-stats)
					$link .= '<a href="'. get_bloginfo('url') . '/stats/?stats_author=' . get_author_name($author->ID) .'" class="stats-link" title="View all comments by ' . get_author_name($author->ID) .'">+</a>';
		
					if ( (! empty($feed_image)) || (! empty($feed)) ) {
						$link .= ' ';
						if (empty($feed_image))
							$link .= '(';
						$link .= '<a href="' . get_author_rss_link(0, $author->ID, $author->user_nicename) . '"';
		
						if ( !empty($feed) ) {
							$title = ' title="' . $feed . '"';
							$alt = ' alt="' . $feed . '"';
							$name = $feed;
							$link .= $title;
						}
		
						$link .= '>';
		
						if ( !empty($feed_image) )
							$link .= "<img src=\"$feed_image\" style=\"border: none;\"$alt$title" . ' />';
						else
							$link .= $name;
						
						$link .= '</a>';
		
						if ( empty($feed_image) )
							$link .= ')';
					}
		
				}
		
				if ( !($posts == 0 && $hide_empty) )
					
					$return .= $link . '</li>';
			}
			if ( !$echo )
				return $return;
			echo $return;
		}
		
		// display all users with a link to their user page
				
		$before_widget = "<li>";
		$before_title = "<h3 class='authors'>";
		$after_title = "</h3><ul>";
		$after_widget = "</ul></li>";
		
 		// It's important to use the $before_widget, $before_title,
 		// $after_title and $after_widget variables in your output.
		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo mushon_list_authors('exclude_admin=0&gravatar=20&optioncount=1');
		echo $after_widget;
	}

	// This is the function that outputs the form to let users edit
	// the widget's title and so on. It's an optional feature, but
	// we'll use it because we can!
	function widget_mushon_list_authors_control() {

		// Collect our widget's options.
		$options = get_option('widget_mushon_list_authors');

		// This is for handing the control form submission.
		if ( $_POST['mushon_list_authors-submit'] ) {
			// Clean up control form submission options
			$newoptions['title'] = strip_tags(stripslashes($_POST['mushon_list_authors-title']));
			$newoptions['count'] = strip_tags(stripslashes($_POST['mushon_list_authors-count']));
		}

		// If original widget options do not match control form
		// submission options, update them.
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_mushon_list_authors', $options);
		}

		// Format options as valid HTML. Hey, why not.
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$count = htmlspecialchars($options['count'], ENT_QUOTES);

// The HTML below is the control form for editing options.
?>
		<div>
		<label for="mushon_list_authors-title" style="line-height:35px;display:block;">Widget title: <input type="text" id="mushon_list_authors-title" name="mushon_list_authors-title" value="<?php echo $title; ?>" /></label>
		<label for="mushon_list_authors-count" style="line-height:35px;display:block;">Number of posts: <input style="width: 25px;" type="text" id="mushon_list_authors-count" name="mushon_list_authors-count" value="<?php echo $count; ?>" /></label>
		<input type="hidden" name="mushon_list_authors-submit" id="mushon_list_authors-submit" value="1" />
		</div>
	<?php
	// end of widget_mushon_list_authors_control()
	}

	// This registers the widget. About time.
	register_sidebar_widget('Gravatared Author List', 'widget_mushon_list_authors');

	// This registers the (optional!) widget control form.
	register_widget_control('Gravatared Author List', 'widget_mushon_list_authors_control');
}

// Delays plugin execution until Dynamic Sidebar has loaded first.
add_action('init', 'widget_mushon_list_authors_init');

?>