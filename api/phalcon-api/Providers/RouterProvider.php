<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Providers;

use Phalcon\Api\Http\Response;
use Phalcon\Api\Middleware\AuthenticationMiddleware;
use Phalcon\Api\Middleware\CORSMiddleware;
use Phalcon\Api\Middleware\NotFoundMiddleware;
use Phalcon\Api\Middleware\ResponseMiddleware;
//use Phalcon\Api\Middleware\TokenUserMiddleware;
//use Phalcon\Api\Middleware\TokenValidationMiddleware;
//use Phalcon\Api\Middleware\TokenVerificationMiddleware;
use Phalcon\Config;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Events\Manager;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\Collection;
use Phalcon\Api\Routes\RoutesInterface;
use Phalcon\Api\Routes\RouteInterface;

/**
 * Class RouterProvider
 *
 * @see https://github.com/nueko/phalcon-jsonapi/blob/master/app/Application.php#L93
 * @package Phalcon\Api\Providers
 */
class RouterProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container): void
    {
        /** @var Micro $application */
        $application   = $container->getShared('application');
        /** @var Manager $eventsManager */
        $eventsManager = $container->getShared('eventsManager');
        /** @var Config $config */
        $config = $container->getShared('config');

        $this->attachRoutes($application, $config);
        $this->attachMiddleware($application, $eventsManager);

        $application->setEventsManager($eventsManager);
    }

    /**
     * Attaches the middleware to the application
     *
     * @param Micro   $application
     * @param Manager $eventsManager
     */
    private function attachMiddleware(Micro $application, Manager $eventsManager)
    {
        $middleware = $this->getMiddleware();

        /**
         * Get the events manager and attach the middleware to it
         */
        foreach ($middleware as $class => $function) {
            $eventsManager->attach('micro', new $class());
            $application->{$function}(new $class());
        }
    }

    /**
     * Attaches the routes to the application; lazy loaded
     *
     * @param Micro $application
     */
    private function attachRoutes(Micro $application, Config $config)
    {
        $routers = $config->get('routers')->toArray();
        foreach ($routers as $router) {
            /** @var RoutesInterface $router */
            $routes = (new $router())->getRoutes();
            foreach ($routes as $route) {
                /** @var RouteInterface $route */
                $collection = new Collection();
                $collection
                    ->setHandler($route->getControllerClass(), true)
                    ->{$route->getHttpMethod()}($route->getHttpRoute(), $route->getControllerMethod());

                $application->mount($collection);
            }
        }

        $application->error(function (\Throwable $exception) use ($application) {
            /** @var Response $res */
            $res   = $application->getService('response');

//            $errorHandler  = $application->getShared('errorHandler'); //Save service in DI and save the error?
//            $errorHandler->saveException($exception);

            $contentType = 'application/json';
            $req = $application->getService('request'); // Can be null if RequestProvider fails, for example for a POST bad formatted
            if ($req) {
                $contentType = $req->getHeader('Content-Type'); // Avoid `->getContentType()`; Can throw an exception
            } else if (isset($_SERVER['CONTENT_TYPE'])) {
                $contentType = $_SERVER['CONTENT_TYPE'];
            }

            $debug = (bool) $application->getService('config')->path('app.debug');

            return $res->sendException($exception, $contentType, $debug);
        });
    }

    /**
     * Returns the array for the middleware with the action to attach
     *
     * @return array
     */
    private function getMiddleware(): array
    {
        return [
            NotFoundMiddleware::class           => 'before',
            CORSMiddleware::class               => 'before',
            AuthenticationMiddleware::class     => 'before',
            //All this validations moved in AuthenticationMiddleware
//            TokenUserMiddleware::class         => 'before',
//            TokenVerificationMiddleware::class => 'before',
//            TokenValidationMiddleware::class   => 'before',
            ResponseMiddleware::class           => 'after',
        ];
    }
}
