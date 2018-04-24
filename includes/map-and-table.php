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
while ( have_posts() ) {
    the_post();
    include 'single-meeting.php';
}
?>

</table>
