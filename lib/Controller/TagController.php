<?php

namespace OCA\People\Controller;

use Exception;

use OCP\IRequest;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCA\People\Db\Tag;
use OCA\People\Db\TagMapper;

use OCA\People\Db\Tagassign;
use OCA\People\Db\TagassignMapper;

class TagController extends Controller {

    private $mapper;
    private $userId;

    public function __construct(string $AppName, IRequest $request, TagMapper $mapper, TagassignMapper $assignMapper, $UserId)
    {
        parent::__construct($AppName, $request);
        $this->mapper = $mapper;
        $this->userId = $UserId;
        $this->assignMapper = $assignMapper;
    }

    /**
     * @NoAdminRequired
     */
    public function index()
    {
        return new DataResponse($this->mapper->findList($this->userId));
    }

    /**
     * @NoAdminRequired
     *
     * @param string $name
     * @param string $color
     */
    public function create( string $name, 
                            string $color)
    {
        if(!strlen($name)){
            return new DataResponse([], Http::STATUS_BAD_REQUEST);
        }

        $tag = new Tag();
        $tag->setUserId($this->userId);
        $tag->setName($name);
        $tag->setColor($color);
        $tag->setFavorite(0);

        try {
            return new DataResponse($this->mapper->insert($tag));
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     * @param string $name
     * @param string $color
     */
    public function update( int $id,
                            string $name, 
                            string $color)
    {
        if(!strlen($name)){
            return new DataResponse([], Http::STATUS_BAD_REQUEST);
        }

        try {
            $tag = $this->mapper->find($id, $this->userId);
            $tag->setName($name);
            $tag->setColor($color);
            return new DataResponse($this->mapper->update($tag));
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     * @param int $favorite
     */
    public function favorite( int $id,
                            int $favorite)
    {
        try {
            $tag = $this->mapper->findWithCount($id, $this->userId);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
        $tag->setFavorite($favorite);
        return new DataResponse($this->mapper->update($tag));
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
            $assignedTag = $this->assignMapper->findByTagId($id);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
        foreach ($assignedTag as $key => $value) {
            $this->assignMapper->delete($value);
        }
        // remove tag
        try {
            $tag = $this->mapper->find($id, $this->userId);
        } catch (Exception $e) {
            return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
        $this->mapper->delete($tag);

        return new DataResponse($tag);
    }
}
