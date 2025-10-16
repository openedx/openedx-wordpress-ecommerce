=== Open edX Commerce ===
Contributors: felipemontoya, julianrg2, mafermazu
Tags: openedx, open edx, ecommerce, lms, courses
Requires at least: 6.3
Tested up to: 6.9
Requires PHP: 8.0
Stable tag: 2.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

You can sell your Open edX courses with WooCommerce using this free and open-source WordPress plugin.

== Description ==

The "Open edX Commerce" package is a free and open-source WordPress plugin that allows you to integrate WooCommerce with your Open edX platform.

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

- **Obtain enrollment information:** This requests the Open edX APIs to retrieve the enrollment status of a user in a course.

Below are some links to help you get started with Open edX WooCommerce Plugin:

- <a href="https://docs.openedx.org/projects/wordpress-ecommerce-plugin/en/latest/plugin_quickstart.html" target="_blank">Quick Start Guide</a>
- <a href="https://docs.openedx.org/projects/wordpress-ecommerce-plugin/en/latest/how-tos/index.html" target="_blank">How-to Guides</a>

**Note**

This plugin calls the APIs from <a href="https://github.com/openedx/edx-platform" target="_blank">Open edX Platform</a>.

More information about the API connection can be found in <a href="https://docs.openedx.org/projects/wordpress-ecommerce-plugin/en/latest/decisions/0002-api-connection.html" target="_blank">Decisions: API connection</a>.

To learn more, you can visit the <a href="https://openedx.org/terms-of-use/" target="_blank">Open edX Terms of Service</a>.

This plugin is maintained by <a href="https://edunext.co/" target="_blank">edunext</a>.

== Installation ==

= Minimum Requirements =

* PHP 8.0 or greater is recommended
* Wordpress 6.3
* [WooCommerce plugin](https://wordpress.org/plugins/woocommerce)

= Automatic installation =

To automatically install Open edX Commerce, log in to your WordPress dashboard. Then, navigate to the Plugins menu and click on "Add New."

In the search field, type "Open edX Commerce" and click "Search Plugins." Once you find the plugin, you can view its details and install it by clicking "Install Now." WordPress will handle the rest of the installation process for you.

= Manual installation =

1. Download the ZIP version on [the release page in the GitHub repository](https://github.com/openedx/openedx-wordpress-ecommerce/releases).

<img src="docs/source/_images/zip-from-release.png" alt="Download ZIP from release">

2. Log in to your WordPress admin dashboard, navigate to the Plugins menu in the sidebar and click **Add New**.

3. Upload the ZIP version of this project.

4. Activate the plugin.

== Quickstart ==

Let's start installing and configuring the Open edx Commerce plugin to connect your WordPress site with the enrollment APIs from your Open edX platform.

[Link to the Quickstart in the documentation.](https://docs.openedx.org/projects/wordpress-ecommerce-plugin/en/latest/plugin_quickstart.html)

== Frequently Asked Questions ==

= Where can I find documentation and user guides? =

If you need help setting up and configuring this plugin, visit the [documentation on Read the Docs].(https://docs.openedx.org/projects/wordpress-ecommerce-plugin/en/latest/index.html) 

= Where can I report bugs or request features? =

Report bugs and request features on the [GitHub repository](https://github.com/openedx/openedx-wordpress-ecommerce/issues).

= Can I contribute? =

Contributions are very welcome. Please read [How To Contribute](https://openedx.atlassian.net/wiki/spaces/COMM/pages/941457737/How+to+Start+Contributing+Code) for details.

This project accepts all contributions, bug fixes, security fixes, maintenance work, or new features. However, please discuss your new feature idea with the maintainers before beginning development to maximize the chances of accepting your change. You can start a conversation by creating a new issue on this repo summarizing your idea.

== Changelog ==

You can find the [Changelog in the GitHub repository.](https://github.com/openedx/openedx-wordpress-ecommerce/blob/main/CHANGELOG.md)
