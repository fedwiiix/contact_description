<?php
namespace OCA\ContactDescription\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Tag extends Entity implements JsonSerializable {

    public $id;
    protected $userId;
    protected $name;
    protected $color;
    protected $favorite;
    protected $count;

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'name' => $this->name,
            'color' => $this->color,
            'favorite' => $this->favorite,
            'count' => $this->count,
        ];
    }
}