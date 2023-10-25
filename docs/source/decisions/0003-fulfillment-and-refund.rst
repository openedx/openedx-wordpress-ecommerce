3. Fulfillment and refund
==========================

Status
******

Accepted


Context
*******

This plugin was created focusing on workflows that are streamlined:

- A course bought in WooCommerce will have the consequence that the enrollment for that course is made in the Open edX platform, understood with the enrollment as a seat in a specific course run.
- If the order is refunded, the associated enrollment will be unenrolled using the soft delete(using the is_active flag) used in regular unenrollments.


Decisions
*********
- When someone generates a WooCommerce order, which changes the status to 'processing', the plugin generates an enrollment request using the Open edX courses items in the order and the email used in the billing address. The plugin uses the hook ``woocommerce_order_status_changed`` listed in `WooCommerce hooks`_, and it uses 'processing' because that means the payment was completed, and the admin can process/post the items. `WooCommerce payment complete function`_.

    .. image:: /_images/decisions/create-openedx-course-as-product.png
        :alt: Product data section in a WooCommerce Product

Screenshot of a product data section in a WooCommerce Product.

- When a WordPress admin refunds an Open edX Course in a WooCommerce Order, the plugin creates an enrollment request of type unenroll. The plugin uses the hook ``woocommerce_order_refunded``, also listed in `WooCommerce hooks`_, and it uses the billing email and the Open edX courses marked as refund items in the refund process.

    .. image:: /_images/decisions/refund-order.png
        :alt: Refund process marking the course to be refunded

Screenshot of a refund process marking the course to be refunded.


Consequences
************
- We shouldn't set our Open edX Courses as Downloables WooCommerce Products because if that happened and someone only bought downloadable products, the order never passed through the 'processing' status. The plugin wouldn't create the enrollment request.
- Refunding is a manual process; a customer can't do it alone.
- It is our responsibility to avoid enrollment errors by setting our product as an Open edX course and setting a correct course_id and course_mode to the enrollment trigger by an order will be performed well.


Rejected Alternatives
*********************

- Make the hooks that trigger the request variables: to keep the plugin simple.

References
**********

- `WooCommerce hooks`_.
- `WooCommerce payment complete function`_.

.. _WooCommerce hooks: https://woocommerce.github.io/code-reference/hooks/hooks.html
.. _WooCommerce payment complete function: https://github.com/woocommerce/woocommerce/blob/abc476a005b405068b07bd4c50d1797c3dcc396d/plugins/woocommerce/includes/class-wc-order.php#L122
