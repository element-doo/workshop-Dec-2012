<?php
namespace NGS\ModelBundle\Listener;

use NGS\Client\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResponseListener
{
    private $container;

    public function __construct($serviceContainer)
    {
        $this->container = $serviceContainer;
    }

    public function acceptsJson(Request $request)
    {
        return strpos($request->headers->get('accept'), 'application/json')===0;
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $controllerResult = $event->getControllerResult();
        $request = $event->getRequest();

        if($request->isXmlHttpRequest() && $this->acceptsJson($request)) {
            if($controllerResult instanceof Response) {
                $responseData = $controllerResult->getContent();
            }
            else {
                $responseData = self::toJsonArray($controllerResult);
            }
            $responseContent = json_encode(array(
                'data'     => $responseData,
                'messages' => $this->container->get('messenger')->readMessages()
            ), true);
            $response = new Response($responseContent);
            $response->headers->set('Content-Type', 'application/json');
            $event->setResponse($response);
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        // skip json encode if response is already json
        if(strpos($response->headers->get('Content-Type'), 'application/json') === 0) {
            return ;
        }


        if($response->isRedirect() && $event->getRequest()->isXmlHttpRequest()) {
            $event->setResponse(new JsonResponse(array(
                'redirect' => $response->headers->get('Location')
            )));
        }
        else if($this->acceptsJson($request)) {
            $responseContent = json_encode(array(
                'data'     => $response->getContent(),
                'messages' => $this->container->get('messenger')->readMessages()
            ), true);
            $event->setResponse(new Response($responseContent));
        }
    }

    // @todo use NGS\ObjectConverter (doesn't handle deep arrays for now)
    private static function toJsonArray($data)
    {
        if(!is_array($data))
            return $data;

        $results = array();
        foreach($data as $key=>$val) {
            if(is_object($val) && method_exists($val, 'toArray'))
                $results[$key] = $val->toArray();
            else
                $results[$key] = self::toJsonArray($val);
        }
        return $results;
    }
}