Configure your WordPress so that Purchases and Refunds Automatically Generate Enrollments
==========================================================================================

At the end of this tutorial, you will have your WordPress fully configured to sell your Open edX courses automatically.

Index
------
- `Requirements`_
- `Create an Open edX Course in WordPress`_
- `Create a WooCommerce Order`_
- `Next Steps`_

Requirements
-------------

To follow this tutorial, you need WooCommerce installed, and your Open edX Commerce plugin must already be connected to your Open edX platform. If you still need to configure it, please complete the :doc:`Quickstart </plugin_quickstart>` before proceeding with this tutorial.


Create an Open edX Course in WordPress
---------------------------------------

To create an Enrollment Request (for enrollment or unenrollment) when someone buys or refunds a course, we must create Open edX Course products in WordPress.

#. In the sidebar of the WordPress admin dashboard, go to **Products** and then to **Add New**.

#. Click the **Open edX Course checkbox** and fill in the information required.

    .. image:: /_images/how-tos/create_openedx_course_product/add-base-info.png
        :alt: Add new Open edX Course product.


    .. warning:: We recommend not to use the **Downloadable check** when you use the **Open edX Course check** to avoid problems creating the enrollment. For more information, visit :doc:`Decisions: Fulfillment and Refund </decisions/0003-fulfillment-and-refund>`.

#. Add more information in your product as a title, description and image.

#. Save or update your changes.

Create a WooCommerce Order
----------------------------

We should create an order and check if the integration is functioning correctly.

Creating an Order from your site
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

#. Got to your product page in your WordPress site
#. Add your course to the cart.
#. Proceed to checkout.
#. Add the **email of the user** you want to enroll in the **billing email address**.
#. Proceed to payment.

    .. note:: You can see this process in the first 5 minutes of this `video demo <https://www.youtube.com/watch?v=TuDT-qwQdyE>`_, in which we used Paypal Sandbox for testing purposes.


Creating an Order Manually
^^^^^^^^^^^^^^^^^^^^^^^^^^^

#. In the sidebar of the WordPress admin dashboard, go to **WooCommerce**, then to **Orders**, and **Add New**.

#. Edit the **billing information** and add **the email of the user** you want to enroll in the billing email address.

#. Select **Add item(s)**, then **Add product(s)**, and select the **Open edX course** you created.

#. Create the order.

#. Change the Status to **Processing**.

    .. image:: /_images/quickstart/update-the-order-manually.png
        :alt: Update the WooCommerce order.

#. Update the order.

Expected Behavior
^^^^^^^^^^^^^^^^^^

- Have a new entry in the Enrollment Requests with an order associated.

    .. image:: /_images/quickstart/order-to-request.png
        :alt: View request from an order.

- Have a course enrollment with the course and user in your Open edX platform.

    .. image:: /_images/quickstart/openedx-course-enrollments.png
        :alt: Course enrollment in your Open edX platform.

Next Steps
-----------

- :doc:`Decisions </decisions/index>`.