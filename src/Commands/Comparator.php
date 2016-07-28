<?php
namespace Giift\Compare\Commands;

class Comparator extends \D2G\Reactor\Command
{
    /**
     * Sets arguments, options, flags, and help array
     */
    public function __construct($args, $opts, $flags)
    {
        parent::__construct($args, $opts, $flags);

        $this->commands = array(
            '__DEFAULT__'=>'__help',
            'raml_to_config'=>array(
                'expecting'=>array(
                    'args'=>array(
                        array(
                            'name'=>'file_path',
                            'type'=>'string',
                            'required'=>true
                        )
                    ),
                    'opts'=>array(
                        'output'=>array(
                            'type'=>'string',
                            'required'=>false,
                            'default'=>null
                        )
                    )
                )
            ),
            'compare'=>array(
                'expecting'=>array(
                    'args'=>array(
                        array(
                            'name'=>'config',
                            'type'=>'string',
                            'required'=>true
                        )
                    ),
                    'opts'=>array(
                        'output'=>array(
                            'type'=>'string',
                            'required'=>false,
                            'default'=>null
                        ),
                        'format'=>array(
                            'type'=>'string',
                            'required'=>false,
                            'default'=>'xml'
                        ),
                        'token'=>array(
                            'type'=>'string',
                            'required'=>false,
                            'default'=>null
                        ),
                        'old-uri'=>array(
                            'type'=>'string',
                            'required'=>false,
                            'default'=>null
                        ),
                        'new-uri'=>array(
                            'type'=>'string',
                            'required'=>false,
                            'default'=>null
                        ),
                        'display-all'=>array(
                            'type'=>'string',
                            'required'=>false,
                            'default'=>null
                        )
                    ),
                    'flags'=>array(
                        'reset'
                    )
                )
            )
        );
    }

    /**
     * Generates config from raml file
     */
    public function raml_to_config()
    {
        // Parse the raml file and create config
        $parsed = new \Giift\Compare\Parser\Raml($this->getArg(0));
        $config = $parsed->create_config();

        if($config)
        {
            if($this->getOpt('output'))
            {
                // Put config in file
                file_put_contents(
                    $this->getOpt('output'),
                    $this->print_json($parsed->get_config())
                );
            }
            else
            {
                // Print config to standard out
                $this->__out($this->print_json($parsed->get_config()));
            }
        }
        else
        {
            // Print missing fields to standard error
            $this->__error($this->print_json($parsed->get_missing_fields()));
        }
    }

    /**
     * Compare the two API versions
     */
    public function compare()
    {
        // Get config from file
        $config = \Giift\Compare\Config::create_from_file($this->getArg(0));

        // Check if token, uri and display options are set
        if($this->getOpt('token'))
        {
            $config['connect']['old']['token'] = $this->getOpt('token');
            $config['connect']['new']['token'] = $this->getOpt('token');
        }
        if($this->getOpt('old-uri'))
        {
            $config['connect']['old']['base_uri'] = $this->getOpt('old-uri');
        }
        if($this->getOpt('new-uri'))
        {
            $config['connect']['new']['base_uri'] = $this->getOpt('new-uri');
        }
        if($this->getOpt('display-all'))
        {
            $config['display_all_results'] = true;
        }

        // Validate config
        $config_object = new \Giift\Compare\Config($config);
        if(!$config_object->validate())
        {
            throw new \Exception();
        }

        // Compare the APIs using the config
        $compare = new \Giift\Compare\Compare($config);

        //Check for reset
        if($this->getFlag('reset'))
        {
            $reset = $this->getFlag('reset');
        }

        $compare->run($reset);

        if($this->getOpt('output') and $this->getOpt('format'))
        {
            // Put results in file
            $compare->to_file($this->getOpt('output'), $this->getOpt('format'));
        }
        else
        {
            // Print results to standard out
            $this->__out($this->print_json($compare->get_results()));
        }
    }

    /**
     * Json encode the array
     * @param array $array
     * @return string
     */
    protected function print_json(array $array)
    {
        return json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
