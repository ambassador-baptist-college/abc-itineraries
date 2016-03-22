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
            <?php
            wp_enqueue_script( 'google-map-api' );
            wp_enqueue_script( 'abc-itineraries-map' );
            wp_enqueue_style( 'abc-itineraries-map' );
            ?>

            <header class="page-header">
                <h1 class="page-title">All Meetings</h1>
                <?php
                    the_archive_description( '<div class="taxonomy-description">', '</div>' );
                ?>
            </header><!-- .page-header -->

            <div id="map"></div>

            <table id="itinerary">
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
                        // variables
                        $begin_date     = get_field( 'begin_date' );
                        $end_date       = get_field( 'end_date' );
                        $am_pm          = get_field( 'am_pm' );
                        $church_name    = get_the_title();
                        $pastor_name    = get_field( 'pastor_name' );
                        $address_1      = get_field( 'address_1' );
                        $address_2      = get_field( 'address_2' );
                        $city           = get_field( 'city' );
                        $state          = get_field( 'state' );
                        $zip            = get_field( 'zip' );
                        $phone          = get_field( 'phone' );
                        $location       = get_field( 'location' );

                        // format
                        $begin_date_object = DateTime::createFromFormat( 'Y-m-d', $begin_date );
                        if ( $end_date ) {
                            $end_date_object = DateTime::createFromFormat( 'Y-m-d', $end_date );
                        }

                        // output dates
                        if ( $end_date ) {
                            echo '<meta itemprop="startDate" content="' . $begin_date_object->format( 'Y-m-d\T12:00' ) . '">' . $begin_date_object->format( 'l, F j' ) . '&ndash;<br/>' . '<meta itemprop="endDate" content="' . $end_date_object->format( 'Y-m-d\T13:00' ) . '">' . $end_date_object->format( 'l, F j, Y' );
                        } else {
                            echo '<meta itemprop="startDate" content="' . $begin_date_object->format( 'Y-m-d\T12:00' ) . '">' . $begin_date_object->format( 'l, F j, Y' );
                        }
                        echo '<meta itemprop="duration" content="0000-00-00T01:00">';

                        // time
                        if ( $am_pm ) {
                            echo ' <span class="meridian">' . $am_pm[0] . '</span>';
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
                            echo '<br/><span class="groupName">' . rtrim( $terms_output, ', ' ) . '</span>';
                        }
                        ?>
                        <script type="application/ld+json">[{"@context":"http://schema.org","@type":"MusicEvent","name":"Ambassador Baptist College","startDate":"<?php the_field( 'begin_date' ); ?>","location":{"@type":"Place","name":"<?php the_title() ?>","address":{"@type":"PostalAddress"<?php
                                    if ( $address_1 ) {
                                        echo ',"streetAddress":"' . $address_1;
                                        if ($address_2 ) {
                                            echo $address_2;
                                        }
                                        echo '"';
                                    }
                                    if ( $city ) {
                                        echo ',"addressLocality":"' . $city . '"';
                                    }
                                    if ( $state ) {
                                        echo ',"addressRegion":"' . $state . '"';
                                    }
                                    if ( $zip ) {
                                        echo ',"postalCode":"' . $zip . '"';
                                    } ?>}}}]</script>
                    </td>
                    <td class="church">
                        <?php
                        // church name
                        echo '<span itemprop="name">' . $church_name . '</span>';

                        // pastor
                        if ( $pastor_name ) {
                            echo '<br/><span class="pastor">' . $pastor_name . '</span>';
                        }
                        ?>
                    </td>
                    <td class="location">
                       <address>
                       <?php
                        // address 1
                        if ( $address_1 ) {
                            echo '<span itemprop="streetAddress">' . $address_1 . '</span><br/>';
                        }

                        // address 2
                        if ( $address_2 ) {
                            echo $address_2 . '<br/>';
                        }

                        // city
                        if ( $city ) {
                            echo '<span itemprop="addressLocality">' . $city . '</span>, ';
                        }
                        // state
                        if ( $state ) {
                            echo '<span itemprop="addressRegion">' .$state . '</span> ';
                        }

                        // zip
                        if ( $zip ) {
                            echo '<span itemprop="postalCode">' .$zip . '</span><br/>';
                        }

                        // phone
                        if ( $phone ) {
                            echo '<a itemprop="telephone" href="tel:' . str_replace( '-', '', $phone ) . '">' . $phone . '</a>';
                        }

                        // location
                        if ( $location ) {
                            echo '<span class="hidden" itemprop="latitude">' . $location['lat'] . '</span><span class="hidden" itemprop="longitude">' . $location['lng'] . '</span>';
                        }
                        ?>
                        </address>
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
