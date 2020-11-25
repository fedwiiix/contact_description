<?php
namespace OCA\People\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Tagassign extends Entity implements JsonSerializable {

    public $id;
    protected $contactId;
    protected $tagId;
    protected $name;
    protected $color;

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'contactId' => $this->contactId,
            'tagId' => $this->tagId,
            'name' => $this->name,
            'color' => $this->color,
        ];
    }
}