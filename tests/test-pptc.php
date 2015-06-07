<?php

class PPTCTest extends WP_UnitTestCase {
	public $user;
	public $audio_file;
	public $post_id     = [];
	public $media_files = [];
	public $options;

	public function setUp() {
        parent::setUp();

        $this->user = $this->factory->user->create();
        $this->setup_options();
		$this->setup_media_files();

		$this->post_id['audio'] = $this->create_post('audio');
		$this->post_id['video'] = $this->create_post('video');

    }

    function setup_options() {
    	$pppc = new PowerpressPlayerCard();
		$options = $pppc->init_option();
		$options['twitter_account'] = '@example';
		$options['title']           = rand_str();
		$options['default_graphic'] = rand_str();
		$options['player_height']   = rand();
		$options['player_width']    = rand();
		update_option('pppc_plugin_options',$options);
		$this->options = $options;
		return;
    }

    function setup_media_files() {
		$this->media_files['audio'] = [];
		$this->media_files['audio']['file'] = 'https://example.com/audio_test.mp3';
		$this->media_files['audio']['payload'] = $this->media_files['audio']['file'] .'
39770015
audio/mpeg
a:3:{s:8:"duration";s:8:"00:16:30";s:8:"subtitle";s:35:"Interview with Michelangelo van Dam";s:6:"author";s:23:"Voices of the ElePHPant";}';
		$this->media_files['video'] = [];
		$this->media_files['video']['file'] = 'https://example.com/video_test.mp4';
		$this->media_files['video']['payload'] = $this->media_files['video']['file'] .'
39770015
video/mp4
a:3:{s:8:"duration";s:8:"00:16:30";s:8:"subtitle";s:25:"Interview with Matt Frost";s:6:"author";s:23:"Voices of the ElePHPant";}';
		return;
    }

    function create_post($type) {
    	$post = array('post_author' => $this->user,'post_status' => 'publish','post_title' => $type . ':' .rand_str(),'post_type' => 'post','post_content' => rand_str() );
    	$post_id = $this->factory->post->create( $post );
		update_post_meta($post_id, 'enclosure', $this->media_files[$type]['payload']);
		return $post_id;
    }

	function test_are_plugins_active() {
		$this->assertTrue( is_plugin_active('powerpress/powerpress.php'));
		$this->assertTrue( is_plugin_active('player-card/player-card.php'));			
	}

    function test_resgister_plugin() {
        global $wpdb;
        $pppc = new PowerpressPlayerCard();

        // clear everything out
        delete_option($pppc->option_key);
        $options = get_option($pppc->option_key);
        $this->assertEmpty($options);
        $plugins_to_active = array(
            'powerpress/powerpress.php'
        );
        update_option( 'active_plugins', $plugins_to_active );

        $this->assertFalse(is_plugin_active('player-card/player-card.php'));

        // activate the plugin
        $plugins_to_active = array(
            'powerpress/powerpress.php',
            'player-card/player-card.php'
        );
        update_option( 'active_plugins', $plugins_to_active );
        $pppc->activate();
        $this->assertTrue(is_plugin_active('player-card/player-card.php'));
        $options = get_option($pppc->option_key);
        $this->assertNotEmpty($options);

        return;
    }

    function test_can_retrive_post() {
		global $wp_query;
		$wp_query = new WP_Query( 'p='.$this->post_id['audio']);
		$pppc = new PowerpressPlayerCard();
		$post_options = $pppc->fetch_post_options();
		$this->assertEquals($post_options['post']->ID,$this->post_id['audio']);		
	}

	function test_can_find_media_file() {
		global $wp_query;
		$wp_query = new WP_Query( 'p='.$this->post_id['audio']);

		$pppc = new PowerpressPlayerCard();
		$post_options = $pppc->fetch_post_options();
		$media_file=$pppc->locate_media_file($post_options);
		$this->assertEquals($media_file,$this->media_files['audio']['file']);
		return;
	}

	function test_can_set_plugin_options() {
		$pppc = new PowerpressPlayerCard();

		$newOptions = $pppc->load_options();

		$this->assertEquals($this->options['twitter_account'],$newOptions['twitter_account']);
		$this->assertEquals($this->options['title'],$newOptions['title']);
		$this->assertEquals($this->options['default_graphic'],$newOptions['default_graphic']);
		$this->assertEquals($this->options['player_height'],$newOptions['player_height']);
		$this->assertEquals($this->options['player_width'],$newOptions['player_width']);
	}

	function test_can_build_card() {
		global $wp_query;
		$media_types = array_keys($this->post_id);
		foreach ($media_types as $type) {
			$card=$this->create_card( $type );
			$this->check_card( $card,$type );
		}

		return;
	}

	function test_main() {
		global $wp_query;

		// Everything works fine
		$wp_query = new WP_Query( 'p='.$this->post_id['audio']);
		ob_start();
		$pppc = new PowerpressPlayerCard();
		$pppc->main();
		$card = ob_get_contents();
		ob_end_clean();
		$this->create_card('audio');
		$this->check_card($card,'audio');

		// No Post failure
		$wp_query = new WP_Query( 'p=-1');
		ob_start();
		$pppc = new PowerpressPlayerCard();
		$pppc->main();
		$card = ob_get_contents();
		ob_end_clean();
		$this->assertEquals('<!-- No Post -->',trim($card));

		// No media failure
		$post = array('post_author' => $this->user,'post_status' => 'publish','post_title' => 'None :' .rand_str(),'post_type' => 'post','post_content' => rand_str() );
    	$post_id = $this->factory->post->create( $post );
		$wp_query = new WP_Query( 'p='.$post_id);
		ob_start();
		$pppc = new PowerpressPlayerCard();
		$pppc->main();
		$card = ob_get_contents();
		ob_end_clean();
		$this->assertEquals('<!-- No Media File -->',trim($card));
		return;
	}

	function test_activate() {
		$pppc = new PowerpressPlayerCard();
		delete_option($pppc->option_key);
		$pppc->activate();
		$options = get_option($pppc->option_key);

		$this->assertEquals(count($options),5);
		$this->assertEquals($options['twitter_account'],'');
		$this->assertEquals($options['title'],'');
		$this->assertEquals($options['player_height'],150);
		$this->assertEquals($options['player_width'],20);
		$this->assertEquals($options['default_graphic'],'');

		return;
	}


	function test_determine_plugin_location() {
		$pppc = new PowerpressPlayerCard();
		$location = $pppc->determine_plugin_location();
		$location_array = explode(DIRECTORY_SEPARATOR,__FILE__);

		$computed_location = $location_array[count($location_array)-3].DIRECTORY_SEPARATOR.'player-card.php';
		$this->assertEquals($location,$computed_location);
		return;
	}

	function test_inactive_blubrry() {
		deactivate_plugins('powerpress/powerpress.php');
		$pppc = new PowerpressPlayerCard();
		$this->assertTrue(is_plugin_inactive($pppc->determine_plugin_location()));
		return;
	}


	/*
	 * Helper Functions
	 */

	function create_card($media_type) {
		$returnValue = '';
		global $wp_query;

		$wp_query = new WP_Query( 'p='.$this->post_id[$media_type]);

		$pppc         = new PowerpressPlayerCard();
		$post_options = $pppc->fetch_post_options();
		$audio_file   = $pppc->locate_media_file($post_options);
		$options      = $pppc->load_options();
		$media_type   = $pppc->identify_media_type($post_options);
		$returnValue  =$pppc->buildCard( $options, $audio_file, $post_options, $media_type );

		return $returnValue;
	}

	function check_card($card,$type) {
		$fake_card = $this->create_card($type);
		$fake_card_array = explode("\n",$fake_card);

		$this->assertContains($fake_card_array[2],$card);
		$this->assertContains($fake_card_array[3],$card);
		$this->assertContains($fake_card_array[4],$card);
		$this->assertContains($fake_card_array[5],$card);
		$this->assertContains($fake_card_array[6],$card);
		$this->assertContains($fake_card_array[7],$card);
		$this->assertContains($fake_card_array[8],$card);
		$this->assertContains($fake_card_array[9],$card);

		return;		
	}

}

