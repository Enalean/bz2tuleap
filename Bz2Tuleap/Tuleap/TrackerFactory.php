<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

/**
 * This is where we know stuff about Bugzilla
 */
class TrackerFactory {
    
    private $value_mapper;
    private $field_mapper;

    /**
     * @var UserMapper
     */
    private $user_mapper;
    private $fields;

    public function __construct(UserMapper $user_mapper) {
        $this->user_mapper  = $user_mapper;
        $this->value_mapper = new IdMapper('V');
        $this->field_mapper = new IdMapper('F');
        $this->fields       = array(
            'submitted_by'   => new Field($this->field_mapper, 'subby', 'submitted_by', 'Submitted by', new NoProperties(), new ReadOnlyFieldPermissions()),
            'submitted_on'   => new Field($this->field_mapper, 'subon', 'submitted_on', 'Submitted on', new NoProperties(), new ReadOnlyFieldPermissions()),
            'last_update_by' => new Field($this->field_mapper, 'luby', 'last_update_by', 'Last update by', new NoProperties(), new ReadOnlyFieldPermissions()),
            'last_update_on' => new Field($this->field_mapper, 'lud', 'last_update_on', 'Last update on', new NoProperties(), new ReadOnlyFieldPermissions()),
            'summary'        => new Field($this->field_mapper, 'string', 'summary', 'Summary', new Properties(array('size' => 61)), new DefaultFieldPermissions()),
            'bugzilla_id'    => new Field($this->field_mapper, 'int', 'bugzilla_id', 'Bugzilla id', new NoProperties(), new ReadOnlyFieldPermissions()),
            'description'    => new Field($this->field_mapper, 'text', 'description', 'Description', new Properties(array('rows' => 7, 'cols' => 80)), new DefaultFieldPermissions()),
            'cc'             => new CCField($this->field_mapper, 'cc', 'CC', new DefaultFieldPermissions()),
            'status'         => new SelectBoxField($this->field_mapper, $this->value_mapper, 'status', "Status", array(
                'NEW',
                'UNCONFIRMED',
                'CONFIRMED',
                'IN_PROGRESS',
                'RESOLVED',
                'VERIFIED',
            ), new DefaultFieldPermissions()),
            'resolution'     => new SelectBoxField($this->field_mapper, $this->value_mapper, 'resolution', "Resolution", array(
                'FIXED',
                'INVALID',
                'WONTFIX',
                'DUPLICATE',
                'WORKSFORME',
            ), new DefaultFieldPermissions()),
            'assigned_to'    => new UsersSelectBoxField($this->field_mapper, 'assigned_to', 'Assigned to', new DefaultFieldPermissions()),
            'severity'       => new SelectBoxField($this->field_mapper, $this->value_mapper, 'severity', "Severity", array(
                'blocker',
                'critical',
                'major',
                'normal',
                'minor',
                'trivial',
                'enhancement',
            ), new DefaultFieldPermissions()),
            'priority'       => new SelectBoxField($this->field_mapper, $this->value_mapper, 'priority', "Priority", array(
                'P1',
                'P2',
                'P3',
                'P4',
                'P5',
            ), new DefaultFieldPermissions()),
            'links'          => new Field($this->field_mapper, 'art_link', 'links', 'Links', new NoProperties(), new DefaultFieldPermissions()),
        );
    }

    public function getTrackerFromBugzilla(SimpleXMLElement $bugzilla_xml) {
        return new Tracker(
            $this->getFields(),
            $this->getReportColumns(),
            $this->getSemantics(),
            $this->getRules(),
            $this->getArtifacts($bugzilla_xml)
        );
    }

    private function getFields() {
        return new FormElements(array(
            new Column($this->field_mapper, array(
                $this->fields['submitted_by'],
                $this->fields['submitted_on'],
            )),

            new Column($this->field_mapper, array(
                $this->fields['last_update_by'],
                $this->fields['last_update_on'],
            )),

            new FieldSet($this->field_mapper, 'Details', array(
                new Column($this->field_mapper, array(
                    $this->fields['summary'],
                )),
                new Column($this->field_mapper, array(
                    $this->fields['bugzilla_id'],
                )),
                $this->fields['description'],
                $this->fields['cc'],
                new Column($this->field_mapper, array(
                    $this->fields['status'],
                    $this->fields['resolution'],
                    $this->fields['assigned_to'],
                )),
                new Column($this->field_mapper, array(
                    $this->fields['severity'],
                    $this->fields['priority'],
                )),
            )),

            new FieldSet($this->field_mapper, 'Links', array(
                $this->fields['links'],
            )),
        ));
    }

    private function getRules() {
        return new Rules(
            new ListRules(
                $this->getListRules('status', 'resolution', array(
                    'NEW'         => array(null),
                    'UNCONFIRMED' => array(null),
                    'CONFIRMED'   => array(null),
                    'IN_PROGRESS' => array(null),
                    'RESOLVED'    => array(
                        'FIXED',
                        'INVALID',
                        'WONTFIX',
                        'DUPLICATE',
                        'WORKSFORME',
                    ),
                    'VERIFIED'    => array(
                        'FIXED',
                        'INVALID',
                        'WONTFIX',
                        'DUPLICATE',
                        'WORKSFORME',
                    )
                ))
            )
        );
    }

    private function getListRules($source_field, $target_field, array $dependencies) {
        $rules = array();
        foreach ($dependencies as $source_value => $target_values) {
            foreach ($target_values as $target_value) {
                $rules[] = $this->getRule($source_field, $target_field, $source_value, $target_value);
            }
        }
        return $rules;
    }

    private function getRule($source_field, $target_field, $source_value, $target_value) {
        return new ListRule(
            $this->fields[$source_field],
            $this->fields[$target_field],
            $this->fields[$source_field]->getValueReference($source_value),
            $this->fields[$target_field]->getValueReference($target_value)
        );
    }

    private function getSemantics() {
        return array(
            new TitleSemantic($this->fields['summary']),
            new StatusSemantic($this->fields['status'], array(
                $this->fields['status']->getValueReference('NEW'),
                $this->fields['status']->getValueReference('UNCONFIRMED'),
                $this->fields['status']->getValueReference('CONFIRMED'),
                $this->fields['status']->getValueReference('IN_PROGRESS'),
            )),
            new AssignedToSemantic($this->fields['assigned_to']),
        );
    }

    private function getReportColumns() {
        return array(
            $this->fields['bugzilla_id'],
            $this->fields['summary'],
            $this->fields['submitted_by'],
            $this->fields['submitted_on'],
            $this->fields['last_update_by'],
            $this->fields['assigned_to'],
        );
    }

    private function getArtifacts(SimpleXMLElement $bugzilla_xml) {
        $artifacts = $this->getArtifactsFromBugzilla($bugzilla_xml);
        array_walk($artifacts, function ($artifact) use ($artifacts) {
            return $artifact->cleanUp($artifacts);
        });
        return $artifacts;
    }

    private function getArtifactsFromBugzilla(SimpleXMLElement $bugzilla_xml) {
        $artifacts = array();
        foreach($bugzilla_xml as $bugzilla_bug) {
            $artifacts[] = new Artifact(
                (int) $bugzilla_bug->bug_id,
                $this->getChangesets($bugzilla_bug)
            );
        }
        return $artifacts;
    }

    private function getChangesets(SimpleXMLElement $bugzilla_bug) {
        $changeset = array($this->getInitialChangeset($bugzilla_bug));
        return array_merge($changeset, $this->getChangesetComments($bugzilla_bug));
    }

    private function getInitialChangeset(SimpleXMLElement $bugzilla_bug) {
        return new Changeset(
            (string)$bugzilla_bug->creation_ts,
            (string)$this->user_mapper->getUser($bugzilla_bug->reporter),
            '',
            $this->getFieldsData($bugzilla_bug)
        );
    }

    private function getChangesetComments(SimpleXMLElement $bugzilla_bug) {
        $changesets = array();
        foreach($bugzilla_bug->long_desc as $long_desc) {
            if ((int)$long_desc->comment_count === 0) {
                continue;
            }
            $changesets[] = new Changeset(
                (string)$long_desc->bug_when,
                $this->user_mapper->getUser($long_desc->who),
                (string) $long_desc->thetext,
                array()
            );
        }
        return $changesets;
    }

    private function getFieldsData(SimpleXMLElement $bugzilla_bug) {
        $values = array(
            new ScalarFieldChange('bugzilla_id', 'int', (int) $bugzilla_bug->bug_id),
            new ScalarFieldChange('summary', 'string', (string) $bugzilla_bug->short_desc),
            new ScalarFieldChange('links', 'art_link', (string) $bugzilla_bug->dependson),
            new ScalarFieldChange('description', 'text', (string) $bugzilla_bug->long_desc[0]->thetext),
            new ListFieldChange('status', $this->value_mapper->getId((string)$bugzilla_bug->bug_status)),
            new ListFieldChange('resolution', $this->value_mapper->getId((string)$bugzilla_bug->resolution)),
            new ListFieldChange('severity', $this->value_mapper->getId((string)$bugzilla_bug->bug_severity)),
            new ListFieldChange('priority', $this->value_mapper->getId((string)$bugzilla_bug->priority)),
            new UsersSelectBoxFieldChange('assigned_to', $this->user_mapper->getUser($bugzilla_bug->assigned_to)),
        );

        if (isset($bugzilla_bug->cc)) {
            $cc_list = array();
            foreach ($bugzilla_bug->cc as $cc) {
                $cc_list[] = (string) $cc;
            }
            $values[] = new CCFieldChange('cc', $cc_list);
        }

        return $values;
    }

}