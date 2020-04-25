<?php

namespace Birgir\Geo;

/**
 * The Geo Query Haversine class for a custom table
 *
 * @uses the Ollie Jones Haversine SQL query: http://www.plumislandmedia.net/mysql/haversine-mysql-nearest-loc/
 *
 * @since 0.2.0
 */


class GeoQueryPostCustomTableHaversine extends GeoQueryAbstract implements GeoQueryInterface
{
    private $db;
    private $table;
    private $lat;
    private $lng;
    private $pid_col;
    private $lat_col;
    private $lng_col;
    private $distance_unit;
    private $radius;


   /**
    * Setup - let's implement the interface
    *
    * @since 0.2.0
    *
    * @param  wpdb     $db         An instance of the wpdb class
    * @param  array    $geo_query  The geo_query part of the WP_Query
    * @return GeoQuery $this       An instance of the GeoQuery class
    */
    
    public function setup( \wpdb $db, $geo_query = array() )
    {
     	$this->db = $db;

        // Default user input:
        $default = array(
			'table'         => 'custom_table',
            'pid_col'       => 'pid',
            'lat_col'       => 'lat',
            'lng_col'       => 'lng',
            'lat'           => 0,
            'lng'           => 0,
            'radius'        => 0,
            'distance_unit' => 111.045,
            'order'         => '',
            'context'       => '',
        );
        $geo_query = wp_parse_args( $geo_query, $default );

         // Sanitize the user input:
         $this->table         = sanitize_key( $geo_query['table'] );
         $this->lat_col       = sanitize_key( $geo_query['lat_col'] );
         $this->lng_col       = sanitize_key( $geo_query['lng_col'] );
         $this->pid_col       = sanitize_key( $geo_query['pid_col'] );
         $this->lat           = floatval( $geo_query['lat'] );
         $this->lng           = floatval( $geo_query['lng'] );
         $this->radius        = floatval( $geo_query['radius'] );
         $this->distance_unit = floatval( $geo_query['distance_unit'] );
         $this->order         = in_array( strtoupper( $geo_query['order'] ), array( 'ASC', 'DESC' ) ) ? strtoupper( $geo_query['order'] ) : '';
    }
    

   /**
    * Algorithm - let's implement the interface
    *
    * @since 0.2.0
    *
    * @param  array    $clauses  
    * @return array    $clauses
    */

    public function algorithm( $clauses = array() )
    {
     	 // Modify SQL query parts:
         $clauses['fields']  = $this->posts_fields(  $clauses['fields']  );
         $clauses['where']   = $this->posts_where(   $clauses['where']   );
         $clauses['join']    = $this->posts_join(    $clauses['join']    );

         if( $this->radius > 0 )
             $clauses['groupby'] = $this->posts_groupby( $clauses['groupby'] );

         if( $this->order )
             $clauses['orderby'] = $this->posts_orderby( $clauses['orderby'] );

        return $clauses;
    }


   /**
    * Modify the SQL for the fields clause
    *
    * @since 0.2.0
    *
    * @param  string $fields
    * @return string $fields
    */

    protected function posts_fields( $fields )
    {
     	$fields .= ", settings.distance_unit * DEGREES( ACOS( LEAST( 1.0, 
                   COS( RADIANS( settings.latpoint ) )
                 * COS( RADIANS( gqct.{$this->lat_col} ) )
                 * COS( RADIANS( settings.longpoint ) - RADIANS( gqct.{$this->lng_col} ) )
                 + SIN( RADIANS( settings.latpoint ) )
                 * SIN( RADIANS( gqct.{$this->lat_col} ))
				 ))) AS distance_value ";

        return $fields;
    }
    
    
   /**
    * Modify the SQL for the where clause
    *
    * @since 0.0.1
    *
    * @param  string $where
    * @return string $where
    */

    protected function posts_where( $where )
    {
        return $where;
    }


   /**
    * Modify the SQL for the groupby clause
    *
    * @since 0.2.0
    *
    * @param  string $groupby
    * @return string $groupby
    */
    
    protected function posts_groupby( $groupby )
    {
     	if( empty( $groupby ) )
            $groupby = " {$this->db->posts}.ID ";

        $groupby .= $this->db->prepare(
            " HAVING distance_value <= %f ",
            $this->radius
        );

        return $groupby;
    }


   /**
    * Modify the SQL for the join clause
    *
    * @since 0.2.0
    *
    * @param  string $join
    * @return string $join
    */

    protected function posts_join( $join )
    {
     	$join .= " INNER JOIN {$this->db->prefix}{$this->table} AS gqct ON ( {$this->db->posts}.ID = gqct.{$this->pid_col} ) ";

        $join .= $this->db->prepare(
            " INNER JOIN ( SELECT %f AS latpoint,  %f AS longpoint, %f AS distance_unit, %f AS radius ) AS settings ",
            $this->lat,
            $this->lng,
            $this->distance_unit,
            $this->radius
        );

        return $join;
    }


   /**
    * Modify the SQL for the orderby clause
    *
    * @since 0.2.0
    *
    * @param  string $orderby
    * @return string $orderby
    */

    protected function posts_orderby( $orderby )
    {
     	return ' distance_value ' . $this->order . ', ' . $orderby;
    }


} // end class

