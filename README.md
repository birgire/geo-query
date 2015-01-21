WordPress: Geo Location Query
=================

###Description

This plugin adds a support for the `geo_query` part of the `WP_query`.

It uses the Haversine SQL optimization by Ollie Jones (see [here](http://www.plumislandmedia.net/mysql/haversine-mysql-nearest-loc/)).

The plugin works on PHP 5.3+.

###Example:

Here's an example of the seven supported input parameters of the geo query part:

    $args = array(
        'post_type'          => 'post',    
        'posts_per_page'     => 10,
	'orderby'            => array( 'title' => 'DESC' ),
        'geo_query' => array(
            'distance_unit'      =>  111.045,       // Default distance unit (km per degree) 
            'lat_meta_key'       =>  'my_lat',      // Meta-key for the latitude data
            'lng_meta_key'       =>  'my_lng',      // Meta-key for the longitude data 
            'lat'                =>  64,            // Latitude point
            'lng'                =>  22,            // Longitude point
            'radius'             =>  150,           // Find locations within a given radius (km)
            'order'              =>  'DESC',        // Order by distance
        ),

    );
    $query = new WP_Query( $args );

If we: 

 - skip the `'radius'` parameter, then there's no distance filtering taking place.

 - skip the geo `'order'` parameter, then the native `'orderby'` parameter is used.

 - use the geo `'order'` parameter, then it will be pre-pended to the native `'orderby'` parameter.


###Changelog

0.0.1 - Init
