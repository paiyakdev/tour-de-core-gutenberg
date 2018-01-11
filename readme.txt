=== Tour de Coure - Gutenberg Edition ===
Contributors: (this should be a list of wordpress.org userid's)
Tags: comments, spam
Requires at least: 4.9
Tested up to: 4.9.1
Stable tag: 0.1.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

A test plugin for experimenting with custom Gutenberg blocks.

== Description ==

If you want to see the streams behind this plugin, go to one of those two places:

https://www.twitch.tv/collections/JftQ1YlKAhXTEQ
https://www.youtube.com/watch?v=E8tzSFPLOgU&list=PLXfBe-Bwd1wM7_eeCGZvizGNthkhMoEiV

In order to get your environment up and running, make sure to run 

`npm install`

in the plugin's folder. After that just run 

`npm run watch-react`

to watch the folder assets/jsx/ for changes. As you edit the files in there(or add new files), babel will compile them to browser-ready JS files.

== Changelog ==

= 0.1.4 =
* Moved over the Team Members CPT into the plugin so that it just works without any extra code
* Created a locked-down template for the Team Members CPT so that we can utilize Gutenberg as a full meta box replacement
* Created a very basic and far from ideal way in which to reuse our block rendering code between the front-end and the back-end. A much better way would be to invest a bit of time to find and setup a PHP template engine that has a compatible JS alternative so that you can 100% reuse the code between PHP and JS. Nonetheless though, our implementation is a good proof of concept that it is possible to reuse your code.

= 0.1.3 =
* Skipped to version 0.1.3 to match the version number with the video series order
* Created a TdC Team Members block that pulls a list of posts from a custom post type, along with some custom fields
* Created a TdC Team Member Info block that serves as a (ACF) meta box replacement

= 0.1.1 =
* Created a TdC Base Component(to handle auto-binding of methods)
* Created a Recent Posts Component that pulls from the REST API
* Added the PHP rendering of the Recent Posts Component
* Stopped using the transform-runtime babel plugin

= 0.1.0 =
* Initial version with all of our npm stuff in place and the first custom Gutenberg block in place.

== Upgrade Notice ==

= 1.0 =
Upgrade notices describe the reason a user should upgrade.  No more than 300 characters.

= 0.5 =
This version fixes a security related bug.  Upgrade immediately.

== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.

== A brief Markdown Example ==

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin

Unordered list:

* something
* something else
* third thing

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`
