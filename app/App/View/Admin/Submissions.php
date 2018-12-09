<?php

namespace App\View\Admin;

class Submissions extends \App\View
{
    public $template = 'views/admin/submissions.twig';

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

        $where = [];
        $params = [
            'type' => 'panels'
        ];

        if ($status = $this->request->get('status')) {
            if ($status == 'juried') {
                $where[] = "(status = 'pending' OR status = 'accepted' OR status = 'declined')";
            }
            else {
                $where[] = "status = :status";
                $params['status'] = $status;
            }
        }

        if (count($where)) {
            $where = 'WHERE '.implode(' AND ', $where);
        }
        else {
            $where = "";
        }

        $order = 'submissions.id ASC';
        if ($this->request->get('order') == 'score') {
            $order = 'avg_score DESC';
        }

        $sql = "SELECT submissions.*, panel.status, avg_score, votes
                FROM submissions
                LEFT JOIN panel ON submission_id = submissions.id
                LEFT JOIN (
                    SELECT submission_id, `type`, COUNT(*) as votes
                    FROM jury_votes
                    GROUP BY submission_id, type
                ) votes ON votes.submission_id = submissions.id AND `type`= :type
                $where
                ORDER BY $order";
        $submissions = $database->select($sql, $params);

        $submissions = array_map(function ($submission) {
            $submission['panel'] = [
                'avg_score' => $submission['avg_score'],
                'status' => $submission['status']
            ];
            return $submission;
        }, $submissions);

        $viewData['submissions'] = $submissions;

        $viewData['form'] = $this->request->query->all();

        return $viewData;
    }
}
