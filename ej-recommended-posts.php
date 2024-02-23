<?php
/**
 * Plugin Name:       Recommended Posts Block
 * Description:       Display a grid of user selected articles (Posts).
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            A F M Jamal Uddin
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ej-recommended-posts
 *
 * @package           ej-blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/* -------------------------------------	NOTE    ------------------------------------------- */
/* Registers the block using the metadata loaded from the `block.json` file. 					*/
/* Behind the scenes, it registers also all assets so they can be enqueued 						*/
/* through the block editor in the corresponding context. 										*/
/* @see https://developer.wordpress.org/reference/functions/register_block_type/ 				*/
/* -------------------------------------------------------------------------------------------- */



/* -------------------------------------------------------------------------------------------- */
/* EJ_Recommended_Posts Class 																	*/
/* The main class that initiates and runs the plugin.											*/
/* @since 0.1.0 																				*/
/* -------------------------------------------------------------------------------------------- */
final class EJ_Recommended_Posts {

	/* -------------------------------------------------------------------------------------------- */
	/* Plugin Version 																				*/
	/* @since 0.1.0 																				*/
	/*  @var string The plugin version.																*/
	/* -------------------------------------------------------------------------------------------- */
	const VERSION = '0.1.0';

	/* -------------------------------------------------------------------------------------------- */
	/* Minimum PHP Version 																			*/
	/* @since 0.1.0 																				*/
	/* @var string Minimum PHP version required to run the plugin.									*/
	/* -------------------------------------------------------------------------------------------- */
	const MINIMUM_PHP_VERSION = '7.0';
	
	/* -------------------------------------------------------------------------------------------- */
	/* Instance 																					*/
	/* @since 0.1.0 																				*/
	/* @access private 																				*/
	/* @static 																						*/
	/* @var EJ_Recommended_Posts The single instance of the class.									*/
	/* -------------------------------------------------------------------------------------------- */	
	private static $_instance = null;

	/** ------------------------------------------------------------------------------------------- */
	/* Instance 																					*/
	/* Ensures only one instance of the class is loaded or can be loaded. 							*/
	/* @since 0.1.0 																				*/
	/* @access public 																				*/
	/* @static 																						*/
	/* @return EJ_Recommended_Posts An instance of the class.										*/
	/* -------------------------------------------------------------------------------------------- */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	/** ------------------------------------------------------------------------------------------- */
	/* Constructor																					*/
	/* @since 0.1.0 																				*/
	/* @access public 																				*/
	/* -------------------------------------------------------------------------------------------- */	
	public function __construct() {

		add_action( 'init', [ $this, 'i18n' ] );
		add_action( 'plugins_loaded', [ $this, 'init' ] );

	}

	/** ------------------------------------------------------------------------------------------- */
	/* Load Textdomain 																				*/
	/* Load plugin localization files. 																*/
	/* Fired by `init` action hook. 																*/
	/* @since 0.1.0 																				*/
	/* @access public 																				*/
	/* -------------------------------------------------------------------------------------------- */	
	public function i18n() {

		load_plugin_textdomain( 'ej-recommended-posts', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );

	}

	/** ------------------------------------------------------------------------------------------- */
	/* Initialize the plugin 																		*/
	/* Checks for basic plugin requirements, if one check fail don't continue, 						*/
	/* if all check have passed load the files required to run the plugin.							*/
	/* Fired by `plugins_loaded` action hook.														*/
	/* @since 0.1.0 																				*/
	/* @access public 																				*/
	/* -------------------------------------------------------------------------------------------- */
	public function init() {

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return;
		}

		// Add Plugin (the original block) actions
		add_action( 'init', [ $this, 'ej_blocks_recommended_posts_block_init' ] );

	}
	
	/** ------------------------------------------------------------------------------------------- */
	/* Admin notice																					*/
	/* Warning when the site doesn't have a minimum required PHP version.							*/
	/* @since 0.1.0 																				*/
	/* @access public 																				*/
	/* -------------------------------------------------------------------------------------------- */
	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'ej-recommended-posts' ),
			'<strong>' . esc_html__( 'Recommended Posts Block', 'ej-recommended-posts' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'ej-recommended-posts' ) . '</strong>',
			 self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/* -------------------------------------------------------------------------------------------- */
	/* Main Callback function to output the data in the front-end. 									*/
	/* Here the paramater '$attributes' is from the 'src/block.json' 								*/
	/* followed by the admin side selection defined in 'src/edit.js'. 								*/
	/* @since 0.1.0 																				*/
	/* @access public 																				*/
	/* -------------------------------------------------------------------------------------------- */
	public function ej_blocks_render_recommended_posts_block($attributes) {

		// arguments and query for the recommended post @see https://developer.wordpress.org/reference/classes/wp_query/
		$args_recommended_posts = array(
			'post_status' => 'publish',
			'post__in' => $attributes['selectedPostIds'],
			'orderby' => 'post__in',
		);
		$query_recommended_posts = new WP_Query($args_recommended_posts);

		// check if there is any posts available
		if ($query_recommended_posts->have_posts()) : 

			// outermost '<div>' starts, 'get_block_wrapper_attributes()' function generates a string of attributes (inc: css class 'wp-block-ej-blocks-ej-recommended-posts')
			$output = '<div ' . get_block_wrapper_attributes() . '>';
				// loop through all the posts
				while ($query_recommended_posts->have_posts()) : $query_recommended_posts->the_post();

					// initialize some variables
					$post_thumbnail = '';	
					$post_id = get_the_ID();
					$post_title = get_the_title();
					$post_permalink = get_the_permalink();
					$post_category_output = '';

					// check for empty post title
					$post_title = $post_title ? $post_title : __('(No title)','ej-recommended-posts');

					// if 'displayFeaturedImage' set to true and the post actually has a featured image, then display the 'large' size image
					if($attributes["displayFeaturedImage"] && has_post_thumbnail()) {
						$post_thumbnail = '<a href="' . esc_url($post_permalink) . '" class="wp-block-ej-blocks-ej-recommended-posts__item-image" title="' . esc_attr($post_title) . '">' . get_the_post_thumbnail( $post_id, 'large' ) . '</a>';
					}

					$post_title_output = '<div class="wp-block-ej-blocks-ej-recommended-posts__item-title"><a href="' . esc_url($post_permalink) . '" title="' . esc_attr($post_title) . '">' . $post_title . '</a></div>';

					$post_date_output = '<time datetime="' . esc_attr( get_the_date('c')) . '">' . esc_html( get_the_date()) . '</time>';

					// get the category link and name and then the output if assigned
					$post_category = get_the_category();
					if($post_category) {
						$category_link = get_term_link($post_category[0]->slug, 'category');
						$category_name = $post_category[0]->name;
						$post_category_output = '<div class="wp-block-ej-blocks-ej-recommended-posts__item-meta-cat"><a href="' . esc_url($category_link) . '" title="' . esc_attr($category_name) . '" rel="category">' . $category_name . '</a></div>';
					}
					
					$readmore_output = '<a href="' . esc_url($post_permalink) . '" class="wp-block-ej-blocks-ej-recommended-posts__item-readmore" title="' . esc_attr__('Read more', 'ej-recommended-posts') . '"><span class="ej-readmore">' . __('Read more', 'ej-recommended-posts') . '</span><span class="ej-readmore-icon" aria-hidden="true"></span></a>';

					// adding all output tags within the outer tag '<div>'
					$output .= '<div class="wp-block-ej-blocks-ej-recommended-posts__item"><!--  start of item container  -->';
					$output .= $post_thumbnail;
					$output .= $post_title_output;
					$output .= $post_date_output;
					$output .= '<div class="wp-block-ej-blocks-ej-recommended-posts__item-meta"><!--  start of meta container  -->';
					$output .= $post_category_output;
					$output .= '<hr>';
					$output .= $readmore_output;
					$output .= '</div><!--  end of meta container  -->';
					$output .= '</div><!--  end of item container  -->';

				endwhile;
			// outermost '<div>' with css class 'wp-block-ej-blocks-ej-recommended-posts' ends 
			$output .= '</div>'; 

		endif;
		// reset the query to show proper posts after this block if any
		wp_reset_query();

		return $output;
	}

	/** ------------------------------------------------------------------------------------------- */
	/* render callback function		 																*/
	/* Fired by `init` action hook. 																*/
	/* @since 0.1.0 																				*/
	/* @access public 																				*/
	/* -------------------------------------------------------------------------------------------- */	
	public function ej_blocks_recommended_posts_block_init() {
		register_block_type( __DIR__ . '/build', array(
			'render_callback' => [ $this, 'ej_blocks_render_recommended_posts_block' ]
		) );
	}


}


EJ_Recommended_Posts::instance();