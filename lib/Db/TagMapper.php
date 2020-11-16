<?php
namespace OCA\ContactDescription\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\QBMapper;

class TagMapper extends QBMapper {

    public function __construct(IDbConnection $db) {
        parent::__construct($db, 'contact_desc_tag_list', Tag::class);
    }

    public function find(int $id, string $userId) {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('id', $qb->createNamedParameter($id)))
            ->andWhere($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
            ;

        return $this->findEntity($qb);
    }

    public function findAll(string $userId) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
           ;

        return $this->findEntities($qb);
    }
}