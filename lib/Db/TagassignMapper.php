<?php

namespace OCA\ContactDescription\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\QBMapper;

class TagassignMapper extends QBMapper
{

    public function __construct(IDbConnection $db)
    {
        parent::__construct($db, 'contact_desc_tag', Tagassign::class);
        $this->tagListDB='contact_desc_tag_list';
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
        $qb->select('s1.tag_id')
            ->addSelect('s2.tag_name')
            ->from($this->getTableName(), 's1')
            ->join('s1', $this->tagListDB, 's2', $qb->expr()->eq('s1.tag_id', 's2.id'))
            ->orderBy('s2.tag_name', 'ASC');

        return $this->findEntities($qb);
    }

    public function findForContact(int $contactId)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('s1.*')
            ->addSelect('s2.tag_name')
            ->from($this->getTableName(), 's1')
            ->where($qb->expr()->eq('contact_id', $qb->createNamedParameter($contactId)))
            ->join('s1', $this->tagListDB, 's2', $qb->expr()->eq('s1.tag_id', 's2.id'))
            ->orderBy('s2.tag_name', 'ASC');

        return $this->findEntities($qb);
    }

    public function findFor(int $contactId, int $tagId)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('contact_id', $qb->createNamedParameter($contactId)))
            ->where($qb->expr()->eq('tag_id', $qb->createNamedParameter($tagId)));

        return $this->findEntity($qb);
    }
}