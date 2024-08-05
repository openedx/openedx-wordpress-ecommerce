Create an Open edX Application for the Plugin Settings
=======================================================

You will learn how to create an Open edX Application for filling out the form in the Open edX Sync Plugin Settings in our WordPress Settings.

If you are using `Tutor`_ for managing your Open edX instance, you can install `tutor-contrib-wordpress`_ and use its commands assist with the
configuration and integration of the Open edX platform with an existing WordPress site.

Index
-------
- `Requisites`_
- `Create an Open edX Application to Configure the Open edX Commerce plugin`_
- `Next Steps`_

Requisites
-----------

Have access to a Django admin dashboard for your Open edX platform.

Create an Open edX Application to Configure the Open edX Commerce plugin
-------------------------------------------------------------------------

#. Go to Applications in your Django Admin in your Open edX instance. (URL: `<domain>/admin/oauth2_provider/application/`)

    .. image:: /_images/how-tos/create_an_openedx_app/applications.png
        :alt: Applications in Django Admin

#. Create an Application with a staff user and **Client Credentials** as **Authorization grant type**.

    .. note:: Why do we need a staff user? Because we use those credentials to create, edit, and delete enrollments, which are staff operations.

    .. image:: /_images/how-tos/create_an_openedx_app/add-application.png
        :alt: Add Application

#. Use your platform **domain** and your **application's client id and client secret** in the Open edX Sync Plugin Settings in your WordPress Settings.

    .. image:: /_images/how-tos/create_an_openedx_app/openedx-sync-plugin-settings.png
        :alt: Open edX Sync Plugin Settings

#. Test the credentials by clicking **Save Changes** and **Generate JWT Token**. 

.. note:: If you do not have credentials to enter the Django Admin, you need to contact an operator of your Open edX instance to provide you the **client id and client secret of an Application** with **Client Credentials** as **Authorization grant type** and **staff user**.

Next Steps
-----------

- :doc:`How-to: Create enrollment requests manually </how-tos/create_enrollment_requests_manually>`.

.. _Tutor: https://docs.tutor.edly.io
.. _tutor-contrib-wordpress: https://github.com/CodeWithEmad/tutor-contrib-wordpress
