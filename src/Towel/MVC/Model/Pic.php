<?php

namespace Towel\MVC\Model;

class Pic extends BaseModel
{
    public $table = 'pic';

    public function findObjectPics($object_type, $object_id)
    {
        $query = $this->db()->createQueryBuilder();
        return $query->select('*')
            ->from($this->table, 'p')
            ->where('p.object_type = ?')
            ->andWhere(('p.object_id = ?'))
            ->setParameter(0, $object_type)
            ->setParameter(1, $object_id)
            ->execute();
    }
}