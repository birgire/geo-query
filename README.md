WordPress plugin: Geo Query
=================

[![Build Status](https://travis-ci.org/birgire/geo-query.svg?branch=master)](https://travis-ci.org/birgire/geo-query)
[![GitHub license](https://img.shields.io/github/license/birgire/geo-query.svg)](https://github.com/birgire/geo-query/blob/master/LICENCE)
[![Packagist](https://img.shields.io/packagist/v/birgir/geo-query.svg)](https://packagist.org/packages/birgir/geo-query)


### Description

This plugin adds a support for the `geo_query` part of the `WP_Query` and `WP_User_Query`.

Supports geo data stored in post/user meta or in a custom table.

It uses the Haversine SQL implementation by Ollie Jones (see [here](http://www.plumislandmedia.net/mysql/haversine-mysql-nearest-loc/)).

The plugin works on PHP 5.3+.

It supports the GitHub Updater.

Activate the plugin and you can use the `geo_query` parameter in all your `WP_Query` and `WP_User_Query` queries.

Few examples are here below, e.g. for the Rest API.


### Installation

Upload the plugin to the plugin folder and activate it.

To install dependencies with Composer (not required):

    composer install

or

    php composer.phar install
	
within our folder. See here for more information on how to install Composer.

Then play with the example below, in your theme or in a plugin.

Have fun ;-)

### Example - Basic `WP_Query` usage:

Here's an example of the default input parameters of the `geo_query` part:

    $args = [
        'post_type'           => 'post',    
        'posts_per_page'      => 10,
        'ignore_sticky_posts' => true,
        'orderby'             => [ 'title' => 'DESC' ],
        'geo_query'           => [
            'lat'                =>  64,                                // Latitude point
            'lng'                =>  -22,                               // Longitude point
            'lat_meta_key'       =>  'geo_lat',                         // Meta-key for the latitude data
            'lng_meta_key'       =>  'geo_lng',                         // Meta-key for the longitude data 
            'radius'             =>  150,                               // Find locations within a given radius (km)
            'order'              =>  'DESC',                            // Order by distance
            'distance_unit'      =>  111.045,                           // Default distance unit (km per degree). Use 69.0 for statute miles per degree.
            'context'            => '\\Birgir\\Geo\\GeoQueryHaversine', // Default implementation, you can use your own here instead.
        ],
    ];
    $query = new WP_Query( $args );

### Example - Rest API usage:

Here's a modified example from @florianweich:

	add_filter( 'rest_query_vars', function ( $valid_vars ) {
		return array_merge( $valid_vars, [ 'geo_location' ] );
	} );

	add_filter( 'rest_post_query', function( $args, $request ) {
		$geo = json_decode( $request->get_param( 'geo_location' ) );
		if ( isset( $geo->lat, $geo->lng ) ) {
			$args['geo_query'] = [
				'lat'                =>  (float) $geo->lat,
				'lng'                =>  (float) $geo->lng,
				'lat_meta_key'       =>  'geo_lat',
				'lng_meta_key'       =>  'geo_lng',
				'radius'             =>  ($geo->radius) ? (float) $geo->radius : 50,
			];
		}
		return $args;
	}, 10, 2 );

Test it with e.g.:

	https://example.com/wp-json/wp/v2/posts?geo_location={"lat":"64.128288","lng":"-21.827774","radius":"50"}

One can use `rest_{custom-post-type-slug}_query` filter for a custom post type.

### Example - Basic `WP_User_Query` usage:

Here's an example from @acobster:

    $args = [
      'role'      => 'subscriber',
      'geo_query' => [
        'lat'          => 47.236,
        'lng'          => -122.435,
        'lat_meta_key' => 'geo_lat',
        'lng_meta_key' => 'geo_lng',
        'radius'       => 1,
        'context'      => '\\Birgir\\Geo\\GeoQueryUserHaversine',
      ],
    ];

    $query = new WP_User_Query( $args );

### Example - Basic `WP_Query` usage for fetching lat/lng data from a custom table with Haversine formula:

	$args = array(
	   	'post_type'           => 'post',    
	   	'posts_per_page'      => 10,
	   	'ignore_sticky_posts' => true,
	   	'orderby'             => array( 'title' => 'DESC' ),		    
		'geo_query' => array(
			'table'         => 'custom_table', // Table name for the geo custom table.
			'pid_col'       => 'pid',          // Column name for the post ID data
			'lat_col'       => 'lat',          // Column name for the latitude data
			'lng_col'       => 'lng',          // Column name for the longitude data 
			'lat'           => 64.0,           // Latitude point
			'lng'           => -22.0,          // Longitude point
			'radius'        => 1,              // Find locations within a given radius (km)
			'order'         => 'DESC',         // Order by distance
			'distance_unit' => 111.045,        // Default distance unit (km per degree). Use 69.0 for statute miles per degree.
			'context'       => '\\Birgir\\Geo\\GeoQueryPostCustomTableHaversine', // Custom table implementation, you can use your own here instead.
		),
	);

	$query = new WP_Query( $args );

Check the unit test method `test_custom_table_in_wp_query()` as a more detailed example.

### Notes on the parameters:

 - The plugin assumes we store the latitudes and longitudes as custom fields ( post meta), so we need to tell the query about meta keys with the `'lat_meta_key'` and `'lng_meta_key'` parameters.

 - Skipping the `'radius'` parameter means that no distance filtering will take place.

 - If we use the `'order'` parameter within the `'geo_query'`, then it will be prepended to the native `'orderby'` parameter.

 - The `'distance_unit'` parameter should be `69.0` for distance in statute miles, else `111.045` for distance in kilometers.

 - If we want to use the optimized Haversine version by Ollie Jones, we use:
        
         'context' => '\\Birgir\\Geo\\GeoQueryHaversineOptimized'

   Notice that on our current plugin setup (i.e. fetching data from the LONGTEXT post meta fields) this isn't more performant than the default `GeoQueryHaversine` class.
   
   A future work could be to use a custom table with indexes, for the optimization to work.


 - If we create our own implementation of the Haversine formula, for example the `GeoQueryCustom` class, we just have to make sure it implements the `GeoQueryInterface` interface:

         'context' => 'GeoQueryCustom'

### Feedback

Any suggestions are welcomed.

### Changelog
0.2.1 (2023-07-26)
- Fix deprecated notice in PHP 8.1 #24

0.2.0 (2020-04-25)
- Support for fetching points from a custom table and doing Haversine formula in WP_Query.

0.1.1 (2019-01-23)
- Fixed #14. Fixed the user query ordering. Props @baden03

0.1.0 (2018-08-06)
- Added support for user queries. Props @acobster
- Fixed Travis issue. Travis runs successfully for PHP 7.2, 7.1,7,5.6 when installed via Composer and also 5.4 when installed without Composer. Skip the 5.4 Composer check for now.

0.0.7 (2018-06-27)
- Fixed #10. Use ^1.0.0 for composer installer. Props @wujekbogdan

0.0.6 (2017-11-16)
- Fixed #6. Support floating point radius. Props @wujekbogdan
- Added integration tests.

0.0.5 (2017-02-26)

- Added fallback for those that don't use Composer
- Removed the vendor directory

0.0.4 (2017-02-26)

- Fixed #4. Props @billzhong .

0.0.3 (2015-04-29)

- Fixed #2. Fixed a typo. Props @Ben764 and @con322.

0.0.2 (2015-03-10)

- Added: Support for the GitHub Updater.
- Updated: README.md
- Changed: Use distance_value instead of distance_in_km in SQL, since we can use miles by changing the distance_unit parameter.

0.0.1 - Init
