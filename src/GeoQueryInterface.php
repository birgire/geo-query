<?php

namespace Birgir\Geo;

/**
 * The Geo Query Interface
 *
 * @since 0.0.1
 */


interface GeoQueryInterface
{
    
   /**
    * Modify the SQL for the fields clause
    *
    * @since 0.0.1
    *
    * @param  array $clauses
    * @return array $clauses
    */

    public function algorithm( $clauses = array() );


   /**
    * Setup
    *
    * @since 0.0.1
    *
    * @param  wpdb     $db         An instance of the wpdb class
    * @param  array    $geo_query  The geo_query part of the WP_Query
    * @return GeoQuery $this       An instance of the GeoQuery class
    */          
          
    public function setup( \wpdb $db, $geo_query = array() );


} // end class

