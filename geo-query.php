<?php
/**
 * Plugin Name:       	Geo Query
 * Description:       	Modify the WP_Query to support the geo_query parameter. Uses the Haversine SQL implementation by Ollie Jones.
 * Plugin URI:        	https://github.com/birgire/geo-query
 * GitHub Plugin URI: 	https://github.com/birgire/geo-query.git
 * Author:	      		Birgir Erlendsson (birgire)
 * Version:           	0.1.0
 * Licence:           	MIT
 */

namespace Birgir\Geo;

/**
 * Init
 */

add_action( 'init', function()
{    
    // Load classes	
    if ( file_exists( __DIR__ . '/vendor/autoload.php' ) )
    {
        // Composer autoload		
        require __DIR__ . '/vendor/autoload.php';
    }
    else
    {
        // Fallback for those who don't use Composer		
        require_once  __DIR__ . '/src/GeoQueryAbstract.php';
        require_once  __DIR__ . '/src/GeoQueryInterface.php';
        require_once  __DIR__ . '/src/GeoQueryContext.php';
        require_once  __DIR__ . '/src/GeoQueryHaversine.php';
        require_once  __DIR__ . '/src/GeoQueryUserHaversine.php';
        require_once  __DIR__ . '/src/GeoQueryHaversineOptimized.php';
    }

    if( class_exists( __NAMESPACE__ . '\\GeoQueryContext' ) )
    {
        // Active		
     	$geo_query_context = new GeoQueryContext();
        $geo_query_context->setup( $GLOBALS['wpdb'] )->activate();
    }

});

