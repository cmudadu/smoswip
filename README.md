# smoswip
Sistema MOnitoraggio Sensori Web In Php

# System Requirements

- Web server with URL rewriting
- PHP 7.4 or newer

# Step 1: Install Composer

Don’t have Composer? It’s easy to install by following the instructions on their download page.

# Step 2: Install Slim

We recommend you install Slim with Composer. Navigate into your project’s root directory and execute the bash command shown below. This command downloads the Slim Framework and its third-party dependencies into your project’s vendor/ directory.

composer require slim/slim:"4.*"

# Step 3: Install a PSR-7 Implementation and ServerRequest Creator

Before you can get up and running with Slim you will need to choose a PSR-7 implementation that best fits your application. In order for auto-detection to work and enable you to use AppFactory::create() and App::run() without having to manually create a ServerRequest you need to install one of the following implementations:
Slim PSR-7

composer require slim/psr7

# Deploying to a shared server

If your shared server runs Apache, then you need to create a .htaccess file in your web server root directory (usually named htdocs, public, public_html or www) with the following content:

<IfModule mod_rewrite.c>
   RewriteEngine on
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^ index.php [QSA,L]
</IfModule>

