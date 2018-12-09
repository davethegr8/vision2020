<?php

namespace App\View\Jury;

class Login extends \App\View
{
    public $template = 'views/jury/login.twig';

    public function getResponse()
    {
        if ($this->request->getMethod() == 'GET') {
            return parent::getResponse();
        }

        $database = $this->app['database'];

        $posted = $this->request->request->all();
        $hashed = password_hash($posted['password'], PASSWORD_DEFAULT);

        $this->app['session']->set('posted', $posted);

        $sql = "SELECT * FROM jury WHERE name = :name";
        $user = $database->selectRow($sql, ['name' => $posted['name']]);

        if (empty($user) || !password_verify($posted['password'], $hashed)) {
            $this->app['session']->getFlashBag()->add('danger', '<b>Error!</b> Login invalid.');
            return $this->app->redirect('/2018/jury/login');
        }

        if (password_needs_rehash($posted['password'], PASSWORD_DEFAULT)) {
            $user['password'] = password_hash($posted['password'], PASSWORD_DEFAULT);
            $database->replace('jury', $user);
        }

        $this->app['session']->remove('posted');
        $this->app['session']->getFlashBag()->add('success', '<b>Success!</b> Hi <b>'.$user['name'].'</b>.');

        $this->app['session']->set('juror', [
            'id' => $user['id'],
            'name' => $user['name']
        ]);

        return $this->app->redirect('/2018/jury/');
    }

    public function getViewData()
    {
        $posted = $this->app['session']->get('posted');

        $data = [
            'fields' => [
                'name' => [
                    'value' => $posted['name'] ?? ''
                ]
            ]
        ];

        return $data;
    }
}
