<?php

namespace Bz2Tuleap\Tuleap;

use TuleapClient;

class RESTArtifactVisitor {

    public function visit(Tracker\Artifact\Artifact $artifact) {
        var_dump($artifact->getId());

        /*$request = new TuleapClient\Request(
            new \Guzzle\Http\Client(
                'http://tuleap_web_1.tuleap-aio-dev.docker/api',
                array(
                    'ssl.certificate_authority' => false
                )
            ),
            'janine',
            'welcome0'
         * 
         */
    }
}
