<?php

class PPTCTest extends WP_UnitTestCase {
	public $user;
	public $audio_file;
	public $post_id;
	public $options;

	public function setUp() {
global $wp_query;
        parent::setUp();
		$this->_audio_file = 'https://elephpant.s3.amazonaws.com/vote_125.mp3';

        $this->user = $this->factory->user->create();
		$post = array('post_author' => $this->user,'post_status' => 'publish','post_title' => rand_str(),'post_type' => 'post','post_content' => rand_str() );

		$this->post_id = $this->factory->post->create( $post );

$payload=$this->_audio_file .'
39770015
audio/mpeg
a:3:{s:8:"duration";s:8:"00:16:30";s:8:"subtitle";s:35:"Interview with Michelangelo van Dam";s:6:"author";s:23:"Voices of the ElePHPant";}';
		
		update_post_meta($this->post_id, 'enclosure', $payload);

		$pppc = new PowerpressPlayerCard();
		$options = $pppc->init_option();
		$options['twitter_account'] = '@example';
		$options['title']           = rand_str();
		$options['default_graphic'] = rand_str();
		$options['player_height']   = rand();
		$options['player_width']    = rand();
		update_option('pppc_plugin_options',$options);
		$this->options = $options;

		$wp_query = new WP_Query( 'p='.$this->post_id );
    }

	function test_are_plugins_active() {
		$this->assertTrue( is_plugin_active('powerpress/powerpress.php'));
		$this->assertTrue( is_plugin_active('player-card/player-card.php'));			
	}

	function test_can_retrive_post() {
		$pppc = new PowerpressPlayerCard();
		$post_options = $pppc->fetch_post_options();
		$this->assertEquals($post_options['post']->ID,$this->post_id);		
	}

	function test_can_find_media_file() {

		$pppc = new PowerpressPlayerCard();
		$post_options = $pppc->fetch_post_options();
		$audio_file=$pppc->locate_media_file($post_options);
		$this->assertEquals($audio_file,$this->_audio_file);
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
		$pppc = new PowerpressPlayerCard();
		$post_options = $pppc->fetch_post_options();
		$audio_file=$pppc->locate_media_file($post_options);
		$options = $pppc->load_options();

		$card=$pppc->buildCard( $options, $audio_file, $post_options );
		$this->assertContains('<meta name="twitter:card" content="player">',$card);
		$this->assertContains('<meta name="twitter:site" content="' .$this->options['twitter_account'] . '">',$card);
		$this->assertContains('<meta name="twitter:title" content="' .$this->options['title'] . '">',$card);
		$this->assertContains('<meta name="twitter:description" content="' .$post_options['post']->post_title . '">',$card);
		$this->assertContains('<meta name="twitter:image" content="' .$this->options['default_graphic'] . '">',$card);
		$this->assertContains('<meta name="twitter:player" content="http://example.org/wp-content/plugins/Users/cal/Projects/player-card/container.php?a=' .urlencode($audio_file) . '">',$card);
		$this->assertContains('<meta name="twitter:player:width" content="' .$this->options['player_width'] . '">',$card);
		$this->assertContains('<meta name="twitter:player:height" content="' .$this->options['player_height'] . '">',$card);

		return;
	}

}

