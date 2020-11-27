<?php

namespace OCA\People\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\QBMapper;

class TagMapper extends QBMapper
{

    public function __construct(IDbConnection $db)
    {
        parent::__construct($db, 'people_tag', Tag::class);
        $this->tagAssignDB = 'people_tag_assign';
    }

    public function find(int $id, string $userId)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('id', $qb->createNamedParameter($id)))
            ->andWhere($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));

        return $this->findEntity($qb);
    }

    public function findAll(string $userId)
    {
        $qb = $this->db->getQueryBuilder();

        $qb->select('id')
            ->addSelect('name')
            ->addSelect('color')
            ->addSelect('favorite')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));

        return $this->findEntities($qb);
    }

    public function findWithCount(int $id, string $userId)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('s1.*')
            ->selectAlias($qb->createFunction('COUNT(s2.id)'), 'count')
            ->from($this->getTableName(), 's1')
            ->leftJoin('s1', $this->tagAssignDB, 's2', $qb->expr()->eq('s1.id', 's2.tag_id'))
            ->where($qb->expr()->eq('s1.id', $qb->createNamedParameter($id)))
            ->andWhere($qb->expr()->eq('s1.user_id', $qb->createNamedParameter($userId)))
            ->groupBy('s1.id');

        return $this->findEntity($qb);
    }

    public function findList(string $userId)
    {

        $qb = $this->db->getQueryBuilder();

        $qb->select('s1.id')
            ->addSelect('s1.name')
            ->addSelect('s1.color')
            ->addSelect('s1.favorite')
            ->selectAlias($qb->createFunction('COUNT(s2.id)'), 'count')
            ->from($this->getTableName(), 's1')
            ->leftJoin('s1', $this->tagAssignDB, 's2', $qb->expr()->eq('s1.id', 's2.tag_id'))
            ->where($qb->expr()->eq('s1.user_id', $qb->createNamedParameter($userId)))
            ->orderBy('s1.name', 'DESC')
            ->groupBy('s1.id');

        return $this->findEntities($qb);
    }

    public function export(string $userId)
    {
        $qb = $this->db->getQueryBuilder();

        $qb->select('id')
            ->addSelect('name')
            ->addSelect('color')
            ->addSelect('favorite')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));

        return $this->findEntities($qb);
    }

    public function findByName(string $name, string $userId)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('id')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('name', $qb->createNamedParameter($name)))
            ->andWhere($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));

        return $this->findEntity($qb);
    }
}
