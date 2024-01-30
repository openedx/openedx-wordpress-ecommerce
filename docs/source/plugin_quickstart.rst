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
- `Create a Product`_
- `Create payment method`_
- `Buy product on LMS as student`_

Installation
-------------

Requirements
^^^^^^^^^^^^^
- Setup https://github.com/edly-io/tutor-contrib-wordpress.git plugin
- WooCommerce plugin.
- Payment Plugins for PayPal WooCommerce plugin.

Manual Installation
^^^^^^^^^^^^^^^^^^^^
#. Download the ZIP version on `the release page in the GitHub repository <https://github.com/openedx/openedx-wordpress-ecommerce/releases>`_.
#. Log in to your WordPress admin dashboard, navigate to the **Plugins menu** in the sidebar, and click **Add New**.
#. Upload the ZIP version of this project.
#. Activate the plugin.

Configure the Plugin
----------------------
#. In the sidebar of the WordPress admin dashboard, go to **Settings**, then to **Open edX Sync Plugin Settings**, and fill that form with your **LMS Open edX platform domain** and **a client_id and client_secret of discovery|discovery-dev from an OAuth application** in your Open edX platform.
#. Click **Save Changes** and then **Generate JWT Token**.
#. You should see a **New token generated** message.

Create a Product
-----------------------------
This plugin is connected with your Open edX platform; if you could create your JWT token. Now, you can create products. To do that, you need to create products manually by following these steps:
#. Enter the Add new option in your WordPress dashboard's products tab.
#. Write the Product's name, below description go to openedx course fill fields(keep in mind the course mode you are setting should be in the course mode table of that course)
#. Create the product by clicking **Publish**.
#. Copy url below the product name.

Create payment method
-----------------------------
Install and activate WooCommerce and Payment Plugins for PayPal WooCommerce plugin in the plugins tab.
#. Enter the Settings option in your WordPress dashboard's Woocommerce tab.
#. Go into the payments and enable the payment method you want, in the case of PayPal fill client ID, and secret key in the API Settings tab(If you are using the website locally you can't create a webhook as it requires the site to be https.
#. Save changes by clicking **Save Changes**.

Buy product on LMS as student
-----------------------------
Go to the browser where you logged in as a learner.
#. Enter the link copied from below the product name.
#. Go to the cart, buy the product and checkout with the payment method you like.
#. You can check your enrollment on the admin side in the enrollments manager in open edx sync tab.
