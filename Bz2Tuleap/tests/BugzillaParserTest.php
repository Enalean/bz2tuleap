<?php

use Bz2Tuleap\Converter;
use PHPUnit\Framework\TestCase;

class BugzillaParserTest extends TestCase
{
    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var String
     */
    private $output_dir;

    /**
     * @var String
     */
    private $source_file;

    protected function setUp() : void
    {
        $this->source_file = __DIR__.'/examples/bugzilla_example.xml';
        $this->output_dir  = __DIR__.'/output';

        mkdir($this->output_dir);

        $this->converter = new Converter();
    }

    protected function tearDown() : void
    {
        exec('rm -rf ' . escapeshellarg($this->output_dir));
    }

    public function testConvertCreatesTheNeededFiles()
    {
        $this->converter->convert($this->source_file, $this->output_dir, 'bugzilla');

        $this->assertTrue(file_exists($this->output_dir.'/project.xml'));
        $this->assertTrue(file_exists($this->output_dir.'/users.xml'));
        $this->assertTrue(is_dir($this->output_dir.'/data'));

        $expected_project_xml = simplexml_load_file(__DIR__.'/expected/bugzilla_project.xml');
        $expected_users_xml   = simplexml_load_file(__DIR__.'/expected/bugzilla_users.xml');

        $this->assertEquals($expected_project_xml, simplexml_load_file($this->output_dir.'/project.xml'));
        $this->assertEquals($expected_users_xml, simplexml_load_file($this->output_dir.'/users.xml'));
    }
}