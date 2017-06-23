<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class Project
{
    /**
     * @var UserMapper
     */
    private $user_mapper;
    /**
     * @var ForeignParserInterface
     */
    private $parser;

    public function __construct(UserMapper $user_mapper, ForeignParserInterface $parser)
    {
        $this->user_mapper = $user_mapper;
        $this->parser      = $parser;
    }

    public function convert(SimpleXMLElement $bugzilla_xml, $data_path)
    {
        $tracker = $this->parser->getTrackerFromXMLContent($bugzilla_xml);

        $project_xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>
                                             <project />');

        $this->projectAttributes($project_xml);
        $this->addUserGroups($project_xml);
        $this->addServices($project_xml);

        $tracker->toXml($project_xml);

        $users_xml = $this->getUsers();

        return array($project_xml, $users_xml);
    }

    private function projectAttributes(SimpleXMLElement $tuleap_xml)
    {
        $tuleap_xml->addAttribute('access', 'public');
        $tuleap_xml->addAttribute('description', '');
        $tuleap_xml->addAttribute('full-name', 'Jira import');
        $tuleap_xml->addAttribute('unix-name', 'jira-import');

        $tuleap_xml->addChild('long-description', '');
    }

    private function addUserGroups(SimpleXMLElement $tuleap_xml)
    {
        $ugroups = $tuleap_xml->addChild('ugroups');
        $ugroup  = $ugroups->addChild('ugroup');
        $ugroup->addAttribute('name', 'project_members');
        $ugroup->addAttribute('description', '');
        $members = $ugroup->addChild('members');
        foreach ($this->user_mapper->getUsers() as $user) {
            $member = $members->addChild('member', $user['username']);
            $member->addAttribute('format', 'username');
        }
    }

    private function addServices(SimpleXMLElement $tuleap_xml)
    {
        $services = $tuleap_xml->addChild('services');
        $service = $services->addChild('service');
        $service->addAttribute('shortname', 'plugin_tracker');
        $service->addAttribute('enabled', 'true');
    }

    private function getUsers()
    {
        $users = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>
                                       <users />');

        foreach ($this->user_mapper->getUsers() as $user_details) {
            $user = $users->addChild('user');
            $user->addChild('id',       $user_details['id']);
            $user->addChild('username', $user_details['username']);
            $user->addChild('realname', $user_details['realname']);
            $user->addChild('email',    $user_details['email']);
            $user->addChild('ldapid', '');
        }

        return $users;
    }
}
