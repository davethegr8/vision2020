<?php

namespace App\View\Admin\Submissions;

class View extends \App\View
{
    public $template = 'views/admin/submissions/view.twig';

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

        $sql = 'SELECT * FROM submissions WHERE id = :id';
        $submission = $database->selectRow($sql, ['id' => $this->request->get('id')]);

        $sql = "SELECT * FROM panel WHERE submission_id = :submission_id";
        $panel = $database->selectRow($sql, [
            'submission_id' => $submission['id']
        ]);

        $sql = "SELECT * FROM photos WHERE table_ref = :table_ref AND table_id = :table_id";
        $panel['photos'] = $database->select($sql, [
            'table_ref' => 'panel',
            'table_id' => $panel['id']
        ]);

        $viewData['submission'] = $submission;
        $viewData['panel'] = $panel;

        $sql = "SELECT *
                FROM auction
                LEFT JOIN photos ON table_ref = :table_ref AND table_id = auction.id
                WHERE submission_id = :submission_id";
        $viewData['auction'] = $database->select($sql, [
            'table_ref' => 'auction',
            'submission_id' => $submission['id']
        ]);

        $viewData['upload_url'] = getenv('UPLOAD_URL');


        $sql = "SELECT jury.id as jury_id, votes.score
                FROM jury
                LEFT JOIN (
                    SELECT * FROM jury_votes
                    WHERE submission_id = :submission_id AND type = :type
                ) votes ON jury.id = jury_id
                ORDER BY jury_id ASC
                ";
        $viewData['votes'] = $database->select($sql, [
            'submission_id' => $submission['id'],
            'type' => 'panels'
        ]);

        return $viewData;
    }
}
