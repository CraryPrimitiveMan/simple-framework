<?php
namespace sf\web;

/**
 * Controller is the base class for classes containing controller logic.
 * @author Harry Sun <sunguangjun@126.com>
 */
class Controller extends \sf\base\Controller
{
    /**
     * Renders a view
     * @param string $view the view name.
     * @param array $params the parameters (name-value pairs) that should be made available in the view.
     */
    public function render($view, $params = [])
    {
        extract($params);
        return require '../views/' . $view . '.php';
    }

    /**
     * Convert a array to json string
     * @param string $data
     */
    public function toJson($data)
    {
        if (is_string($data)) {
            return $data;
        }
        return json_encode($data);
    }
}