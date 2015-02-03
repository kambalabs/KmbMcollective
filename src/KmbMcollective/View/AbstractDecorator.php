<?php
namespace KmbMcollective\View;

use Zend\View\HelperPluginManager;

abstract class AbstractDecorator
{
    /** @var HelperPluginManager */
    protected $viewHelperManager;

    /**
     * @param $object
     * @param $context
     * @return string
     */
    abstract public function decorate($object, $context = null);

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
