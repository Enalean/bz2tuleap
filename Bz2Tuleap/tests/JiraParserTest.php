<?php

use Bz2Tuleap\Converter;
use PHPUnit\Framework\TestCase;

class JiraParserTest extends TestCase
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

    protected function setUp()
    {
        $this->source_file = __DIR__.'/examples/jira_example.xml';
        $this->output_dir  = __DIR__.'/output';

        mkdir($this->output_dir);

        $this->converter = new Converter();
    }

    protected function tearDown()
    {
        exec('rm -rf ' . escapeshellarg($this->output_dir));
    }

    public function testConvertCreatesTheNeededFiles()
    {
        $this->converter->convert($this->source_file, $this->output_dir, 'jira');

        $this->assertTrue(file_exists($this->output_dir.'/project.xml'));
        $this->assertTrue(file_exists($this->output_dir.'/users.xml'));
        $this->assertTrue(is_dir($this->output_dir.'/data'));

        $expected_project_content = file_get_contents(__DIR__.'/expected/project.xml');
        $expected_user_content    = file_get_contents(__DIR__.'/expected/users.xml');

        $this->assertEquals($expected_project_content, file_get_contents($this->output_dir.'/project.xml'));
        $this->assertEquals($expected_user_content, file_get_contents($this->output_dir.'/users.xml'));
    }
}