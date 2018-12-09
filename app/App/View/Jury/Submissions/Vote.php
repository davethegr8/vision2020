<?php

namespace App\View\Jury\Submissions;

use Symfony\Component\HttpFoundation\JsonResponse;

class Vote extends \App\View
{
    public function getResponse()
    {
        if ($response = $this->requireJuryLogin()) {
            return $response;
        }

        $data = $this->getViewData();
        return new JsonResponse($data);
    }

    public function getViewData()
    {
        $viewData = [];

        $database = $this->app['database'];
        $type = $this->request->get('type');

        $sql = 'SELECT * FROM submissions WHERE id = :id';
        $submission = $database->selectRow($sql, ['id' => $this->request->get('id')]);

        // todo handle types

        $sql = "SELECT * FROM panel WHERE submission_id = :submission_id";
        $panel = $database->selectRow($sql, [
            'submission_id' => $submission['id']
        ]);

        $sql = "SELECT *
                FROM jury_votes
                WHERE jury_id = :jury_id
                    AND submission_id = :submission_id
                    AND type = :type
                    AND type_id = :type_id
                ";

        $juror = $this->app['session']->get('juror');

        $data = [
            'jury_id' => $juror['id'],
            'submission_id' => $submission['id'],
            'type' => $type,
            'type_id' => $panel['id']
        ];
        $row = $database->selectRow($sql, $data);

        $data['score'] = $this->request->get('score');
        if (empty($row)) {
            $ok = $database->insert('jury_votes', $data);
        }
        else {
            $data['id'] = $row['id'];
            $ok = $database->update('jury_votes', $data, 'id = :id');
        }

        $result = $database->insert('jury_votes_history', [
            'jury_id' => $juror['id'],
            'submission_id' => $submission['id'],
            'type' => $type,
            'type_id' => $panel['id'],
            'score' => $data['score'],
            'created' => (new \DateTime)->format('Y-m-d H:i:s')
        ]);


        $sql = "SELECT AVG(score) as average
                FROM jury_votes
                WHERE submission_id = :submission_id AND type = :type AND type_id = :type_id";
        $avg = $database->selectRow($sql, [
            'submission_id' => $submission['id'],
            'type' => $type,
            'type_id' => $panel['id']
        ]);

        if ($type == 'panels') {
            $result = $database->update('panel', [
                'id' => $panel['id'],
                'avg_score' => $avg['average']
            ], 'id = :id');
        }

        return [
            'ok' => $ok,
            'score' => $data['score']
        ];
    }
}
