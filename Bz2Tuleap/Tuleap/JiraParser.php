<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class JiraParser implements ForeignParserInterface
{

    private $field_mapper;
    /**
     * @var UserMapper
     */
    private $user_mapper;

    public function __construct(UserMapper $user_mapper)
    {
        $this->field_mapper = new IdMapper('F');
        $this->user_mapper = $user_mapper;


        $this->user_mapper->getUser(new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>
                                             <user name="testman">testman@example.com</user>'));
    }

    public function getTrackerFromBugzilla(SimpleXMLElement $bugzilla_xml)
    {
        $this->fields = array(
            'summary' => new Field(
                $this->field_mapper, 'string', 'summary', 'Summary', new Properties(array('size' => 61)), new DefaultFieldPermissions()
            )
        );

        return new Tracker(
            $this->getFields(),
            $this->getReportColumns(),
            $this->getSemantics(),
            new Rules(new ListRules([])),
            $this->getArtifacts($bugzilla_xml)
        );
    }

    private function getFields() {
        return new FormElements(array($this->fields['summary']));
    }

    private function getSemantics() {
        return array(
            new TitleSemantic($this->fields['summary'])
        );
    }

    private function getReportColumns() {
        return array(
            $this->fields['summary'],
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
            'testman',
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
                'testman',
                (string) $comment,
                Comment::HTML,
                []
            );
        }
        return $changesets;
    }

    private function getFieldsData(SimpleXMLElement $jira_issue) {
        $values = array(
            new ScalarFieldChange('summary', 'string', (string) $jira_issue->summary),
        );

        return $values;
    }
}