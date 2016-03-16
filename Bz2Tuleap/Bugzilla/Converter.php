<?php

namespace Bz2Tuleap\Bugzilla;

use Bz2Tuleap\Tuleap\Project;
use Bz2Tuleap\Tuleap\Tracker;
use Bz2Tuleap\Tuleap\Tracker\Field;
use Bz2Tuleap\Tuleap\Tracker\Semantic;
use Bz2Tuleap\Tuleap\Tracker\Rule;
use Bz2Tuleap\Tuleap\Tracker\Artifact;

use SimpleXMLElement;

/**
 * This is where we know stuff about Bugzilla
 */
class Converter {

    private $data_path;
    private $value_mapper;
    private $field_mapper;

    /**
     * @var UserMapper
     */
    private $user_mapper;
    private $fields;

    public function __construct($data_path) {
        $this->user_mapper  = new UserMapper();
        $this->value_mapper = new Field\IdMapper('V');
        $this->field_mapper = new Field\IdMapper('F');
        $this->data_path = $data_path;
    }

    public function getProject(SimpleXMLElement $bugzilla_xml) {
        $tracker = $this->getTrackerFromBugzilla($bugzilla_xml);
        return new Project($this->user_mapper->getUsers(), $tracker);
    }

    private function getTrackerFromBugzilla(SimpleXMLElement $bugzilla_xml) {
        $this->initFields($bugzilla_xml);
        return new Tracker(
            $this->getFields(),
            $this->getReportColumns(),
            $this->getSemantics(),
            $this->getRules(),
            $this->getArtifacts($bugzilla_xml)
        );
    }

    private function initFields(SimpleXMLElement $bugzilla_xml) {
        $this->fields = array(
            'submitted_by'   => new Field\Field($this->field_mapper, 'subby', 'submitted_by', 'Submitted by', new Field\NoProperties(), new Field\ReadOnlyPermissions()),
            'submitted_on'   => new Field\Field($this->field_mapper, 'subon', 'submitted_on', 'Submitted on', new Field\NoProperties(), new Field\ReadOnlyPermissions()),
            'last_update_by' => new Field\Field($this->field_mapper, 'luby', 'last_update_by', 'Last update by', new Field\NoProperties(), new Field\ReadOnlyPermissions()),
            'last_update_on' => new Field\Field($this->field_mapper, 'lud', 'last_update_on', 'Last update on', new Field\NoProperties(), new Field\ReadOnlyPermissions()),
            'summary'        => new Field\Field($this->field_mapper, 'string', 'summary', 'Summary', new Field\Properties(array('size' => 61)), new Field\DefaultPermissions()),
            'bugzilla_id'    => new Field\Field($this->field_mapper, 'int', 'bugzilla_id', 'Bugzilla id', new Field\NoProperties(), new Field\ReadOnlyPermissions()),
            'description'    => new Field\Field($this->field_mapper, 'text', 'description', 'Description', new Field\Properties(array('rows' => 7, 'cols' => 80)), new Field\DefaultPermissions()),
            'cc'             => new Field\CC($this->field_mapper, 'cc', 'CC', new Field\DefaultPermissions()),
            'status'         => new Field\SelectBox($this->field_mapper, $this->value_mapper, 'status', "Status", array(
                'NEW',
                'UNCONFIRMED',
                'CONFIRMED',
                'ASSIGNED',
                'REOPENED',
                'IN_PROGRESS',
                'RESOLVED',
                'VERIFIED',
                'CLOSED',
            ), new Field\DefaultPermissions()),
            'resolution'     => new Field\SelectBox($this->field_mapper, $this->value_mapper, 'resolution', "Resolution", array(
                'FIXED',
                'INVALID',
                'WONTFIX',
                'DUPLICATE',
                'WORKSFORME',
                'MOVED',
                'NOT_ECLIPSE',
            ), new Field\DefaultPermissions()),
            'assigned_to'    => new Field\UsersSelectBox($this->field_mapper, 'assigned_to', 'Assigned to', new Field\DefaultPermissions()),
            'severity'       => new Field\SelectBox($this->field_mapper, $this->value_mapper, 'severity', "Severity", array(
                'blocker',
                'critical',
                'major',
                'normal',
                'minor',
                'trivial',
                'enhancement',
            ), new Field\DefaultPermissions()),
            'priority'       => new Field\SelectBox($this->field_mapper, $this->value_mapper, 'priority', "Priority", array(
                'P1',
                'P2',
                'P3',
                'P4',
                'P5',
            ), new Field\DefaultPermissions()),
            'links'            => new Field\Field($this->field_mapper, 'art_link', 'links', 'Links', new Field\NoProperties(), new Field\DefaultPermissions()),
            'attachments'      => new Field\Field($this->field_mapper, 'file', 'attachments', 'Attachments', new Field\NoProperties(), new Field\DefaultPermissions()),
            'product'          => new Field\SelectBox($this->field_mapper, $this->value_mapper, 'product', 'Product', $this->getUsedValuesFor($bugzilla_xml, 'product'), new Field\DefaultPermissions()),
            'component'        => new Field\SelectBox($this->field_mapper, $this->value_mapper, 'component', 'Component', $this->getUsedValuesFor($bugzilla_xml, 'component'), new Field\DefaultPermissions()),
            'version'          => new Field\SelectBox($this->field_mapper, $this->value_mapper, 'version', 'Version', $this->getUsedValuesFor($bugzilla_xml, 'version'), new Field\DefaultPermissions()),
            'target_milestone' => new Field\SelectBox($this->field_mapper, $this->value_mapper, 'target_milestone', 'Target milestone', $this->getUsedValuesFor($bugzilla_xml, 'target_milestone'), new Field\DefaultPermissions()),
            'hardware'         => new Field\SelectBox($this->field_mapper, $this->value_mapper, 'hardware', 'Hardware', $this->getUsedValuesFor($bugzilla_xml, 'rep_platform'), new Field\DefaultPermissions()),
            'os'               => new Field\SelectBox($this->field_mapper, $this->value_mapper, 'os', 'OS', $this->getUsedValuesFor($bugzilla_xml, 'op_sys'), new Field\DefaultPermissions()),
        );
    }

    private function getUsedValuesFor(SimpleXMLElement $bugzilla_xml, $field) {
        $values = array();
        foreach ($bugzilla_xml->bug as $bug) {
            $bugzilla_value = (string)$bug->$field;
            if (! $this->correspondsToNone($field, $bugzilla_value)) {
                $values[$bugzilla_value] = true;
            }
        }
        return array_filter(array_keys($values));
    }

    private function correspondsToNone($name, $value) {
        if ($name == 'version' && $value == 'unspecified') {
            return true;
        }
        if ($name == 'milestone' && $value == '---') {
            return true;
        }
        return false;
    }

    private function getFields() {
        return new Field\FormElements(array(
            new Field\Column($this->field_mapper, array(
                $this->fields['submitted_by'],
                $this->fields['submitted_on'],
            )),

            new Field\Column($this->field_mapper, array(
                $this->fields['last_update_by'],
                $this->fields['last_update_on'],
            )),

            new Field\FieldSet($this->field_mapper, 'Details', array(
                new Field\Column($this->field_mapper, array(
                    $this->fields['summary'],
                )),
                new Field\Column($this->field_mapper, array(
                    $this->fields['bugzilla_id'],
                )),
                $this->fields['description'],
                $this->fields['cc'],
                $this->fields['attachments'],
            )),

            new Field\FieldSet($this->field_mapper, 'Progress', array(
                new Field\Column($this->field_mapper, array(
                    $this->fields['status'],
                    $this->fields['resolution'],
                    $this->fields['assigned_to'],
                )),
                new Field\Column($this->field_mapper, array(
                    $this->fields['severity'],
                    $this->fields['priority'],
                )),
            )),

            new Field\FieldSet($this->field_mapper, 'Product and versions', array(
                new Field\Column($this->field_mapper, array(
                    $this->fields['product'],
                    $this->fields['component'],
                    $this->fields['hardware'],
                )),
                new Field\Column($this->field_mapper, array(
                    $this->fields['version'],
                    $this->fields['target_milestone'],
                    $this->fields['os'],
                )),
            )),

            new Field\FieldSet($this->field_mapper, 'Links', array(
                $this->fields['links'],
            )),
        ));
    }

    private function getRules() {
        return new Rule\Rules(
            new Rule\ListRules(
                $this->getListRules('status', 'resolution', array(
                    'NEW'         => array(null),
                    'UNCONFIRMED' => array(null),
                    'CONFIRMED'   => array(null),
                    'IN_PROGRESS' => array(null),
                    'ASSIGNED'    => array(null),
                    'REOPENED'    => array(null),
                    'RESOLVED'    => array(
                        'FIXED',
                        'INVALID',
                        'WONTFIX',
                        'DUPLICATE',
                        'WORKSFORME',
                        'MOVED',
                        'NOT_ECLIPSE',
                    ),
                    'VERIFIED'    => array(
                        'FIXED',
                        'INVALID',
                        'WONTFIX',
                        'DUPLICATE',
                        'WORKSFORME',
                        'MOVED',
                        'NOT_ECLIPSE',
                    ),
                    'CLOSED'      => array(
                        'FIXED',
                        'INVALID',
                        'WONTFIX',
                        'DUPLICATE',
                        'WORKSFORME',
                        'MOVED',
                        'NOT_ECLIPSE',
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
        return new Rule\ListRule(
            $this->fields[$source_field],
            $this->fields[$target_field],
            $this->fields[$source_field]->getValueReference($source_value),
            $this->fields[$target_field]->getValueReference($target_value)
        );
    }

    private function getSemantics() {
        return array(
            new Semantic\Title($this->fields['summary']),
            new Semantic\Status($this->fields['status'], array(
                $this->fields['status']->getValueReference('NEW'),
                $this->fields['status']->getValueReference('UNCONFIRMED'),
                $this->fields['status']->getValueReference('CONFIRMED'),
                $this->fields['status']->getValueReference('ASSIGNED'),
                $this->fields['status']->getValueReference('REOPENED'),
                $this->fields['status']->getValueReference('IN_PROGRESS'),
            )),
            new Semantic\AssignedTo($this->fields['assigned_to']),
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
            $files = $this->getFiles($bugzilla_bug, (string)$bugzilla_xml['urlbase']);
            $artifacts[] = new Artifact\Artifact(
                (int) $bugzilla_bug->bug_id,
                $this->getChangesets($bugzilla_bug, $files),
                $files
            );
        }
        return $artifacts;
    }

    private function getFiles(SimpleXMLElement $bugzilla_xml, $baseurl) {
        $files = array();
        foreach ($bugzilla_xml->attachment as $attachment) {
            $attchid = (int) $attachment->attachid;
            $id      = 'attach_'.$attchid;
            $size    = (int) $attachment->size;
            $path    = $this->data_path.'/'.$id;
            if (file_exists($path) && filesize($path) == $size) {
                echo "Attachment $attchid was already downloaded, skip\n";
            } else {
                echo "Downloading attachment $attchid ($size)\n";
                file_put_contents(
                    $path,
                    //file_get_contents($baseurl.'/attachment.cgi?id='.$attchid)
                    $path
                );
            }
            $files[$attchid] = array(
                'id'          => $id,
                'path'        => $path,
                'filename'    => (string) $attachment->filename,
                'filetype'    => (string) $attachment->type,
                'filesize'    => $size,
                'description' => (string) $attachment->desc,
            );
        }
        return new Artifact\Changeset\FilesData($files);
    }

    private function getChangesets(SimpleXMLElement $bugzilla_bug, Artifact\Changeset\FilesData $files) {
        $changeset = array($this->getInitialChangeset($bugzilla_bug)); // WARNING: add attch here too
        return array_merge($changeset, $this->getChangesetComments($bugzilla_bug, $files));
    }

    private function getInitialChangeset(SimpleXMLElement $bugzilla_bug) {
        return new Artifact\Changeset\Changeset(
            (string)$bugzilla_bug->creation_ts,
            (string)$this->user_mapper->getUser($bugzilla_bug->reporter),
            '',
            $this->getFieldsData($bugzilla_bug)
        );
    }

    private function getChangesetComments(SimpleXMLElement $bugzilla_bug, Artifact\Changeset\FilesData $files) {
        $changesets = array();
        foreach($bugzilla_bug->long_desc as $long_desc) {
            if ((int)$long_desc->comment_count === 0) {
                continue;
            }
            $changesets[] = new Artifact\Changeset\Changeset(
                (string)$long_desc->bug_when,
                $this->user_mapper->getUser($long_desc->who),
                (string) $long_desc->thetext,
                $this->getCommentChanges($long_desc, $files)
            );
        }
        return $changesets;
    }

    private function getCommentChanges(SimpleXMLElement $long_desc, Artifact\Changeset\FilesData $files) {
        $values = array();
        if (isset($long_desc->attachid)) {
            $values[] = new Artifact\Changeset\FileFieldChange('attachments', $files->getFile((int) $long_desc->attachid));
        }
        return $values;
    }

    private function getFieldsData(SimpleXMLElement $bugzilla_bug) {
        $values = array(
            new Artifact\Changeset\ScalarFieldChange('bugzilla_id', 'int', (int) $bugzilla_bug->bug_id),
            new Artifact\Changeset\ScalarFieldChange('summary', 'string', (string) $bugzilla_bug->short_desc),
            new Artifact\Changeset\ScalarFieldChange('links', 'art_link', (string) $bugzilla_bug->dependson),
            new Artifact\Changeset\ScalarFieldChange('description', 'text', (string) $bugzilla_bug->long_desc[0]->thetext),
            new Artifact\Changeset\ListFieldChange('status', $this->getValueId($this->fields['status'], $bugzilla_bug, 'bug_status')),
            new Artifact\Changeset\ListFieldChange('resolution', $this->getValueId($this->fields['resolution'], $bugzilla_bug, 'resolution')),
            new Artifact\Changeset\ListFieldChange('severity', $this->getValueId($this->fields['severity'], $bugzilla_bug, 'bug_severity')),
            new Artifact\Changeset\ListFieldChange('priority', $this->getValueId($this->fields['priority'], $bugzilla_bug, 'priority')),
            new Artifact\Changeset\ListFieldChange('product', $this->getValueId($this->fields['product'], $bugzilla_bug, 'product')),
            new Artifact\Changeset\ListFieldChange('component', $this->getValueId($this->fields['component'], $bugzilla_bug, 'component')),
            new Artifact\Changeset\ListFieldChange('version', $this->getValueId($this->fields['version'], $bugzilla_bug, 'version')),
            new Artifact\Changeset\ListFieldChange('target_milestone', $this->getValueId($this->fields['target_milestone'], $bugzilla_bug, 'target_milestone')),
            new Artifact\Changeset\ListFieldChange('hardware', $this->getValueId($this->fields['hardware'], $bugzilla_bug, 'rep_platform')),
            new Artifact\Changeset\ListFieldChange('os', $this->getValueId($this->fields['os'], $bugzilla_bug, 'op_sys')),
            new Artifact\Changeset\UsersSelectBoxFieldChange('assigned_to', $this->user_mapper->getUser($bugzilla_bug->assigned_to)),
        );

        if (isset($bugzilla_bug->cc)) {
            $cc_list = array();
            foreach ($bugzilla_bug->cc as $cc) {
                $cc_list[] = (string) $cc;
            }
            $values[] = new Artifact\Changeset\CCFieldChange('cc', $cc_list);
        }

        return $values;
    }

    private function getValueId(Field\SelectBox $field, SimpleXMLElement $bugzilla_bug, $bugzilla_field_name) {
        $bugzilla_value = (string)$bugzilla_bug->$bugzilla_field_name;
        if ($this->correspondsToNone($bugzilla_field_name, $bugzilla_value)) {
            return null;
        }
        return $field->getValueId($bugzilla_value);
    }
}