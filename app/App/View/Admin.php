<?php

namespace App\View;

class Admin extends \App\View
{
    public $template = 'views/admin.twig';

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

        $sql = "SELECT COUNT(*) as total
                FROM submissions";
        $viewData['submissions'] = $this->app['database']->selectRow($sql);

        $sql = "SELECT COUNT(*) as total
                FROM panel";
        $viewData['panels'] = $this->app['database']->selectRow($sql);

        $sql = "SELECT COUNT(*) as total
                FROM auction";
        $viewData['auction'] = $this->app['database']->selectRow($sql);

        $sql = "SELECT COUNT(*) as total, SUM(hits) as hits
                FROM invites";
        $viewData['invites'] = $this->app['database']->selectRow($sql);

        return $viewData;
    }
}
