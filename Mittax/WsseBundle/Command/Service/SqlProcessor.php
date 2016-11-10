<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 01.11.16
 * Time: 17:43
 */

namespace Mittax\WsseBundle\Command\Service;


use Doctrine\ORM\EntityManager;

class SqlProcessor
{
    /**
     * @var EntityManager
     */
    private $_entityManager;

    /**
     * SqlProcessor constructor.
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;
    }

    /**
     * @param string $query
     * @return array
     * @throws InvalidArgumentException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executeQuery(string $query) : array
    {
        $db = $this->_entityManager->getConnection();

        $stmt = $db->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll();
    }
}