CONTENTS OF THIS FILE
---------------------

* Introduction
* Requirements
* Recommended modules
* Installation
* Configuration
* Maintainers

INTRODUCTION
------------

TinyMCE is a platform independent web based Javascript HTML WYSIWYG editor
control released as Open Source under LGPL by Moxiecode Systems AB.
It has the ability to convert HTML TEXTAREA fields or other HTML elements to
editor instances.

* For a full description of the module, visit the project page:
  https://www.drupal.org/project/tinymce

* To submit bug reports and feature suggestions, or track changes:
  https://www.drupal.org/project/issues/tinymce

REQUIREMENTS
------------

This module requires the following modules:

* Text Editor (Core module)

INSTALLATION
------------

* Install as you would normally install a contributed Drupal module. Visit
  https://www.drupal.org/project/tinymce for further information.

CONFIGURATION
-------------

* Configure TinyMCE in Administration » Configuration » Text formats and
  Editors

  - Configure any text format to use TinyMCE as editor

    Once selected as editor for a given text format, TinyMCE will be displayed
    for each formatted text field using this text format.

  - Configure TinyMCE options

    Use the editor settings field to configure TinyMCE with a json object as
    awaited by the library to initialize it. More details here:
    https://www.tiny.cloud/docs/demo/full-featured/.

  - Download the library (self hosted version)

    If you choose to host the library yourself you can download it here:
    https://www.tiny.cloud/get-tiny/self-hosted/.
    This usage prevents any notice to appear on the editor (see cloud version).

  - Obtain an API key (cloud version)

    If you choose to use the editor in its cloud version, an API key is
    required. Otherwise, a notice will be displayed on the editor to inform the
    user of a registration requirement. To register your website domain and
    remove this notice, please see this page:
    https://www.tiny.cloud/docs/quick-start/#step3addyourapikey.

  - Enable image upload

    To enable image upload in the editor, please add the following parameters
    to the editor settings in the json object:
    ```
    "automatic_uploads": true,
    "images_upload_url": "/tinymce/upload",
    ```
    Please note that /tinymce/upload will be accessible only to user with
    TinyMCE "Upload files" permission.

* Customize the way TinyMCE is loaded in Administration » Configuration »
  Text formats » TinyMCE Settings tab.

MAINTAINERS
-----------

Current maintainers:
* Nicolas Loye (nicoloye) https://www.drupal.org/u/nicoloye
* Léo Prada (nixou) https://www.drupal.org/u/nixou
* Hakim Rachidi (hakimr) https://www.drupal.org/u/hakimr

Previous maintainers:
* kreynen http://drupal.org/user/48877
* Allie Micka http://drupal.org/user/15091
* m3avrck http://drupal.org/user/12932
* nedjo http://drupal.org/user/4481
* Steve McKenzie http://drupal.org/user/45890
* ufku http://drupal.org/user/9910
* Matt Westgate <drupal AT asitis DOT org> and Jeff Robbins <robbins AT jjeff DOT com>
* Richard Bennett <richard.b AT gritechnologies DOT com>

This project has been sponsored by:
* Actency:
  We are actively engaged in the development and promotion of Drupal.
  Within the community, we share our experience and communicate our passion, in
  particular on the occasion of major events such as Drupagora, DrupalCamp,
  DrupalCon. In addition, we organized the first MeetUp in eastern France, in
  Strasbourg. Visit https://www.actency.fr for more information.
