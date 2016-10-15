<?php

namespace App\Presenters;

use Nette;

/**
 * Error presenter.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

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
