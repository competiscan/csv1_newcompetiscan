=== FAQ Schema For Pages And Posts ===
Contributors: krystianszastok, onlinemarketinggurus
Donate link: https://krystianszastok.co.uk/
Tags: SEO, JSON-LD, rich snippets, schema, schema.org, structured data
Requires at least: 4.6
Tested up to: 5.3
Stable tag: 2.3.0
Requires PHP: 5.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==
FAQ Schema For Pages And Posts (By [Online Marketing Gurus](https://www.onlinemarketinggurus.com.au/) and [Krystian Szastok](https://krystianszastok.co.uk/) allows you to turn questions and answers on your page (your mini FAQ sections) into JSON-LD code on the fly.

The idea is to add as much meaning as possible into your pages so that Google can understand them better.

You can simply copy and paste as you edit your WordPress page/post into the plugin, as it appears at the bottom of the editor. Once you’ve saved the page, the JSON-LD schema will be present in the HEAD of your page/post — and voila, you’re on your way to a making a great impact across rankings. 

If you’re looking for a tutorial, check out our comprehensive video [on YouTube here.](https://www.youtube.com/watch?v=F7e-L-9KWcI). This tool is created for free by Australia’s best-rated SEO Agency and the United Kingdom’s foremost SEO consultant. 

== Features == 
 * Add JSON-LD schema easily to HEAD of any page or post
 * Build on-page accordion FAQs 
 * Mass export to edit multiple page FAQs from a spreadsheet and reimport back in
 * Simple copy and paste possible as editor present within a page/post
 * Have a chance to gain more visibility on Google’s search result pages
 * Secure and sanitised 

== Installation ==

1. Upload the wp-faq-schema-for-pages-and-posts folder to the /wp-content/plugins/directory
2. Activate the plugin through the ‘Plugins’ menu in WordPress
3. Edit your page or post to see the fields for Q&A at the end of them
4. Add in questions and answers and update the post.

== Using the plugin == 

Just to insert JSON-LD in a given page, go to that page/post and you’ll find new fields at the bottom the editor in the WP backend.

If you’d like to also display your Questions and Answers via a FAQ, use the shortcode. Title defines the heading above the FAQs. If you’d like to use an accordion, add accordion=1 as in the example. Remove that part not to use an accordion and just show all FAQs open (ie. [wp-faq-schema title=”Car FAQs” accordion=1] )

If you’d like to mass edit, use the Export section to export all of the pages and posts with their ids – and then edit the file and import it back. Note: For multiple FAQs for same post add more row at CSV file with same ID and title.

Add the shortcode inside of the page or post content to display the FAQ. The shortcode tag is wp-faq-schema. It will take title and accordion attributes.


== Example below: ==

[wp-faq-schema] – This code will only show FAQs of that page or post.
[wp-faq-schema title=”Car FAQs”] – This code will show FAQs with “Car FAQs” title.
[wp-faq-schema accordion=1] – This code will show FAQs as accordion.
[wp-faq-schema title=”Car FAQs” accordion=1] – This code will show FAQs as accordion with “Car FAQs” title.

== Frequently Asked Questions ==

= What is FAQ Schema? =
The FAQPage structured data helps Google understand that the page contains some FAQ (Frequently Asked Questions). 

= Why should I use FAQ Schema? =
The FAQPage structured data helps Google understand that the page contains valuable information, in the form of commonly asked questions and answers. This allows Google to deliver more detailed answers on SERPs. 

= When should I use FAQ Schema? =
You should use the FAQ Schema on pages that contain questions and answers to do with a given product, service or page information.

= When shouldn't I use FAQ Schema? =
It shouldn’t be used on pages that allow users to submit answers and questions.

= Where to find support for this plugin? = 
You can contact us over at the official  plugin page: [FAQ Schema Plugin at Online Marketing Gurus](https://www.onlinemarketinggurus.com.au/faq-schema-plugin).

== Screenshots ==

1. Simple backend panel at the end of each page or post, allowing for unique FAQ schema to be added, saves on page update.
2. Front end output in the source code, validates perfectly with Google's tester.

== Changelog ==

= 1.0.0 =
* Initial release

= 2.0.0 =
* Added export and import, FAQ within the page and lots more!

= 2.2.0 =
* Added ability to add links within the code

= 2.3.0 =
* Added ability to deploy Schema on all types of custom pages, categories etc