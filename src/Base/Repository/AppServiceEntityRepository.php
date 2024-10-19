<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

abstract class AppServiceEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        $entityClass = str_replace('Repository', 'Entity', substr(static::class, 0, -10));

        parent::__construct($registry, $entityClass);
    }

    protected function fetchAllNumericSingle(string $sql, array $params=[]) : array
    {
        $result = $this->_em->getConnection()->fetchAllNumeric($sql, $params);
        $numeric = array_map(fn ($items) => end($items), $result);

        return $numeric;
    }

    protected function getKeyValueMultipleColumns(string $sql, array $params=[]) :array
    {
        $data = $this->_em->getConnection()->fetchAllAssociative($sql, $params);
        $result = [];

        if ($data) {
            foreach ($data as $item) {
                $firstValueWillBeKey = array_shift($item);
                $result[$firstValueWillBeKey] = $item;
            }
        }

        return $result;
    }
}
