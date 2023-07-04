### Openedx Woocommerce Plugin

- Contributors: [List of WordPress.org user IDs]
- Donate link: [https://edunext.co/](https://edunext.co/)
- Tags: comments, spam
- Requires at least: 0.0.0
- Tested up to: 6.2.2
- Requires PHP: 8.2.7
- Stable tag: 0.0.0
- License: fill
- License URI: fill

# Openedx Woocommerce Plugin

The WooCommerce Integration plugin aims to seamlessly integrate the Open edX platform with the popular WooCommerce e-commerce system. It allows users to purchase courses through WooCommerce, which automatically creates enrollments in Open edX. The plugin streamlines workflows and eliminates the need for a separate e-commerce service within the Open edX platform.

## Description

The WooCommerce Integration plugin is a robust solution designed to facilitate the integration of the Open edX platform with the WooCommerce e-commerce system. By leveraging the extensive capabilities of WooCommerce, this plugin enables users to easily purchase courses and seamlessly enroll in them through Open edX.

The plugin addresses the challenges posed by the deprecation of the current e-commerce service used in the Open edX community. It provides an alternative solution by integrating Open edX directly with a third-party commerce platform, reducing the maintenance burden and unlocking opportunities for a wider audience to access e-commerce services.

With the WooCommerce Integration plugin, the process of purchasing a course becomes streamlined. When a course is bought in WooCommerce, the plugin automatically creates an enrollment for that course in the Open edX platform. Conversely, if an order is refunded, the associated enrollment is unenrolled, ensuring synchronization between the two systems.

Key features of the plugin include:

- WooCommerce Plugin: A WordPress plugin with WooCommerce as a dependency, offering a robust handler for order actions and easy association of WooCommerce products with Open edX courses. It provides a local record in WordPress for order fulfillment status and allows actions like retry or edit.

- Open edX API: An API endpoint within the Open edX platform that enables the creation of enrollments and synchronization with WooCommerce. The API supports registering enrollments for existing users or creating CourseEnrollmentAllowed objects for new users.

- Extensibility: The plugin is designed to be extensible, allowing WordPress developers to connect custom logic to themes or other plugins through extension points. This flexibility enables power users and developers to handle innovative and complex scenarios related to e-commerce.

## Installation

The installation of the plugin is simple and can be done in a few steps:

1. **Download the Plugin**
   - Go to the latest release of the plugin on the project's repository.
   - Download the .zip file that contains the plugin.

2. **Access the WordPress Admin Area**
   - Log in to your WordPress website.
   - Navigate to the admin area.

3. **Navigate to the Plugins Section**
   - In the WordPress admin area, locate the "Plugins" section.
   - It is usually found in the sidebar menu.

4. **Add a New Plugin**
   - Click on "Add New" within the Plugins section.

5. **Upload the Plugin**
   - Select the "Upload Plugin" button.
   - Choose the previously downloaded .zip file of the plugin.
   - Click on "Install Now" to start the installation process.

6. **Activate the Plugin**
   - Once the installation is complete, click on the "Activate" button to activate the plugin.

7. **Start Using the Plugin**
   - The plugin is now installed and activated.
   - You can begin using its features and configure its settings as needed.

Please refer to any accompanying documentation or instructions provided with the plugin for specific configuration steps or additional setup requirements. If you encounter any issues during the installation process, you can consult the plugin's support resources or seek assistance from the WordPress community.

## Frequently Asked Questions

### A question that someone might have

An answer to that question.

### What about foo bar?

Answer to foo bar dilemma.

## Screenshots

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png` (or jpg, jpeg, gif).
2. This is the second screen shot

## Changelog

### 1.0
* A change since the previous version.
* Another change.

## Arbitrary section

You may provide arbitrary sections, in the same format as the ones above. This may be of use for extremely complicated plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or "installation." Arbitrary sections will be shown below the built-in sections outlined above.
