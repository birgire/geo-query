<?php

namespace Birgir\Geo;

/**
 * The Geo Query User Haversine class
 *
 * @uses the Ollie Jones Haversine SQL query: http://www.plumislandmedia.net/mysql/haversine-mysql-nearest-loc/
 *
 * @since 0.1.0
 */


class GeoQueryUserHaversine implements GeoQueryInterface
{
    private $db;
    private $lat;
    private $lng;
    private $lat_meta_key;
    private $lng_meta_key;
    private $distance_unit;
    private $radius;


   /**
    * Setup - let's implement the interface
    *
    * @since 0.1.0
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
            'lat_meta_key'       => 'lat',
            'lng_meta_key'       => 'lng',
            'lat'                => 0,
            'lng'                => 0,
            'radius'             => 0,
            'distance_unit'      => 111.045,
            'order'              => '',
            'context'            => '',
        );
        $geo_query = wp_parse_args( $geo_query, $default );

         // Sanitize the user input:
         $this->lat_meta_key  = sanitize_key( $geo_query['lat_meta_key'] );
         $this->lng_meta_key  = sanitize_key( $geo_query['lng_meta_key'] );
         $this->lat           = floatval( $geo_query['lat'] );
         $this->lng           = floatval( $geo_query['lng'] );
         $this->radius        = floatval( $geo_query['radius'] );
         $this->distance_unit = floatval( $geo_query['distance_unit'] );
         $this->order         = in_array( strtoupper( $geo_query['order'] ), array( 'ASC', 'DESC' ) ) ? strtoupper( $geo_query['order'] ) : '';
    }


   /**
    * Algorithm - let's implement the interface
    *
    * @since 0.1.0
    *
    * @param  array    $clauses
    * @return array    $clauses
    */

    public function algorithm( $clauses = array() )
    {
         // Modify SQL query parts:
         $clauses['fields']  = $this->users_fields(  $clauses['fields']  );
         $clauses['where']   = $this->users_where(   $clauses['where']   );
         $clauses['from']    = $this->users_from(    $clauses['from']    );

         // honor the orderby param
         $orderby = isset( $clauses['orderby'] ) ? $clauses['orderby'] : '';
         if( $this->order )
             $clauses['orderby'] = $this->users_orderby( $orderby );

        return $clauses;
    }


   /**
    * Modify the SQL for the fields clause
    *
    * @since 0.1.0
    *
    * @param  string $fields
    * @return string $fields
    */

    protected function users_fields( $fields )
    {
     	  $fields .= ", settings.distance_unit * DEGREES( ACOS(
                   COS( RADIANS( settings.latpoint ) )
                 * COS( RADIANS( mtlat.meta_value ) )
                 * COS( RADIANS( settings.longpoint ) - RADIANS( mtlng.meta_value ) )
                 + SIN( RADIANS( settings.latpoint ) )
                 * SIN( RADIANS( mtlat.meta_value )))) AS distance_value ";

        return $fields;
    }


   /**
    * Modify the SQL for the where clause
    *
    * @since 0.1.0
    *
    * @param  string $where
    * @return string $where
    */

    protected function users_where( $where )
    {
        // check for radius
        if( $this->radius > 0 ) {
            // ensure we don't mess up any existing HAVING clause
            if (strpos($where, ' HAVING ') !== false) {
                $where .= ' HAVING ';
            }

            // add the HAVING clause with the radius value
            $where .= $this->db->prepare(
                " HAVING distance_value <= %f ",
                $this->radius
            );
        }

        return $where;
    }


   /**
    * Modify the SQL for the HAVING clause
    *
    * @since 0.1.0
    *
    * @return string $having
    */

    protected function users_having()
    {
        return $this->db->prepare(
            " HAVING distance_value <= %f ",
            $this->radius
        );
    }


   /**
    * Modify the SQL for the FROM/JOIN clauses
    *
    * @since 0.1.0
    *
    * @param  string $from
    * @return string $from
    */

    protected function users_from( $from )
    {
     	$from .= $this->db->prepare(
            " INNER JOIN {$this->db->usermeta} AS mtlat ON ( {$this->db->users}.ID = mtlat.user_id AND mtlat.meta_key = '%s' ) ",
            $this->lat_meta_key
        );

        $from .= $this->db->prepare(
            " INNER JOIN {$this->db->usermeta} AS mtlng ON ( {$this->db->users}.ID = mtlng.user_id AND mtlng.meta_key = '%s' ) ",
            $this->lng_meta_key
        );

        $from .= $this->db->prepare(
            " INNER JOIN ( SELECT %f AS latpoint,  %f AS longpoint, %f AS distance_unit, %f AS radius ) AS settings ",
            $this->lat,
            $this->lng,
            $this->distance_unit,
            $this->radius
        );

        return $from;
    }


   /**
    * Modify the SQL for the orderby clause
    *
    * @since 0.1.0
    *
    * @param  string $orderby
    * @return string $orderby
    */

    protected function users_orderby( $orderby )
    {
     	return ' distance_value ' . $this->order . ', ' . $orderby;
    }


} // end class

