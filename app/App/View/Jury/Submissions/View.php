<?php

namespace App\View\Jury\Submissions;

class View extends \App\View
{
    public $template = 'views/jury/submissions/view.twig';

    public function getResponse()
    {
        if ($response = $this->requireJuryLogin()) {
            return $response;
        }

        $database = $this->app['database'];
        $juror = $this->app['session']->get('juror');

        if ($this->request->get('unvoted') == true) {
            $id = $this->request->get('id');
            if ('random' == $id || 'first' == $id) {
                $sql = "SELECT panel.*, submissions.*, jury_votes.score
                        FROM panel
                        LEFT JOIN submissions ON panel.submission_id = submissions.id
                        LEFT JOIN jury_votes ON type_id = panel.id AND type = :type AND jury_id = :jury_id
                        WHERE panel.status = :status AND score IS NULL
                        ORDER BY submissions.id ASC";

                $rows = $database->select($sql, [
                    'type' => 'panels',
                    'jury_id' => $juror['id'],
                    'status' => 'pending'
                ]);

                $ids = array_column($rows, 'id');

                if (empty($ids)) {
                    return $this->app->redirect('/2018/jury/submissions');
                }

                if ('random' == $id) {
                    $id = array_rand(array_flip($ids));
                } elseif('first' == $id) {
                    $id = array_shift($ids);
                } else {
                    $id = 0;
                }

                return $this->app->redirect('/2018/jury/submissions/view/panels/'.$id);
            }
        }

        $sql = 'SELECT *
                FROM submissions
                WHERE id = :id';
        $this->submission = $database->selectRow($sql, [
            'id' => $this->request->get('id')
        ]);

        $sql = "SELECT *
                FROM panel
                WHERE panel.status = :status AND submission_id = :submission_id";
        $this->panel = $database->selectRow($sql, [
            'submission_id' => $this->submission['id'],
            'status' => 'pending'
        ]);

        if (!$this->panel) {
            return $this->app->redirect('/2018/jury/submissions');
        }

        return parent::getResponse();
    }

    public function getViewData()
    {
        $viewData = [];

        $juror = $this->app['session']->get('juror');
        $database = $this->app['database'];

        $sql = "SELECT *
                FROM photos
                WHERE table_ref = :table_ref AND table_id = :table_id";
        $this->panel['photos'] = $database->select($sql, [
            'table_ref' => 'panel',
            'table_id' => $this->panel['id']
        ]);

        $sql = "SELECT *
                FROM jury_votes
                WHERE type_id = :type_id AND type = :type AND jury_id = :jury_id";
        $viewData['vote'] = $database->selectRow($sql, [
            'type_id' => $this->panel['id'],
            'type' => 'panels',
            'jury_id' => $juror['id']
        ]);

        if (empty($viewData['vote'])) {
            $viewData['vote'] = ['score' => null];
        }

        $viewData['submission'] = $this->submission;
        $viewData['panel'] = $this->panel;

        $sql = "SELECT *
                FROM auction
                LEFT JOIN photos ON table_ref = :table_ref AND table_id = auction.id
                WHERE submission_id = :submission_id";
        $viewData['auction'] = $database->select($sql, [
            'table_ref' => 'auction',
            'submission_id' => $this->submission['id']
        ]);

        $viewData['upload_url'] = getenv('UPLOAD_URL');

        return $viewData;
    }
}
