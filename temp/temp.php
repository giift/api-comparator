<?php

foreach($routes as $route)
{
    // $resource = $this->parsed->getResourceByPath($route['path']);

    // $config['endpoint'] = $resource->getUri();
    // if(is_null($config['endpoint']))
    // {
    //     $this->missing_fields[$config['endpoint']][] = 'endpoint';
    // }

    // $config['params'] = null;
    // $config['query_params'] = array();

    // Get the method type, query parameters, body paramaters and content type for each method
    // foreach($resource->getMethods() as $method)
    // {
        // if(!empty($method->getQueryParameters()))
        // {
        //     foreach ($method->getQueryParameters() as $param)
        //     {
        //         $config['query_params'][$param->getKey()] = $param->getExample();
        //         if(is_null($config['query_params']))
        //         {
        //             $this->missing_fields[$config['endpoint']] = 'query params: '.$param->getKey();
        //         }
        //     }
        //     $config['query_params'] = http_build_query($config['query_params']);
        // }

        // $config['method'] = $method->getType();
        // if(is_null($config['method']))
        // {
        //     $this->missing_fields[$config['endpoint']][] = 'method';
        // }

        // $config['content_types'] = $method->getResponse(200)->getTypes();
        // if(is_null($config['content_types']))
        // {
        //     $this->missing_fields[$config['endpoint']][] = 'content_types';
        // }

        // Get body parameters
        // if(!empty($method->getBodies()))
        // {
            // foreach($method->getBodies() as $body)
            // {
            //     if($body->getMediaType() === 'application/json')
            //     {
            //         $config['params'] = json_decode($body->getExample(), true);
            //         // json_decode adds 'id'=>filepath to the array
            //         unset($config['params']['id']);
            //         if(is_null($config['params']))
            //         {
            //             $this->missing_fields[$config['endpoint']] = 'params';
            //         }
            //     }
                // application/x-www-form-urlencoded and multipart/form-data
                // else
                // {
                    // if(!empty($body->getParameters()))
                    // {
                    //     foreach($body->getParameters() as $params)
                    //     {
                    //         if(!empty($params->getEnum()))
                    //         {
                    //             $enums = $params->getEnum();
                    //             $config['params'][$params->getKey()] = $enums[0];
                    //         }
                    //         elseif(!is_null($params->getExample()))
                    //         {
                    //             $config['params'][$params->getKey()] = $params->getExample();
                    //         }
                    //         else
                    //         {
                    //             $this->missing_fields[$config['endpoint']] = 'params: '.$params->getKey();
                    //         }
                    //     }
                    // }
                    // else
                    // {
                    //     $this->missing_fields[$config['endpoint']] = 'params';
                    // }
                // }

                // $this->add_methods(
                //     $resource,
                //     $config['endpoint'],
                //     $config['method'],
                //     $config['content_types'],
                //     $config['params'],
                //     $config['query_params']
                // );
        //     }
        // }
        // else
        // {
        //     $this->add_methods(
        //         $resource,
        //         $config['endpoint'],
        //         $config['method'],
        //         $config['content_types'],
        //         $config['params'],
        //         $config['query_params']
        //     );
        // }
    // }
}