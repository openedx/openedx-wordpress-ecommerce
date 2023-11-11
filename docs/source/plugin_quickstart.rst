Quickstart
===========

----------------------------------------------------------------------------------------------------
Connect the Open edX Commerce plugin for WordPress to the Enrollment API of your Open edX Platform
----------------------------------------------------------------------------------------------------

Let us start installing and configuring the Open edx Commerce plugin to connect your WordPress site with the enrollment APIs from your Open edX platform.

Index
------
- `Installation`_
- `Configure the Plugin`_
- `Create an Enrollment Request`_
- `Next Steps`_

Installation
-------------

Requirements
^^^^^^^^^^^^^

- PHP 8.0 or greater is recommended
- Wordpress 6.3.1 or greater
- `WooCommerce plugin. <https://wordpress.org/plugins/woocommerce>`_

Manual Installation
^^^^^^^^^^^^^^^^^^^^

#. Download the ZIP version on `the release page in the GitHub repository <https://github.com/openedx/openedx-wordpress-ecommerce/releases>`_.

    .. image:: /_images/zip-from-release.png
        :alt: ZIP from releases

#. Log in to your WordPress admin dashboard, navigate to the **Plugins menu** in the sidebar and click **Add New**.

#. Upload the ZIP version of this project.

#. Activate the plugin.


Configure the Plugin
----------------------

#. In the sidebar of the WordPress admin dashboard, go to **Settings**, then to **Open edX Sync Plugin Settings**, and fill that form with your **LMS Open edX platform domain** and **a client_id and client_secret from an OAuth application** in your Open edX platform.

#. Click **Save Changes** and then **Generate JWT Token**.

    .. image:: /_images/how-tos/create_an_openedx_app/openedx-sync-plugin-settings.png
        :alt: Expected Result JWT token

#. You should see a **New token generated** message.

    .. note:: To know more about how to fill this settings, you can visit :doc:`How-to: Create an Open edX Application for the Plugin Settings </how-tos/create_an_openedx_app>`.


Create an Enrollment Request
-----------------------------

This plugin is connected with your Open edX platform; if you could create your JWT token. Now, if you want to rectify that, you can create enrollments. To do that, you need to generate enrollment requests manually by following these steps:

#. Enter the Enrollments Manager option in your WordPress dashboard's Open edX Sync tab, and click **Add New**.

    .. image:: /_images/how-tos/create_enroll_request/menu.png
        :alt: Enrollments Manager option

#. Fill the form with a **course ID** from a course in your Open edX platform, **an email from an existing user**, and **a course mode** from the course you use in the course ID field. We will use **Enroll** as a request type.

#. Create an enrollment by clicking **Save and update Open edX**.

    .. image:: /_images/how-tos/create_enroll_request/expected-result.png
        :alt: Expected Result Enroll Request


    .. note:: To know more about how to fill this settings, you can visit :doc:`How-to: Create enrollment requests manually </how-tos/create_enrollment_requests_manually>`.


Next Steps
-----------

- :doc:`Tutorial: Configure your WordPress so that purchases and refunds automatically generate enrollments </tutorials/configuration_to_automate_enrolls>`.
