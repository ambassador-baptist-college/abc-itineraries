// initialize map
var map;
function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: {
            lat: 35.3192571,
            lng:  -81.6586981
        },
        zoom: 6
    });
}

// get all churches and add markers
jQuery(document).ready(function() {
    // get data from table
    var locations = [];
    jQuery('table#itinerary tr.meeting').each(function(){
        var church = {};
        church.ID           = jQuery(this).attr('id');
        church.churchName   = jQuery(this).find('[itemprop="name"]').html();
        church.pastorName   = jQuery(this).find('.pastor').html();
        church.address1     = jQuery(this).find('[itemprop="streetAddress"]').html();
        church.city         = jQuery(this).find('[itemprop="addressLocality"]').html();
        church.state        = jQuery(this).find('[itemprop="addressRegion"]').html();
        church.zip          = jQuery(this).find('[itemprop="postalCode"]').html();
        church.telephone    = jQuery(this).find('[itemprop="telephone"]').html();
        church.beginDate    = jQuery(this).find('[itemprop="startDate"]').attr('content');
        church.meridian     = jQuery(this).find('.meridian').html();
        church.groupName    = jQuery(this).find('span.groupName').html();
        church.latitude     = jQuery(this).find('[itemprop="latitude"]').html();
        church.longitude    = jQuery(this).find('[itemprop="longitude"]').html();
        church.groupName    = jQuery(this).find('.groupName').html();

        locations.push(church);
    });

    // iterate over array and add markers to map
    var infoWindow = new google.maps.InfoWindow({}),
        LatLngList = new Array();
    for (var i = 0; i < locations.length; i++) {
        var thisLocation = locations[i];

        // add pins
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(thisLocation.latitude, thisLocation.longitude),
            title: serviceDate(thisLocation.beginDate) + ': ' + thisLocation.churchName + ' in ' + thisLocation.city + ', ' + thisLocation.state + ' ' + thisLocation.zip,
            map: map
        });

        // add to latLngList
        if (thisLocation.latitude && thisLocation.longitude) {
            LatLngList.push(new google.maps.LatLng(thisLocation.latitude, thisLocation.longitude));
        }

        // add click listener
        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                var thisLocation = locations[i];

                // set infoWindow content
                var infoWindowContent = '<h1>' + thisLocation.churchName + '</h1><h2>' + serviceDate(thisLocation.beginDate);
                infoWindowContent += thisLocation.meridian ? ' ' + thisLocation.meridian : '';
                infoWindowContent += ': ' + thisLocation.groupName + '</h2><p>';
                infoWindowContent += thisLocation.pastorName ? thisLocation.pastorName + '<br/>' : '';
                infoWindowContent += thisLocation.address1 ? thisLocation.address1 + '<br/>' : '';
                infoWindowContent += thisLocation.city ? thisLocation.city + ', ' : '';
                infoWindowContent += thisLocation.state ? thisLocation.state + ' ' : '';
                infoWindowContent += thisLocation.zip ? thisLocation.zip + '<br/>' : '';
                infoWindowContent += thisLocation.telephone ? thisLocation.telephone : '';
                infoWindowContent += '</p>';

                // display infoWindow
                infoWindow.setContent(infoWindowContent);
                infoWindow.open(map, marker);

                // style table
                console.info(thisLocation.ID);
                jQuery('.meeting.highlight').removeClass('highlight');
                jQuery('#' + thisLocation.ID).addClass('highlight');
            }
        })(marker, i));
    }

    // fit to bounds
    var bounds = new google.maps.LatLngBounds();
    for (var j in LatLngList) {
        bounds.extend(LatLngList[j]);
    }
    map.fitBounds(bounds);

    // date helper function
    function serviceDate(beginDate) {
        var beginDate = new Date(beginDate),
            serviceMonth = beginDate.getMonth() + 1,
            serviceDay = beginDate.getDate(),
            dateString = serviceMonth + '/' + serviceDay;

        return dateString;
    }
});
