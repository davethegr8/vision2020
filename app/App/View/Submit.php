<?php

namespace App\View;

class Submit extends \App\View
{
    public $template = 'views/submit.twig';

    public function getResponse()
    {
        if ($this->request->getMethod() == 'POST') {
            $database = $this->app['database'];

            $post = $this->request->request->all();

            $submission = [
                'name' => $post['name'],
                'phone' => $post['phone'],
                'email' => $post['email'],
                'address' => $post['address'],
                'contact' => $post['contact'],
                'bio' => $post['bio'],
                'created' => (new \DateTime)->format('Y-m-d H:i:s')
            ];

            $database->insert('submissions', $submission);
            $submission['id'] = $database->lastInsertId();

            $forPanels = count($this->request->files->get('panel')) > 0;
            if ($forPanels) {
                $panelSubmission = [
                    'submission_id' => $submission['id'],
                    'in_person' => $post['panel']['in_person']
                ];
                $database->insert('panel', $panelSubmission);
                $panelSubmission['id'] = $database->lastInsertId();

                $panelPhotos = $this->request->files->get('panel')['example'];

                foreach ($panelPhotos as $example) {
                    $filename = md5(uniqid()).'.'.$example->guessExtension();

                    $photo = [
                        'name' => $example->getClientOriginalName(),
                        'filename' => $filename,
                        'table_ref' => 'panel',
                        'table_id' => $panelSubmission['id']
                    ];
                    $database->insert('photos', $photo);

                    $example->move(getenv('UPLOAD_DIR'), $filename);
                }
            }

            $auctionPhotos = $this->request->files->get('auction');

            foreach ($post['auction']['title'] as $index => $title) {
                $auctionItem = [
                    'title' => $title,
                    'medium' => $post['auction']['medium'][$index],
                    'size' => $post['auction']['size'][$index],
                    'price' => intval($post['auction']['minimum'][$index]),
                ];

                $exists = !empty(array_filter(array_values($auctionItem)));

                $auctionItem['submission_id'] = $submission['id'];

                if (!$exists) {
                    continue;
                }

                $database->insert('auction', $auctionItem);
                $auctionItem['id'] = $database->lastInsertId();

                $auctionPhoto = $auctionPhotos['photo'][$index];
                if ($auctionPhoto) {
                    $filename = md5(uniqid()).'.'.$auctionPhoto->guessExtension();

                    $photo = [
                        'name' => $auctionPhoto->getClientOriginalName(),
                        'filename' => $filename,
                        'table_ref' => 'auction',
                        'table_id' => $auctionItem['id']
                    ];
                    $database->insert('photos', $photo);

                    $auctionPhoto->move(getenv('UPLOAD_DIR'), $filename);
                }
            }

            $message = "new v2020 submission: https://vision2020.loveburien.com/2018/admin/submissions/".$submission['id']."/view";
            $this->app['slack']->send($message);

            $this->app['session']->getFlashBag()->add('success', 'Thank you for your submission.');
            return $this->app->redirect('/2018/submit');
        }

        return parent::getResponse();
    }

    public function getViewData()
    {
        return [
            'allowed' => [
                'panel' => false,
                'auction' => true
            ]
        ];
    }
}
