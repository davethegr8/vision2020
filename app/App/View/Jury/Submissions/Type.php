<?php

namespace App\View\Jury\Submissions;

class Type extends \App\View
{
    public $template = 'views/jury/submissions/panels.twig';

    public function getResponse()
    {
        if ($response = $this->requireJuryLogin()) {
            return $response;
        }

        return parent::getResponse();
    }

    public function getViewData()
    {
        $juror = $this->app['session']->get('juror');
        $viewData = [];

        if ('random' == $this->request->get('sort')) {
            $sort = 'RAND()';
        }
        else {
            $sort = 'submissions.id ASC';
        }

        // these are the logged in user's votes
        $sql = "SELECT panel.*, submissions.*, jury_votes.score, photos.name as original_name, photos.filename
                FROM panel
                LEFT JOIN photos ON table_ref = 'panel' AND table_id = panel.id
                LEFT JOIN submissions ON panel.submission_id = submissions.id
                LEFT JOIN jury_votes ON type_id = panel.id AND type = :type AND jury_id = :jury_id
                WHERE panel.status = :status
                ORDER BY $sort";

        $rows = $this->app['database']->select($sql, [
            'type' => 'panels',
            'jury_id' => $juror['id'],
            'status' => 'pending'
        ]);

        $panels = [];

        foreach($rows as $row) {
            $panel = $row;
            unset($panel['original_name']);
            unset($panel['filename']);
            $panel['photos'] = [];

            if (!array_key_exists($panel['submission_id'], $panels)) {
                $panels[$panel['submission_id']] = $panel;
            }

            $panels[$panel['submission_id']]['photos'][] = [
                'name' => $row['original_name'],
                'filename' => $row['filename']
            ];
        }

        $viewData['panels'] = [
            'rows' => $panels,
            'total' => count($panels),
            'unvoted' => count(array_filter($panels, function ($row) {
                return !$row['score'];
            }))
        ];

        return $viewData;
    }
}
