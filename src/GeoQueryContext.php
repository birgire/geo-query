<?php

namespace Birgir\Geo;

/**
 * The Geo Query class
 *
 * @since 0.0.1
 */


class GeoQueryContext
{

   /**
    * Setup
    *
    * @since 0.0.1
    *
    * @param  wpdb $db
    * @return $this
    */

    public function setup( wpdb $db )
    {
        $this->db = $db;
        return $this;
    }   

   
   /**
    * Add the posts_clauses filter
    *
    * @since 0.0.1
    *
    * @return void
    */

    public function activate()
    {
     	add_filter( 'posts_clauses', array( $this, 'posts_clauses' ), 99, 2 );
        return $this;
    }


   /**
    * Modify the SQL for the fields, orderby, groupby, join and where clauses, according to the given context ( algorithm ).
    *
    * @since 0.0.1
    *
    * @param  array    $clauses   Array of clauses parts
    * @param  WP_Query $q         An instance of WP_Query
    * @return string $clauses     Array of clauses parts
    */

    public function posts_clauses( $clauses, \WP_Query $q )
    {
        // Get user input:
     	$geo_query = $q->get( 'geo_query' );

        if( is_array( $geo_query ) && ! empty( $geo_query ) )
        {
            // Run this filter callback only once:
            remove_filter( current_filter(), array( $this, __FUNCTION__ ) );

            // Default implementation:
            $class = __NAMESPACE__ . '\\GeoQueryHaversine';

            // Check if the user wants another implementation:
            if( ! empty( $geo_query['context'] ) )
                $class = preg_replace( '/[^a-z0-9\\\]+/i', '', $geo_query['context'] );

            // Create an implementation instance, if the class exists and implements the GeoQueryInterface interface:
            if( class_exists( $class ) && in_array( __NAMESPACE__ . '\\GeoQueryInterface', class_implements( $class ) ) )
            {
                $g = new $class;
                $g->setup( $this->db, $geo_query );
                $clauses = $g->algorithm( $clauses );
            }
        }
        return $clauses;
    }

} // end class

