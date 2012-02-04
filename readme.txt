=== Plugin Name ===
Contributors: mattdeclaire
Tags: custom post types, admin
Requires at least: 3.3.1
Tested up to: 3.3.1
Stable tag: trunk

Create custom post types through the admin system.

== Description ==

= Custom Post Types =

WordPress can be more than just a blog.  By default, WordPress allows you to create blog posts and pages (and links, and comments,...).  But it can do more than that.  With a little elbow grease, you can put in all kinds of different information.  *Blog post* and *page* are two different types of *posts*.  WordPress also allows you to create *custom post types*.  Your site could have the post type *employee*, or *product*, or *movie*, or *super hero*.

Normally, you need to be a code monkey to pull this off.  If requires quite a bit of programming.  **Postyper** aims to remedy that.  It provides you with an interface to define your own custom post types, without the computer science degree.

Once you have your custom post types defined, you'll need to work on a way to display them.  Postyper creates archives for you, and individual pages for your posts.  But in order to display your custom field information, you'll need to modify your templates.  For this, you'll probably need to get a template developer.

= For Template Developers =

Postyper uses WordPress's native post meta system.  So all custom information entered by editors can be retrieved using the [get_post_meta()](http://codex.wordpress.org/Function_Reference/get_post_meta) or [get_post_custom()](http://codex.wordpress.org/Function_Reference/get_post_custom) functions.  You can also use the WordPress hierarchy to create templates for each post type, including single-*postype*.php, archive-*postype*.php, etc.

== Installation ==

1. Upload the `postyper` directory to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= How does Postyper store post information? =

Postyper uses WordPress's native post meta system.  All fields that you add to your post types ultimately result in the creation of post meta on the post type.  So, using this data in your template works just like normal.  You aren't locked into any proprietary PHP calls.

= If I uninstall Postyper, do I loose all my data? =

Uninstalling Postyer will remove the mechanism for defining your custom post types, but all the data is still in the database.  If you choose to quit using Postyper, you can easily replace it with `register_post_type` calls, and access your existing data.

== Changelog ==

= 0.2 =
* Drastically improved field editing UI.
* Added "post" as field type.  You can now create a field for a type that allows you to select a post of another type.

= 0.1 =
* This first version works, but is beta.  Feel free to use it, but you should expect changes before we hit 1.0.

== Future Changes ==

We're excited about the potential of Postyper and will continue to develop its functionality.  Future improvements include:

1. Shortcodes for displaying meta field information.
1. More field types.
1. Unicorns.