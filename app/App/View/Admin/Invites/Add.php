<?php

namespace App\View\Admin\Invites;

class Add extends \App\View
{
    public $template = 'views/admin/invites/add.twig';

    public function getResponse()
    {
        if ($this->request->getMethod() == 'POST') {
            $database = $this->app['database'];

            $post = $this->request->request->all();

            $ok = $database->insert('invites', $post);

            if ($ok) {
                $this->app['session']->getFlashBag()->add('success', 'Invite saved');
                return $this->app->redirect('/2018/admin/invites');
            }
        }

        return parent::getResponse();
    }

    public function getViewData()
    {
        $viewData = [];

        $viewData['key'] = substr(md5(uniqid()), 0, 16);
        return $viewData;
    }
}
