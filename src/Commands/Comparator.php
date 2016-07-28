<?php
namespace Giift\Compare\Commands;

class Comparator extends \D2G\Reactor\Command
{
    /**
     *
     */
    public function __construct($args, $opts, $flags)
    {
        // options to provide config as array/file or create from raml file
        // output options - to file/json, display all results
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
                        'token'=>array(
                            'type'=>'string',
                            'required'=>false,
                            'default'=>null
                        ),
                        'old-url'=>array(
                            'type'=>'string',
                            'required'=>false,
                            'default'=>null
                        ),
                        'new_url'=>array(
                            'type'=>'string',
                            'required'=>false,
                            'default'=>null
                        ),
                        'index'=>array(
                            'type'=>'integer',
                            'required'=>false,
                            'default'=>0
                        )
                    ),
                    'flags'=>array(
                        'reset'
                    )
                )
            )
        );
    }

    public function raml_to_config()
    {
        //Get the config from raml file
    }

    public function compare()
    {
        //add token and base uri if provided
        //add index and reset
        //compare config
    }
}