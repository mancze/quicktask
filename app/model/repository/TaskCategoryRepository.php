<?php
namespace App\Model\Repository;

use App\Model\Entity;
use Kdyby\Doctrine\EntityManager;

class TaskCategoryRepository extends AbstractRepository
{
    /** @var \Kdyby\Doctrine\EntityRepository */
    private $taskCategory;

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);
        $this->taskCategory = $this->entityManager->getRepository(Entity\TaskCategory::getClassName());
    }

    /**
     * @param number $id
     * @return Entity\TaskCategory|null
     */
    public function getById($id)
    {
        return $this->taskCategory->find($id);
    }

    /**
     * @return Entity\TaskCategory[]
     */
    public function getAll()
    {
        return $this->taskCategory->findBy(array());
    }

    /**
     * @param Entity\TaskCategory $taskCategory
     */
    public function insert(Entity\TaskCategory $taskCategory)
    {
        $this->entityManager->persist($taskCategory);
        $this->entityManager->flush();
    }
}
