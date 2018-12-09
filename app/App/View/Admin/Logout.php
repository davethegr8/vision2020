<?php

namespace App\View\Admin;

class Logout extends \App\View
{
    public function getResponse()
    {
        if ($response = $this->requireLogin()) {
            return $response;
        }

        $this->app['session']->remove('user');
        $this->app['session']->getFlashBag()->add('success', 'Logged out.');

        return $this->app->redirect('/2018/admin/login');
    }
}
