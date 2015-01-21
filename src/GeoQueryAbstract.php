<?php

namespace Birgir\Geo;

/**
 * The Geo Query Abstract class
 *
 * @since 0.0.1
 */


abstract class GeoQueryAbstract
{
    
   /**
    * Modify the SQL for the fields clause
    *
    * @since 0.0.1
    *
    * @param  string $fields
    * @return string $fields
    */

    abstract protected function posts_fields( $fields );
    
    
   /**
    * Modify the SQL for the where clause
    *
    * @since 0.0.1
    *
    * @param  string $where
    * @return string $where
    */

    abstract protected function posts_where( $where );


   /**
    * Modify the SQL for the groupby clause
    *
    * @since 0.0.1
    *
    * @param  string $groupby
    * @return string $groupby
    */
    
    abstract protected function posts_groupby( $where );


   /**
    * Modify the SQL for the join clause
    *
    * @since 0.0.1
    *
    * @param  string $join
    * @return string $join
    */

    abstract protected function posts_join( $join );


   /**
    * Modify the SQL for the orderby clause
    *
    * @since 0.0.1
    *
    * @param  string $orderby
    * @return string $orderby
    */

    abstract protected function posts_orderby( $orderby );

} // end class

