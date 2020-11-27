<?php

namespace OCA\People\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\QBMapper;

use OCP\DB\QueryBuilder\IQueryBuilder;

class TagassignMapper extends QBMapper
{

    public function __construct(IDbConnection $db)
    {
        parent::__construct($db, 'people_tag_assign', Tagassign::class);
        $this->tagListDB = 'people_tag';
        $this->userListDB = 'people';
    }

    public function find(int $id)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('id', $qb->createNamedParameter($id)));

        return $this->findEntity($qb);
    }

    public function findAll()
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('s1.*')
            ->addSelect('s2.name')
            ->addSelect('s2.color')
            ->from($this->getTableName(), 's1')
            ->join('s1', $this->tagListDB, 's2', $qb->expr()->eq('s1.tag_id', 's2.id'))
            ->orderBy('s2.name', 'ASC');

        return $this->findEntities($qb);
    }

    public function findForContact(int $contactId)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('s1.*')
            ->addSelect('s2.name')
            ->addSelect('s2.color')
            ->from($this->getTableName(), 's1')
            ->where($qb->expr()->eq('contact_id', $qb->createNamedParameter($contactId)))
            ->join('s1', $this->tagListDB, 's2', $qb->expr()->eq('s1.tag_id', 's2.id'))
            ->orderBy('s2.name', 'ASC');

        return $this->findEntities($qb);
    }

    public function findByTagId(int $tagId)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('tag_id', $qb->createNamedParameter($tagId)));

        return $this->findEntities($qb);
    }

    public function findByContactId(int $contactId)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('contact_id', $qb->createNamedParameter($contactId)));

        return $this->findEntities($qb);
    }

    public function export(string $userId)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('s1.*')
            ->addSelect('s2.name')
            ->addSelect('s2.color')
            ->addSelect('s2.favorite')
            ->from($this->getTableName(), 's1')
            ->join('s1', $this->tagListDB, 's2', $qb->expr()->eq('s1.tag_id', 's2.id'))
            ->where($qb->expr()->eq('s2.user_id', $qb->createNamedParameter($userId)));

        return $this->findEntities($qb);
    }

    public function cleanAssigned()
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('id')->from($this->userListDB);
        $contactIds = $this->findEntities($qb);

        $qb = $this->db->getQueryBuilder();
        $qb->select('id')->from($this->tagListDB);
        $tagIds = $this->findEntities($qb);

        $qb = $this->db->getQueryBuilder();
        $qb->delete($this->getTableName())
            ->where($qb->expr()->notIn('contact_id', $qb->createNamedParameter($this->toIdArray($contactIds), IQueryBuilder::PARAM_STR_ARRAY)))
            ->orWhere($qb->expr()->notIn('tag_id', $qb->createNamedParameter($this->toIdArray($tagIds), IQueryBuilder::PARAM_STR_ARRAY)));
        return $qb->execute();
    }

    private function toIdArray(array $arr)
    {
        $rep = array();
        foreach ($arr as $e) {
            array_push($rep, $e->id);
        }
        return $rep;
    }
}
