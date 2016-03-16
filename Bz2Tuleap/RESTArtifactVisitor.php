<?php

namespace Bz2Tuleap\Tuleap;


class RESTArtifactVisitor {

    private $fields;

    public function __construct(Tracker $tracker) {
        $this->mapTrackerFields($tracker->getField());
    }

    public function visit(Tracker\Artifact\Artifact $artifact) {

        $data = array(
            'status'     => null,
            'resolution' => null,
        );
        foreach ($artifact->getChangesets() as $changeset) {
            foreach ($changeset->getFieldChanges() as $field_change) {
                $this->addDataForField($data, $field_change, 'status');
                $this->addDataForField($data, $field_change, 'resolution');
            }
        }
        var_dump('PUT '.$artifact->getId().' status='.$data['status'].' resolution='.$data['resolution']);

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

    private function addDataForField(array &$data, $field_change, $field_name) {
        if ($field_change->getName() == $field_name) {
            $data[$field_name] = $this->fields[$field_name]->getLabelByValueId($field_change->getValue());
        }
    }

    private function mapTrackerFields($parent_field) {
        foreach ($parent_field->getChildren() as $field) {
            if ($field->getName()) {
                $this->fields[$field->getName()] = $field;
            }
            $this->mapTrackerFields($field);
        }
    }
}
