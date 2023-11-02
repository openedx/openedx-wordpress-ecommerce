4. Naming of the plugin
========================

Status
******

Accepted


Context
*******

Initially, we wanted a very descriptive name for this plugin (Open edX WooCommerce Plugin) to avoid confusion because there are different types of plugins in Open edX. Still, for `WordPress plugin guidelines`_, we cannot submit a plugin with the word "plugin" or trademarks as "woocommerce."

Decision
********

- Change the name of the plugin to "Open edX Commerce."


Consequences
************

- Change the name in all the code, including files and database names.

- The WordPress plugin doesn't have the same name as the GitHub repository to store more information about this plugin that adds information in the Open edX context.


Rejected Alternatives
*********************

- Use "ecommerce" in the name: to avoid confusion with the current e-commerce service.


References
**********

- `WordPress plugin guidelines`_.
- `Current ecommerce service`_.


.. _WordPress plugin guidelines: https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/#17-plugins-must-respect-trademarks-copyrights-and-project-names
.. _Current ecommerce service: https://github.com/openedx/ecommerce
