<?php
/*
 *  Author: Todd Motto | @toddmotto
 *  URL: html5blank.com | @html5blank
 *  Custom functions, support, custom post types and more.
 */

/*------------------------------------*\
	External Modules/Files
\*------------------------------------*/

// Load any external files you have here

/*------------------------------------*\
	Theme Support
\*------------------------------------*/


require_once(__DIR__ . '/vendor/autoload.php');
Timber::$dirname = array('views');
$timber = new Timber\Timber();

if (!isset($content_width))
{
    $content_width = 900;
}

if (function_exists('add_theme_support'))
{
    // Add Menu Support
    add_theme_support('menus');

    // Add Thumbnail Theme Support
    add_theme_support('post-thumbnails');
    add_image_size('large', 700, '', true); // Large Thumbnail
    add_image_size('medium', 250, '', true); // Medium Thumbnail
    add_image_size('small', 120, '', true); // Small Thumbnail
    add_image_size('custom-size', 700, 200, true); // Custom Thumbnail Size call using the_post_thumbnail('custom-size');

    // Add Support for Custom Backgrounds - Uncomment below if you're going to use
    /*add_theme_support('custom-background', array(
	'default-color' => 'FFF',
	'default-image' => get_template_directory_uri() . '/img/bg.jpg'
    ));*/

    // Add Support for Custom Header - Uncomment below if you're going to use
    /*add_theme_support('custom-header', array(
	'default-image'			=> get_template_directory_uri() . '/img/headers/default.jpg',
	'header-text'			=> false,
	'default-text-color'		=> '000',
	'width'				=> 1000,
	'height'			=> 198,
	'random-default'		=> false,
	'wp-head-callback'		=> $wphead_cb,
	'admin-head-callback'		=> $adminhead_cb,
	'admin-preview-callback'	=> $adminpreview_cb
    ));*/

    // Enables post and comment RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Localisation Support
    load_theme_textdomain('html5blank', get_template_directory() . '/languages');
}

add_filter( 'timber/context', 'add_to_context' );

//use mailtrap for development
function mailtrap($phpmailer) {
  $phpmailer->isSMTP();
  $phpmailer->Host = 'smtp.mailtrap.io';
  $phpmailer->SMTPAuth = true;
  $phpmailer->Port = 2525;
  $phpmailer->Username = '541566cbb17403f14';
  $phpmailer->Password = 'd9a4f5250f346e';
}

add_action('phpmailer_init', 'mailtrap');
//Add categories and tags to Pages
function add_taxonomies_to_pages() {
 register_taxonomy_for_object_type( 'post_tag', 'page' );
 register_taxonomy_for_object_type( 'category', 'page' );
 }
add_action( 'init', 'add_taxonomies_to_pages' );

function add_to_context( $context ) {
    global $post;
    // So here you are adding data to Timber's context object, i.e...
    $context['wp_template'] = $GLOBALS['current_theme_template'];
    
    //GET HOSTNAME INFO
    $hostname = $_SERVER['SERVER_NAME']; 


    $context["menu"] = new Timber\Menu( 'normal' );

    $custom_logo_id = get_theme_mod( 'custom_logo' );
    $logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
    if ( has_custom_logo() )
        $context["logo"] =  esc_url( $logo[0] );

    return $context;
}

add_action( 'wpcf7_init', 'custom_add_form_bulma_inputs' );
 
function custom_add_form_bulma_inputs() {
    wpcf7_add_form_tag( array('bhinput','bhinput*'), 'bulma_horizontal_input_form_tag_handler', array( 'name-attr' => true ) );
    wpcf7_add_form_tag( array('bhradio','bhradio*'), 'bulma_horizontal_radio_form_tag_handler', true );
    wpcf7_add_form_tag( array('bhsubmit'), 'bulma_horizontal_submit_form_tag_handler');
    wpcf7_add_form_tag( array('bhselect','bhselect*'), 'bulma_horizontal_select_form_tag_handler', array( 'name-attr' => true ));
}
add_filter( 'wpcf7_validate_bhinput', 'wpcf7_bhinput_validation_filter', 20, 2 );
add_filter( 'wpcf7_validate_bhinput*', 'wpcf7_bhinput_validation_filter', 20, 2 );
 
function wpcf7_bhinput_validation_filter( $result, $tag ) {
    //This is nothing more than a local copy of wpcf7_text_validation_filter
    $name = $tag->name;
    $input_type = explode(":", $tag->options[0])[1];
    $tag->basetype = $input_type;

    $value = isset( $_POST[$name] )
        ? trim( wp_unslash( strtr( (string) $_POST[$name], "\n", " " ) ) )
        : '';

    if ( 'text' == $tag->basetype || "textarea" == $tag->basetype) {
        if ( $tag->is_required() && '' == $value ) {
            $result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
        }
    }

    if ( 'email' == $tag->basetype ) {
        if ( $tag->is_required() && '' == $value ) {
            $result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
        } elseif ( '' != $value && ! wpcf7_is_email( $value ) ) {
            $result->invalidate( $tag, wpcf7_get_message( 'invalid_email' ) );
        }
    }

    if ( 'url' == $tag->basetype ) {
        if ( $tag->is_required() && '' == $value ) {
            $result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
        } elseif ( '' != $value && ! wpcf7_is_url( $value ) ) {
            $result->invalidate( $tag, wpcf7_get_message( 'invalid_url' ) );
        }
    }

    if ( 'tel' == $tag->basetype ) {
        if ( $tag->is_required() && '' == $value ) {
            $result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
        } elseif ( '' != $value && ! wpcf7_is_tel( $value ) ) {
            $result->invalidate( $tag, wpcf7_get_message( 'invalid_tel' ) );
        }
    }

    if ( '' !== $value ) {
        $maxlength = $tag->get_maxlength_option();
        $minlength = $tag->get_minlength_option();

        if ( $maxlength && $minlength && $maxlength < $minlength ) {
            $maxlength = $minlength = null;
        }

        $code_units = wpcf7_count_code_units( stripslashes( $value ) );

        if ( false !== $code_units ) {
            if ( $maxlength && $maxlength < $code_units ) {
                $result->invalidate( $tag, wpcf7_get_message( 'invalid_too_long' ) );
            } elseif ( $minlength && $code_units < $minlength ) {
                $result->invalidate( $tag, wpcf7_get_message( 'invalid_too_short' ) );
            }
        }
    }

    return $result;
}
function bulma_horizontal_select_form_tag_handler($tag)
{
    $required = " ";
    if (strpos($tag->type, "*")) 
        $required = 'aria-required="true"';

    $label = $tag->values[0];
    $input_size = "";
    $atts = array(
        'name' => $tag->name
    );
    foreach ($tag->options as $option) {
        $opt = explode(":", $option);
        if($opt[0] == "size")
            $input_size = $opt[1];
    }

    $input = "<select ".wpcf7_format_atts( $atts )." ".$required.">";
    foreach ( $tag->values as $k => $v ) {
        if($k > 0)
        {
            $selected = "";
            if($k == 1)
                $selected = "selected";

            $input .= '<option value="'.esc_html($v).'" '.$selected.'>'.esc_html($v).'</option>';
        }
    }
    $input .= "</select>";

    $formtag = sprintf('<div class="field is-horizontal">
    <div class="field-label is-%1$s"><label class="label">%2$s</label></div>
    <div class="field-body">
        <div class="field"><div class="control"><div class="select is-rounded">%3$s</div></div></div>
    </div>
    </div>',
    $input_size,
    $label,
    $input);

    return $formtag;

}


function bulma_horizontal_submit_form_tag_handler($tag){
    $classes = "";
    $txt= "";
    $atts = array(
        'type' => "submit"
    );
    foreach ($tag->options as $option) {
        $opt = explode(":", $option);
        if($opt[0] == "class")
        {
            if(empty($classes))
                $classes .= $opt[1];
            else
                $classes .= " ".$opt[1];
        }
    }
    foreach ( $tag->values as $val ) {
        $txt .= esc_html($val);
    }

    $formtag = sprintf('<div class="field submit">
        <div class="control"><button class="%1$s" %2$s>%3$s</button></div></div>',
    $classes,wpcf7_format_atts( $atts ),$txt);

    return $formtag;
}
function bulma_horizontal_radio_form_tag_handler($tag){
    $atts = array(
        'type' => "radio",
        'name' => $tag->name,
    );
    $input_size = "";
    $label = "";
    $radio1 = "";
    $radio2 = "";
    foreach ($tag->options as $option) {
        $opt = explode(":", $option);
        if ($opt[0] == "radio1") {
            if(empty($radio1))
                $radio1 .= $opt[1];
            else
                $radio1 .= " ".$opt[1];
        }
        elseif ($opt[0] == "radio2") {
            if(empty($radio2))
                $radio2 .= $opt[1];
            else
                $radio2 .= " ".$opt[1];
        }
        elseif($opt[0] == "size")
            $input_size = $opt[1];
    }

    foreach ( $tag->values as $val ) {
        $label .= esc_html($val);
    }

    $input1 = sprintf(
        '<input %1$s checked /> %2$s',
        wpcf7_format_atts( $atts ),$radio1 );
    $input2 = sprintf(
        '<input %1$s /> %2$s',
        wpcf7_format_atts( $atts ),$radio2 );

    $formtag = sprintf('<div class="field is-horizontal">
    <div class="field-label is-%1$s"><label class="label">%2$s</label></div>
    <div class="field-body">
        <div class="field"><div class="control has-radios"><label class="radio">%3$s </label><label class="radio">%4$s </label></div></div>
    </div>
    </div>',
    $input_size,$label,$input1,$input2);

    return $formtag;
 
}

function bulma_horizontal_input_form_tag_handler( $tag ) 
{

    $tag = new WPCF7_FormTag( $tag );
    $class = wpcf7_form_controls_class( $tag->type );

    if ( empty( $tag->name ) ) {
        return '';
    }

    $input_size = "";
    $input_type = "";
    $placeholder = "";
    $label = "";

    foreach ($tag->options as $option) {
        $opt = explode(":", $option);
        if($opt[0] == "type")
            $input_type = $opt[1];
        elseif($opt[0] == "placeholder")
        {
            if(empty($placeholder))
                $placeholder = $opt[1];
            else
                $placeholder .= " ".$opt[1];
        }
        elseif($opt[0] == "size")
            $input_size = $opt[1];
    }
    $atts = array(
        'name' => $tag->name,
        'placeholder' => $placeholder,
        'class' => ($input_type == "textarea" ? "textarea" : "input")." is-".$input_size." ".$tag->get_class_option( $class )
    );

    foreach ( $tag->values as $val ) {
        $label .= esc_html($val);
    }

    if($input_type == "textarea")
        $input = sprintf('<textarea %s></textarea>',wpcf7_format_atts( $atts ) );
    else
    {
        $atts["type"] = $input_type;
        $input = sprintf('<input %s />',wpcf7_format_atts( $atts ) );
    }


    $formtag = sprintf('<div class="field">
    <label class="label">%2$s</label>
    <div class="control"><span class="wpcf7-form-control-wrap %3$s">%4$s</span></div></div>',
    $input_size,
    $label,
    $tag->name,
    $input);
 
    return $formtag;
}

/*
 * Custom change of the HTML code whte validation error occurs.
 */
function filter_wpcf7_validation_error( $error, $name, $instance ) { 
    // make filter magic happen here... 
    $error=str_replace("class=\"","class=\"content is-small help is-danger has-text-centered-desktop ", $error);
    return $error; 
}; 
add_filter( 'wpcf7_validation_error', 'filter_wpcf7_validation_error', 10, 3 ); 


/*------------------------------------*\
	Functions
\*------------------------------------*/

// HTML5 Blank navigation
function html5blank_nav()
{
	wp_nav_menu(
	array(
		'theme_location'  => 'header-menu',
		'menu'            => '',
		'container'       => 'div',
		'container_class' => 'menu-{menu slug}-container',
		'container_id'    => '',
		'menu_class'      => 'menu',
		'menu_id'         => '',
		'echo'            => true,
		'fallback_cb'     => 'wp_page_menu',
		'before'          => '',
		'after'           => '',
		'link_before'     => '',
		'link_after'      => '',
		'items_wrap'      => '<ul>%3$s</ul>',
		'depth'           => 0,
		'walker'          => ''
		)
	);
}

// Load HTML5 Blank scripts (header.php)
function html5blank_header_scripts()
{
    if ($GLOBALS['pagenow'] != 'wp-login.php' && !is_admin()) {

        wp_enqueue_style( 'fontAwesome', "https://use.fontawesome.com/releases/v5.0.13/css/all.css" );

    	wp_register_script('conditionizr', get_template_directory_uri() . '/js/lib/conditionizr-4.3.0.min.js', array(), '4.3.0'); // Conditionizr
        wp_enqueue_script('conditionizr'); // Enqueue it!

        wp_register_script('modernizr', get_template_directory_uri() . '/js/lib/modernizr-2.7.1.min.js', array(), '2.7.1'); // Modernizr
        wp_enqueue_script('modernizr'); // Enqueue it!

        wp_deregister_script( 'jquery' );

        // CDN hosted jQuery placed in the header, as some plugins require that jQuery is loaded in the header.
        wp_enqueue_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js', array(), '3.2.1', false );  

        wp_enqueue_style( 'gb', get_template_directory_uri() . '/dist/css/app.css', ["fontAwesome"] );
        wp_enqueue_script( 'gb', get_template_directory_uri() .'/dist/js/app.js', array(), '1.0', true );

    }
}

// Load HTML5 Blank conditional scripts
function html5blank_conditional_scripts()
{
    if (is_page('pagenamehere')) {
        wp_register_script('scriptname', get_template_directory_uri() . '/js/scriptname.js', array('jquery'), '1.0.0'); // Conditional script(s)
        wp_enqueue_script('scriptname'); // Enqueue it!
    }
}

// Load HTML5 Blank styles
function html5blank_styles()
{
    wp_register_style('normalize', get_template_directory_uri() . '/normalize.css', array(), '1.0', 'all');
    wp_enqueue_style('normalize'); // Enqueue it!

    wp_register_style('html5blank', get_template_directory_uri() . '/style.css', array(), '1.0', 'all');
    wp_enqueue_style('html5blank'); // Enqueue it!
}

// Register HTML5 Blank Navigation
function register_html5_menu()
{
    register_nav_menus(array( // Using array to specify more menus if needed
        'header-menu' => __('Header Menu', 'html5blank'), // Main Navigation
        'sidebar-menu' => __('Sidebar Menu', 'html5blank'), // Sidebar Navigation
        'extra-menu' => __('Extra Menu', 'html5blank') // Extra Navigation if needed (duplicate as many as you need!)
    ));
}

// Remove the <div> surrounding the dynamic navigation to cleanup markup
function my_wp_nav_menu_args($args = '')
{
    $args['container'] = false;
    return $args;
}

// Remove Injected classes, ID's and Page ID's from Navigation <li> items
function my_css_attributes_filter($var)
{
    return is_array($var) ? array() : '';
}

// Remove invalid rel attribute values in the categorylist
function remove_category_rel_from_category_list($thelist)
{
    return str_replace('rel="category tag"', 'rel="tag"', $thelist);
}

// Add page slug to body class, love this - Credit: Starkers Wordpress Theme
function add_slug_to_body_class($classes)
{
    global $post;
    if (is_home()) {
        $key = array_search('blog', $classes);
        if ($key > -1) {
            unset($classes[$key]);
        }
    } elseif (is_page()) {
        $classes[] = sanitize_html_class($post->post_name);
    } elseif (is_singular()) {
        $classes[] = sanitize_html_class($post->post_name);
    }

    return $classes;
}

// If Dynamic Sidebar Exists
if (function_exists('register_sidebar'))
{
    // Define Sidebar Widget Area 1
    register_sidebar(array(
        'name' => __('Widget Area 1', 'html5blank'),
        'description' => __('Description for this widget-area...', 'html5blank'),
        'id' => 'widget-area-1',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));

    // Define Sidebar Widget Area 2
    register_sidebar(array(
        'name' => __('Widget Area 2', 'html5blank'),
        'description' => __('Description for this widget-area...', 'html5blank'),
        'id' => 'widget-area-2',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
}

// Remove wp_head() injected Recent Comment styles
function my_remove_recent_comments_style()
{
    global $wp_widget_factory;
    remove_action('wp_head', array(
        $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
        'recent_comments_style'
    ));
}

// Pagination for paged posts, Page 1, Page 2, Page 3, with Next and Previous Links, No plugin
function html5wp_pagination()
{
    global $wp_query;
    $big = 999999999;
    echo paginate_links(array(
        'base' => str_replace($big, '%#%', get_pagenum_link($big)),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $wp_query->max_num_pages
    ));
}

// Custom Excerpts
function html5wp_index($length) // Create 20 Word Callback for Index page Excerpts, call using html5wp_excerpt('html5wp_index');
{
    return 20;
}

// Create 40 Word Callback for Custom Post Excerpts, call using html5wp_excerpt('html5wp_custom_post');
function html5wp_custom_post($length)
{
    return 40;
}

// Create the Custom Excerpts callback
function html5wp_excerpt($length_callback = '', $more_callback = '')
{
    global $post;
    if (function_exists($length_callback)) {
        add_filter('excerpt_length', $length_callback);
    }
    if (function_exists($more_callback)) {
        add_filter('excerpt_more', $more_callback);
    }
    $output = get_the_excerpt();
    $output = apply_filters('wptexturize', $output);
    $output = apply_filters('convert_chars', $output);
    $output = '<p>' . $output . '</p>';
    echo $output;
}

// Custom View Article link to Post
function html5_blank_view_article($more)
{
    global $post;
    return '... <a class="view-article" href="' . get_permalink($post->ID) . '">' . __('View Article', 'html5blank') . '</a>';
}

// Remove Admin bar
function remove_admin_bar()
{
    return false;
}

// Remove 'text/css' from our enqueued stylesheet
function html5_style_remove($tag)
{
    return preg_replace('~\s+type=["\'][^"\']++["\']~', '', $tag);
}

// Remove thumbnail width and height dimensions that prevent fluid images in the_thumbnail
function remove_thumbnail_dimensions( $html )
{
    $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
    return $html;
}

// Custom Gravatar in Settings > Discussion
function html5blankgravatar ($avatar_defaults)
{
    $myavatar = get_template_directory_uri() . '/img/gravatar.jpg';
    $avatar_defaults[$myavatar] = "Custom Gravatar";
    return $avatar_defaults;
}

// Threaded Comments
function enable_threaded_comments()
{
    if (!is_admin()) {
        if (is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
            wp_enqueue_script('comment-reply');
        }
    }
}

// Custom Comments Callback
function html5blankcomments($comment, $args, $depth)
{
	$GLOBALS['comment'] = $comment;
	extract($args, EXTR_SKIP);

	if ( 'div' == $args['style'] ) {
		$tag = 'div';
		$add_below = 'comment';
	} else {
		$tag = 'li';
		$add_below = 'div-comment';
	}
?>
    <!-- heads up: starting < for the html tag (li or div) in the next line: -->
    <<?php echo $tag ?> <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
	<?php if ( 'div' != $args['style'] ) : ?>
	<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
	<?php endif; ?>
	<div class="comment-author vcard">
	<?php if ($args['avatar_size'] != 0) echo get_avatar( $comment, $args['180'] ); ?>
	<?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link()) ?>
	</div>
<?php if ($comment->comment_approved == '0') : ?>
	<em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.') ?></em>
	<br />
<?php endif; ?>

	<div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
		<?php
			printf( __('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),'  ','' );
		?>
	</div>

	<?php comment_text() ?>

	<div class="reply">
	<?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
	</div>
	<?php if ( 'div' != $args['style'] ) : ?>
	</div>
	<?php endif; ?>
<?php }

/*------------------------------------*\
	Actions + Filters + ShortCodes
\*------------------------------------*/

// Add Actions
function inner_page( $atts, $content = null ){
    $a = shortcode_atts( array(
        'slug' => 'NULL',
    ), $atts );

    if($slug != 'NULL'){
        ob_start();
        get_template_part($a['slug']);
        return ob_get_clean();
    }
}

add_action('init', 'register_section_shortcodes');
function register_section_shortcodes(){
    add_shortcode('inner_page', 'inner_page');
}
add_action('init', 'html5blank_header_scripts'); // Add Custom Scripts to wp_head
add_action('wp_print_scripts', 'html5blank_conditional_scripts'); // Add Conditional Page Scripts
add_action('get_header', 'enable_threaded_comments'); // Enable Threaded Comments
//add_action('wp_enqueue_scripts', 'html5blank_styles'); // Add Theme Stylesheet
add_action('init', 'register_html5_menu'); // Add HTML5 Blank Menu
add_action('init', 'create_post_type_html5'); // Add our HTML5 Blank Custom Post Type
add_action('widgets_init', 'my_remove_recent_comments_style'); // Remove inline Recent Comment Styles from wp_head()
add_action('init', 'html5wp_pagination'); // Add our HTML5 Pagination

// Remove Actions
remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
remove_action('wp_head', 'index_rel_link'); // Index link
remove_action('wp_head', 'parent_post_rel_link', 10, 0); // Prev link
remove_action('wp_head', 'start_post_rel_link', 10, 0); // Start link
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // Display relational links for the posts adjacent to the current post.
remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

// Add Filters
add_filter('avatar_defaults', 'html5blankgravatar'); // Custom Gravatar in Settings > Discussion
add_filter('body_class', 'add_slug_to_body_class'); // Add slug to body class (Starkers build)
add_filter('widget_text', 'do_shortcode'); // Allow shortcodes in Dynamic Sidebar
add_filter('widget_text', 'shortcode_unautop'); // Remove <p> tags in Dynamic Sidebars (better!)
add_filter('wp_nav_menu_args', 'my_wp_nav_menu_args'); // Remove surrounding <div> from WP Navigation
// add_filter('nav_menu_css_class', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> injected classes (Commented out by default)
// add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> injected ID (Commented out by default)
// add_filter('page_css_class', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> Page ID's (Commented out by default)
add_filter('the_category', 'remove_category_rel_from_category_list'); // Remove invalid rel attribute
add_filter('the_excerpt', 'shortcode_unautop'); // Remove auto <p> tags in Excerpt (Manual Excerpts only)
add_filter('the_excerpt', 'do_shortcode'); // Allows Shortcodes to be executed in Excerpt (Manual Excerpts only)
add_filter('excerpt_more', 'html5_blank_view_article'); // Add 'View Article' button instead of [...] for Excerpts
add_filter('show_admin_bar', 'remove_admin_bar'); // Remove Admin bar
add_filter('style_loader_tag', 'html5_style_remove'); // Remove 'text/css' from enqueued stylesheet
add_filter('post_thumbnail_html', 'remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to thumbnails
add_filter('image_send_to_editor', 'remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to post images

// Remove Filters
remove_filter('the_excerpt', 'wpautop'); // Remove <p> tags from Excerpt altogether

// Shortcodes
add_shortcode('html5_shortcode_demo', 'html5_shortcode_demo'); // You can place [html5_shortcode_demo] in Pages, Posts now.
add_shortcode('html5_shortcode_demo_2', 'html5_shortcode_demo_2'); // Place [html5_shortcode_demo_2] in Pages, Posts now.
add_filter( 'wpcf7_load_css', '__return_false' );

// Shortcodes above would be nested like this -
// [html5_shortcode_demo] [html5_shortcode_demo_2] Here's the page title! [/html5_shortcode_demo_2] [/html5_shortcode_demo]

/*------------------------------------*\
	Custom Post Types
\*------------------------------------*/

// Create 1 Custom Post type for a Demo, called HTML5-Blank
function create_post_type_html5()
{
    register_taxonomy_for_object_type('category', 'html5-blank'); // Register Taxonomies for Category
    register_taxonomy_for_object_type('post_tag', 'html5-blank');
    register_post_type('html5-blank', // Register Custom Post Type
        array(
        'labels' => array(
            'name' => __('HTML5 Blank Custom Post', 'html5blank'), // Rename these to suit
            'singular_name' => __('HTML5 Blank Custom Post', 'html5blank'),
            'add_new' => __('Add New', 'html5blank'),
            'add_new_item' => __('Add New HTML5 Blank Custom Post', 'html5blank'),
            'edit' => __('Edit', 'html5blank'),
            'edit_item' => __('Edit HTML5 Blank Custom Post', 'html5blank'),
            'new_item' => __('New HTML5 Blank Custom Post', 'html5blank'),
            'view' => __('View HTML5 Blank Custom Post', 'html5blank'),
            'view_item' => __('View HTML5 Blank Custom Post', 'html5blank'),
            'search_items' => __('Search HTML5 Blank Custom Post', 'html5blank'),
            'not_found' => __('No HTML5 Blank Custom Posts found', 'html5blank'),
            'not_found_in_trash' => __('No HTML5 Blank Custom Posts found in Trash', 'html5blank')
        ),
        'public' => true,
        'hierarchical' => true, // Allows your posts to behave like Hierarchy Pages
        'has_archive' => true,
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'thumbnail'
        ), // Go to Dashboard Custom HTML5 Blank post for supports
        'can_export' => true, // Allows export in Tools > Export
        'taxonomies' => array(
            'post_tag',
            'category'
        ) // Add Category and Post Tags support
    ));
}

/*------------------------------------*\
	ShortCode Functions
\*------------------------------------*/

// Shortcode Demo with Nested Capability
function html5_shortcode_demo($atts, $content = null)
{
    return '<div class="shortcode-demo">' . do_shortcode($content) . '</div>'; // do_shortcode allows for nested Shortcodes
}

// Shortcode Demo with simple <h2> tag
function html5_shortcode_demo_2($atts, $content = null) // Demo Heading H2 shortcode, allows for nesting within above element. Fully expandable.
{
    return '<h2>' . $content . '</h2>';
}



?>
