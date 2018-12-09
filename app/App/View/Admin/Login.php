<?php

namespace App\View\Admin;

class Login extends \App\View
{
    public $template = 'views/admin/login.twig';

    public function getResponse()
    {
        if ($this->request->getMethod() == 'GET') {
            return parent::getResponse();
        }

        $database = $this->app['database'];

        $posted = $this->request->request->all();
        $hashed = password_hash($posted['password'], PASSWORD_DEFAULT);

        $this->app['session']->set('posted', $posted);

        $sql = "SELECT * FROM admin WHERE username = :username";
        $user = $database->selectRow($sql, ['username' => $posted['username']]);

        if (empty($user) || !password_verify($posted['password'], $hashed)) {
            $this->app['session']->getFlashBag()->add('danger', '<b>Error!</b> Login invalid.');
            return $this->app->redirect('/2018/admin/login');
        }

        if (password_needs_rehash($posted['password'], PASSWORD_DEFAULT)) {
            $user['password'] = password_hash($posted['password'], PASSWORD_DEFAULT);
            $database->replace('admin', $user);
        }

        $this->app['session']->remove('posted');
        $this->app['session']->getFlashBag()->add('success', '<b>Success!</b> Hi <b>'.$user['username'].'</b>.');

        $this->app['session']->set('user', [
            'username' => $user['username']
        ]);

        return $this->app->redirect('/2018/admin/');
    }

    public function getViewData()
    {
        $posted = $this->app['session']->get('posted');

        $data = [
            'fields' => [
                'username' => [
                    'value' => $posted['username'] ?? ''
                ]
            ]
        ];

        return $data;
    }
}
