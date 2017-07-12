<?php

namespace Bz2Tuleap\Jira;

use Bz2Tuleap\Tuleap\ExternalToolInterface;

class Jira implements ExternalToolInterface
{
    /**
     * @var JiraParser
     */
    private $parser;
    /**
     * @var JiraUserMapper
     */
    private $user_mapper;

    public function __construct(JiraParser $parser, JiraUserMapper $user_mapper)
    {
        $this->parser      = $parser;
        $this->user_mapper = $user_mapper;
    }

    public function getUserMapper()
    {
        return $this->user_mapper;
    }

    public function getParser()
    {
        return $this->parser;
    }

    public function getProjectName()
    {
        return 'Jira import';
    }

    public function getProjectShortName()
    {
        return 'jira-import';
    }
}