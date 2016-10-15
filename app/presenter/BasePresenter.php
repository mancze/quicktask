<?php

namespace App\Presenters;

use Nette;

/**
 * Error presenter.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    
    protected function startup()
    {
        parent::startup();
        $this->redrawControl("flashMessages");
    }

    /**
     * Saves the message to template, that can be displayed after redirect.
     * Automatically invalidates the flash messages snippet.
     * @param  string
     * @param  string
     * @return \stdClass
     */
    public function flashMessage($message, $type = 'info')
    {
        parent::flashMessage($message, $type);
        $this->redrawControl("flashMessages");
    }

    /**
     * Redirect to another presenter, action or signal unless the request is ajax.
     * @param  int      [optional] HTTP error code
     * @param  string   destination in format "[//] [[[module:]presenter:]action | signal! | this] [#fragment]"
     * @param  array|mixed
     * @return void
     * @throws Nette\Application\AbortException
     */
    public function redirectIfNotAjax($code, $destination = NULL, $args = array())
    {
        if ($this->isAjax()) {
            return;
        }

        return call_user_func_array(array($this, "redirect"), func_get_args());
    }
}
