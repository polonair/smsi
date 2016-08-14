<?php

namespace Polonairs\SmsiBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SmsiCollector extends DataCollector
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'method' => $request->getMethod(),
            'acceptable_content_types' => $request->getAcceptableContentTypes(),
        );
    }
    public function getMethod()
    {
        return $this->data['method'];
    }
    public function getAcceptableContentTypes()
    {
        return $this->data['acceptable_content_types'];
    }
    public function getName()
    {
        return 'polonairs.smsi.collector';
    }
}