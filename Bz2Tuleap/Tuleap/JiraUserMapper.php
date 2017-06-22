<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class JiraUserMapper implements UserMapper
{
    private $user_id = 0;
    private $users;

    public function getUserFromAssignee(SimpleXMLElement $assignee)
    {
        return $this->getUserFromReporter($assignee);
    }

    public function getUserFromReporter(SimpleXMLElement $reporter)
    {
        $username = (string) $reporter['username'];

        if (! isset($this->users[$username])) {
            $this->users[$username] = array(
                'id'       => $this->user_id++,
                'realname' => (string) $reporter,
                'email'    => "$username@example.com",
                'username' => $username,
            );
        }

        if ($this->users[$username]["realname"] === $username) {
            $this->users[$username]["realname"] = (string) $reporter;
        }

        return $username;
    }

    public function getUserFromComment(SimpleXMLElement $comment)
    {
        $username = (string) $comment['author'];

        if (! isset($this->users[$username])) {
            $this->users[$username] = array(
                'id'       => $this->user_id++,
                'realname' => $username,
                'email'    => "$username@example.com",
                'username' => $username,
            );
        }

        return $username;
    }

    public function getUsers()
    {
        return $this->users;
    }
}