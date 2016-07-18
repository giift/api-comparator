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
     *         'method_type'=>array(
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
     *     'method_type'=>array(
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

            // Get the method type, query parameters and content type for each method
            foreach($resource->getMethods() as $method)
            {
                if(!empty($method->getQueryParameters()))
                {
                    $config['params'][] = $method->getQueryParameters();
                    if(is_null($method->getQueryParameters()))
                    {
                        $this->missing_fields[$config['endpoint']][] = 'params';
                    }
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
            }

            // Set uri parameters
            if(!empty($resource->getUriParameters()))
            {
                foreach($resource->getUriParameters() as $params)
                {
                    // Add method for each enum
                    if(!empty($params->getEnum()))
                    {
                        $enums = $params->getEnum();
                        foreach ($enums as $key => $value)
                        {
                            $pos = strpos($config['endpoint'], '{');
                            $endpoint = substr($config['endpoint'], 0, $pos).$value;

                            $this->config_object->add_method(
                                $endpoint,
                                $config['method'],
                                $config['content_types'],
                                $config['params']
                            );
                        }
                    }
                    // Add method for example
                    elseif(!is_null($params->getExample()))
                    {
                        $example = $params->getExample();
                        $pos = strpos($config['endpoint'], '{');
                        $config['endpoint'] = substr($config['endpoint'], 0, $pos).$example;

                        $this->config_object->add_method(
                            $config['endpoint'],
                            $config['method'],
                            $config['content_types'],
                            $config['params']
                        );
                    }
                }
            }
            else
            {
                $this->config_object->add_method(
                    $config['endpoint'],
                    $config['method'],
                    $config['content_types'],
                    $config['params']
                );
            }
        }
        $this->config = $this->config_object->get_config();

        return empty($this->missing_fields);
    }
}
