<?php

namespace App\Presenters;

use Nette\Application\UI\Form;

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
        return $control;
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
        throw new \Nette\NotImplementedException();
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
