# MailChimp plugin for Craft CMS 3.x

This plugin exposes a MailChimp client API as a Craft service who can be called everywhere in the project with adding juist a few lines of code. For now it covers the basic commands for subscribing and updating members on a selected list. It has support for both single updates as well as batch updates. This is the most flexibele MailChimp plugin ever! Because it provides just a basic client and everything else can be added as "extra" functionality.


## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later, and of course a MailChimp account. This plugin makes calls to MailChimp's latest API version 3.0, so you need an API key as well.


## Installation

To install the plugin, follow these instructions.

*While Craft 3 is still in beta, you'll need to use Composer to download and install the plugin.*

1. Open your terminal and go to your Craft project:

	cd /path/to/project

2. Then tell Composer to load the plugin:

	composer require White/Craft/MailChimp

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for the MailChimp plugin.


To get a MailChimp API key, follow these steps

1. Login to MailChimp, go to Account → Extras → API Keys then 

2. Create a new key, or reuse an existing key


## Setup

After installing the plugin, go to Settings → Plugins → MailChimp in the Craft admin panel. Add your API key and then select the default MailChimp list to add/update subscribers.


## Examples

These API requests are covered:

https://developer.mailchimp.com/documentation/mailchimp/reference/lists/
https://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/


## Support

This MailChimp plugin for Craft CMS is brought to you by [Digital Agency WHITE](https://www.white.nl/)