<?php

namespace Giift\Compare;

class Compare
{
    protected $connect = array();
    protected $methods = array();
    protected $display_all = null;
    protected $index = 0;
    protected $differences = array();
    protected $results = array();

    /**
     * Sets the methods, base uri, token, and display option
     * @param array $config
     *
     * <pre>
     * $config = array(
     *     'connect'=>array(
     *         'old'=>array(
     *             // string Access token
     *             'token'=>'',
     *             // string Base uri
     *             'base_uri'=>''
     *         ),
     *         'new'=>array(
     *             // string Access token
     *             'token'=>'',
     *             // string Base uri
     *             'base_uri'=>''
     *         )
     *     ),
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
     *     ),
     *     // boolean Display all results (true) or only differences (false)
     *     'display_all_results'=>''
     * );
     * </pre>
     */
    public function __construct(array $config)
    {
        if(isset($config['connect']))
        {
            $this->set_connect($config['connect']);
        }
        if(isset($config['methods']))
        {
            $this->set_methods($config['methods']);
        }
        if(isset($config['display_all_results']))
        {
            $this->set_display_all($config['display_all_results']);
        }
    }

    /**
     * Returns base uri and token for both environments
     * @return array
     *
     * <pre>
     * $array = array(
     *     'connect'=>array(
     *         'old'=>array(
     *             // string Access token
     *             'token'=>'',
     *             // string Base uri
     *             'base_uri'=>''
     *         ),
     *         'new'=>array(
     *             // string Access token
     *             'token'=>'',
     *             // string Base uri
     *             'base_uri'=>''
     *         )
     *     )
     * );
     * </pre>
     */
    public function get_connect()
    {
        return $this->connect;
    }

    /**
     * Returns the methods
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
     */
    public function get_methods()
    {
        return $this->methods;
    }

    /**
     * Returns display option
     * @return string
     */
    public function get_display_all()
    {
        return $this->display_all;
    }

    /**
     * Returns the differences
     * @return array
     *
     * <pre>
     * $array = array(
     *     'method_key'=>array(
     *         'old'=>'',
     *         'new'=>''
     *     )
     * );
     * </pre>
     */
    public function get_differences()
    {
        return $this->differences;
    }

    /**
     * Returns index of the methods
     * @return int
     */
    public function get_index()
    {
        return $this->index;
    }

    /**
     * Returns the results
     * @return string
     */
    public function get_results()
    {
        return $this->results;
    }

    /**
     * Sets the base uri and token for both environments
     * @param array $connect
     *
     * <pre>
     * $connect = array(
     *     'connect'=>array(
     *         'old'=>array(
     *             // string Access token
     *             'token'=>'',
     *             // string Base uri
     *             'base_uri'=>''
     *         ),
     *         'new'=>array(
     *             // string Access token
     *             'token'=>'',
     *             // string Base uri
     *             'base_uri'=>''
     *         )
     *     )
     * );
     * </pre>
     */
    public function set_connect(array $connect)
    {
        $this->connect = $connect;
    }

    /**
     * Set method
     * @param array $methods
     *
     * <pre>
     * $methods = array(
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
     *             'content_types'=>''
     *         )
     *     )
     * );
     * </pre>
     */
    public function set_methods(array $methods)
    {
        $this->methods = $methods;
    }

    /**
     * Set display option
     * @param boolean $display_all
     */
    public function set_display_all($display_all)
    {
        $this->display_all = $display_all;
    }

    /**
     * Set the index
     * @param int $index
     */
    public function set_index($index)
    {
        $this->index = $index;
    }

    /**
     * Adds a method
     * @param string $endpoint
     * @param string $method
     * @param array $content_types
     * @param array $params
     *
     * <pre>
     * $content_types = array(
     *     'types'
     * );
     * $params = array(
     *     'key' => 'value'
     * );
     * </pre>
     */
    public function add_method($endpoint, $method, array $content_types, array $params=null)
    {
        $new_method = array();
        $new_method['endpoint'] = $endpoint;
        $new_method['method'] = $method;
        $new_method['content_types'] = $content_types;

        if(!is_null($params))
        {
            $new_method['params'] = $params;
        }

        $this->method[] = $new_method;
    }

    /**
     * Reset the differences
     */
    public function reset_differences()
    {
        $this->differences = array();
    }

    /**
     * Reset the index
     */
    public function reset_index()
    {
        $this->index = 0;
    }

    /**
     * Executes methods for both envirnoments and stores the results
     * @param boolean $reset
     * @return boolean
     */
    public function run($reset = true)
    {
        if($reset)
        {
            $this->reset_index();
        }

        $config = $this->get_connect();
        $methods = array_slice($this->get_methods(), $this->get_index());

        // Loop through methods and execute for both environments
        foreach($methods as $method)
        {
            $start_time = microtime(true);

            // Set uri and tokens for the methods of both environments
            $old_method = new \Giift\Simplecurl\Curl\Json;
            $old_method->set_url($config['old']['base_uri'].$method['endpoint']);
            $old_method->add_header('Authorization', $config['old']['token']);

            $new_method = new \Giift\Simplecurl\Curl\Json;
            $new_method->set_url($config['new']['base_uri'].$method['endpoint']);
            $new_method->add_header('Authorization', $config['new']['token']);

            // Set the method type and post data
            $old_method->set_method($method['method']);
            $new_method->set_method($method['method']);

            if(array_key_exists('params', $method))
            {
                if($method['method'] === 'POST')
                {
                    $old_method->set_post_str(http_build_query($method['params']));
                    $new_method->set_post_str(http_build_query($method['params']));
                }
                else
                {
                    $old_method->set_post_str(http_build_query($method['params']), false);
                    $new_method->set_post_str(http_build_query($method['params']), false);
                }
            }

            // Execute the methods and compare results
            if($old_method->execute() and $new_method->execute())
            {
                $result = array('name' => $method['endpoint']);

                if(!$this->compare($old_method, $new_method, $method['endpoint']))
                {
                    $result['differences'] = $this->get_differences();
                }
            }
            else
            {
                $result['errors'] = array(
                    'old' => $old_method->get_error(true),
                    'new' => $new_method->get_error(true)
                );
            }

            $time = microtime(true) - $start_time;
            $result['time'] = $time;

            $this->results[] = $result;
            $this->reset_differences();
            $this->index++;
        }

        return empty($this->differences);
    }

    /**
     * Compares the responses from both environments
     * @param  \Simplecurl\Curl $old
     * @param  \Simplecurl\Curl $new
     * @return  boolean
     */
    public function compare($old, $new, $method)
    {
        if($this->check_md5($old->_raw, $new->_raw))
        {
            return true;
        }
        return ($this->check_json($old->_response, $new->_response, array($method)));
    }

    /**
     * Uses md5 to compare results of both environments
     * @param  string $old
     * @param  string $new
     * @return  boolean
     */
    protected function check_md5($old, $new)
    {
        if(md5($old) == md5($new))
        {
            return true;
        }
        return false;
    }

    /**
     * Recursively compares results from both environments
     * @param  array $old
     * @param  array $new
     * @return  boolean
     */
    protected function check_json($old, $new, $path = array())
    {
        foreach($old as $key => $value)
        {
            $path[] = $key;

            if(!isset($new[$key]) and isset($value))
            {
                $this->log_diff($value, null, $path);
            }
            elseif(is_array($value) and !is_array($new[$key]))
            {
                $this->log_diff($value, $new[$key], $path);
            }
            elseif(is_array($value) and is_array($new[$key]))
            {
                $this->check_json($value, $new[$key], $path);
            }
            elseif($value !== $new[$key])
            {
                $this->log_diff($value, $new[$key], $path);
            }
        }

        return empty($this->differences);
    }

    /**
     * Stores the differences
     * @param  string $old
     * @param  string $new
     * @param  array $path
     */
    protected function log_diff($old, $new, $path)
    {
        $this->differences[implode('.', $path)] = array(
            'old' => $old,
            'new' => $new
        );
    }

    /**
     * Puts output in specified file
     * @param string $output_file
     * @param string $format
     *
     * @throws \Exception
     */
    public function to_file($output_file, $format)
    {
        $output = $this->format($format);

        try
        {
            if(is_dir($output_file))
            {
                throw new \Exception("Cannot write to a directory");
            }
            if(!file_put_contents($output_file, $output))
            {
                throw new \Exception("Could not write to file");
            }
        }
        catch(\Exception $e)
        {
            echo $e->getMessage();
        }
    }

    /**
     * Formats the output
     * @param  string $format
     * @return  string
     */
    public function format($format = 'json')
    {
        switch($format)
        {
            case 'json':
                $output = json_encode($this->get_results(),JSON_PRETTY_PRINT);
                break;
            case 'csv':
                $output = $this->to_csv();
                break;
            case 'xml':
                $output = $this->to_junit();
                break;
            default:
                $output = null;
                break;
        }

        return $output;
    }

    /**
     * Converts the results to csv format
     * @return string
     */
    public function to_csv()
    {
        $line = array();

        foreach($this->get_results() as $result)
        {
            if($this->display_all)
            {
                $line[] = '"'.$result['name'].'"';
            }
            if(array_key_exists('differences', $result))
            {
                foreach ($result['differences'] as $key => $value)
                {
                    $line[] = '"'.$result['name'].'","'.$key.'","'.$value['old'].'","'.$value['new'].'"';
                }
            }
        }
        $data = implode("\n", $line);

        $csv = new \League\Plates\Engine('../templates');

        return $csv->render('csv', array('differences' => $data));
    }

    /**
     * Put results in junit format template
     * @return string
     */
    public function to_junit()
    {
        $results = $this->get_results();

        // Set data to put in junit template
        $info = array(
            'duration' => 0,
            'errors' => 0,
            'failures' => 0,
            'tests' => count($results),
            'skips' => count($this->get_methods()) - count($results),
            'testcases' => null
        );

        // Create testcase template
        foreach($results as $result)
        {
            if ($this->display_all or array_key_exists('differences', $result))
            {
                $data = array(
                    'name' => urlencode($result['name']),
                    'time' => $result['time'],
                    'fail' => false,
                    'error' => false
                );

                $info['duration'] += $result['time'];

                // Set error messages
                if(array_key_exists('errors', $result))
                {
                    $data['error'] = true;
                    $data['error_message'] = json_encode($result['errors'], JSON_PRETTY_PRINT);
                    $info['errors']++;
                }

                // Set data if there are differences
                if(array_key_exists('differences', $result))
                {
                    $data['fail'] = true;
                    $data['differences'] = json_encode($result['differences'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

                    $info['failures']++;
                }

                $testcase = new \League\Plates\Engine('../templates/junit');
                $info['testcases'] .= $testcase->render('testcases', $data);
            }
        }

        // Display results in junit template
        $tpl = new \League\Plates\Engine('../templates');

        return $tpl->render('junit', $info);
    }
}
