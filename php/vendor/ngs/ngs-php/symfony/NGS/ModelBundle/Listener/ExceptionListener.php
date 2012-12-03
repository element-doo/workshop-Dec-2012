<?php
namespace NGS\ModelBundle\Listener;

use NGS\Client\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionListener
{
    private $container;

    public function __construct($serviceContainer)
    {
        $this->container = $serviceContainer;
    }

    /**
     * Global unhandled exception handler
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $templating = $this->container->get('templating');
        $messenger = $this->container->get('messenger');

        $messenger->error($exception->getMessage());

        $request = $event->getRequest();

        // change response based on the caught exception
        if($request->isXmlHttpRequest()) {
            $response = new JsonResponse(array(
                'messages' => $messenger->readMessages()));
            $event->setResponse($response);
        }
        else if($exception instanceof NotFoundException) {
            //TODO Billevo!?
            $content = $templating->render('BillevoAdminBundle:Exception:notFound.html.twig');
        }
        else if($exception instanceof NotFoundHttpException) {
            //TODO Billevo!?
            $content = $templating->render('BillevoAdminBundle:Exception:routeNotFound.html.twig');
        }

        if(isset($content))
            $event->setResponse(new Response($content));
    }
}
