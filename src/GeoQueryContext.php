<?php

namespace Birgir\Geo;

/**
 * The Geo Query class
 *
 * @since 0.1.0
 */


class GeoQueryContext
{
    private $db;

   /**
    * Setup
    *
    * @since 0.0.1
    *
    * @param  wpdb $db
    * @return $this
    */

    public function setup( \wpdb $db )
    {
        $this->db = $db;
        return $this;
    }   

   
   /**
    * Add posts_clauses and pre_user_query filters.
    *
    * @since 0.0.1
	* @since 0.1.0 Adds the pre_user_query filter.
    *
    * @return void
    */

    public function activate()
    {
        add_filter( 'posts_clauses', array( $this, 'posts_clauses' ), 99, 2 );
        add_action( 'pre_user_query', array( $this, 'pre_user_query' ), 99, 2 );
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

            // Filter the query clauses:
            $clauses = $this->get_query_clauses( $geo_query, $clauses );
        }
        return $clauses;
    }

    public function pre_user_query( \WP_User_Query $q ) {
        // get user input
        $geo_query = $q->get('geo_query');

        if (is_array($geo_query) && ! empty( $geo_query )) {

            // get the existing clauses
            $clauses = array(
                'fields'  => $q->query_fields,
                'from'    => $q->query_from,
                'where'   => $q->query_where,
                'orderby' => $q->query_orderby,
            );

            // override default context explictly for a user query
            if (empty($geo_query['context'])) {
                $geo_query['context'] = __NAMESPACE__ . '\\GeoQueryUserHaversine';
            }

            // get new values for each clause
            $clauses = $this->get_query_clauses( $geo_query, $clauses );

            // replace clauses in the running query
            $q->query_fields  = $clauses['fields'];
            $q->query_from    = $clauses['from'];
            $q->query_where   = $clauses['where'];
            $q->query_orderby = $clauses['orderby'];
        }
    }

    protected function get_query_clauses( array $geo, array $clauses ) {
      // Default implementation:
      $class = __NAMESPACE__ . '\\GeoQueryHaversine';

      // Check if the user wants another implementation:
      if( ! empty( $geo['context'] ) )
          $class = preg_replace( '/[^a-z0-9\\\]+/i', '', $geo['context'] );

      // Create an implementation instance, if the class exists and implements the GeoQueryInterface interface:
      if( class_exists( $class ) && in_array( __NAMESPACE__ . '\\GeoQueryInterface', class_implements( $class ) ) )
      {
          $g = new $class;
          $g->setup( $this->db, $geo );
          $clauses = $g->algorithm( $clauses );
      }

      return $clauses;
    }

} // end class

