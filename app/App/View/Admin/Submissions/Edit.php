<?php

namespace App\View\Admin\Submissions;

use Symfony\Component\HttpFoundation\JsonResponse;

class Edit extends \App\View
{
    public function getResponse()
    {
        if ($response = $this->requireLogin()) {
            return $response;
        }

        $data = $this->getViewData();

        return new JsonResponse($data);
    }

    public function getViewData()
    {
        $viewData = [];

        $database = $this->app['database'];

        $update = [
            'submission_id' => $this->request->get('id'),
            'status' => $this->request->get('status')
        ];
        $ok = $database->update('panel', $update, 'submission_id = :submission_id');

        $update['ok'] = $ok;

        return $update;
    }
}
