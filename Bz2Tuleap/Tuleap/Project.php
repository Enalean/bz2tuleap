<?php

namespace Bz2Tuleap\Tuleap;

class Project {

    /**
     * @var Tracker
     */
    private $tracker;

    /**
     * @var array
     */
    private $users;

    public function __construct(array $users, Tracker $tracker) {
        $this->users   = $users;
        $this->tracker = $tracker;
    }

    public function getTracker() {
        return $this->tracker;
    }

    public function getUsers() {
        return $this->users;
    }

    public function accept(IProjectVisitor $visitor) {
        $visitor->visit($this);
    }
}
