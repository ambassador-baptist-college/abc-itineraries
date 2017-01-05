<?php
wp_enqueue_script( 'google-map-api' );
wp_enqueue_script( 'abc-itineraries-map' );
wp_enqueue_style( 'abc-itineraries-map' );
?>

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
                echo '<meta itemprop="startDate" content="' . $begin_date_object->format( 'Y-m-d\T12:00' ) . '">' . $begin_date_object->format( 'l, F j, Y' ) . '<meta itemprop="endDate" content="' . $begin_date_object->format( 'Y-m-d\T13:00' ) . '">';
            }
            echo '<meta itemprop="duration" content="0000-00-00T01:00">';

            // time
            if ( $am_pm ) {
                echo ' <span class="meridian">' . implode( ', ', $am_pm ) . '</span>';
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
            <?php
            if ( is_user_logged_in() ) {
                echo '<br/><a href="' . get_edit_post_link() . '">Edit</a>';
            }
            ?>
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
?>

</table>
