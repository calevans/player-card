<?php
// figure out where we are stored
$location = $pppc->determine_plugin_location();
?>
<form method="post" action="admin.php?page=<?php echo $location;?>">

<h2><?php _e('PowerPress Twitter Player Card', 'pppc'); ?></h2>
<label for="twitter_account">Twitter Account : </label>
<input type="text" name="twitter_account" id="twitter_account" size="30" value="<?php echo $plugin_options['twitter_account'];?>" /> <br />

<label for="title">Title : </label>
<input type="text" name="title" id="title" size="50" value="<?php echo $plugin_options['title'];?>" /> <br />

<label for="player_height">Player Height : </label>
<input type="text" name="player_height" id="player_height" size="5" value="<?php echo $plugin_options['player_height'];?>" /> <br />

<label for="player_width">Player Width : </label>
<input type="text" name="player_width" id="player_width" size="5" value="<?php echo $plugin_options['player_width'];?>" /> <br />

<label for="default_graphic">Default Graphic : </label>
<input id="default_graphic" type="hidden" name="default_graphic" value="<?php echo $plugin_options['default_graphic'];?>" />
<img id="default_graphic_display" src="<?php echo $plugin_options['default_graphic'];?>" />
<input id="default_graphic_upload_button" class="button" type="button" value="Set Default Image" <?php echo ((empty($plugin_options['default_graphic'])) ? ' style="display: block;" ' : ' style="display: none;" ' );?> />
<input id="default_graphic_remove_button" class="button" type="button" value="Remove Default Image" <?php echo ((empty($plugin_options['default_graphic'])) ? ' style="display: none;" '  : ' style="display: block;" ');?>/>

<p class="submit">
<input type="hidden" name="action" value="update" />
<input id="settingsbutton" type="submit" class="button-primary" value="<?php _e('Save Changes', 'pppc') ?>" />
</p>

<div style="margin:5%;">
<h2><?php _e('Instructions', 'pppc'); ?></h2>
<ol>
<li>Install and properly configure PowerPress and enter at least one post with a media file.</li>
<li>Fill in the options above and Save Changes.</li>
<li>(optional) Open your post in a browser and view the source. Look for <code>&lt;!-- Begin Cal's Twitter Player Card Insert-a-tron --></code>. Make sure that <strong>all</strong> the fields have values.</li>
<li>Validate your card using the <a href="https://cards-dev.twitter.com/validator">Twitter Card Validator</a>. You will need to be loged into twitter with the same account that you specified above. You can run the validator as many times as you like.</li>
<li>When it validates, you will see a message that you are not "whitelisted". Once your card validates, you can request to be whitelisted. It usually takes 2-5 minutes. You will know you have been whitelisted when you try and validate the card again and it tells you you are whitelisted.

</ol>
<p>
	Remember, <strong>everything has to be https</strong>, including your default graphic and the media file. PowerPress won't allow you to enter an https url for your media file. This plugin will force it to https. It is your responsibility to make sure that both versions are available.
</p>
<p>
	For full details on Twitter's Player Card spec, check out <a href="https://dev.twitter.com/cards/types/player">https://dev.twitter.com/cards/types/player</a>. 
</p>
</div>