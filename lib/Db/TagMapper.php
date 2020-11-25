<?php
namespace OCA\ContactDescription\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\QBMapper;

class TagMapper extends QBMapper {

    public function __construct(IDbConnection $db) {
        parent::__construct($db, 'contact_desc_tag_list', Tag::class);
        $this->tagAssignDB='contact_desc_tag';
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

    public function findList(string $userId) {

        $qb = $this->db->getQueryBuilder();

        $qb->select('s1.*')
           ->selectAlias($qb->createFunction('COUNT(s2.id)'), 'count')
           ->from($this->getTableName(), 's1')
           ->leftJoin('s1', $this->tagAssignDB, 's2', $qb->expr()->eq('s1.id', 's2.tag_id'))
           //->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
           //->orderBy('s2.name', 'ASC');
           ->groupBy('s1.id')
           ;

        return $this->findEntities($qb);
    }
}