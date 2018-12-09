<?php

namespace App;

use Silex\Application as SilexApp;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class View
{
    public $template = 'page.default.twig';

    protected $request;
    protected $app;

    public function getResponse()
    {
        $data = $this->getViewData();

        // todo: break up these renderers into separate classes
        if ($this->isCSV()) {
            return $this->renderCSV($data);
        }

        // todo: break up these renderers into separate classes
        if ($this->isJSON()) {
            return $this->app->json($data);
        }

        // todo: break up these renderers into separate classes
        return $this->app['twig']->render($this->template, $data);
    }

    public function getViewData()
    {
        return [];
    }

    public function render(Request $request, Application $app) {
        $this->request = $request;
        $this->app = $app;

        $response = $this->getResponse();

        if ($response instanceof Response) {
            return $response;
        }

        return $response;
    }

    public function isCSV()
    {
        return $this->request->get('format') == 'csv';
    }

    public function renderCSV($data)
    {
        $response = new Response;
        $response->headers->set("Content-type", "text/csv");

        $data = array_values($data);

        ob_start();
        $out = fopen('php://output', 'w');
        fputcsv($out, array_keys($data[0]));
        foreach ($data as $item) {
            $item = array_map(function ($col) {
                return preg_replace("/[\r\n]/", " ", $col);
            }, $item);
            fputcsv($out, $item);
        }
        fclose($out);
        $response->setContent(ob_get_clean());

        return $response;
    }

    public function isJSON()
    {
        $this->request->get('format') == 'json';
    }

    public function requireLogin()
    {
        $user = $this->app['session']->get('user');

        if (null === $user) {
            return $this->app->redirect($this->app['config']['root'].'/2018/admin/login');
        }
    }

    public function requireAnon()
    {
        $user = $this->app['session']->get('user');

        if (null !== $user) {
            return $this->app->redirect($this->app['config']['root'].'/2018/admin/');
        }
    }

    public function requireJuryLogin()
    {
        $user = $this->app['session']->get('juror');

        if (empty($user['id'])) {
            return $this->app->redirect($this->app['config']['root'].'/2018/jury/login');
        }
    }
}
