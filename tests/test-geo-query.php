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

	/**
	 * Tests a search query.
	 *
	 * @ticket 9
	 */
	public function test_search_query() {
		// Arrange.

		$p1 = self::factory()->post->create( array( 
			'post_title' => 'London',
		) );
		add_post_meta( $p1, 'my_lat', '51.509865' );
		add_post_meta( $p1, 'my_lng', '-0.118092' );

		$p2 = self::factory()->post->create( array( 
			'post_title' => 'Reykjavik',
		) );
		add_post_meta( $p2, 'my_lat', '64' );
		add_post_meta( $p2, 'my_lng', '-22.101' );

		// Act.
		$args = array(
			's'                   => 'london', // Search.
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

		// Assert.
		$this->assertSame( 1, count( $query->posts ) );
		$this->assertSame( 'London', $query->posts[0]->post_title );
		$this->assertSame( 1881 , (int) $query->posts[0]->distance_value );
	}

	/**
	 * Tests a search query that should give empty result.
	 *
	 * @ticket 9
	 */
	public function test_search_query_should_empty_result() {
		// Arrange.
		$p1 = self::factory()->post->create( array( 
			'post_title' => 'London',
		) );
		add_post_meta( $p1, 'my_lat', '51.509865' );
		add_post_meta( $p1, 'my_lng', '-0.118092' );

		$p2 = self::factory()->post->create( array( 
			'post_title' => 'Reykjavik',
		) );
		add_post_meta( $p2, 'my_lat', '64' );
		add_post_meta( $p2, 'my_lng', '-22.101' );

		// Act.
		$args = array(
			's'                   => 'akureyri', // Search.
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

		// Assert.
		$this->assertEmpty( $query->posts );
	}

	/**
	 * Tests for applying a geo query on the search query with `pre_get_posts hook`.
	 *
	 * @ticket 9
	 */
	public function test_search_query_with_pre_get_posts_hook() {
		// Arrange.
		$p1 = self::factory()->post->create( array( 
			'post_title' => 'London',
		) );
		add_post_meta( $p1, 'my_lat', '51.509865' );
		add_post_meta( $p1, 'my_lng', '-0.118092' );

		$p2 = self::factory()->post->create( array( 
			'post_title' => 'Reykjavik',
		) );
		add_post_meta( $p2, 'my_lat', '64' );
		add_post_meta( $p2, 'my_lng', '-22.101' );

		// Act.
		$args = array(
			's'                   => 'london', // Search.
	    	'post_type'           => 'post',    
	    	'posts_per_page'      => 10,
	    	'ignore_sticky_posts' => true,
	    	'orderby'             => array( 'title' => 'DESC' ),		    
		);
	
		add_action( 'pre_get_posts', array( $this, 'override_search_query' ) );
		$query = new WP_Query( $args );
		remove_action( 'pre_get_posts', array( $this, 'override_search_query' ) );
		
		// Assert.
		$this->assertCount( 1, $query->posts );
		$this->assertContains( 'RADIANS', $query->request );
		$this->assertSame( 'London', $query->posts[0]->post_title );
		$this->assertSame( 1881 , (int) $query->posts[0]->distance_value );
	}

	/**
	 * Tests for applying a geo query on users.
	 */
	public function test_1_km_user_query() {
		$u1 = self::factory()->user->create( array(
			'first_name' => 'Site',
			'last_name'  => 'Crafting',
		));
		add_user_meta( $u1, 'my_lat', 47.236567 );
		add_user_meta( $u1, 'my_lng', -122.4357428 );

		$u2 = self::factory()->user->create( array(
			'first_name' => 'Point',
			'last_name'  => 'Defiance',
		));
		add_user_meta( $u2, 'my_lat', 47.3048779 );
		add_user_meta( $u2, 'my_lng', -122.5230098 );

		$u3 = self::factory()->user->create( array(
			'first_name' => 'Space',
			'last_name'  => 'Needle',
		));
		add_user_meta( $u3, 'my_lat', 47.6205099 );
		add_user_meta( $u3, 'my_lng', -122.3514661 );

		$args = array(
			'role'      => 'subscriber',
			'geo_query' => array(
				'lat'          => 47.236,
				'lng'          => -122.435,
				'lat_meta_key' => 'my_lat',
				'lng_meta_key' => 'my_lng',
				'radius'       => 1,
				'context'      => '\\Birgir\\Geo\\GeoQueryUserHaversine',
			),
		);

		$query = new WP_User_Query( $args );

		$this->assertCount( 1, $query->results );
		$this->assertSame( 'Site', $query->results[0]->first_name );
	}

	/**
	 * Tests for query ordering when applying a geo query on users.
	 *
	 * @ticket 14
	 */
	public function test_user_query_ordering() {
		$u1 = self::factory()->user->create( array(
			'display_name' => 'B - Site Crafting',
		));
		add_user_meta( $u1, 'my_lat', 47.236567 );
		add_user_meta( $u1, 'my_lng', -122.4357428 );

		$u2 = self::factory()->user->create( array(
			'display_name' => 'A - Site Crafting',
		));
		add_user_meta( $u2, 'my_lat', 47.236567 );
		add_user_meta( $u2, 'my_lng', -122.4357428 );

		$u3 = self::factory()->user->create( array(
			'display_name' => 'A - Point Defiance',
		));
		add_user_meta( $u3, 'my_lat', 47.3048779 );
		add_user_meta( $u3, 'my_lng', -122.5230098 );

		$u4 = self::factory()->user->create( array(
			'display_name' => 'B - Point Defiance',
		));
		add_user_meta( $u4, 'my_lat', 47.3048779 );
		add_user_meta( $u4, 'my_lng', -122.5230098 );

		$u5 = self::factory()->user->create( array(
			'display_name' => 'Space Needle',
		));
		add_user_meta( $u5, 'my_lat', 47.6205099 );
		add_user_meta( $u5, 'my_lng', -122.3514661 );

		$args = array(
			'role'      => 'subscriber',
			'geo_query' => array(
				'lat'          => 47.236,
				'lng'          => -122.435,
				'lat_meta_key' => 'my_lat',
				'lng_meta_key' => 'my_lng',
				'radius'       => 40, // Exclude Space Needle.
				'context'      => '\\Birgir\\Geo\\GeoQueryUserHaversine',
				'order'        => 'DESC',
			),
			'orderby'     => 'display_name',
			'order'       => 'ASC',
		);

		$query = new WP_User_Query( $args );

		$this->assertCount( 4, $query->results );
		$this->assertSame(
			array( 'A - Point Defiance', 'B - Point Defiance', 'A - Site Crafting', 'B - Site Crafting' ),
			array(
				$query->results[0]->display_name,
				$query->results[1]->display_name,
				$query->results[2]->display_name,
				$query->results[3]->display_name,
			)
		);
	}

	/**
	 * Callback to apply a geo query.
	 *
	 * @param WP_Query $query Instance of WP_Query.
	 */
	public function override_search_query( \WP_Query $query ) {
		 if ( $query->is_search() ) {
			$query->set( 'geo_query', array(
				'lat'                =>  64.128288,                         // Latitude point
				'lng'                =>  -21.827774,                        // Longitude point
				'lat_meta_key'       =>  'my_lat',                          // Meta-key for the latitude data
				'lng_meta_key'       =>  'my_lng',                          // Meta-key for the longitude data 
				'radius'             =>  5000,                               // Find locations within a given radius (km)
				'order'              =>  'DESC',                            // Order by distance
				'distance_unit'      =>  111.045,                           // Default distance unit (km per degree). Use 69.0 for statute miles per degree.
				'context'            => '\\Birgir\\Geo\\GeoQueryHaversine', // Default implementation, you can use your own here instead.
			) );
		}
	}
}
