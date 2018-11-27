<?php

namespace sf\base;

/**
 * Controller is the base class for classes containing controller logic.
 *
 * @author Harry Sun <sunguangjun@126.com>
 */
class Controller
{
    /**
     * @var string the ID of this controller.
     */
    public $id;
    /**
     * @var Action the action that is currently being executed.
     */
    public $action;
}
