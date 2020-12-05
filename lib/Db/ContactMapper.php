<?php

namespace OCA\People\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\QBMapper;

class ContactMapper extends QBMapper
{
    public function __construct(IDbConnection $db)
    {
        parent::__construct($db, 'people', Contact::class);
    }

    public function find(int $id, string $userId)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('id')
            ->addSelect('name')
            ->addSelect('last_name')
            ->addSelect('work')
            ->addSelect('hobbies')
            ->addSelect('birth')
            ->addSelect('birth_notif')
            ->addSelect('address')
            ->addSelect('description')
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
            ->addSelect('last_name')
            ->addSelect('updated')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
            ->orderBy('updated', 'ASC');

        return $this->findEntities($qb);
    }

    public function export(string $userId)
    {
        $qb = $this->db->getQueryBuilder();

        $qb->select('id')
            ->addSelect('name')
            ->addSelect('last_name')
            ->addSelect('work')
            ->addSelect('hobbies')
            ->addSelect('birth')
            ->addSelect('birth_notif')
            ->addSelect('address')
            ->addSelect('description')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
            ->orderBy('updated', 'ASC');

        return $this->findEntities($qb);
    }

    public function findByName(string $name, string $lastName, string $userId)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('id')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('name', $qb->createNamedParameter($name)))
            ->andWhere($qb->expr()->eq('last_name', $qb->createNamedParameter($lastName)))
            ->andWhere($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));

        return $this->findEntity($qb);
    }
}
