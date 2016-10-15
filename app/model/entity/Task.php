<?php
namespace App\Model\Entity;

use Kdyby\Doctrine\Entities\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Task extends BaseEntity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="TaskCategory")
     * @ORM\JoinColumn(name="task_category_id", referencedColumnName="id", nullable=true)
     */
    protected $taskCategory;

    /**
     * @ORM\ManyToOne(targetEntity="TaskGroup")
     * @ORM\JoinColumn(name="task_group_id", referencedColumnName="id", nullable=false)
     */
    protected $taskGroup;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="date", nullable=false)
     */
    protected $date;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $completed = false;

    /**
     * @return number
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return TaskCategory|null
     */
    public function getTaskCategory()
    {
        return $this->taskCategory;
    }

    /**
     * @param TaskCategory|null $taskCategory
     */
    public function setTaskCategory($taskCategory)
    {
        $this->taskCategory = $taskCategory;
    }

    /**
     * @return TaskGroup
     */
    public function getTaskGroup()
    {
        return $this->taskGroup;
    }

    /**
     * @param TaskGroup $taskGroup
     */
    public function setTaskGroup($taskGroup)
    {
        $this->taskGroup = $taskGroup;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate($date)
    {
        if (!$date instanceof \DateTime) {
            $date = new \DateTime($date);
        }
        $this->date = $date;
    }

    /**
     * @return bool
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * @param bool $completed
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;
    }
}
