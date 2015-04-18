[![Build Status](https://travis-ci.org/calevans/player-card.svg?branch=master)](https://travis-ci.org/calevans/player-card)
[![Code Climate](https://codeclimate.com/github/calevans/player-card/badges/gpa.svg)](https://codeclimate.com/github/calevans/player-card)
[![Test Coverage](https://codeclimate.com/github/calevans/player-card/badges/coverage.svg)](https://codeclimate.com/github/calevans/player-card)

# Powerpress Player Card

Cal Evans <cal@calevans.com>

Plugin Name: Player-card

Version: 1.1.3

Description: Add-on to [Blubrry PowerPress](https://wordpress.org/plugins/powerpress/) to add a [Twitter Player Card](https://dev.twitter.com/cards/overview) on any post that includes an enclosure.

Author: Cal Evans

Author URI: http://blog.calevans.com/2015/04/04/twitter-player-card-for-blubrry-powerpress-wordpress-plugin/

Copyright: 2015 E.I.C.C., Inc.

License: MIT


## Instructions

1. Install and properly configure PowerPress and enter at least one post with a media file.
1. Install and activate this plugin.
	1. Download the zip file from the github repo.
	1. From your WordPress plugin dashboard click on "Add New".
	1. Click on "Upload Plugin".
	1. Click "Choose File".
	1. Find the file on your file system.
	1. Click "Open"
	1. Click "Install Now"
1. Fill in the options above and Save Changes.
1. (optional) Open your post in a browser and view the source. Look for <code>&lt;!-- Begin Cal's Twitter Player Card Insert-a-tron --></code>. Make sure that **all** the fields have values.
1. Validate your card using the [Twitter Card Validator](https://cards-dev.twitter.com/validator). You will need to be loged into twitter with the same account that you specified above. You can run the validator as many times as you like.
1. When it validates, you will see a message that you are not "whitelisted". Once your card validates, you can request to be whitelisted. It usually takes 2-5 minutes. You will know you have been whitelisted when you try and validate the card again and it tells you you are whitelisted.

**Everything has to be https**. This includes your default graphic and the media file. PowerPress won't allow you to enter an https url for your media file. This plugin will force it to https. It is your responsibility to make sure that both versions are available.

## Versions

- 1.0   - Inital Release
- 1.1   - Added support for both audio and video
- 1.1.1 - Fixed errors introduced with the reorg for testing.
- 1.1.2 - Fixed error where downloading form github added -master to the directory name and this caused the player to not be found and the settings save to redirect to the wrong page.
- 1.1.3 - Removed the static methods. Increased the test covereage.
