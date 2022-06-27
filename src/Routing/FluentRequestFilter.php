<?php
namespace Tractorcow\Fluent\Routing;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\Middleware\HTTPMiddleware;
use Tractorcow\Fluent\Fluent;

/**
 * Fluent initialisation filter to run during director init
 *
 * @package fluent
 * @author Damian Mooyman <damian.mooyman@gmail.com>
 */
class FluentRequestFilter implements HTTPMiddleware
{
    public function process(HTTPRequest $request, callable $delegate)
    {
        // Ensures routes etc are setup
        // We need to inject the presented session temporarily, as there is no current controller set
        FluentSession::with_session($request->getSession(), function () {
            Fluent::init();
        });

        return $delegate($request);
    }

}
