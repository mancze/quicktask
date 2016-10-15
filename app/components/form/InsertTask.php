<?php

namespace App\Components\Form;

use App\Model\Entity\Task;
use App\Model\Entity\TaskGroup;
use App\Model\Repository\TaskRepository;
use App\Model\Repository\TaskGroupRepository;
use App\Model\Repository\TaskCategoryRepository;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

class InsertTask extends Control
{
    
    /** @var TaskRepository */
    public $taskRepository;
    /** @var TaskGroupRepository */
    public $taskGroupRepository;
    /** @var TaskCategoryRepository */
    public $taskCategoryRepository;
    /** @var number */
    public $idTaskGroup;
    /** @var callable[] function(Control $sender, Task $task) */
    public $onTaskAdded;

    /**
     * @param TaskRepository $taskRepository
     * @param TaskGroupRepository $taskGroupRepository
     * @param TaskCategoryRepository $taskCategoryRepository
     */
    public function __construct(TaskRepository $taskRepository, TaskGroupRepository $taskGroupRepository, TaskCategoryRepository $taskCategoryRepository)
    {
        parent::__construct();
        $this->taskRepository = $taskRepository;
        $this->taskGroupRepository = $taskGroupRepository;
        $this->taskCategoryRepository = $taskCategoryRepository;
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
        
        $categories = array(null => "");
        foreach ($this->taskCategoryRepository->getAll() as $category) {
            $categories[$category->getId()] = $category->getName();
        }
        
        $form->addSelect('category', 'Category', $categories);
        
        $form->addText('date', 'Date')
            ->setRequired('Please fill task date');
        
        $form->addSubmit('submit', 'Add');
        $form->onSuccess[] = function($f, $v) {
            $this->onSuccess($f, $v);
        };
        $form->onSubmit[] = function() {
            $this->onSubmit();
        };
        return $form;
    }

    /**
     * @param Form $form
     * @param $values
     */
    public function onSuccess(Form $form, $values)
    {
        $taskEntity = new Task();
        
        $taskEntity->setName($values->name);
        $taskEntity->setDate($values->date);
        
        if ($values->category) {
            $taskCategory = $this->taskCategoryRepository->getById($values->category);
            $taskEntity->setTaskCategory($taskCategory);
        }
        
        $taskGroup = $this->taskGroupRepository->getById($this->idTaskGroup);
        $taskEntity->setTaskGroup($taskGroup);
        
        // persist
        $this->taskRepository->insert($taskEntity);

        // clear the form
        $form->setValues(array(), true);

        // raise event
        $this->onTaskAdded($this, $taskEntity);
    }

    protected function onSubmit()
    {
        // invalidate
        $this->redrawControl();
    }
}
