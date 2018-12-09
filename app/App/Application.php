<?php

namespace App;

use Silex\Application as SilexApp;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class Application extends SilexApp {

    const VERSION = 1;

    protected $routes = [];

    public function __construct($routeFile = '') {
        parent::__construct();

        if($routeFile) {
            $this->addRoutes($routeFile);
        }
    }

    // add routes
    // TODO move routes work into router.php
    public function addRoutes($routeFile) {
        $routes = Yaml::parse(file_get_contents($routeFile))['routes'];
        foreach($routes as $route) {
            $this->routes[] = $route;
        }
    }

    public function getRoutes() {
        return $this->routes;
    }

    public function replaceRoutes($routeFile) {
        $this->removeRoutes();
        $this->addRoutes($routeFile);
    }

    public function removeRoutes() {
        $this->routes = [];
    }

    protected function parseRoutes() {
        $self = $this;

        foreach($this->routes as $route) {
            $actions = $route;
            unset($actions['url']);

            foreach($actions as $httpMethod => $def) {
                $httpMethod = strtolower($httpMethod);

                if(isset($def['redirect'])) {
                    $callable = function (SilexApp $app, Request $request) use ($def) {
                        $params = $request->attributes->get('_route_params');
                        $def['redirect'] = interpolate($def['redirect'], $params);
                        return $app->redirect($def['redirect']);
                    };
                }
                elseif(isset($def['script'])) {
                    $callable = function (SilexApp $app, Request $request) use ($def) {
                        $instance = new ScriptHandler($app, $request);
                        return $instance->run($def);
                    };
                }
                elseif(isset($def['callable'])) {
                    $callable = $def['callable'];
                }
                else {
                    $callable = $def['controller'].'::'.($def['method'] ?: 'render');
                }

                $out = $this->$httpMethod($route['url'], $callable);

                // before middleware
                if(isset($def['middleware']) && array_key_exists('before', $def['middleware'])) {
                    foreach($def['middleware']['before'] as $action) {
                        $out->before($action);
                    }
                }

                // after middleware
                if(isset($def['middleware']) && array_key_exists('after', $def['middleware'])) {
                    foreach($def['middleware']['after'] as $action) {
                        $out->after($action);
                    }
                }

                // handle assert
                if(isset($def['assert'])) {
                    foreach($def['assert'] as $key => $value) {
                        $out->assert($key, $value);
                    }
                }

                // handle when
                if(isset($def['when'])) {
                    foreach($def['when'] as $key => $value) {
                        $out->when($key, $value);
                    }
                }

                // handle value
                if(isset($def['value'])) {
                    foreach($def['value'] as $key => $value) {
                        $out->value($key, $value);
                    }
                }
            }
        }
    }

    // run
    public function run(Request $ignored = null) {
        $app = $this;

        $this->parseRoutes();

        $this->error(function (\Exception $e, Request $request, $code) {
            return new Response($e->getMessage(), $code, $request->headers->all());
        });

        $this->after(function (Request $request, Response $response) {
            if(is_a($response, '\Symfony\Component\HttpFoundation\JsonResponse')) {
                $callback = $request->get('callback');
                $content = $response->getContent();

                if($callback && strpos($content, $callback) !== 0) {
                    $content = $callback.'('.$content.')';
                    $response->setContent($content);
                }
            }

            return $response;
        });

        parent::run();
    }

}
