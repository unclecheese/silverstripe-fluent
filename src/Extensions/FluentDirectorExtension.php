<?php

namespace Tractorcow\Fluent\Extensions;

use SilverStripe\CMS\Controllers\ModelAsController;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;
use Tractorcow\Fluent\Fluent;
use Tractorcow\Fluent\Routing\FluentRootURLController;

class FluentDirectorExtension extends Extension
{
    public function updateRules(&$routes)
    {
        unset($routes['$URLSegment//$Action/$ID/$OtherID']);
        // Explicit routes
        foreach (Fluent::locales() as $locale) {
            $url = Fluent::alias($locale);
            $routes[$url.'/$URLSegment!//$Action/$ID/$OtherID'] = array(
                'Controller' => ModelAsController::class,
                Fluent::config()->query_param => $locale
            );
            $routes[$url] = array(
                'Controller' => FluentRootURLController::class,
                Fluent::config()->query_param => $locale
            );
        }

        // Merge all other routes (maintain priority)
        foreach (Config::inst()->get(Director::class, 'rules') as $key => $route) {
            if (!isset($routes[$key])) {
                $routes[$key] = $route;
            }
        }

        // Home page route
        $routes[''] = array(
            'Controller' => FluentRootURLController::class,
        );

        // If we do not wish to detect the locale automatically, fix the home page route
        // to the default locale for this domain.
        if (!Fluent::config()->detect_locale) {
            $routes[''][Fluent::config()->query_param] = Fluent::default_locale(true);
        }

        // If default locale doesn't have prefix, replace default route with
        // the default locale for this domain
        if (Fluent::disable_default_prefix()) {
            $routes['$URLSegment//$Action/$ID/$OtherID'] = array(
                'Controller' => ModelAsController::class,
                Fluent::config()->query_param => Fluent::default_locale(true)
            );
        } else {
            $routes['$URLSegment//$Action/$ID/$OtherID'] = ModelAsController::class;
        }
    }
}
