<?php
namespace App\Components\Form;

use App\Model\Entity\Task;
use App\Model\Entity\TaskGroup;
use App\Model\Repository\TaskRepository;
use App\Model\Repository\TaskGroupRepository;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

class InsertTask extends Control
{
    /** @var TaskRepository*/
    public $taskRepository;
    /** @var TaskGroupRepository*/
    public $taskGroupRepository;
    /** @var number */
    public $idTaskGroup;
    /** @var callable[] function(Control $sender, Task $task) */
    public $onTaskAdded;

    /**
     * @param TaskRepository $taskRepository
     * @param TaskGroupRepository $taskGroupRepository
     */
    public function __construct(TaskRepository $taskRepository, TaskGroupRepository $taskGroupRepository)
    {
        parent::__construct();
        $this->taskRepository = $taskRepository;
        $this->taskGroupRepository = $taskGroupRepository;
    }

    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/templates/InsertTask.latte');
        $template->render();
    }

    /**
     * @param int $idTaskGroup
     */
    public function setTaskGroupId($idTaskGroup)
    {
        $this->idTaskGroup = $idTaskGroup;
    }

    /**
     * @return Form
     */
    protected function createComponentInsertTaskForm()
    {
        $form = new Form();
        $form->addText('name', 'Name')
            ->setRequired('Please fill task name');
        $form->addText('date', 'Date')
            ->setRequired('Please fill task date');
        $form->addHidden('idTaskGroup', $this->idTaskGroup);
        $form->addSubmit('submit', 'Add');
        $form->onSuccess[] = array($this, 'insertTaskFromSuccess');
        return $form;
    }

    /**
     * @param Form $form
     * @param $values
     */
    public function insertTaskFromSuccess(Form $form, $values)
    {
        $taskGroup = $this->taskGroupRepository->getById($values->idTaskGroup);

        $taskEntity = new Task();
        $taskEntity->setName($values->name);
        $taskEntity->setDate($values->date);
        $taskEntity->setTaskGroup($taskGroup);
        $this->taskRepository->insert($taskEntity);
        
        $this->onTaskAdded($this, $taskEntity);
    }
}
