<?php

namespace Birgir\Geo;

/**
 * The Geo Query Haversine Optimized class
 *
 * @uses the Ollie Jones Haversine SQL optimization: http://www.plumislandmedia.net/mysql/haversine-mysql-nearest-loc/
 *
 * @since 0.0.1
 */


class GeoQueryHaversineOptimized extends GeoQueryHaversine implements IGeoQueryInterface
{
    
   /**
    * Modify the SQL for the where clause
    *
    * @since 0.0.1
    *
    * @param  string $where
    * @return string $where
    */

    public function posts_where( $where )
    {
            $where .= "
                 AND mtlat.meta_value
                     BETWEEN settings.latpoint
                      - ( settings.radius / settings.distance_unit )
                 AND settings.latpoint
                      + ( settings.radius / settings.distance_unit )
                 AND mtlng.meta_value
                     BETWEEN settings.longpoint
                      - ( settings.radius / ( settings.distance_unit * COS( RADIANS( settings.latpoint ) ) ) )
                 AND settings.longpoint
                      + ( settings.radius / ( settings.distance_unit * COS( RADIANS( settings.latpoint ) ) ) )
             ";

        return $where;
    }


} // end class

