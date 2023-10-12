=== Open edX WooCommerce Plugin ===
Contributors: felipemontoya, julianrg2, mafermazu
Tags: openedx, open edx, woocommerce, lms, courses
Requires at least: 6.3
Tested up to: 6.3.1
Requires PHP: 8.0
Stable tag: 1.13.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

You can sell your Open edX courses with WooCommerce using this free and open-source WordPress plugin.

== Description ==

The "Open edX WooCommerce Plugin" is a free and open-source WordPress plugin that allows you to integrate WooCommerce with your Open edX platform.

**What does this mean?**
You can create Open edX courses as products in WooCommerce, and when you perform purchase or refund operations for these products, your Open edX platform will reflect these changes.

Here are some things you can do with this plugin:

- **Create Open edX courses as products:** When you create products using WooCommerce, you can designate them as Open edX courses. If you do, you can assign the course mode and course_id you registered in your Open edX platform.

- **Add settings for the connection with Open edX:** You'll have a new option in your WordPress settings to store authentication-related information for your Open edX platform.

- **Enrollment Manager:** You'll have a table that records all enrollment requests made through purchasing products that are Open edX courses.

- **View the enrollment requests from the orders:** When a person purchases WordPress, a WooCommerce order is generated. If an order includes a product that is an Open edX course, you can easily access the related enrollment request created with this plugin.

- **Create enrollments in Open edX:** When an order containing an Open edX course is processed, it automatically creates an enrollment request.
    - You can also include the option to apply the "force" flag, disregarding the course's enrollment end dates.
    - Starting from version Quince of Open edX, you can use the option to create enrollment allowed for non-registered users on the platform.

- **Create soft unenrollments from refunds:** The enrollment record is maintained, but the "is_active" attribute of the enrollment is false. Deleting an "enrollment allowed" is also supported, but only from version Quince.
Obtain enrollment information: This requests the Open edX APIs to retrieve the enrollment status of a user in a course.


Below are some links to help you get started with Open edX WooCommerce Plugin:

- <a href="https://edunext-docs-openedx-woocommerce-plugin.readthedocs-hosted.com/en/latest/quickstart.html" target="_blank">Quick Start Guide</a>
- <a href="https://edunext-docs-openedx-woocommerce-plugin.readthedocs-hosted.com/en/latest/how-tos/index.html" target="_blank">How-to Guides</a>

== Installation ==

= Minimum Requirements =

* PHP 8.0 or greater is recommended
* Wordpress 6.3
* [WooCommerce plugin](https://wordpress.org/plugins/woocommerce)

= Manual installation =

- Download the ZIP version in our [GitHub repository](https://github.com/eduNEXT/openedx-woocommerce-plugin/releases).

<img src="https://i.ibb.co/YTSLYf4/zip-from-release.png" alt="Download ZIP from release">

- Log in to your WordPress dashboard, navigate to the Plugins menu, click "Add New," and upload the ZIP version of this project.

== Frequently Asked Questions ==

= Where can I find documentation and user guides? =

If you need help setting up and configuring this plugin, visit the [documentation on Read the Docs].(https://edunext-docs-openedx-woocommerce-plugin.readthedocs-hosted.com/en/latest/index.html) 

= Where can I report bugs or request features? =

Report bugs and request features on the [GitHub repository](https://github.com/eduNEXT/openedx-woocommerce-plugin/issues).

= Can I contribute? =

Contributions are very welcome. Please read [How To Contribute](https://openedx.atlassian.net/wiki/spaces/COMM/pages/941457737/How+to+Start+Contributing+Code) for details.

This project accepts all contributions, bug fixes, security fixes, maintenance work, or new features. However, please discuss your new feature idea with the maintainers before beginning development to maximize the chances of accepting your change. You can start a conversation by creating a new issue on this repo summarizing your idea.



== Changelog ==

You can find the [Changelog in the GitHub repository.](https://github.com/eduNEXT/openedx-woocommerce-plugin/blob/main/CHANGELOG.md)
