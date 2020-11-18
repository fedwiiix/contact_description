<?php
namespace OCA\ContactDescription\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Tagassign extends Entity implements JsonSerializable {

    public $id;
    protected $contactId;
    protected $tagId;
    protected $tagName;

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'contactId' => $this->contactId,
            'tagId' => $this->tagId,
            'tagName' => $this->tagName,
        ];
    }
}