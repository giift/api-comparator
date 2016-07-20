<?php

class temp
{
    protected function get_bodies($method, $resource, $config)
    {
        foreach($method->getBodies() as $body)
        {
            if($body->getMediaType() == 'application/json')
            {
                $config['params'] = json_decode($body->getExample(), true);
                unset($config['params']['id']);
                $this->set_uri_params($resource, $config);
            }
            else
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
                }
                $this->set_uri_params($resource, $config);
            }
        }
    }

    /**
     * Add a method for each enum or example
     * @param NamedParameter[] $params
     * @param string $endpoint
     * @param string $method
     * @param array $content_types
     * @param array $config_params
     * @param array $query_params
     *
     * $content_types = array(
     *     'types'
     * );
     *
     * $config_params = array(
     *     'key'=>'value'
     * );
     *
     * $query_params = array(
     *     'key'=>'value'
     * );
     *
     */
    protected function add_methods($params, $endpoint, $method, $content_types, array $config_params=null, array $query_params=null)
    {
        // Add method for each enum
        if(!empty($params->getEnum()))
        {
            foreach ($params->getEnum() as $key => $value)
            {
                $pos = strpos($endpoint, '{');
                if($pos === false)
                {
                    $config_params[$params->getKey()] = $value;
                }
                else
                {
                    $new_endpoint = substr($endpoint, 0, $pos).$value;
                }

                if(!is_null($query_params))
                {
                    $new_endpoint .= '?'.http_build_query($query_params);
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
            $example = $params->getExample();
            $pos = strpos($endpoint, '{');
            if($pos === false)
            {
                $config_params[$params->getKey()] = $example;
            }
            else
            {
                $endpoint = substr($endpoint, 0, $pos).$example;
            }

            if(!is_null($query_params))
            {
                $endpoint .= '?'.http_build_query($query_params);
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
     * Set uri params if given
     * @param \Raml\Resource $resource
     * @param array $config
     */
    protected function set_uri_params($resource, $config)
    {
        if(!empty($resource->getUriParameters()))
        {
            foreach($resource->getUriParameters() as $params)
            {
                $this->add_methods(
                    $params,
                    $config['endpoint'],
                    $config['method'],
                    $config['content_types'],
                    $config['params'],
                    $config['query_params']
                );
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
}