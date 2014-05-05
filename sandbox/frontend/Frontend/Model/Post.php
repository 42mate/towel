<?php

namespace Frontend\Model;

use \Towel\MVC\Model\BaseModel;

class Post extends BaseModel
{
    public $table = 'post';

    const STATUS_PUBLISHED = 'p';
    const STATUS_NOT_PUBLISHED = 'n';
    const PAGE_SIZE = 25;
    const TYPE_SELLING = 'selling';
    const TYPE_SEARCHING = 'searching';

    public function findAllSales($page = 0, $location = '') {
        $query = $this->db()->createQueryBuilder();

        $query->select("p.*, u.username")
            ->from($this->table, 'p')
            ->where('p.status = ?')
            ->setParameter(0, self::STATUS_PUBLISHED)
            ->andWhere('p.post_type = ?')
            ->setParameter(1, self::TYPE_SELLING)
            ->innerJoin('p', 'app_user', 'u', ' p.user_id = u.id' );

        if (!empty($location)) {
            $query->andWhere('p.fb_location_id = :location')
                ->setParameter(2, $location);
        }

        $query->setFirstResult($page * self::PAGE_SIZE);
        $query->setMaxResults(self::PAGE_SIZE);
        $query->orderBy('id', 'DESC');

        return $query->execute();
    }

    public function findAllSearching($page = 0, $location = '') {
        $query = $this->db()->createQueryBuilder();

        $query->select("p.*, u.username")
            ->from($this->table, 'p')
            ->where('p.status = ?')
            ->setParameter(0, self::STATUS_PUBLISHED)
            ->andWhere('p.post_type = ?')
            ->setParameter(1, self::TYPE_SEARCHING)
            ->innerJoin('p', 'app_user', 'u', ' p.user_id = u.id' );

        if (!empty($location)) {
            $query->andWhere('p.fb_location_id = :location')
                ->setParameter(2, $location);
        }

        $query->setFirstResult($page * self::PAGE_SIZE);
        $query->setMaxResults(self::PAGE_SIZE);
        $query->orderBy('id', 'DESC');

        return $query->execute();
    }

}