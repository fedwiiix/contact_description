<?php
namespace OCA\ContactDescription\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Tag extends Entity implements JsonSerializable {

    public $id;
    protected $userId;
    protected $tagName;
    protected $favorite;

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'tagName' => $this->tagName,
            'favorite' => $this->favorite,
        ];
    }
}