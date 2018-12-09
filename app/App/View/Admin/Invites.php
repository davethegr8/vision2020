<?php

namespace App\View\Admin;

class Invites extends \App\View
{
    public $template = 'views/admin/invites.twig';

    public function getResponse()
    {
        if ($response = $this->requireLogin()) {
            return $response;
        }

        return parent::getResponse();
    }

    public function getViewData()
    {
        $viewData = [];

        $database = $this->app['database'];
        $submissions = $database->select('SELECT * FROM invites ORDER BY id ASC');

        $viewData['invites'] = $submissions;
        return $viewData;
    }
}
