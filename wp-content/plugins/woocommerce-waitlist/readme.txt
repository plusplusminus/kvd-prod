=== WooCommerce Waitlist ===
Requires at least: 3.9
Tested up to: 4.3
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
WC requires at least: 2.1.0
WC tested up to: 2.4.6

This plugin enables registered users to request an email notification when an out-of-stock product comes back into stock. It tallies these registrations in the admin panel for review and provides details.

== Description ==
The WooCommerce Waitlist extension lets you track demand for out-of-stock and backordered items, making sure your customers feel informed, and therefore more likely to buy.
With the WooCommerce Waitlist extension, customers of your site can sign up to be notified by email when an out-of-stock product becomes available. As the site owner you can also review which users are on the waiting list for which products, and sort your products by the number of people registered on the waiting list.

== Installation ==
1) Unzip and upload the plugin’s folder to your /wp-content/plugins/ directory
2) Activate the extension through the ‘Plugins’ menu in WordPress

== Frequently Asked Questions ==
Can a customer view all the products they are on a waiting list for?
There is an experimental shortcode [woocommerce_my_waitlist] which will display a table listing all the products that the currently logged in user is waiting for.

Are customers put on a waitlist in a particular order?
No. It doesn’t operate on a ‘first come, first serve’ basis.

Does this work for affiliate products?
No. At the moment stock status has no bearing on the output of an affiliate product listing so these have been left well alone.

Does this work for variable products?
There is a known issue when using WooCommerce Waitlist in conjunction with variable products that prevents the ‘Join Waitlist’ button from being displayed  when the ‘Out of Stock Visibility’ option is set to ON. The only current solution to this problem is to turn this option off.

How do I change the subject / content of emails?
The content of the email and the subject line are both editable via the WooCommerce email system. WooCommerce Waitlist adds a new section to the ‘Emails’ tab of WooCommerce Settings where this can be managed. For more information please see the WooCommerce Documentation.

What if I don’t want users to be automatically emailed when a product is back in stock?
We’ve got you covered. Add the following snippet to your functions.php file in your theme and no email will be sent out and users will remain on the waitlist.
add_filter( 'wcwl_automatic_mailouts_are_disabled', '__return_true' );

What if I want to email users automatically, but don’t want them to be removed from the waitlist?
We’ve got that one too. Add the following snippet to your functions.php file in your theme and users will remain on the waitlist until they purchase that product.
add_filter( 'wcwl_persistent_waitlists_are_disabled', '__return_false' );

Why does the Waitlist only show up for some products?
If you’re using the Advanced Notifications extension make sure you disable the backorder setting.

== Changelog ==

2015.11.23 - version 1.4.5
* Updated translation template (.pot)

2015.10.21 - version 1.4.4
* Updated docblock
* Removed debugging code on edit product screen

2015.10.21 - version 1.4.3
* Fixed bug where waitlists weren't showing on product edit screen when persistent waitlists were enabled
* Added notification text to product edit screen when persistent waitlists are enabled

2015.10.18 - version 1.4.2
* Fixed update bug for variations, conflict with another plugin

2015.10.14 - version 1.4.1
* Fixed version numbers

2015.10.12 - version 1.4.0
* Added new feature - Waitlist Archives for recording a history of mailed out waitlists

2015.09.18 - version 1.3.13
* Fixed bug with mailouts not working when updating stock when using WC 2.4

2015.08.12 - version 1.3.12
* Fixed bug with updating waitlists for variables when using WooCommerce 2.4

2015.07.08 - version 1.3.11
* filtered text on "new account" tab of woocommerce email settings with gettext

2015.07.08 - version 1.3.10
* removed meta-box and instead added a waitlist tab to the product data panel on product edit pages
* added readme

2015.05.25 - version 1.3.9
* changed Admin UI to show the waitlist meta-box if there are any users on the waitlist, rather than if the product is out of stock

2015.05.25 - version 1.3.8
* fixed settings bug, settings now being updated correctly
* updated translation functions
* fixed frontend button bug, now outputting same button type if user is logged in or not

2015.04.21 - version 1.3.7
* Fix - Potential XSS with add_query_arg

2015.01.11 - version 1.3.6
* added notice and deactivated plugin if WooCommerce is not at least version 2.0
* removed functionality for WooCommerce versions less than 2.0
* updated settings functions for woocommerce v2.3.0

2014.12.19 - version 1.3.5
* fix \"Email Address\" hard coded string

2014.12.01 - version 1.3.4
* Fixed bug where deleting users triggered a PHP error
* Fixed bug causing php notice when updating with quick edit

2014.11.26 - version 1.3.3
* WordPress 4.0.1 compatability
* Fixed bug removing users from waitlist when \'enable stock management\' was ticked on certain products (related to quickedit bug)
* Refactored and annotated all functions
* Fixed \'woocommerce_my_waitlist\' shortcode so it can be displayed on any page
* Fixed bug with mailouts not sending when product was updated using quick edit

2014.11.19 - version 1.3.2
 * fix \"Join Waitlist\" hard coded string

2014.09.11 - version 1.3.1
 * fix version number, causing endless update loop

2014.09.05 - version 1.3.0
* WordPress 4.0 compatability
* WooCommerce 2.2 compatability
* Added support for non-registered and logged out users to join waitlists
* Added support for Admin to add users to waitlist from product page
* Added support for users to be removed from waitlists when they are deleted from wordpress
* Added frontend fixes for variable and grouped products and css and jquery

2014.03.03 - version 1.2.0
* Added support for WC_Mail templates
* Added support for Bulk Stock Management
* Fixed ‘call to member function on non-object’ notice in Frontend_UI notice

2014.02.25 - version 1.1.8
* Added filterable version of automatic mailout control

2014.02.24 - version 1.1.7
* Fix deprecated call to WooCommerce->add_message
* Fix broken link to Inventory Settings after 2.1 change

2014.02.18 - version 1.1.6
 * Fix in security issue in wp-admin

2013.11.06 - version 1.1.5
 * [woocommerce_my_waitlist] only displays for logged in users
 * [woocommerce_my_waitlist] not dependent on WP numberposts setting

2013.10.31 - version 1.1.4
 * Patch fixed the error with 1.1.3 - no closures in PHP 5.2 dummy! Happy Halloween everyone

2013.10.29 - version 1.1.3
 * Added a beta shortcode to display a user waitlist using [woocommerce_my_waitlist]

2013.02.21 - version 1.1.2
 * Fixed a bug that prevented in-stock variable products from being added directly after an out-of-stock variation was clicked

2013.02.21 - version 1.1.1
 * Added filterable version of persistent waitlist support

2013.01.24 - version 1.1.0
 * Added support for waitlists on product variations
 * Added control to auto waitlist creation to allow it to be turned off
 * Added dismissable admin nag alerting shop managers to turn off \'Hide out of stock products\' setting
 * Replace WCWL_HOOK_PREFIX constant with greppable string
 * Added correct plugin URI to plugin meta
 * Improved WC 2.0 compat
 * Improved PHPDocs


2012.01.04 - version 1.0.4
 * WC 2.0 compat
 * Added several missing translatable strings
 * Improved efficiency on activaton task that was causing memory issues on stores with many products
 * Re-instated WCWL_SLUG

2012.12.04 - version 1.0.3
 * New updater

2012.12.03 - version 1.0.2
 * Fixed a bug that caused the mailto: value to be empty when emailing all users on a waitilist
 * Removed some debugging output that hadn\'t been cleaned up properly!
 * Removed WCWL_SLUG for codestyling localisation
 * WC future compat
 * Login URL switch to my account page

2012.11.08 - version 1.0.1
 * Fixed a bug that caused only products with an existing waitlist to be displayed when sorting by waitlist
 * Fixed a bug that caused no products to be displayed when sorting by waitlist on some installs
 * Refined waitlist custom column display to be more coherent with existing Admin UI
 * Added cleanup on uninstall

2012.10.01 - version 1.0
 * First Release
