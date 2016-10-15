<?php
namespace App\Model\Repository;

use App\Model\Entity;
use Kdyby\Doctrine\EntityManager;

class TaskRepository extends AbstractRepository
{
    /** @var \Kdyby\Doctrine\EntityRepository */
    private $task;

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);
        $this->task = $this->entityManager->getRepository(Entity\Task::getClassName());
    }

    /**
     * @param number $id
     * @return Entity\Task|null
     */
    public function getById($id)
    {
        return $this->task->find($id);
    }

    /**
     * @param number $idTaskGroup
     * @param array|null $orderBy Optional ORDER BY specifier.
     * @return Entity\Task[]
     */
    public function getByTaskGroup($idTaskGroup, array $orderBy = null)
    {
        return $this->task->findBy(array('taskGroup' => $idTaskGroup), $orderBy);
    }

    /**
     * @param number $idTaskGroup
     * @param array|null $orderBy Optional ORDER BY specifier.
     * @return Entity\Task[]
     */
    public function getByTaskGroupAndName($idTaskGroup, $name, array $orderBy = null)
    {
        $builder = $this->task->createQueryBuilder("t")
            ->where("t.taskGroup = :taskGroup")
            ->andWhere("LOWER(t.name) LIKE LOWER(:query)")
            ->autoJoinOrderBy($orderBy)
            ->setParameter("taskGroup", $idTaskGroup)
            ->setParameter("query", "%" . $name . "%");
        
        return $builder->getQuery()->getResult();
    }

    /**
     * @param Entity\Task $task
     */
    public function insert(Entity\Task $task)
    {
        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }
}
