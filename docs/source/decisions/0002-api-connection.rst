2. API connection
==================

Status
******

Accepted


Context
*******

We must connect this plugin with the available APIs in the Open edX platform to perform commerce actions raised in `Purpose of this repo`_.


Decisions
*********

- This plugin uses the OAuth2 and JWT authentication following the standards in Open edX from the `OEP-42 Authentication`_.
- To generate the JWT token, we use information such as client_id and client_secret as specified in the doc of `How to Use the REST API`_.
- The JWT must be created with a Staff user to use all the endpoints this plugin needs.
- We use the LMS APIs to do the basic functionalities.
    - To obtain info about enrollments, we use ``GET /enrollment/v1/enrollments/``
    - To create enrollments, ``POST /enrollment/v1/enrollment``
    - To perform an unenroll, we use the same endpoint to create enrollment, but we set the attribute is_active in False.
    - We can pass the flag force in the request to allow you to enroll; disregard the course's enrollment end dates.
    - To know if a user exists in the platform, ``GET /user/v1/accounts``
    - If the user does not exist yet, we can get, create, or delete an enrollment allowed with: ``GET, POST, DELETE /api/enrollment/v1/enrollment_allowed/``


Consequences
************

- You need to create an OAuth Application in your platform with a Staff user to use this plugin.
- The backward compatibility depends on the JWT support in previous Open edx versions.
- We can use the course enrollment allowed API since Quince.
- The actions of this plugin are restricted by the endpoints mentioned above. For example, We can't enroll someone in a course that is in invite-only mode.


Rejected Alternatives
*********************

- Create another plugin to add the needed endpoints: we could use the existing `LMS APIs`_, and we added the course enrollment-allowed API to the edx-platform and brought email support in some of the endpoints.


References
**********

- `Purpose of this repo`_.
- `OEP-42 Authentication`_.
- `How to Use the REST API`_.
- `LMS APIs`_.

.. _Purpose of this repo: 0001-purpose-of-this-repo.html
.. _OEP-42 Authentication: https://docs.openedx.org/projects/openedx-proposals/en/latest/best-practices/oep-0042-bp-authentication.html#oauth2-and-jwts
.. _How to Use the REST API: https://docs.openedx.org/projects/edx-platform/en/latest/how-tos/use_the_api.html
.. _LMS APIs: https://docs.openedx.org/projects/edx-platform/en/latest/references/lms_apis.html#lms-apis
