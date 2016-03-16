<?php

namespace Bz2Tuleap\Tuleap;

use Bz2Tuleap\Tuleap\Tracker\Field;
use Bz2Tuleap\Tuleap\Tracker\Rule;

use SimpleXMLElement;

class Tracker {

    /**
     * @var Rules
     */
    private $rules;

    /**
     * @var array
     */
    private $semantics;

    /**
     * @var FormElements
     */
    private $fields;
    private $artifacts;
    private $report_columns;

    public function __construct(Field\FormElements $fields, array $report_columns, array $semantics, Rule\Rules $rules, array $artifacts) {
        $this->fields         = $fields;
        $this->report_columns = $report_columns;
        $this->semantics      = $semantics;
        $this->artifacts      = $artifacts;
        $this->rules = $rules;
    }

    /**
     * @return Field\IFormElement
     */
    public function getField() {
        return $this->fields;
    }

    /**
     * @return Tracker\Artifact\Artifact[]
     */
    public function getArtifacts() {
        return $this->artifacts;
    }

    /**
     * @return Field\IField[]
     */
    public function getReportColumns() {
        return $this->report_columns;
    }

    /**
     * @return array
     */
    public function getSemantics() {
        return $this->semantics;
    }

    /**
     * @return Rule\Rules
     */
    public function getRules() {
        return $this->rules;
    }

    public function accept(ITrackerVisitor $visitor) {
        $visitor->visit($this);
    }
}
