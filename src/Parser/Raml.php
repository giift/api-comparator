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
     *         'field'
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
     * @param  string $method_uri
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

        foreach ($routes as $route)
        {
            $resource = $this->parsed->getResourceByPath($route['path']);

            // Get the endpoint
            $endpoint = $this->get_endpoint($resource);

            // Set the methods
            foreach ($resource->getMethods() as $method)
            {
                $this->set_method($resource, $method, $endpoint);
            }
        }

        // Get the config
        $this->config = $this->config_object->get_config();

        return empty($this->missing_fields);
    }

    /**
     * Returns the endpoint
     * @param  \Raml\Resource $resource
     * @return string
     */
    protected function get_endpoint(\Raml\Resource $resource)
    {
        $endpoint = $resource->getUri();
        if(is_null($endpoint))
        {
            $this->missing_fields[][] = 'endpoint';
        }

        return $endpoint;
    }

    /**
     * Set methods
     * @param \Raml\Resource $resource
     * @param \Raml\Method $method
     * @param string $endpoint
     */
    protected function set_method(\Raml\Resource $resource, \Raml\Method $method, $endpoint)
    {
        $params = null;
        $query_params = null;

        // Get query params
        if(!empty($method->getQueryParameters()))
        {
            $query_params = $this->get_query_params($method, $endpoint);
        }

        // Get method type
        $method_type = $this->get_method_type($method, $endpoint);

        // Get content type
        $content_types = $this->get_content_types($method, $endpoint);

        // Get body params
        if(!empty($method->getBodies()))
        {
            $params = $this->get_body_params($method, $endpoint);
        }

        // Add method
        $this->add_methods(
            $resource,
            $endpoint,
            $method_type,
            $content_types,
            $params,
            $query_params
        );
    }

    /**
     * Returns the query parameters
     * @param  \Raml\Method $method
     * @param  string $endpoint
     * @return string
     */
    protected function get_query_params(\Raml\Method $method, $endpoint)
    {
        $query_params = array();
        foreach ($method->getQueryParameters() as $param)
        {
            $query_params[$param->getKey()] = $param->getExample();
            if(empty($query_params))
            {
                $this->missing_fields[$endpoint][] = 'query params: '.$param->getKey();
            }
        }

        return http_build_query($query_params);
    }

    /**
     * Returns the method type
     * @param  \Raml\Method $method
     * @param  string $endpoint
     * @return string
     */
    protected function get_method_type(\Raml\Method $method, $endpoint)
    {
        $method_type = $method->getType();
        if(is_null($method_type))
        {
            $this->missing_fields[$endpoint][] = 'method';
        }

        return $method_type;
    }

    /**
     * Returns the content type of the method
     * @param  \Raml\Method $method
     * @param  string $endpoint
     * @return array
     *
     * <pre>
     * $content_types = array(
     *     'types'
     * );
     * </pre>
     */
    protected function get_content_types(\Raml\Method $method, $endpoint)
    {
        $content_types = $method->getResponse(200)->getTypes();
        if(empty($content_types))
        {
            $this->missing_fields[$endpoint][] = 'content_types';
        }

        return $content_types;
    }

    /**
     * Returns the body params
     * @param  \Raml\Method $method
     * @param  string $endpoint
     * @return array
     *
     * <pre>
     * $array = array(
     *     'key'=>'value'
     * );
     * </pre>
     */
    protected function get_body_params(\Raml\Method $method, $endpoint)
    {
        $media_types = $this->get_media_types($method);

        if(
            in_array('application/x-www-form-urlencoded', $media_types)
            or
            in_array('multipart/form-data', $media_types)
        )
        {
            return $this->get_form_params($method, $endpoint);
        }
        else
        {
            return array();
        }
    }

    /**
     * Get available media types for the body
     * @param  \Raml\Method $method
     * @return array
     *
     * <pre>
     * $media_types = array(
     *     'media_types'
     * );
     * </pre>
     */
    protected function get_media_types(\Raml\Method $method)
    {
        $media_types = array();
        foreach ($method->getBodies() as $body)
        {
            $media_types[] = $body->getMediaType();
        }
        return $media_types;
    }

    /**
     * Returns form body params
     * @param  \Raml\Method $method
     * @param  string $endpoint
     * @return array
     *
     * <pre>
     * $body_params = array(
     *     'key'=>'value'
     * );
     * </pre>
     */
    protected function get_form_params(\Raml\Method $method, $endpoint)
    {
        $body = null;
        $body_params = null;

        try
        {
            $body = $method->getBodyByType('application/x-www-form-urlencoded');
        }
        catch (\Exception $e)
        {
            try
            {
                $body = $method->getBodyByType('multipart/form-data');
            }
            catch (\Exception $e)
            {
                $body = null;
            }
        }

        if(!is_null($body) and !empty($body->getParameters()))
        {
            $body_params = $this->get_params($body, $endpoint);
        }

        return $body_params;
    }

    /**
     * Returns the form body parameters
     * @param  \Raml\WebFormBody $body
     * @param  string $endpoint
     * @return array
     *
     * <pre>
     * $body_params = array(
     *     'key'=>'value'
     * );
     * </pre>
     */
    protected function get_params(\Raml\WebFormBody $body, $endpoint)
    {
        $body_params = array();
        foreach($body->getParameters() as $params)
        {
            if(!empty($params->getEnum()))
            {
                $enums = $params->getEnum();
                $body_params[$params->getKey()] = $enums[0];
            }
            elseif(!is_null($params->getExample()))
            {
                $body_params[$params->getKey()] = $params->getExample();
            }
            else
            {
                $this->missing_fields[$endpoint][] = 'body params: '.$params->getKey();
            }
        }

        return $body_params;
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
    protected function add_methods(
        \Raml\Resource $resource,
        $endpoint,
        $method,
        array $content_types,
        array $config_params = null,
        $query_params = null
    )
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
                        $new_endpoint = $this->set_endpoint($endpoint, $value, $query_params);

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
                    $new_endpoint = $this->set_endpoint($endpoint, $params->getExample(), $query_params);

                    $this->config_object->add_method(
                        $new_endpoint,
                        $method,
                        $content_types,
                        $config_params
                    );
                }
            }
        }
        else
        {
            if(!is_null($query_params))
            {
                $endpoint = $endpoint.'?'.$query_params;
            }

            $this->config_object->add_method(
                $endpoint,
                $method,
                $content_types,
                $config_params
            );
        }
    }

    /**
     * Add the uri and query parameters to the endpoint
     * @param string $endpoint
     * @param string $uri_param
     * @param string $query_params
     */
    protected function set_endpoint($endpoint, $uri_param, $query_params = null)
    {
        // Set endpoint
        $pos = strpos($endpoint, '{');
        $new_endpoint = substr($endpoint, 0, $pos).$uri_param;

        // Add query params
        if(!is_null($query_params))
        {
            $new_endpoint .= '?'.$query_params;
        }

        return $new_endpoint;
    }
}
