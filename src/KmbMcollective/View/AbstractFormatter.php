<?php
namespace KmbMcollective\View;

use Zend\Mvc\Controller\PluginManager;

abstract class AbstractFormatter
{
    /** @var PluginManager */
    protected $viewHelperManager;

    /**
     * @return string
     */

    /**
     * @param $object
     * @param $context
     * @return string
     */
    abstract public function format($object);

    /**
     * Get ViewHelperManager.
     *
     * @return HelperPluginManager
     */
    public function getViewHelperManager()
    {
        return $this->viewHelperManager;
    }

    /**
     * Set ViewHelperManager.
     *
     * @param HelperPluginManager $viewHelperManager
     * @return AbstractDecorator
     */
    public function setViewHelperManager($viewHelperManager)
    {
        $this->viewHelperManager = $viewHelperManager;
        return $this;
    }

    /**
     * @param       $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, array $arguments = array())
    {
        $plugin = $this->getViewHelperManager()->get($name);
        if (is_callable($plugin)) {
            return call_user_func_array($plugin, $arguments);
        }

        return $plugin;
    }
}
