<?php

namespace App\View;

class Jury extends \App\View
{
    public $template = 'views/jury.twig';

    public function getResponse()
    {
        if ($response = $this->requireJuryLogin()) {
            return $response;
        }

        return $this->app->redirect('/2018/jury/submissions');
    }

    // public function getViewData()
    // {
    //     $juror = $this->app['session']->get('juror');

    //     $viewData = [];

    //     // these are the logged in user's votes
    //     $sql = "SELECT panel.*, submissions.*, jury_votes.score
    //             FROM panel
    //             LEFT JOIN submissions ON panel.submission_id = submissions.id
    //             LEFT JOIN jury_votes ON type_id = panel.id AND type = :type AND jury_id = :jury_id
    //             WHERE panel.status = :status";

    //     $rows = $this->app['database']->select($sql, [
    //         'type' => 'panel',
    //         'jury_id' => $juror['id'],
    //         'status' => 'pending'
    //     ]);

    //     $viewData['panels'] = [
    //         'rows' => $rows,
    //         'total' => count($rows),
    //         'unvoted' => count(array_filter($rows, function ($row) {
    //             return !$row['score'];
    //         }))
    //     ];

    //     $sql = "SELECT COUNT(*) as total
    //             FROM auction";
    //     $viewData['auction'] = $this->app['database']->selectRow($sql);

    //     // all submissions
    //     // random submission

    //     return $viewData;
    // }
}
