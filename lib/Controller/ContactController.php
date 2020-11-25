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
use OCA\People\Db\TagMapper;

class ContactController extends Controller
{

    private $mapper;
    private $userId;

    public function __construct(string $AppName, IRequest $request, ContactMapper $mapper, TagassignMapper $assignMapper, TagMapper $tagMapper, $UserId)
    {
        parent::__construct($AppName, $request);
        $this->mapper = $mapper;
        $this->userId = $UserId;
        $this->assignMapper = $assignMapper;
        $this->tagMapper = $tagMapper;
    }

    /**
     * @NoAdminRequired
     */
    public function index()
    {
        try {
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
            $rep->assign = $this->assignMapper->findForContact($id);
            $rep->contact = $this->mapper->find($id, $this->userId);
            return new DataResponse($rep);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
    }

    /**
     * @NoAdminRequired
     *
     * @param string $name
     * @param string $lastName
     * @param string $description
     * @param string $work
     * @param string $hobbies
     * @param string $birth
     * @param int $birthNotif
     */
    public function create(
        string $name,
        string $lastName,
        string $description,
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
        $contact->setWork($work);
        $contact->setHobbies($hobbies);
        $contact->setBirth($birth);
        $contact->setBirthNotif($birthNotif);
        $contact->setUserId($this->userId);
        $contact->setCreated($date->getTimestamp());

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
        string $work,
        string $hobbies,
        string $birth,
        int $birthNotif
    ) {
        if (strlen($name) < 3) {
            return new DataResponse([], Http::STATUS_BAD_REQUEST);
        }


        try {
            $contact = $this->mapper->find($id, $this->userId);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
        $contact->setName($name);
        $contact->setLastName($lastName);
        $contact->setDescription($description);
        $contact->setWork($work);
        $contact->setHobbies($hobbies);
        $contact->setBirth($birth);
        $contact->setBirthNotif($birthNotif);
        $contact->setUserId($this->userId);
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
        // remove contact
        try {
            $contact = $this->mapper->find($id, $this->userId);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
        $this->mapper->delete($contact);
        return new DataResponse($contact);
    }

    /**
     * @NoAdminRequired
     *
     */
    public function export()
    {
        try {
            $rep->assign = $this->assignMapper->export($this->userId);
            $rep->contact = $this->mapper->export($this->userId);

            $array = array();
            foreach ($rep->contact as $contact) {
                $assigned = array();
                foreach ($rep->assign as $assign) {
                    if ($assign->getContactId() == $contact->getId()) {
                        array_push($assigned, array(
                            "name" => is_null($assign->getName()) ? '' : $assign->getName(),
                            "color" => is_null($assign->getColor()) ? '' : $assign->getColor(),
                        ));
                    }
                }
                array_push($array, array(
                    "name" => is_null($contact->getName()) ? '' : $contact->getName(),
                    "last_name" => is_null($contact->getLastName()) ? '' : $contact->getLastName(),
                    "description" => is_null($contact->getDescription()) ? '' : $contact->getDescription(),
                    "work" => is_null($contact->getWork()) ? '' : $contact->getWork(),
                    "hobbies" => is_null($contact->getHobbies()) ? '' : $contact->getHobbies(),
                    "birth" => is_null($contact->getBirth()) ? '' : $contact->getBirth(),
                    "birth_notif" => is_null($contact->getBirthNotif()) ? '' : $contact->getBirthNotif(),
                    "tag" => $assigned
                ));
            }
            return new DataResponse($array);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
    }
}
