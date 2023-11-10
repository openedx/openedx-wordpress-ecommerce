Refund an order with an Open edX product
=========================================

You will learn how to refund an Open edX product from a WooCommerce order.

Index
------
- `Refund an Open edX course product`_
- `Expected behavior`_
- `Next Steps`_

Refund an Open edX course product
----------------------------------
To refund products in order with WooCommerce, you can follow the `WooCommerce Refund Documentation <https://woo.com/document/woocommerce-refunds/>`_.

For a refund to generate an unenrollment in your Open edX platform, the following is required:

- The item you will refund must be an Open edX Course (:doc:`How-to: Create an Open edX Course in WordPress </how-tos/create_openedx_course_wordpress>`).

- You need to add a Quantity for that item.

    .. image:: /_images/decisions/refund-order.png
        :alt: Refund process marking the course to be refunded

Expected behavior
------------------

- When the refund is made, an Enrollment Request with the "Un-enroll" request type will automatically be created in your WordPress site.

- Have a course enrollment with the course and user and the ``is_active`` flag in ``False`` in your Open edX platform.

Next Steps
-----------

- :doc:`Decisions </decisions/index>`.