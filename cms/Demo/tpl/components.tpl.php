<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   Web\{APPNAME}
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;
?>

<div class="content">
    <div class="floater">
        <h1>CMS</h1>
        <p>The content and the components of an application can be modified in the CMS module. In order to test this you can <a href="<?= UriFactory::build('{/prefix}/backend/cms/application/list'); ?>">login</a> to the backend, navigate to the CMS module and modify this application.</p>
        <h2>Content</h2>
        <p>Depending on the type of application you can have many different types of content. For example you might have multiple pages where every page displays specific types of content (e.g. single page content, posts, lists etc.). With the CMS contents you can create different types of content and load them on the specific pages. Generally, contant can be created by writing markdown (incl. custom markdown elements which may reference interfaces to other modules).</p>

        <p>In addition to use the CMS content you may also interact with many modules in order to show specific parts of their content in the application. One option could be to show specific news posts, image files from the media module, a calendar, navigation elements, a shop etc. These types of contents must be loaded and implement in the application templates and controllers.</p>
        <h2>Files</h2>
        <p>In the files section of the CMS you can directly modify all application files, move them to different directories, delete them and upload new files. Changes take immediate effect.</p>


        <h1>Structure</h1>
        <p>While an application can have various structural components in the following section we will discuss the most important elements.</p>

        <h1>Info</h1>
        <p>Every application requires a info file which contains general information about the application. This file is used in order to define the name, category, dependencies as well as author and version information.</p>

<code><pre>
{
    "name": {
        "id": 1999100000,
        "internal": "Demo",
        "external": "Demo"
    },
    "category": "Web",
    "version": "1.0.0",
    "requirements": {
        "phpOMS": "1.0.0",
        "phpOMS-db": "1.0.0"
    },
    "creator": {
        "name": "Orange Management",
        "website": "www.spl1nes.com"
    },
    "description": "The backend application.",
    "directory": "Demo",
    "dependencies": {}
}
</pre></code>

        <h2>Application</h2>
        <p>The application file is basically the main entry point. In this file the core aspects of the application are setup e.g. routing, localization, dispatching, user data, cache, database, ...</p>

        <blockquote>The concrete implementation depends on the type of application but as a starting point you may want to checkout the source code of this application.</blockquote>

        <h2>Routes</h2>
        <p>In the routing file you can specify what kind of controller function (page) should be loaded. In here it's also possible (optionally) to define the request method, permissions and data validation in order to redirect to the controller function.</p>

        <p>While it is recommended to use the routes file for these definitions it's also possible to do this directly in the application code.</p>

<code><pre>
&lt;?php declare(strict_types=1);

use phpOMS\Router\RouteVerb;

return [
    '^(\/[a-zA-Z]*|\/)$' => [
        [
            'dest' => '\Web\Demo\Controller\AppController:viewFront',
            'verb' => RouteVerb::GET,
        ],
    ],
    '^(\/[a-zA-Z]*|\/)/components(\?.*|$)$' => [
        [
            'dest' => '\Web\Demo\Controller\AppController:viewComponents',
            'verb' => RouteVerb::GET,
        ],
    ],
    '^(\/[a-zA-Z]*|\/)/imprint(\?.*|$)$' => [
        [
            'dest' => '\Web\Demo\Controller\AppController:viewImprint',
            'verb' => RouteVerb::GET,
        ],
    ],
    '^(\/[a-zA-Z]*|\/)/terms(\?.*|$)$' => [
        [
            'dest' => '\Web\Demo\Controller\AppController:viewTerms',
            'verb' => RouteVerb::GET,
        ],
    ],
    '^(\/[a-zA-Z]*|\/)/privacy(\?.*|$)$' => [
        [
            'dest' => '\Web\Demo\Controller\AppController:viewDataPrivacy',
            'verb' => RouteVerb::GET,
        ],
    ],
];
</pre></code>

        <h2>Controller</h2>
        <p>The controller usually implements the end-points / functions defined in the routes. In here input validation, model initialization, template definitions and API calls are implemented. You can define as many controllers as you want to for your application in order to keep it nice and structure. For global applications like the Backend application, API and other well defined applications such as Support, HumanResourceTimeRecording etc. many modules already implement their own controllers which can be used.</p>

        <h2>View</h2>
        <p>In many cases the default View from the framework can be used but you maybe want to define your own views for more granular view logic you can do so and load them in the controllers. The logic in these files should be limited to view logic.</p>

        <h2>Assets</h2>
        <p>You can provide as many assets in an application as you like. This includes stylesheets, javascript files, images, manifests, service worker, etc. Depending on the assets there are different ways how and where to load them. Stylesheets may be loaded directly in the template, during the application setup in the application file or in the controllers. Javascript files and stylesheets can be loaded in the head, inline or at the bottom/end of the page.</p>

        <blockquote>Of course you may also load uploaded assets from other modules (e.g. Media module)</blockquote>

        <h2>Localization</h2>
        <p>Localized content is automatically loaded from the CMS module or other modules which are referenced in an application. For application specific localization (e.g. static translations in templates) you may provide language files <strong>*.lang.php</strong> which are loaded during the application setup based on the user and/or application configuration.</p>

<code><pre>
&lt;?php
declare(strict_types=1);

return [[
    ':meta'       => 'Demo application in english',
    'AppTitle'    => 'Demo Application',
    'AppSubtitle' => 'Simple application example for the CMS module.',
]];
</pre></code>

        <h2>Templates</h2>
        <p>The template files contain structural components (layouts) and static page elements. You can pass data to the templates from the controller through the view and you can access the request object to read the request data directly. The logic in these files should be limited template logic.</p>
    </div>
</div>