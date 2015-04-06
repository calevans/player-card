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
			$wp_query = new WP_Query( 'p='.$this->post_id[$type]);

			$pppc = new PowerpressPlayerCard();
			$post_options = $pppc->fetch_post_options();
			$audio_file=$pppc->locate_media_file($post_options);
			$options = $pppc->load_options();
			$media_type = $pppc->identify_media_type($post_options);
			$card=$pppc->buildCard( $options, $audio_file, $post_options, $media_type );
			$this->assertContains('<meta name="twitter:card" content="player">',$card);
			$this->assertContains('<meta name="twitter:site" content="' .$this->options['twitter_account'] . '">',$card);
			$this->assertContains('<meta name="twitter:title" content="' .$this->options['title'] . '">',$card);
			$this->assertContains('<meta name="twitter:description" content="' .$post_options['post']->post_title . '">',$card);
			$this->assertContains('<meta name="twitter:image" content="' .$this->options['default_graphic'] . '">',$card);
			$this->assertContains('<meta name="twitter:player" content="' . plugins_url('player-card') . '/container.php?a=' .urlencode($audio_file) . '&t=' . $type . '">',$card);
			$this->assertContains('<meta name="twitter:player:width" content="' .$this->options['player_width'] . '">',$card);
			$this->assertContains('<meta name="twitter:player:height" content="' .$this->options['player_height'] . '">',$card);
		}

		return;
	}

}

