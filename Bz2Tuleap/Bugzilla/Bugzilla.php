<?php

namespace Bz2Tuleap\Bugzilla;

use Bz2Tuleap\Tuleap\ExternalToolInterface;

class Bugzilla implements ExternalToolInterface
{
    /**
     * @var BugzillaParser
     */
    private $parser;

    /**
     * @var BugzillaUserMapper
     */
    private $user_mapper;

    public function __construct(BugzillaParser $parser, BugzillaUserMapper $user_mapper)
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
        return 'Bugzilla import';
    }

    public function getProjectShortName()
    {
        return 'bugzilla-import';
    }
}