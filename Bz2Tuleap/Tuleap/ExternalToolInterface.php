<?php

namespace Bz2Tuleap\Tuleap;


interface ExternalToolInterface
{
    public function getUserMapper();
    public function getParser();
    public function getProjectName();
    public function getProjectShortName();
}