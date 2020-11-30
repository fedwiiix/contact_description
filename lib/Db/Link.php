<?php

namespace OCA\People\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Link extends Entity implements JsonSerializable
{

    public $id;
    protected $contactId;
    protected $contactIdBis;
    protected $type;
    protected $birth;
    protected $birthNotif;

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'contact_id' => $this->contactId,
            'contact_id_bis' => $this->contactIdBis,
            'type' => $this->type,
            'birth' => $this->birth,
            'birth_notif' => $this->birthNotif,
        ];
    }
}
