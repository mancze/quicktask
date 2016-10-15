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
    /** @var string */
    protected $query;
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
     * @param string $query Optional search query
     */
    public function actionTaskGroup($idTaskGroup, $query = null)
    {
        $this->idTaskGroup = $idTaskGroup;
        $this->query = $query;
        $this->loadTasks();
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
        $this->query = null;
        $this->flashMessage('Task was created.', 'success');
        
        if ($this->isAjax()) {
            // reload the tasks
            $this->loadTasks();
            $this->redrawControl("tasks");
            $this->redrawControl("search");
        }
        else {
            $this->redirect("this");
        }
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
     * @return void
     */
    protected function loadTasks()
    {
        $orderBy = array("date" => "DESC");
        $this->tasks = $this->query ? 
            $this->taskRepository->getByTaskGroupAndName($this->idTaskGroup, $this->query, $orderBy) :
            $this->taskRepository->getByTaskGroup($this->idTaskGroup, $orderBy);
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

        $this->redrawControl("tasks");
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
    
    protected function createComponentSearchTask()
    {
        $form = new Form();
        $form->addText("query", "Search")
            ->setDefaultValue($this->query);
        
        $form->addSubmit("doSubmit", "Search");
        $form->addSubmit("doClear", "Clear");
        $form->onSuccess[] = function($f, $v) { $this->processSearch($f, $v); };
        
        return $form;
    }
    
    
    protected function processSearch(Form $form, $values) {
        $query = $values->query;
        
        if ($form["doClear"]->isSubmittedBy()) {
            $form->setValues(array(), true);
            $query = null;
        }
        
        if ($this->isAjax()) {
            $this->query = $query;
            $this->loadTasks();
            $this->redrawControl("search");
            $this->redrawControl("tasks");
        }
        else {
            $this->redirect("this", array("query" => $query));
        }
    }
}
