<?php

namespace App\View;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Image extends \App\View
{
    public function getResponse()
    {
        $path = getenv('UPLOAD_DIR').$this->request->get('key');

        return new BinaryFileResponse($path);
    }
}
