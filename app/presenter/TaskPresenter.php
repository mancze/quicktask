<?php

namespace App\Presenters;

use App\Model\Entity\Task;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\Arrays;

/**
 * Class TaskPresenter
 * @package App\Presenters
 */
class TaskPresenter extends BasePresenter
{
    /** @var \App\Model\Repository\TaskGroupRepository @inject */
    public $taskGroupRepository;
    /** @var \App\Model\Repository\TaskRepository @inject */
    public $taskRepository;
    /** @var \App\Factories\Modal\IInsertTaskGroupFactory @inject */
    public $insertTaskGroupFactory;
    /** @var \App\Factories\Form\IInsertTaskFactory @inject */
    public $insertTaskFactory;
    /** @var number */
    protected $idTaskGroup;
    /** @var Task[] */
    protected $tasks;

    public function renderDefault()
    {
        $this->template->taskGroups = $this->getTaskGroups();
    }

    /**
     * @param int $id
     */
    public function handleDeleteTaskGroup($id)
    {
        $this->taskGroupRepository->delete($id);
        if ($this->isAjax()) {
            $this->redrawControl('taskGroups');
        } else {
            $this->redirect('this');
        }
    }

    /**
     * @param number $idTaskGroup
     */
    public function actionTaskGroup($idTaskGroup)
    {
        $this->loadTasks($idTaskGroup);
    }

    /**
     * @return \App\Components\Modal\InsertTaskGroup
     */
    protected function createComponentInsertTaskGroupModal()
    {
        $control = $this->insertTaskGroupFactory->create();
        return $control;
    }

    /**
     * @return \App\Components\Form\InsertTask
     */
    protected function createComponentInsertTaskForm()
    {
        $control = $this->insertTaskFactory->create();
        
        $control->setTaskGroupId($this->idTaskGroup);
        $control->onTaskAdded[] = function($s, $t) { $this->onTaskAdded($s, $t); };
        
        return $control;
    }
    
    protected function onTaskAdded(Control $s, Task $t) {
        $this->flashMessage('Task was created.', 'success');
        $this->redirect("this");
    }

    /**
     * @return array
     */
    protected function getTaskGroups()
    {
        $result = array();
        $taskGroups = $this->taskGroupRepository->getAll();
        foreach ($taskGroups as $taskGroup) {
            $item = array();
            $item['id'] = $taskGroup->getId();
            $item['name'] = $taskGroup->getName();
            $result[] = $item;
        }
        return $result;
    }

    /**
     * Loads the list of tasks for given group.
     * @param number $idTaskGroup
     * @return void
     */
    protected function loadTasks($idTaskGroup)
    {
        $this->idTaskGroup = $idTaskGroup;
        $this->tasks = $this->taskRepository->getByTaskGroup($idTaskGroup, array("date" => "DESC"));
    }

    protected function processTaskList(Form $form)
    {
        $values = $form->getValues(true);
        
        foreach ($this->tasks as $task) {
            $taskValues = Arrays::get($values["tasks"], $task->getId(), null);
            if (!$taskValues) {
                continue;
            }
            
            $task->setCompleted($taskValues["completed"]);
            $this->taskRepository->updateEntity($task);
        }
        
        $this->redrawControl($form->getName());
        $this->redirectIfNotAjax("this");
    }

    protected function createComponentTaskList()
    {
        $form = new Form();

        $tasks = $this->tasks;
        $tasksContainer = $form->addContainer("tasks");

        foreach ($tasks as $task) {
            $taskContainer = $tasksContainer->addContainer($task->getId());
            $taskContainer->addCheckbox("completed", $task->getName())
                    ->setDefaultValue($task->getCompleted())
                    ->setOption("task", $task);
        }

        $form->addSubmit("doSubmit", "Save");
        $form->onSuccess[] = function($f) { $this->processTaskList($f); };

        return $form;
    }
}
