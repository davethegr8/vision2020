<?php

namespace App\View\Jury;

class Logout extends \App\View
{
    public function getResponse()
    {
        if ($response = $this->requireLogin()) {
            return $response;
        }

        $this->app['session']->remove('juror');
        $this->app['session']->getFlashBag()->add('success', 'Logged out.');

        return $this->app->redirect('/2018/jury/login');
    }
}
