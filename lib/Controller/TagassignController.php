<?php

namespace OCA\ContactDescription\Controller;

use Exception;

use OCP\IRequest;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCA\ContactDescription\Db\Tagassign;
use OCA\ContactDescription\Db\TagassignMapper;

class TagassignController extends Controller {

    private $mapper;
    private $userId;

    public function __construct(string $AppName, IRequest $request, TagassignMapper $mapper, $userId)
    {
        parent::__construct($AppName, $request);
        $this->mapper = $mapper;
        $this->userId = $userId;
    }

    /**
     * @NoAdminRequired
     */
    public function index()
    {
        return new DataResponse($this->mapper->findAll());
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function show(int $id)
    {
        return new DataResponse($this->mapper->findForContact($id));
    }

    /**
     * @NoAdminRequired
     *
     * @param int $contactId
     * @param int $tagId
     */
    public function create( int $contactId, 
                            int $tagId)
    {
        $tagAssign = new Tagassign();
        $tagAssign->setContactId($contactId);
        $tagAssign->setTagId($tagId);

        try {
            return new DataResponse($this->mapper->insert($tagAssign));
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     *
     * @param int $contactId
     * @param int $tagId
     */
    public function destroy(int $contactId, 
                            int $tagId)
    {
        try {
            $tag = $this->mapper->findFor($contactId, $tagId);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
        $this->mapper->delete($tag);
        return new DataResponse($tag);
    }
}
