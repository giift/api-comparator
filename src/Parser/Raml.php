<?php

namespace Giift\Compare\Parser;

class Raml
{
    protected $parsed = null;
    protected $config_object = null;
    protected $config = array();
    protected $missing_fields = array();

    /**
     * Parses the raml file
     * @param string $file_path
     */
    public function __construct($file_path)
    {
        $parser = new \Raml\Parser();
        $this->parsed = $parser->parse($file_path);

        $this->config_object = new \Giift\Compare\Config();
        $this->config = $this->config_object->get_config();
    }

    /**
     * Returns parsed raml file
     * @return \Raml\ApiDefinition
     */
    public function get_parsed()
    {
        return $this->parsed;
    }

    /**
     * Returns the config
     * @return array
     *
     * <pre>
     * $array = array(
     *     'methods'=>array(
     *         array(
     *             // string Method uri
     *             'endpoint'=>'',
     *             // string Type of method
     *             'method'=>'',
     *             // array  Parameters required for method
     *             'params'=>array(
     *                 // string
     *                 'key'=>'value'
     *             ),
     *             // array Types of response
     *             'content_types'=>array(
     *                 'types'
     *             )
     *         )
     *     )
     * );
     * </pre>
     *
     */
    public function get_config()
    {
        return $this->config;
    }

    /**
     * Returns the missing fields in all methods
     * @return array
     *
     * <pre>
     * $array = array(
     *     'method_uri'=>array(
     *             'field'
     *         )
     *     )
     * );
     * </pre>
     *
     */
    public function get_missing_fields()
    {
        return $this->missing_fields;
    }

    /**
     * Returns missing fields in a method
     * @param string $method_uri
     * @return array
     *
     * <pre>
     * $array = array(
     *     'method_uri'=>array(
     *         'field'
     *     )
     * );
     * </pre>
     *
     */
    public function get_missing_field($method_uri)
    {
        if(isset($this->missing_fields[$method_uri]))
        {
            return $this->missing_fields[$method_uri];
        }
    }

    /**
     * Generates the config from parsed raml file
     * @return boolean
     */
    public function create_config()
    {
        $routes = $this->parsed->getResourcesAsUri()->getRoutes();
        foreach($routes as $route)
        {
            $resource = $this->parsed->getResourceByPath($route['path']);

            $config['endpoint'] = $resource->getUri();
            if(is_null($config['endpoint']))
            {
                $this->missing_fields[$config['endpoint']][] = 'endpoint';
            }

            $config['params'] = null;
            $config['query_params'] = null;

            // Get the method type, query parameters, body paramaters and content type for each method
            foreach($resource->getMethods() as $method)
            {
                if(!empty($method->getQueryParameters()))
                {
                    foreach ($method->getQueryParameters() as $param)
                    {
                        $config['query_params'][$param->getKey()] = $param->getExample();
                    }
                    $config['query_params'] = http_build_query($config['query_params']);
                }

                $config['method'] = $method->getType();
                if(is_null($config['method']))
                {
                    $this->missing_fields[$config['endpoint']][] = 'method';
                }

                $config['content_types'] = $method->getResponse(200)->getTypes();
                if(is_null($config['content_types']))
                {
                    $this->missing_fields[$config['endpoint']][] = 'content_types';
                }

                // Get body parameters
                if(!empty($method->getBodies()))
                {
                    foreach($method->getBodies() as $body)
                    {
                        if($body->getMediaType() === 'application/json')
                        {
                            $config['params'] = json_decode($body->getExample(), true);
                            // json_decode adds 'id'=>filepath to the array
                            unset($config['params']['id']);
                            if(is_null($config['params']))
                            {
                                $this->missing_fields[$config['endpoint']] = 'params: example';
                            }

                            $this->add_methods(
                                $resource,
                                $config['endpoint'],
                                $config['method'],
                                $config['content_types'],
                                $config['params'],
                                $config['query_params']
                            );
                        }
                        // application/x-www-form-urlencoded and multipart/form-data
                        else
                        {
                            if(!empty($body->getParameters()))
                            {
                                foreach($body->getParameters() as $params)
                                {
                                    if(!empty($params->getEnum()))
                                    {
                                        $config['params'][$params->getKey()] = $params->getEnum()[0];
                                    }
                                    elseif(!is_null($params->getExample()))
                                    {
                                        $config['params'][$params->getKey()] = $params->getExample();
                                    }
                                    else
                                    {
                                        $this->missing_fields[$config['endpoint']] = 'params: enum/example';
                                    }
                                }
                            }
                            else
                            {
                                $this->missing_fields[$config['endpoint']] = 'body params';
                            }

                            $this->add_methods(
                                $resource,
                                $config['endpoint'],
                                $config['method'],
                                $config['content_types'],
                                $config['params'],
                                $config['query_params']
                            );
                        }
                    }
                }
                else
                {
                    $this->add_methods(
                        $resource,
                        $config['endpoint'],
                        $config['method'],
                        $config['content_types'],
                        $config['params'],
                        $config['query_params']
                    );
                }
            }
        }
        $this->config = $this->config_object->get_config();

        return empty($this->missing_fields);
    }

    /**
     * Add method for each uri parameter enum/example
     * @param \Raml\Resource $resource
     * @param string $endpoint
     * @param string $method
     * @param array $content_types
     * @param array $config_params
     * @param string $query_params
     *
     * $content_types = array(
     *     'types'
     * );
     *
     * $config_params = array(
     *     'key'=>'value'
     * );
     *
     */
    protected function add_methods($resource, $endpoint, $method, $content_types, array $config_params=null, $query_params=null)
    {
        // Get uri parameters
        if(!empty($resource->getUriParameters()))
        {
            foreach($resource->getUriParameters() as $params)
            {
                // Add method for each enum
                if(!empty($params->getEnum()))
                {
                    foreach ($params->getEnum() as $key => $value)
                    {
                        // Set endpoint
                        $pos = strpos($endpoint, '{');
                        $new_endpoint = substr($endpoint, 0, $pos).$value;

                        // Add query params
                        if(!is_null($query_params))
                        {
                            $new_endpoint .= '?'.$query_params;
                        }

                        $this->config_object->add_method(
                            $new_endpoint,
                            $method,
                            $content_types,
                            $config_params
                        );
                    }
                }
                // Add method for example
                elseif(!is_null($params->getExample()))
                {
                    // Set endpoint
                    $pos = strpos($endpoint, '{');
                    $endpoint = substr($endpoint, 0, $pos).$params->getExample();

                    // Add query params
                    if(!is_null($query_params))
                    {
                        $endpoint .= '?'.$query_params;
                    }

                    $this->config_object->add_method(
                        $endpoint,
                        $method,
                        $content_types,
                        $config_params
                    );
                }
            }
        }
        else
        {
            $this->config_object->add_method(
                $endpoint,
                $method,
                $content_types,
                $config_params
            );
        }
    }
}
