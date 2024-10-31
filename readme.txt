=== My Trending Post ===
Contributors: tranchesdunet
Author: tranchesdunet (Jean-Marc Bianca)
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=RZRLGED6KXV9W
Author URI: http://www.tranchesdunet.com/
Plugin URI: http://www.tranchesdunet.com/my-trending-post
Tags: twitter,trending,topic,post,automation,cron
Requires at least: 3.1
Tested up to: 3.4.1
Stable tag: 0.2

Automatically searches the Trending Topic on Twitter and send a tweet with a link to an article related in your blog

== Description ==

**My Trending Post** is a plugin which search for Trending Topics on Twitter every hour. It then compare the Trending Topics with 
your post's blog and send a tweet with a link to your post if it match with the topic.

Features:

* An automatic tweet from a user defined twitter account, with a link related to one of your blog's post and the World trending topic (the plugin will check for Trending Topics every hours)
* An hashtag will be added after the link, with the matched's topic
* A **smart algorithm** to separate concatenated camel words (e.g. #10FavouriteBands ==> 10 Favourite Bands) and so increase the matching between yours posts and the trending topics (often concatenated)
* The post sent will be flagged and so won't be sent again until 7 days
* The possibility of **excluding one or more categories** of article to be tweeted **(Now available in the Lite version)**

Additionaly, if you get the Full Version, you will have:

* **The Zone choice** between World and any other countries covered by Twitter Trending Topics
* The possibility of enabling/disabling the sending of the tweets
* The possibility of enabling/disabling the sending of an email for each tweet sent (with email address and title settings)
* The possibility of adding a text before the link in the tweet (like **hashtag** or everything else)
* The possibility of adding a text after the link in the tweet (like **hashtag** or everything else)
* The possibility to use an **url shortener** in your tweets (tinyurl or Bit.ly) to prevent exceeding the 140char limit of Twitter
* The choice of a delay before retweeting the same article
* The possibility of **excluding one or more categories** of article to be tweeted

**Note on the support:**
For better support, I will only answer question/suggestion/etc through a dedicaced page on my website : www.tranchesdunet.com/my-trending-post
You can post either in english or french (preferably).

**Disclaimer:**
This plugin use the Twitter API as it is free. I will not be responsible if the Twitter API become unfree and so, bring to an end of working.


Feel free to test it and report any bugs/suggestions

Be sure to check out my other plugin:

* [SideNails](http://wordpress.org/extend/plugins/sidenails/)
* [Hotlink2Watermark](http://wordpress.org/extend/plugins/hotlink2watermark/)

== Installation ==

1. Upload `my-trending-post` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the admin interface and authenticate you to a Twitter account
4. If you want to exclude some categories from being tweeted, just check them and update your settings
4. You're done!

== Screenshots ==

1. A sample of a Tweet sent by `My Trending Post` with Bit.Ly url shortener and additional text options enable.
2. The interface of the Full Version

== Frequently Asked Questions ==

= How can I change the language for something else than English? =

Currently this plugin is only available in English. It will be available in French in a forecoming release.

= What are the prerequesite for this plugin? =

You must have a Twitter account from which the Tweets will be sent. 
Also, you'll have more 'matching posts' if your blog is focused on several topics and not only one, as the trending topics are on a wide variety of subjects

= Your plugin's doing a great work. How can I rewards you? =

Thanks for the compliment. 
Firstly, you can buy the full version of this plugin, with a bunch of great advantages: choice of trending zone, include of custom text in the tweet, email report, etc...
Secondly, you can use the [Paypal](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=RZRLGED6KXV9W) form on the plugin's admin page for buying me a coffe/beer/champagne, as you want ;)

== Changelog ==

= 0.1 =
* First public release

= 0.2 =
* Swapping the "Excluding categories" feature from Full version to Lite version
