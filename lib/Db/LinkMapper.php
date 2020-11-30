<?php

namespace OCA\People\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;

class LinkMapper extends QBMapper
{

    public function __construct(IDbConnection $db)
    {
        parent::__construct($db, 'people_link', Link::class);
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

        $qb->select('*')
            ->from($this->getTableName());

        return $this->findEntities($qb);
    }

    public function findByContactId(int $contactId)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('contact_id', $qb->createNamedParameter($contactId)))
            ->orWhere($qb->expr()->eq('contact_id_bis', $qb->createNamedParameter($contactId)))
            ;

        return $this->findEntities($qb);
    }

    public function findByContactIds(int $contactId, int $contactIdBis)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('contact_id', $qb->createNamedParameter($contactId)))
            ->andWhere($qb->expr()->eq('contact_id_bis', $qb->createNamedParameter($contactIdBis)))
            //->orWhere($qb->expr()->eq('contact_id', $qb->createNamedParameter($contactIdBis)))
            //->andWhere($qb->expr()->eq('contact_id_bis', $qb->createNamedParameter($contactId)))
            ;

        return $this->findEntity($qb);
    }

    public function clean()
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('id')->from($this->userListDB);
        $contactIds = $this->findEntities($qb);

        $qb = $this->db->getQueryBuilder();
        $qb->delete($this->getTableName())
            ->where($qb->expr()->notIn('contact_id', $qb->createNamedParameter($this->toIdArray($contactIds), IQueryBuilder::PARAM_STR_ARRAY)))
            ->orWhere($qb->expr()->notIn('contact_id_bis', $qb->createNamedParameter($this->toIdArray($contactIds), IQueryBuilder::PARAM_STR_ARRAY)));
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
