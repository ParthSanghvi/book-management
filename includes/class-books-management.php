<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://profiles.wordpress.org/parthsanghvi/
 * @since      1.0.0
 *
 * @package    Books_Management
 * @subpackage Books_Management/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Books_Management
 * @subpackage Books_Management/includes
 * @author     Parth <parthsanghvi2811@gmail.com>
 */
class Books_Management {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Books_Management_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'BOOKS_MANAGEMENT_VERSION' ) ) {
			$this->version = BOOKS_MANAGEMENT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'books-management';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->register_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Books_Management_Loader. Orchestrates the hooks of the plugin.
	 * - Books_Management_i18n. Defines internationalization functionality.
	 * - Books_Management_Admin. Defines all hooks for the admin area.
	 * - Books_Management_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-books-management-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-books-management-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-books-management-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-books-management-public.php';

		$this->loader = new Books_Management_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Books_Management_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Books_Management_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Books_Management_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Books_Management_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action('wp_ajax_nopriv_book_filter', $plugin_public, 'bm_handle_book_ajax_requests');
		$this->loader->add_action('wp_ajax_book_filter', $plugin_public,
			'bm_handle_book_ajax_requests'
		);

		$this->loader->add_action('wp_ajax_nopriv_book_search', $plugin_public, 'bm_handle_book_search_ajax_request');
		$this->loader->add_action('wp_ajax_book_search', $plugin_public,
			'bm_handle_book_search_ajax_request'
		);

		$this->loader->add_action('wp_ajax_nopriv_load_more_books', $plugin_public, 'bm_load_more_books');
		$this->loader->add_action('wp_ajax_load_more_books', $plugin_public,
			'bm_load_more_books'
		);


	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Books_Management_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}


/**
 * Register the custom post type named Book.
 */
public function bm_register_custom_post_type() {
	$labels = array(
		'name'               => __( 'Books', 'books-management' ),
		'singular_name'      => __( 'Book', 'books-management' ),
		'menu_name'          => __( 'Books', 'books-management' ),
		'add_new'            => __( 'Add New', 'books-management' ),
		'add_new_item'       => __( 'Add New Book', 'books-management' ),
		'edit_item'          => __( 'Edit Book', 'books-management' ),
		'new_item'           => __( 'New Book', 'books-management' ),
		'view_item'          => __( 'View Book', 'books-management' ),
		'search_items'       => __( 'Search Books', 'books-management' ),
		'not_found'          => __( 'No books found', 'books-management' ),
		'not_found_in_trash' => __( 'No books found in Trash', 'books-management' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'menu_icon' 		 => 'dashicons-book',
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'books' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 20,
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
	);

	register_post_type( 'book', $args );
}


/**
 * Register custom taxonomies named Authors and Publication for Book Post Type.
 */
public function bm_register_custom_taxonomies() {
	register_taxonomy(
		'author',
		'book',
		array(
			'label' => __( 'Authors', 'books-management' ),
			'rewrite' => array( 'slug' => 'author' ),
			'hierarchical' => true,
			'labels' => array(
				'add_new_item' => __( 'Add New Author', 'books-management' ),
			),
		)
	);

	register_taxonomy(
		'publication',
		'book',
		array(
			'label' => __( 'Publications', 'books-management' ),
			'rewrite' => array( 'slug' => 'publication' ),
			'hierarchical' => true,
			'labels' => array(
				'add_new_item' => __( 'Add New Publication', 'books-management' ),
			),
		)
	);
}

/**
 * Shortcode for grid layout of books.
 */
public function bm_book_grid_shortcode( $atts ) {
	ob_start();
	?>
	<h1><?php echo esc_html__( 'Library', 'books-management' ); ?></h1>
	<div class="book-filter-form">
		<form id="book-filter" method="post">
			<?php wp_nonce_field( 'book_filter_nonce', 'book_filter_nonce_field' ); ?>
			<input type="text" name="s" id="book-search" placeholder="Search book">
			<select name="author" id="author">
				<option value=""><?php echo esc_html__( 'Select author', 'books-management' ); ?></option>
				<?php
				$author_terms = get_terms( array(
					'taxonomy' => 'author',
					'hide_empty' => false,
				) );

				foreach ( $author_terms as $author ) {
					echo '<option value="' . $author->term_id . '">' . $author->name . '</option>';
				}
				?>
			</select>

			<select name="publication" id="publication">
				<option value=""><?php echo esc_html__( 'Select publication', 'books-management' ); ?></option>
				<?php
				$publication_terms = get_terms( array(
					'taxonomy' => 'publication',
					'hide_empty' => false,
				) );

				foreach ( $publication_terms as $publication ) {
					echo '<option value="' . $publication->term_id . '">' . $publication->name . '</option>';
				}
				?>
			</select>

			<button id="form-submit" type="button"><?php echo esc_html__( 'Filter', 'books-management' ); ?></button>
		</form>
	</div>

	<div class="book-grid">
		<?php
		$args = array(
			'post_type'      => 'book',
			'posts_per_page' => 10,
		);

		$books_query = new WP_Query( $args );

		if ( $books_query->have_posts() ) :
			while ( $books_query->have_posts() ) :
				$books_query->the_post();
				$image_url = BM_PLUGIN_URL . '/public/images/book-placeholder.png';
				?>
				<div <?php post_class('book-item'); ?>>

					<?php if ( has_post_thumbnail() ) { ?>
						<div class="book-thumbnail"><?php the_post_thumbnail(); ?></div>
					<?php }else{?>
						<div class="book-thumbnail"><img src="<?php echo esc_url($image_url); ?>"></div>
					<?php }?>
					<h4 class="book-title"><a href="<?php echo esc_url( get_the_permalink());?>"><?php echo esc_html(get_the_title(),'books-management'); ?></a></h4>
					<div class="book-details">
						<p><strong><?php esc_html_e( 'Published Date:', 'books-management' ); ?></strong> <?php echo get_the_date(); ?></p>
						<?php
						$authors = get_the_terms( get_the_ID(), 'author' );
						if ( $authors && ! is_wp_error( $authors ) ) :
							?>
							<p><strong><?php esc_html_e( 'Author:', 'books-management' ); ?></strong>
								<?php foreach ( $authors as $author ) : ?>
									<a href="<?php echo esc_url( get_term_link( $author ) ); ?>"><?php echo esc_html( $author->name ); ?></a>
								<?php endforeach; ?>
							</p>
						<?php endif; ?>
						<?php
						$publications = get_the_terms( get_the_ID(), 'publication' );
						if ( $publications && ! is_wp_error( $publications ) ) :
							?>
							<p><strong><?php esc_html_e( 'Publication:', 'books-management' ); ?></strong>
								<?php
								$num_publications = count( $publications );
								$counter = 1;
								foreach ( $publications as $publication ) :
									?>
									<a href="<?php echo esc_url( get_term_link( $publication ) ); ?>"><?php echo esc_html( $publication->name ); ?></a>
									<?php
									if ( $counter < $num_publications ) {
										echo ', ';
									}
									$counter++;
								endforeach;
								?>
							</p>
						<?php endif; ?>

					</div>
				</div>
				<?php
			endwhile;
			wp_reset_postdata();
		else :
			echo '<p>' . esc_html__( 'No books found.', 'books-management' ) . '</p>';
		endif;
		?>
	</div>
	<div class="load-more-container">
		<button id="load-more"><?php esc_html_e( 'Load More', 'books-management' ); ?></button>
	</div>
	<?php

	return ob_get_clean();
}


/*
	Function to hide page title if shortcode is added to the page.
*/
	public function bm_hide_page_title_for_shortcode_pages($title) {
		global $post;

		if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'book_grid')) {
			return '';
		}

		return $title;
	}

	public function register_hooks(){
		add_action('init',array($this,'bm_register_custom_post_type'));
		add_action('init',array($this,'bm_register_custom_taxonomies'));
		add_shortcode( 'book_grid', array( $this, 'bm_book_grid_shortcode' ) );
		add_filter('the_title', array($this,'bm_hide_page_title_for_shortcode_pages'));
	}
}

