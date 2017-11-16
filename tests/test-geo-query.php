<?php
/**
 * Class Test_Geo_Query
 *
 * @package Geo_Query
 */

/**
 * Test Geo Query
 */
class Test_Geo_Query extends WP_UnitTestCase {

	/**
	 * Tests fetching points within 1 km radius
	 *
	 */
	public function test_1_km_radius() {

		$p1 = self::factory()->post->create( array( 
			'post_title' => 'Reykjavik #1',
		) );
		add_post_meta( $p1, 'my_lat', '64' );
		add_post_meta( $p1, 'my_lng', '-22.001' );

		$p2 = self::factory()->post->create( array( 
			'post_title' => 'Reykjavik #2',
		) );
		add_post_meta( $p2, 'my_lat', '64' );
		add_post_meta( $p2, 'my_lng', '-22.101' );

		$p3 = self::factory()->post->create( array( 
			'post_title' => 'Akureyri',
		) );
		add_post_meta( $p3, 'my_lat', '65.6839' );
		add_post_meta( $p3, 'my_lng', '-18.1105' );

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
		        	'radius'             =>  1,                                 // Find locations within a given radius (km)
		        	'order'              =>  'DESC',                            // Order by distance
		        	'distance_unit'      =>  111.045,                           // Default distance unit (km per degree). Use 69.0 for statute miles per degree.
		        	'context'            => '\\Birgir\\Geo\\GeoQueryHaversine', // Default implementation, you can use your own here instead.
    			),
		);
	
		$query = new WP_Query( $args );

		$this->assertSame( 1, count( $query->posts ) );
		$this->assertSame( 'Reykjavik #1', $query->posts[0]->post_title );
	}

	/**
	 * Tests fetching points within a radius less than 1km
	 *
	 * @ticket 6
	 */
	public function test_radius_less_than_1_km() {

		$p1 = self::factory()->post->create( array( 
			'post_title' => 'Reykjavik #1',
		) );
		add_post_meta( $p1, 'my_lat', '64' );
		add_post_meta( $p1, 'my_lng', '-22.001' );

		$p2 = self::factory()->post->create( array( 
			'post_title' => 'Reykjavik #2',
		) );
		add_post_meta( $p2, 'my_lat', '64' );
		add_post_meta( $p2, 'my_lng', '-22.101' );

		$p3 = self::factory()->post->create( array( 
			'post_title' => 'Akureyri',
		) );
		add_post_meta( $p3, 'my_lat', '65.6839' );
		add_post_meta( $p3, 'my_lng', '-18.1105' );

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
		        	'radius'             =>  0.54321,                               // Find locations within a given radius (km)
		        	'order'              =>  'DESC',                            // Order by distance
		        	'distance_unit'      =>  111.045,                           // Default distance unit (km per degree). Use 69.0 for statute miles per degree.
		        	'context'            => '\\Birgir\\Geo\\GeoQueryHaversine', // Default implementation, you can use your own here instead.
    			),
		);
	
		$query = new WP_Query( $args );

		$this->assertSame( 1, count( $query->posts ) );
		$this->assertSame( 'Reykjavik #1', $query->posts[0]->post_title );
	}

	/**
	 * Tests distance from Reykjavik - London
	 *
	 * @ticket 6
	 */
	public function test_distance_from_reykjavik_london() {

		$p1 = self::factory()->post->create( array( 
			'post_title' => 'London',
		) );
		add_post_meta( $p1, 'my_lat', '51.509865' );
		add_post_meta( $p1, 'my_lng', '-0.118092' );

		$args = array(
		    	'post_type'           => 'post',    
		    	'posts_per_page'      => 10,
		    	'ignore_sticky_posts' => true,
		    	'orderby'             => array( 'title' => 'DESC' ),		    
			'geo_query' => array(
				'lat'                =>  64.128288,                         // Latitude point
		  		'lng'                =>  -21.827774,                        // Longitude point
		        	'lat_meta_key'       =>  'my_lat',                          // Meta-key for the latitude data
		        	'lng_meta_key'       =>  'my_lng',                          // Meta-key for the longitude data 
		        	'radius'             =>  5000,                               // Find locations within a given radius (km)
		        	'order'              =>  'DESC',                            // Order by distance
		        	'distance_unit'      =>  111.045,                           // Default distance unit (km per degree). Use 69.0 for statute miles per degree.
		        	'context'            => '\\Birgir\\Geo\\GeoQueryHaversine', // Default implementation, you can use your own here instead.
    			),
		);
	
		$query = new WP_Query( $args );

		$this->assertSame( 1, count( $query->posts ) );
		$this->assertSame( 'London', $query->posts[0]->post_title );
		$this->assertSame( 1881 , (int) $query->posts[0]->distance_value );
	}
}
