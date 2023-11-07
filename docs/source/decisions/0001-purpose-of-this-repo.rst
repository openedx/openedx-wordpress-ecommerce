1. Purpose of this Repo
=======================

Status
******

Accepted


Context
*******

The Open edX community currently uses the e-commerce repo to manage commerce-related tasks. However, that repo is in `the process of being deprecated`_. 2U is building an extensible and pluggable next-generation commerce platform (commerce coordinator). However, this platform may be more complex and require more technical capabilities than all operators have. Therefore, `Axim looked for proposals for creating a WooCommerce Discovery`_ to have another way to sell Open edX courses easily.

In that discovery (`WooCommerce Discovery`_), we realized we needed to create a Wordpress plugin that would connect the Open edX APIs with the WooCommerce orders in Wordpress. 

Decision
********

- Have a WordPress plugin that allows the connection between Open edX and WooCommerce.


Consequences
************

- We must install a WordPress plugin to sell Open edX courses with WooCommerce.
- We need to maintain this plugin.


Rejected Alternatives
*********************

- Use WooCommerce Webhooks: WooCommerce offers a natively supported way of managing webhooks. Still, the payload from a paid order did not include metadata that could be used to link the WooCommerce product to a course. It would have required then that the openedx plugin contained e-commerce information to link courses, programs, or discounts.

- Maintain the Open edX eduNEXT extension in its current form: eduNEXT has published about five years ago with a similar plugin called `Open edX LMS and WordPress integrator`_. The code for this plugin is also available on GitHub. However, after years of supporting different initiatives using this plugin, we have concluded that the order handler needs to be more robust and built with a stronger foundation. Also, this plugin initially started as a way of making the information contained in the user-info cookie available to the menu header, and this and other such features grew organically into a plugin that needs to be better defined and focused. 


References
**********

- `Ecommerce deprecation`_.
- `Axim looked for proposals for creating a WooCommerce Discovery`_.
- `WooCommerce Discovery`_.
- `Open edX LMS and WordPress integrator`_.

.. _the process of being deprecated: https://github.com/openedx/public-engineering/issues/22
.. _Ecommerce deprecation: https://github.com/openedx/public-engineering/issues/22
.. _Axim looked for proposals for creating a WooCommerce Discovery: https://discuss.openedx.org/t/tcril-funded-contribution-woocommerce-discovery/9337
.. _WooCommerce Discovery: https://docs.google.com/document/d/1gImq4DFy3B_JSZlH3tCj5bmPQXji0OCnw1SbGB8bVxw/edit?usp=sharing
.. _Open edX LMS and WordPress integrator: https://wordpress.org/plugins/edunext-openedx-integrator/
