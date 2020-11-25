<?php
namespace OCA\People\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Contact extends Entity implements JsonSerializable {

    public $id;
    protected $userId;
    protected $name;
    protected $lastName;
    protected $description;
    protected $work;
    protected $hobbies;
    protected $birth;
    protected $birthNotif;
    protected $created;

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'name' => $this->name,
            'lastName' => $this->lastName,
            'description' => $this->description,
            'work' => $this->work,
            'hobbies' => $this->hobbies,
            'birth' => $this->birth,
            'birthNotif' => $this->birthNotif,
            'created' => $this->created,
        ];
    }
}