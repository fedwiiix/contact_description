<?php

namespace OCA\ContactDescription\Controller;

use Exception;

use OCP\IRequest;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCA\ContactDescription\Db\Contact;
use OCA\ContactDescription\Db\ContactMapper;

class ContactController extends Controller {

    private $mapper;
    private $userId;

    public function __construct(string $AppName, IRequest $request, ContactMapper $mapper, $UserId)
    {
        parent::__construct($AppName, $request);
        $this->mapper = $mapper;
        $this->userId = $UserId;
    }

    /**
     * @NoAdminRequired
     */
    public function index()
    {
        return new DataResponse($this->mapper->findAll($this->userId));
    }

    /**
     * @NoAdminRequired
     * 
     * @param string $genre
     * @param string $page
     */
    public function list(string $genre, int $page)
    {
        //return new DataResponse($this->mapper->findList($this->userId));
        /*if($genre=="all"){
            return new DataResponse($this->mapper->findAll($this->userId, $page));
        }else if($genre=="listed"){
            //return new DataResponse($this->mapper->findListed($this->userId, $page));
        }else if($genre=="best"){
          //  return new DataResponse($this->mapper->findBestOrder($this->userId, $page));
        }else{
          //  return new DataResponse($this->mapper->findAllByGenre($this->userId, $genre, $page));
        }
                            try {
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }*/
    }

    /*/**
     * @NoAdminRequired
     * 
     * @param string $search
     */
    /*public function search(string $search)
    {
       // return new DataResponse($this->mapper->search($this->userId, $search));
    }*/

    /**
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function show(int $id)
    {
        try {
            return new DataResponse($this->mapper->find($id, $this->userId));
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
    public function create( string $name,
                            string $lastName,
                            string $description,
                            string $work,
                            string $hobbies,
                            string $birth,
                            int $birthNotif)
    {
        if(strlen($name) < 3){
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
    public function update( int $id,
                            string $name,
                            string $lastName,
                            string $description,
                            string $work,
                            string $hobbies,
                            string $birth,
                            int $birthNotif)
    {
        if(strlen($name) < 3){
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
        try {
            $contact = $this->mapper->find($id, $this->userId);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
        $this->mapper->delete($contact);
        return new DataResponse($contact);
    }
}
