<?php

namespace OCA\People\Controller;

use Exception;

use OCP\IRequest;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCA\People\Db\Link;
use OCA\People\Db\LinkMapper;

use OCA\People\Db\ContactMapper;

class LinkController extends Controller
{

    private $mapper;
    private $userId;

    public function __construct(string $AppName, IRequest $request, LinkMapper $mapper, ContactMapper $contactMapper, $UserId)
    {
        parent::__construct($AppName, $request);
        $this->mapper = $mapper;
        $this->userId = $UserId;
        $this->contactMapper = $contactMapper;
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     * 
     */
    public function show(int $id)
    {
        return new DataResponse($this->showAll($id, false));
    }

    public function showAll(int $id, bool $export)
    {
        $array = array();
        $links = $this->mapper->findByContactId($id);

        foreach ($links as $link) {
            try {
                if ($export) {
                    array_push($array, $this->generateExport($link, $id));
                } else {
                    array_push($array, $this->generateResponse($link, $id));
                }
            } catch (Exception $e) {
            }
        }
        return $array;
    }

    private function generateResponse(Link $link, int $id)
    {
        $idBis = $link->getContactId() == $id ? $link->getContactIdBis() : $link->getContactId();
        $contact = $this->contactMapper->find($idBis, $this->userId);

        return array(
            "id" => $link->id,
            "contactId" => $idBis,
            "type" => intval($link->getType()),
            "birth" => is_null($link->getBirth()) ? '' : $link->getBirth(),
            "birthNotif" => is_null($link->getBirthNotif()) ? '' : $link->getBirthNotif(),
            "name" => is_null($contact->getName()) ? '' : $contact->getName(),
            "lastName" => is_null($contact->getLastName()) ? '' : $contact->getLastName(),
        );
    }

    private function generateExport(Link $link, int $id)
    {

        $idBis = $link->getContactId() == $id ? $link->getContactIdBis() : $link->getContactId();
        $contact = $this->contactMapper->find($idBis, $this->userId);

        return array(
            "type" => intval($link->getType()),
            "birth" => is_null($link->getBirth()) ? '' : $link->getBirth(),
            "birth_notif" => is_null($link->getBirthNotif()) ? '' : $link->getBirthNotif(),
            "name" => is_null($contact->getName()) ? '' : $contact->getName(),
            "last_name" => is_null($contact->getLastName()) ? '' : $contact->getLastName(),
        );
    }

    /**
     * @NoAdminRequired
     *
     * @param int $contactId
     * @param int $contactIdBis
     * @param int $type
     * @param string $birth
     * @param int $birthNotif
     */
    public function create(
        int $contactId,
        int $contactIdBis,
        int $type,
        string $birth,
        int $birthNotif
    ) {
        if ($contactId == $contactIdBis) {
            return new DataResponse([], Http::STATUS_BAD_REQUEST);
        }

        // if exist - update
        $link = $this->checkContactExist($contactId, $contactIdBis);
        $link->setContactId($contactId);
        $link->setContactIdBis($contactIdBis);
        $link->setType($type);
        $link->setBirth($birth);
        $link->setBirthNotif($birthNotif);
        //return $link->id;

        if ($link->id) {
            $newLink = $this->mapper->update($link);
        } else {
            $newLink = $this->mapper->insert($link);
        }
        try {
            return new DataResponse($this->generateResponse($newLink, $contactId));
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     * @param int $contactId
     * @param int $contactIdBis
     * @param int $type
     * @param string $birth
     * @param int $birthNotif
     */
    public function update(
        int $id,
        int $contactId,
        int $contactIdBis,
        int $type,
        string $birth,
        int $birthNotif
    ) {
        if ($contactId == $contactIdBis) {
            return new DataResponse([], Http::STATUS_BAD_REQUEST);
        }

        try {
            $link = $this->checkContactExist($contactId, $contactIdBis);
            $link->setContactId($contactId);
            $link->setContactIdBis($contactIdBis);
            $link->setType($type);
            $link->setBirth($birth);
            $link->setBirthNotif($birthNotif);
            $newLink = $this->mapper->update($link);

            return new DataResponse($this->generateResponse($newLink, $contactId));
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function destroy(int $id)
    {
        // remove link
        try {
            $link = $this->mapper->find($id);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
        $this->mapper->delete($link);

        return new DataResponse($link);
    }

    public function destroyByContact(int $contactId)
    {
        try {
            $links = $this->mapper->findByContactId($contactId);
            foreach ($links as $link) {
                $this->mapper->delete($link);
            }
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
        return new DataResponse();
    }

    private function checkContactExist(int $id1, int $id2)
    {
        try {
            return $this->mapper->findByContactIds($id1, $id2);
        } catch (Exception $e) {
        }
        try {
            return $this->mapper->findByContactIds($id2, $id1);
        } catch (Exception $e) {
        }
        return new Link();
    }

    public function clean()
    {
        try {
            return $this->mapper->clean();
        } catch (Exception $e) {
        }
    }
}
