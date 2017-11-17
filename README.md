WordPress plugin: Geo Query
=================

[![Build Status](https://travis-ci.org/birgire/geo-query.svg?branch=master)](https://travis-ci.org/birgire/geo-query)

[![GitHub license](https://img.shields.io/github/license/birgire/geo-query.svg)](https://github.com/birgire/geo-query/blob/master/LICENCE)


### Description

This plugin adds a support for the `geo_query` part of the `WP_query`.

It uses the Haversine SQL implementation by Ollie Jones (see [here](http://www.plumislandmedia.net/mysql/haversine-mysql-nearest-loc/)).

The plugin works on PHP 5.3+.

It supports the GitHub Updater.

Activate the plugin and you can use the `geo_query` parameter in all your `WP_Query` queries.

### Installation

Upload the plugin to the plugin folder and activate it.

To install dependencies with Composer (not required):

    composer install

or

    php composer.phar install
	
within our folder. See here for more information on how to install Composer.

Then play with the example below, in your theme or in a plugin.

Have fun ;-)

### Example:

Here's an example of the supported input parameters of the `geo_query` part:

    $args = array(
        'post_type'           => 'post',    
        'posts_per_page'      => 10,
        'ignore_sticky_posts' => true,
        'orderby'             => array( 'title' => 'DESC' ),
        'geo_query' => array(
            'lat'                =>  64,                                // Latitude point
            'lng'                =>  -22,                               // Longitude point
            'lat_meta_key'       =>  'my_lat',                          // Meta-key for the latitude data
            'lng_meta_key'       =>  'my_lng',                          // Meta-key for the longitude data 
            'radius'             =>  150,                               // Find locations within a given radius (km)
            'order'              =>  'DESC',                            // Order by distance
            'distance_unit'      =>  111.045,                           // Default distance unit (km per degree). Use 69.0 for statute miles per degree.
            'context'            => '\\Birgir\\Geo\\GeoQueryHaversine', // Default implementation, you can use your own here instead.
        ),
    );
    $query = new WP_Query( $args );

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
