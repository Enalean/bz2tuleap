<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class UserMapper {

    private $user;

    public function getUser(SimpleXMLElement $bugzilla_user_node) {
        $email = (string) $bugzilla_user_node;

        $at_place = strpos($email, '@');
        if ($at_place !== false) {
            $username = substr($email, 0, $at_place);
        } else {
            $username = $email;
        }

        if (! isset($this->users[$username])) {
            $this->users[$username] = array(
                'realname' => $bugzilla_user_node['name'],
            );
        }
        return $username;
    }



    public function getUsers() {
        return $this->users;
    }
}