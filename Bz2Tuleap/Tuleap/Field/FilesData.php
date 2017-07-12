<?php

namespace Bz2Tuleap\Tuleap\Field;

use SimpleXMLElement;

class FilesData {

    private $files;

    public function __construct(array $files) {
        $this->files = $files;
    }

    public function getFile($id) {
        if (isset($this->files[$id])) {
            return $this->files[$id];
        }
    }

    public function toXml(SimpleXMLElement $parent) {
        foreach ($this->files as $file) {
            $file_node = $parent->addChild('file');
            $file_node->addAttribute('id', $file['id']);
            $file_node->addChild('filename', $file['filename']);
            $file_node->addChild('path', 'data/'.$file['id']);
            $file_node->addChild('filesize', $file['filesize']);
            $file_node->addChild('filetype', $file['filetype']);
            $file_node->addChild('description', $file['description']);
        }
    }
}
