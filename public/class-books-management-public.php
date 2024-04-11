<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/parthsanghvi/
 * @since      1.0.0
 *
 * @package    Books_Management
 * @subpackage Books_Management/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Books_Management
 * @subpackage Books_Management/public
 * @author     Parth <parthsanghvi2811@gmail.com>
 */
class Books_Management_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Books_Management_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Books_Management_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/books-management-public.css', array(), $this->version, 'all' );


	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Books_Management_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Books_Management_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( 'books-management-public', plugin_dir_url( __FILE__ ) . 'js/books-management-public.js', array( 'jquery' ), $this->version, false );
	
   		wp_localize_script( 'books-management-public', 'book_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

	}

	/*
		Callback function for ajax book filter.
	*/
	function handle_book_ajax_requests() {

	    if ( ! isset( $_POST['book_filter_nonce_field'] ) || ! wp_verify_nonce( $_POST['book_filter_nonce_field'], 'book_filter_nonce' ) ) {
	        wp_die( 'Nonce verification failed.', 'book_filter_nonce_verification_failed', array( 'response' => 403 ) );
	    }

	    $author_id = isset( $_POST['selectedAuthor'] ) ? intval( $_POST['selectedAuthor'] ) : 0;
	    $publication_id = isset( $_POST['selectedPublication'] ) ? intval( $_POST['selectedPublication'] ) : 0;
	    $paged = isset( $_POST['paged'] ) ? intval( $_POST['paged'] ) : 1;

	    $args = array(
	        'post_type' => 'book',
	        'posts_per_page' => 10,
	        'paged' => $paged
	    );

	    if ( $author_id && $publication_id ) {
	        $args['tax_query'] = array(
	            'relation' => 'AND',
	            array(
	                'taxonomy' => 'author',
	                'field' => 'term_id',
	                'terms' => $author_id,
	            ),
	            array(
	                'taxonomy' => 'publication',
	                'field' => 'term_id',
	                'terms' => $publication_id,
	            ),
	        );
	    } elseif ( $author_id || $publication_id ) {
	        $args['tax_query'] = array(
	            'relation' => 'OR',
	            array(
	                'taxonomy' => 'author',
	                'field' => 'term_id',
	                'terms' => $author_id,
	            ),
	            array(
	                'taxonomy' => 'publication',
	                'field' => 'term_id',
	                'terms' => $publication_id,
	            ),
	        );
	    }

	    $books_query = new WP_Query( $args );

	    ob_start();
	    if ( $books_query->have_posts() ) :
	        while ( $books_query->have_posts() ) : $books_query->the_post();
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
						<p><strong><?php esc_html_e('Publication Date:','books-management');?></strong> <?php echo get_the_date(); ?></p>
						<?php
						$authors = get_the_terms( get_the_ID(), 'author' );
						if ( $authors && ! is_wp_error( $authors ) ) :
							?>
							<p><strong><?php esc_html_e('Author:','books-management');?></strong>
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
	    else :
	        echo '<p>' . esc_html__( 'No books found.', 'books-management' ) . '</p>';
	    endif;
	    
	    $output = ob_get_clean();

	    $response = array(
		    'html' => $output
		);

	    wp_send_json_success( $response );
	    wp_die();
}

function handle_book_search_ajax_request() {

    if ( ! isset( $_POST['book_filter_nonce_field'] ) || ! wp_verify_nonce( $_POST['book_filter_nonce_field'], 'book_filter_nonce' ) ) {
        wp_send_json_error( 'Nonce verification failed.', 403 );
    }

    $search_text = isset( $_POST['searchText'] ) ? sanitize_text_field( $_POST['searchText'] ) : '';

    $args = array(
        'post_type' => 'book',
        'posts_per_page' => -1,
        's' => $search_text
    );
    $books_query = new WP_Query( $args );

    ob_start();
    if ( $books_query->have_posts() ) :
        while ( $books_query->have_posts() ) : $books_query->the_post();
        	$image_url = BM_PLUGIN_URL . 'public/images/book-placeholder.png';
            ?>
           <div <?php post_class('book-item'); ?>>
				<?php if ( has_post_thumbnail() ) { ?>
                    	<div class="book-thumbnail"><?php the_post_thumbnail(); ?></div>
                	<?php }else{?>
                		<div class="book-thumbnail"><img src="<?php echo esc_url($image_url); ?>"></div>
                	<?php }?>
				<h4 class="book-title"><a href="<?php echo esc_url( get_the_permalink());?>"><?php echo esc_html(get_the_title(),'books-management'); ?></a></h4>
				<div class="book-details">
					<p><strong><?php esc_html_e('Publication Date:','books-management');?></strong> <?php echo get_the_date(); ?></p>
					<?php
					$authors = get_the_terms( get_the_ID(), 'author' );
					if ( $authors && ! is_wp_error( $authors ) ) :
						?>
						<p><strong><?php esc_html_e('Author:','books-management');?></strong>
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
    else :
        echo '<p>' . esc_html__( 'No books found.', 'books-management' ) . '</p>';
    endif;
    $output = ob_get_clean();

    $response = array(
	    'html' => $output
	);

    wp_send_json_success( $response );
    wp_die();
}


/*
	Ajax call back function to load more books.
*/
function load_more_books() {

    $page = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $offset = ($page - 1) * 10;

    $args = array(
        'post_type'      => 'book',
        'posts_per_page' => 10,
        'offset'         => $offset,
    );

    $books_query = new WP_Query( $args );
    ob_start();
    if ( $books_query->have_posts() ) :
        while ( $books_query->have_posts() ) :
            $books_query->the_post();
            ?>
             <div class="book-item">
				<div class="book-thumbnail"><?php the_post_thumbnail(); ?></div>
				<h4 class="book-title"><a href="<?php echo esc_url( get_the_permalink());?>"><?php echo esc_html(get_the_title(),'books-management'); ?></a></h4>
				<div class="book-details">
					<p><strong><?php esc_html_e('Publication Date:','books-management');?></strong> <?php echo get_the_date(); ?></p>
					<?php
					$authors = get_the_terms( get_the_ID(), 'author' );
					if ( $authors && ! is_wp_error( $authors ) ) :
						?>
						<p><strong<?php esc_html_e('Author:','books-management');?></strong>
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
        $reached_end = $books_query->max_num_pages <= 1;
    else :
    	$reached_end = true;
    endif;
    $output = ob_get_clean();
    $response = array(
	    'html' => $output,
	    'reached_end' => $reached_end
	);

	wp_send_json_success( $response );
    wp_die();
}


}