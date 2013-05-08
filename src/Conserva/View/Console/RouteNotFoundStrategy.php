<?php

namespace Conserva\View\Console;

use Zend\Mvc\View\Console\RouteNotFoundStrategy as DefaultRouteNotFoundStrategy;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Application;
use Zend\View\Model\ConsoleModel;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\Mvc\Exception\RuntimeException;
use Zend\Console\Adapter\AdapterInterface as ConsoleAdapter;
use Zend\Console\ColorInterface as Color;

class RouteNotFoundStrategy extends DefaultRouteNotFoundStrategy implements ListenerAggregateInterface {
    
    /**
     * A template for message to show in console when an exception has occurred.
     * @var string|callable
     */
    protected $message = <<<EOT
======================================================================
   The conserva has thrown an exception!
======================================================================
 :banner
 :usage
 :report
----------------------------------------------------------------------
:stack

EOT;
    
    /**
     * Get current template for message that will be shown in Console.
     *
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Set template for message that will be shown in Console.
     * The message can be a string (template) or a callable (i.e. a closure).
     *
     * The closure is expected to return a string and will be called with 2 parameters:
     *        Exception $exception           - the exception being thrown
     *        boolean   $displayExceptions   - whether to display exceptions or not
     *
     * If the message is a string, one can use the following template params:
     *
     *   :className   - full class name of exception instance
     *   :message     - exception message
     *   :code        - exception code
     *   :file        - the file where the exception has been thrown
     *   :line        - the line where the exception has been thrown
     *   :stack       - full exception stack
     *
     * @param string|callable  $message
     * @return ExceptionStrategy
     */
    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }
    
    /**
     * Attach the aggregate to the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events) {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'handleRouteNotFoundError'), -1);
    }
    
    /**
     * Detect if an error is a route not found condition
     *
     * If a "controller not found" or "invalid controller" error type is
     * encountered, sets the response status code to 404.
     *
     * @param  MvcEvent $e
     * @throws RuntimeException
     * @throws ServiceNotFoundException
     * @return void
     */
    public function handleRouteNotFoundError(MvcEvent $e) {
        $error = $e->getError();
        if (empty($error)) {
            return;
        }

        $response = $e->getResponse();
        $request  = $e->getRequest();

        switch ($error) {
            case Application::ERROR_CONTROLLER_NOT_FOUND:
            case Application::ERROR_CONTROLLER_INVALID:
            case Application::ERROR_ROUTER_NO_MATCH:
                $this->reason = $error;
                if (!$response) {
                    $response = new ConsoleResponse();
                    $e->setResponse($response);
                }
                $response->setMetadata('error', $error);
                break;
            default:
                return;
        }

        $result = $e->getResult();
        if ($result instanceof Response) {
            // Already have a response as the result
            return;
        }

        // Prepare Console View Model
        $model = new ConsoleModel();
        $model->setErrorLevel(1);

        // Fetch service manager
        $sm = $e->getApplication()->getServiceManager();
        
        // Try to fetch module manager
        $mm = null;
        try{
            $mm = $sm->get('ModuleManager');
        } catch (ServiceNotFoundException $e) {
            // The application does not have or use module manager, so we cannot use it
        }

        // Try to fetch current console adapter
        try{
            $console = $sm->get('console');
            if (!$console instanceof ConsoleAdapter) {
                throw new ServiceNotFoundException();
            }
        } catch (ServiceNotFoundException $e) {
            // The application does not have console adapter
            throw new RuntimeException('Cannot access Console adapter - is it defined in ServiceManager?');
        }

        // Try to fetch router
        $router = null;
        try{
            $router = $sm->get('Router');
        } catch (ServiceNotFoundException $e) {
            // The application does not have a router
        }

        // Retrieve the script's name (entry point)
        $scriptName = '';
        if ($request instanceof ConsoleRequest) {
            $scriptName = basename($request->getScriptName());
        }

        // Get application banner
        $banner = $this->getConsoleBanner($console, $mm);

        // Get application usage information
        $usage = $this->getConsoleUsage($console, $scriptName, $mm, $router);
        
        $reason    = (isset($this->reason) && !empty($this->reason)) ? $this->reason : 'unknown';
        $reasons   = array(
            Application::ERROR_CONTROLLER_NOT_FOUND => 'Could not match to a controller',
            Application::ERROR_CONTROLLER_INVALID   => 'Invalid controller specified',
            Application::ERROR_ROUTER_NO_MATCH      => 'Invalid arguments or no arguments provided',
            'unknown'                               => 'Unknown',
        );
        $report = sprintf("Reason for failure: %s", $reasons[$reason]);

        $exceptions = '';
        
        $exception = $e->getParam('exception', false);
        if ($exception && $this->reason) {
            while ($exception instanceof \Exception) {
                $exceptions .= sprintf("Exception: %s\nTrace:\n%s\n", $exception->getMessage(), $exception->getTraceAsString());
                $exception   = $exception->getPrevious();
            }
        }
        
        $result = str_replace(
            array(
                ':banner',
                ':usage',
                ':report',
                ':stack',
            ),array(
                $banner ? rtrim($banner)        : '',
                $usage  ? trim($usage) : '',
                $console->colorize($report, Color::RED),
                $exceptions,
            ),
            $this->message
        );
        
        // Inject the text into view
        //$result  = $banner ? rtrim($banner, "\r\n")        : '';
        //$result .= $usage  ? "\n\n" . trim($usage, "\r\n") : '';
        //$result .= "\n"; // to ensure we output a final newline
        //$result .= $this->reportNotFoundReason($e);
        $model->setResult($result);

        // Inject the result into MvcEvent
        $e->setResult($model);
    }
    
}