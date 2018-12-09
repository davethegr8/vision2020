<?php

namespace App\View\Jury;

class Submissions extends \App\View
{
    public $template = 'views/jury/submissions.twig';

    public function getResponse()
    {
        if ($response = $this->requireJuryLogin()) {
            return $response;
        }

        return parent::getResponse();
    }

    public function getViewData()
    {
        $viewData = [];

        $juror = $this->app['session']->get('juror');

        $sort = 'submissions.id ASC';
        if ('random' == $this->request->get('sort')) {
            $sort = 'RAND()';
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

        foreach ($rows as $row) {
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

        $sql = "SELECT COUNT(*) as total
                FROM auction";
        $viewData['auction'] = $this->app['database']->selectRow($sql);

        // all submissions
        // random submission

        return $viewData;
    }
}
