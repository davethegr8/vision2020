<?php

namespace App\View;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Invite extends \App\View
{
    public function getResponse()
    {
        $key = $this->request->get('key');

        if ($key) {
            $sql = "UPDATE `invites` SET `hits`=`hits`+1 WHERE `key` = :key";
            $statement = $this->app['database']->prepare($sql);
            $statement->execute(['key' => $key]);
        }

        return $this->app->redirect('/2018/call-for-art-and-artists');
    }
}
