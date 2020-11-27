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
use OCA\People\Db\Tagassign;
use OCA\People\Db\Tag;

class SettingController extends Controller
{

    private $userId;

    public function __construct(string $AppName, IRequest $request, ContactMapper $contactMapper, TagassignMapper $assignMapper, TagMapper $tagMapper, $UserId)
    {
        parent::__construct($AppName, $request);
        $this->contactMapper = $contactMapper;
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
            return new DataResponse($this->assignMapper->cleanAssigned());
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     *
     */
    public function export()
    {
        try {
            $rep = (object)[];
            $rep->assign = $this->assignMapper->export($this->userId);
            $rep->contact = $this->contactMapper->export($this->userId);

            $array = array();
            foreach ($rep->contact as $contact) {
                $assigned = array();
                foreach ($rep->assign as $assign) {
                    if ($assign->getContactId() == $contact->getId()) {
                        array_push($assigned, array(
                            "name" => is_null($assign->getName()) ? '' : $assign->getName(),
                            "color" => is_null($assign->getColor()) ? '' : $assign->getColor(),
                            "favorite" => is_null($assign->getFavorite()) ? 0 : $assign->getFavorite(),
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
            return new DataResponse([], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     * 
     * @param string $json
     * @param bool $update
     *
     */
    public function import(string $json, bool $update)
    {

        try {
            $jsonArray = json_decode($json);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_BAD_REQUEST);
        }


        $date = new \DateTime();
        $insertedTags = array();
        $rep = (object)[];

        foreach ($jsonArray as $c) {
            // add tags
            foreach ($c->tag as $t) {
                if (!array_key_exists($t->name, $insertedTags)) {
                    try {
                        $tag = new Tag();
                        $tag->setUserId($this->userId);
                        $tag->setName($t->name);
                        $tag->setColor(isset($t->color) ? $t->color : "#31cc7c");
                        $tag->setFavorite(isset($t->favorite) ? $t->favorite : 0);

                        $rep = $this->tagMapper->insert($tag);
                        $insertedTags[$t->name] = $rep->id;
                    } catch (Exception $e) {
                        try {
                            $rep = $this->tagMapper->findByName($t->name, $this->userId);
                            if ($update) {
                                $tag->setId($rep->id);
                                $rep = $this->tagMapper->update($tag);
                            }
                            $insertedTags[$t->name] = $rep->id;
                        } catch (Exception $e) {
                        }
                    }
                }
            }
        }

        foreach ($jsonArray as $c) {

            // add contact
            if (isset($c->name) && !empty($c->name)) {
                try {
                    $contact = new Contact();
                    $contact->setName($c->name);
                    $contact->setLastName($c->last_name);
                    $contact->setDescription($c->description);
                    $contact->setWork($c->work);
                    $contact->setHobbies($c->hobbies);
                    $contact->setBirth($c->birth);
                    $contact->setBirthNotif($c->birth_notif);
                    $contact->setUserId($this->userId);
                    $contact->setCreated($date->getTimestamp());

                    $rep = $this->contactMapper->insert($contact);
                } catch (Exception $e) {
                    if ($update) {
                        $rep = $this->contactMapper->findByName($c->name, $c->last_name, $this->userId);
                        $contact->setId($rep->id);
                        $rep = $this->contactMapper->update($contact);
                    }
                }
            }

            // add assign
            if (!is_null($rep->id)) {
                foreach ($c->tag as $t) {
                    if (array_key_exists($t->name, $insertedTags)) {
                        try {
                            $tagAssign = new Tagassign();
                            $tagAssign->setContactId($rep->id);
                            $tagAssign->setTagId($insertedTags[$t->name]);

                            $this->assignMapper->insert($tagAssign);
                        } catch (Exception $e) {
                        }
                    }
                }
            }
        }
    }
}
