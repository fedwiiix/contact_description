<?php

namespace OCA\People\Controller;

use Exception;

use OCP\IRequest;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCA\People\Db\Contact;
use OCA\People\Db\ContactMapper;

use OCA\People\Db\TagassignMapper;

use OCA\People\Controller\LinkController;

class ContactController extends Controller
{

    private $mapper;
    private $userId;

    public function __construct(string $AppName, IRequest $request, ContactMapper $mapper, TagassignMapper $assignMapper, LinkController $linkController, $UserId)
    {
        parent::__construct($AppName, $request);
        $this->mapper = $mapper;
        $this->userId = $UserId;
        $this->assignMapper = $assignMapper;
        $this->linkController = $linkController;

    }

    /**
     * @NoAdminRequired
     */
    public function index()
    {
        try {
            $rep = (object)[];
            $rep->assign = $this->assignMapper->findAll();
            $rep->contact = $this->mapper->findAll($this->userId);
            return new DataResponse($rep);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function show(int $id)
    {
        try {
            $rep = (object)[];
            $rep->assign = $this->assignMapper->findForContact($id);
            $rep->contact = $this->mapper->find($id, $this->userId);
            $rep->link = $this->linkController->showAll($id, false);
            return new DataResponse($rep);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
    }

    /*private function getLinks(){
        $array = array();
        $links = $this->mapper->findAll();
        foreach ($links as $link) {
            if ($link->getContactId() == $id || $link->getContactIdBis() == $id) {
                array_push($array, $this->generateResponse($link, $id));
            }
        }
    }*/

    /**
     * @NoAdminRequired
     *
     * @param string $name
     * @param string $lastName
     * @param string $description
     * @param string $address
     * @param string $work
     * @param string $hobbies
     * @param string $birth
     * @param int $birthNotif
     */
    public function create(
        string $name,
        string $lastName,
        string $description,
        string $address,
        string $work,
        string $hobbies,
        string $birth,
        int $birthNotif
    ) {
        if (strlen($name) < 3) {
            return new DataResponse([], Http::STATUS_BAD_REQUEST);
        }
        $date = new \DateTime();

        $contact = new Contact();
        $contact->setName($name);
        $contact->setLastName($lastName);
        $contact->setDescription($description);
        $contact->setAddress($address);
        $contact->setWork($work);
        $contact->setHobbies($hobbies);
        $contact->setBirth($birth);
        $contact->setBirthNotif($birthNotif);
        $contact->setUserId($this->userId);
        $contact->setUpdated($date->getTimestamp());

        try {
            return new DataResponse($this->mapper->insert($contact));
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     * @param string $name
     * @param string $lastName
     * @param string $description
     * @param string $address
     * @param string $work
     * @param string $hobbies
     * @param string $birth
     * @param int $birthNotif
     */
    public function update(
        int $id,
        string $name,
        string $lastName,
        string $description,
        string $address,
        string $work,
        string $hobbies,
        string $birth,
        int $birthNotif
    ) {
        if (strlen($name) < 3) {
            return new DataResponse([], Http::STATUS_BAD_REQUEST);
        }
        $date = new \DateTime();

        try {
            $contact = $this->mapper->find($id, $this->userId);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
        $contact->setName($name);
        $contact->setLastName($lastName);
        $contact->setDescription($description);
        $contact->setAddress($address);
        $contact->setWork($work);
        $contact->setHobbies($hobbies);
        $contact->setBirth($birth);
        $contact->setBirthNotif($birthNotif);
        $contact->setUserId($this->userId);
        $contact->setUpdated($date->getTimestamp());
        return new DataResponse($this->mapper->update($contact));
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function destroy(int $id)
    {
        // remove all asign
        try {
            $assignedTag = $this->assignMapper->findByContactId($id);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
        foreach ($assignedTag as $key => $value) {
            $this->assignMapper->delete($value);
        }
        // remove links
        $this->linkController->destroyByContact($id);
        // remove contact
        try {
            $contact = $this->mapper->find($id, $this->userId);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
        $this->mapper->delete($contact);
        return new DataResponse($contact);
    }
}
