<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;
use Bz2Tuleap\Tuleap\Field\CCField;
use Bz2Tuleap\Tuleap\Field\CCFieldChange;
use Bz2Tuleap\Tuleap\Field\DateFieldChange;
use Bz2Tuleap\Tuleap\Field\DefaultFieldPermissions;
use Bz2Tuleap\Tuleap\Field\Field;
use Bz2Tuleap\Tuleap\Field\FileFieldChange;
use Bz2Tuleap\Tuleap\Field\FilesData;
use Bz2Tuleap\Tuleap\Field\ListFieldChange;
use Bz2Tuleap\Tuleap\Field\NoFieldPermissions;
use Bz2Tuleap\Tuleap\Field\NoProperties;
use Bz2Tuleap\Tuleap\Field\OpenListField;
use Bz2Tuleap\Tuleap\Field\OpenListFieldChange;
use Bz2Tuleap\Tuleap\Field\ReadOnlyFieldPermissions;
use Bz2Tuleap\Tuleap\Field\ScalarFieldChange;
use Bz2Tuleap\Tuleap\Field\SelectBoxField;
use Bz2Tuleap\Tuleap\Field\StructureField;
use Bz2Tuleap\Tuleap\Field\TextFieldChange;
use Bz2Tuleap\Tuleap\Field\UsersSelectBoxField;
use Bz2Tuleap\Tuleap\Field\UsersSelectBoxFieldChange;
use Bz2Tuleap\Tuleap\Field\Properties;
use Bz2Tuleap\Tuleap\Semantic\AssignedToSemantic;
use Bz2Tuleap\Tuleap\Semantic\DescriptionSemantic;
use Bz2Tuleap\Tuleap\Semantic\StatusSemantic;
use Bz2Tuleap\Tuleap\Semantic\TitleSemantic;

class JiraParser implements ForeignParserInterface
{

    private $field_mapper;

    /**
     * @var JiraUserMapper
     */
    private $user_mapper;

    private $fields;

    private $value_mapper;

    public function __construct(JiraUserMapper $user_mapper)
    {
        $this->field_mapper = new IdMapper('F');
        $this->value_mapper = new IdMapper('V');
        $this->user_mapper  = $user_mapper;
    }

    public function getTrackerFromBugzilla(SimpleXMLElement $bugzilla_xml)
    {
        $this->fields = [
            'summary' => new Field(
                $this->field_mapper, 'string', 'summary', 'Summary', new Properties(array('size' => 61)), new DefaultFieldPermissions()
            ),
            'description'=> new Field(
                $this->field_mapper, 'text', 'description', 'Description', new Properties(array('rows' => 7, 'cols' => 80)), new DefaultFieldPermissions()
            ),
            'status' => new SelectBoxField($this->field_mapper, $this->value_mapper, 'status', "Status", [
                'Open',
                'In Progress',
                'Resolved',
                'Closed',
                'Reopened',
                //Verified
                'In Review'
            ], new DefaultFieldPermissions()),
            'assignee' => new UsersSelectBoxField(
                $this->field_mapper, 'assignee', 'Assignee', new DefaultFieldPermissions()
            ),
            'reporter'       => new Field($this->field_mapper, 'subby', 'reporter', 'Reporter', new NoProperties(), new ReadOnlyFieldPermissions()),
            'submitted_on'   => new Field($this->field_mapper, 'subon', 'submitted_on', 'Submitted on', new NoProperties(), new ReadOnlyFieldPermissions()),
            'last_update_by' => new Field($this->field_mapper, 'luby', 'last_update_by', 'Last update by', new NoProperties(), new ReadOnlyFieldPermissions()),
            'last_update_on' => new Field($this->field_mapper, 'lud', 'last_update_on', 'Last update on', new NoProperties(), new ReadOnlyFieldPermissions()),
            'priority'       => new SelectBoxField($this->field_mapper, $this->value_mapper, 'priority', "Priority", [
                'Blocker',
                'Critical',
                'Major',
                'Minor',
                'Trivial',
            ], new DefaultFieldPermissions()),
            'type' => new SelectBoxField($this->field_mapper, $this->value_mapper, 'type', "Type", [
                'Bug',
                'New Feature',
                'Task',
                'Improvement',
                'Patch',
            ], new DefaultFieldPermissions()),
            'environment' => new Field(
                $this->field_mapper, 'text', 'environment', 'Environment',new Properties(array('rows' => 7, 'cols' => 80)), new DefaultFieldPermissions()
            ),
            'resolution' => new SelectBoxField($this->field_mapper, $this->value_mapper, 'resolution', "Resolution", [
                'Unresolved',
                'Fixed',
                "Won't Fix",
                'Duplicate',
                'Incomplete',
                'Cannot Reproduce',
                'Postponed',
                'Not A Defect',
                'Done',
            ], new DefaultFieldPermissions()),
            'resolved'=> new Field(
                $this->field_mapper, 'date', 'resolved', 'Resolved', new Properties(array('display_time' => true)), new DefaultFieldPermissions()
            ),
            'labels'=> new OpenListField(
                $this->field_mapper, 'labels', 'Labels', new DefaultFieldPermissions()
            ),
        ];

        return new Tracker(
            $this->getFields(),
            $this->getReportColumns(),
            $this->getSemantics(),
            new Rules(new ListRules([])),
            $this->getArtifacts($bugzilla_xml)
        );
    }

    private function getFields() {
        return new FormElements(
            [
                new Column(
                    $this->field_mapper,
                    [
                        new FieldSet($this->field_mapper, 'Details', [
                            new Column($this->field_mapper, [
                                $this->fields['summary'],
                                $this->fields['type'],
                                $this->fields['priority'],
                                $this->fields['labels'],
                                $this->fields['environment'],
                            ]),
                            new Column($this->field_mapper, [
                                $this->fields['status'],
                                $this->fields['resolution'],
                            ]),
                        ]),
                        new FieldSet($this->field_mapper, 'Description', [
                            $this->fields['description']
                        ])
                    ]
                ),
                new Column(
                    $this->field_mapper,
                    [
                        new FieldSet($this->field_mapper, 'People', [
                            $this->fields['assignee'],
                            $this->fields['reporter'],
                            $this->fields['last_update_by'],
                        ]),
                        new FieldSet($this->field_mapper, 'Dates', [
                            $this->fields['submitted_on'],
                            $this->fields['last_update_on'],
                            $this->fields['resolved'],
                        ]),
                    ]
                )
            ]
        );
    }

    private function getSemantics() {
        return [
            new TitleSemantic($this->fields['summary']),
            new DescriptionSemantic($this->fields['description']),
            new StatusSemantic($this->fields['status'], array(
                $this->fields['status']->getValueReference('Open'),
                $this->fields['status']->getValueReference('In Progress'),
                $this->fields['status']->getValueReference('Reopened'),
//                $this->fields['status']->getValueReference('Verified'),
                $this->fields['status']->getValueReference('In Review'),
            )),
            new AssignedToSemantic($this->fields['assignee']),
        ];
    }

    private function getReportColumns() {
        return array(
            $this->fields['summary'],
            $this->fields['status'],
            $this->fields['assignee'],
        );
    }

    private function getArtifacts(SimpleXMLElement $jira_xml) {
        $artifacts = array();
        foreach($jira_xml->channel->item as $jira_issue) {
            $files = new FilesData([]);
            $artifacts[] = new Artifact(
                (int) $jira_issue->key['id'],
                $this->getChangesets($jira_issue, $files),
                $files
            );
        }
        return $artifacts;
    }

    private function getChangesets(SimpleXMLElement $jira_issue, FilesData $files) {
        $changeset = array($this->getInitialChangeset($jira_issue));
        return array_merge($changeset, $this->getChangesetComments($jira_issue, $files));
    }

    private function getInitialChangeset(SimpleXMLElement $jira_issue) {
        return new Changeset(
            (string)$jira_issue->created,
            (string)$this->user_mapper->getUserFromReporter($jira_issue->reporter),
            '',
            Comment::HTML,
            $this->getFieldsData($jira_issue)
        );
    }

    private function getChangesetComments(SimpleXMLElement $jira_issue, FilesData $files) {
        $changesets = array();
        if (! isset($jira_issue->comments)) {
            return [];
        }
        foreach($jira_issue->comments->comment as $comment) {
            $changesets[] = new Changeset(
                (string)$comment['created'],
                (string)$this->user_mapper->getUserFromComment($comment),
                (string) $comment,
                Comment::HTML,
                []
            );
        }
        return $changesets;
    }

    private function getFieldsData(SimpleXMLElement $jira_issue) : array
    {
        $values = [
            new ScalarFieldChange('summary', 'string', (string) $jira_issue->summary),
            new TextFieldChange('description', 'text', (string) $jira_issue->description, TextFieldChange::HTML),
            new ListFieldChange('status', $this->getValueId($this->fields['status'], $jira_issue, 'status')),
            new ListFieldChange('priority', $this->getValueId($this->fields['priority'], $jira_issue, 'priority')),
            new ListFieldChange('type', $this->getValueId($this->fields['type'], $jira_issue, 'type')),
            new TextFieldChange('environment', 'text', (string) $jira_issue->environment, TextFieldChange::HTML),
            new ListFieldChange('resolution', $this->getValueId($this->fields['resolution'], $jira_issue, 'resolution')),
        ];

        $assignee = $this->user_mapper->getUserFromAssignee($jira_issue->assignee);
        if ($assignee) {
            $values[] =  new UsersSelectBoxFieldChange('assignee', $assignee);

        }

        if (isset($jira_issue->resolved)) {
            $values[] = new DateFieldChange('resolved', $jira_issue->resolved);
        }

        $label_values = array();
        foreach ($jira_issue->labels->label as $label) {
            $label_values[] = (string)$label;
        }

        $values[] = new OpenListFieldChange('labels', $label_values);

        return $values;
    }

    private function getValueId(SelectBoxField $field, SimpleXMLElement $jira_issue, $jira_field_name)
    {
        $jira_value = (string)$jira_issue->$jira_field_name;
        if ($this->correspondsToNone($jira_field_name, $jira_value)) {
            return null;
        }
        return $field->getValueId($jira_value);
    }

    private function correspondsToNone($name, $value)
    {
        if ($name === 'status' && $value == '') {
            return true;
        }

        return false;
    }
}