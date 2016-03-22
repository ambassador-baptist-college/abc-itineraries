<?php
/**
 * The template for displaying archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each one. For example, tag.php (Tag archives),
 * category.php (Category archives), author.php (Author archives), etc.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

        <?php if ( have_posts() ) : ?>

            <header class="page-header">
                <h1 class="page-title">All Meetings</h1>
                <?php
                    the_archive_description( '<div class="taxonomy-description">', '</div>' );
                ?>
            </header><!-- .page-header -->

            <table>
                <thead>
                    <tr>
                        <td class="date">Date</td>
                        <td class="church">Church</td>
                        <td class="location">Location</td>
                    </tr>
                </thead>
            <?php
            // Start the Loop.
            while ( have_posts() ) : the_post(); ?>
                <tr id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <td class="date">
                       <?php
                        // format
                        $begin_date = DateTime::createFromFormat( 'Y-m-d', get_field( 'begin_date' ) );
                        if ( get_field( 'end_date' ) ) {
                            $end_date = DateTime::createFromFormat( 'Y-m-d', get_field( 'end_date' ) );
                        }

                        // output dates
                        if ( get_field( 'end_date' ) ) {
                            echo $begin_date->format( 'l, F j' ) . '&ndash;' . $end_date->format( 'l, F j, Y' );
                        } else {
                            echo $begin_date->format( 'l, F j, Y' );
                        }

                        // time
                        if ( get_field( 'am_pm' ) ) {
                            echo ' ';
                            the_field( 'am_pm' );
                        }

                        // group
                        $terms = get_the_terms( get_the_ID(), 'group-name' );
                        if ( $terms ) {
                            $terms_output = NULL;
                            foreach ( $terms as $term ) {
                                $terms_output .= sprintf(
                                    '<a href="%1$s" title="%2$s">%2$s</a>, ',
                                    get_term_link( $term->term_id ),
                                    $term->name
                                );
                            }
                            echo rtrim( '<br/>' . $terms_output, ', ' );
                        }
                        ?>
                    </td>
                    <td class="church">
                        <?php
                        // church name
                        the_title();

                        // pastor
                        if ( get_field( 'pastor_name' ) ) {
                            echo '<br/><span class="pastor">' . get_field( 'pastor_name' ) . '</span>';
                        }
                        ?>
                    </td>
                    <td class="location">
                       <?php
                        // address 1
                        if ( get_field( 'address_1' ) ) {
                            the_field( 'address_1' );
                            echo '<br/>';
                        }

                        // address 2
                        if ( get_field( 'address_2' ) ) {
                            the_field( 'address_2' );
                            echo '<br/>';
                        }

                        // city
                        if ( get_field( 'city' ) ) {
                            the_field( 'city' );
                            echo ', ';
                        }
                        // state
                        if ( get_field( 'state' ) ) {
                            the_field( 'state' );
                            echo ' ';
                        }

                        // zip
                        if ( get_field( 'zip' ) ) {
                            the_field( 'zip' );
                            echo '<br/>';
                        }

                        // phone
                        if ( get_field( 'phone' ) ) {
                            echo '<a href="tel:' . str_replace( '-', '', get_field( 'phone' ) ) . '">' . get_field( 'phone' ) . '</a>';
                        }
                        ?>
                    </td>
                </tr><!-- #post-## -->

            <?php
            // End the loop.
            endwhile;

            // Previous/next page navigation.
            the_posts_pagination( array(
                'prev_text'          => __( 'Previous page', 'twentysixteen' ),
                'next_text'          => __( 'Next page', 'twentysixteen' ),
                'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'twentysixteen' ) . ' </span>',
            ) ); ?>

            </table>

        <?php
        // If no content, include the "No posts found" template.
        else :
            get_template_part( 'template-parts/content', 'none' );

        endif;
        ?>

        </main><!-- .site-main -->
    </div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
