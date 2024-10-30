=== beyondConnect ===
Contributors: beyondsoftware
Tags: classes, courses, dance courses, dance classes, 
Donate link: https:/beyond-sw.com
Requires at least: 6.0
Tested up to: 6.1
Requires PHP: 8.0
Stable tag: 2.2.16
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

beyondConnect connects Wordpress with the beyond software services.

== Description ==
beyond software is a swiss manangement software (SaaS) for schools and is specialized for dance schools and dance centers. It gives you the opportunity to manage the data for your school on the internet. 

This WordPress plugin is connecting with beyond software.  To use it on your WordPress website you need to obtain a url and a key. Please write an e-mail to info@beyond-sw.com to get your details.

== Installation ==
1. Upload the \"beyondconnect\" Directory to the \"/wp-content/plugins/\" directory.
2. Activate the plugin through the \"Plugins\" menu in WordPress.

== Frequently Asked Questions ==
= Is this plugin working without subscription to beyond-sw.com =
No, this plugin is useless without subscription to beyond-sw.com.

== Screenshots ==

== Changelog ==
= 2.2.16 =
* Support for Refresh Tokens
= 2.2.15 =
* Bug fixes
* Enhanced Logging
= 2.2.14 =
* Bug fixes
= 2.2.13 =
* Bug fixes
= 2.2.12 =
* Bug fixes
= 2.2.11 =
* Set compatibility to WordPress 6.0
= 2.2.10 =
* Bug fixes
= 2.2.9 =
* Bug fixes
= 2.2.8 =
* Bug fixes
= 2.2.7 =
* Bug fixes
= 2.2.5 =
* Ninja Forms action add registration:
    * if no sex is specified for the registration, then the sex of the address is taken
= 2.2.4 =
* Support for prevention of duplicate addresses
* Bug fixes
= 2.2.3 =
* New actions added
    * bc_addContinuation
= 2.2.2 =
* Support for weglot translations added
* Bug fixes (Ninja Forms action process after save)
= 2.2.1 =
* New functions added:
    * subscribeToNewsletter
    * unsubscribeFromNewsletter
* New Ninja Forms actions added:
    * add email to newsletter
* Bug fixes
= 2.2.0 =
* Support for SIX Saferpay payment added
* New shortcodes added:
    * beyondconnect_openitems_list
    * beyondconnect_openitems_list_element
    * beyondconnect_openitems_list_button_addtocart
    * beyondconnect_cart_button_emptycart
    * beyondconnect_cart_button_paycart
    * beyondconnect_cart_list
    * beyondconnect_cart_element
    * beyondconnect_cart_button_removefromcart
    * beyondconnect_saferpay_iframe
    * beyondconnect_subscriptiontypes_list
    * beyondconnect_subscriptiontypes_list_element
    * beyondconnect_subscriptiontypes_list_button_addtocart
* Function added:
    * bc_getValue
* Actions added:
    * admin_post_bc_logout
    * admin_post_bc_addToCart
    * admin_post_bc_buyaddSubscriptionToCart
    * admin_post_bc_removeFromCart
    * admin_post_bc_emptyCart
    * admin_post_bc_saferpayInitialize
    * admin_post_nopriv_bc_logout
    * admin_post_nopriv_bc_addToCart
    * admin_post_nopriv_bc_buyaddSubscriptionToCart
    * admin_post_nopriv_bc_removeFromCart
    * admin_post_nopriv_bc_emptyCart
    * admin_post_nopriv_bc_saferpayInitialize
    * bc_assignRoles
    * bc_processPayment
    * bc_saferpayAssert
    * bc_saferpayCapture
* Bug fixes
= 2.1.24 =
* New shortcodes added:
	* beyondconnect_registrations_list
	* beyondconnect_registrations_list_element
	* beyondconnect_subscriptions
	* beyondconnect_subscriptions_element
	* beyondconnect_subscriptions_attendances
	* beyondconnect_subscriptions_attendances_element
	* beyondconnect_subscriptions_list
	* beyondconnect_subscriptions_list_element
	* beyondconnect_subscriptionsattendances_list
    * beyondconnect_subscriptionsattendances_list_element
    * beyondconnect_subscriptions_list_collapsible
    * beyondconnect_subscriptions_list_popupable
* Bug fixes
* Support for impersonation added
= 2.1.23 =
* Bug fixes (array_change_key_case)
= 2.1.22 =
* Support for edit address in Ninja Forms added
* Support for check address integrity added
* Bug fixes (shortcode beyondconnect_courses_structure)
= 2.1.21 =
* Bug fixes (shortcode beyondconnect_courses_structure)
= 2.1.20 =
* Support for login and forgot password in Ninja Forms added
= 2.1.19 =
* Support for global variables in Ninja Forms added
* Calculations for shortcodes added
* Bug fixes (BC Liste: Teacher - Firstname Lastname)
= 2.1.18 =
* Temporary fix for SSL-Error removed
* Redesign of Settings Page
= 2.1.17 =
* Temporary fix for SSL-Error
= 2.1.16 =
* Minor bug fixes
= 2.1.15 =
* Minor bug fixes
= 2.1.14 =
* Support for customclass added
* New shortcodes for global variables added
	* beyondconnect_setglobals
	* beyondconnect_getglobals
= 2.1.13 =
* New shortcodes added:
	* beyondconnect_coursdates_list
	* beyondconnect_coursdates_list_element
	* beyondconnect_coursdates_list_collapsible
	* beyondconnect_coursdates_list_popupable
* Property alternativeclass and alternativetexttovisble added
= 2.1.12 =
* Bug fixes (replace bool)
= 2.1.11 =
* Support for visible added
= 2.1.10 =
* Support for top and skip added
* Bug fixes (widgets)
= 2.1.9 =
* Bug fixes (querystring)
= 2.1.8 =
* Support for table and querystring prefix added
* Bug fixes (target)
= 2.1.7 =
* Bug fixes
= 2.1.6 =
* Bug fixes (2nd expand level)
= 2.1.5 =
* Support for date format dd.mm.yyyy in Ninja Forms added
= 2.1.4 =
* Property select text for BC List added
= 2.1.3 =
* Action BC Add Parent added
= 2.1.2 =
* Bug fixes (Ninja conditional logic)
= 2.1.1 =
* Property emptytext for shortcode beyondconnect_courses_list added
* Filter istAnmeldungMoeglich in BC List CoursesIDTitleDate added
* Performance improvement
* Bug fixes (translation)
= 2.1.0 =
* Support for Ninja Forms added
= 2.0.4 =
* New shortcodes added:
	* beyondconnect_courses
	* beyondconnect_courses_element
	* beyondconnect_courses_groups_element
	* beyondconnect_courses_dates_element
* Bug fix (in dashboard)
= 2.0.3 =
* Dashboard improved
* SOAP completely removed
* Bug fix (uri without ending slash, curl)
= 2.0.2 =
* Format parameters added to placeholders
* Querystring placeholders added
* Full support for expand property
* Bug fix (missing beyond.php)
= 2.0.1 =
* Connects to the new RESTful API with OData standard

== Upgrade Notice ==
= 2.0.3 =
*IMPORTANT: Before you install the new version, please contact us so that we can provide you with the necessary details for the settings. 
*This version of beyondConnect uses the new RESTful API with OData standard. You may need to make minor adjustments to the shortcodes or widgets after upgrading.
= 2.0.2 =
*IMPORTANT: Before you install the new version, please contact us so that we can provide you with the necessary details for the settings. 
*This version of beyondConnect uses the new RESTful API with OData standard. You may need to make minor adjustments to the shortcodes or widgets after upgrading.
= 2.0.1 =
*IMPORTANT: Before you install the new version, please contact us so that we can provide you with the necessary details for the settings. 
*This version of beyondConnect uses the new RESTful API with OData standard. You may need to make minor adjustments to the shortcodes or widgets after upgrading.
