<?php
require '../vendor/autoload.php';

$parsed = new \Giift\Compare\Parser\Raml('/Users/KohChinWee/dev/api-anisha/cart.raml');

if($parsed->create_config())
{
    $config = $parsed->get_config();
    $conf = new \Giift\Compare\Config($config);

    // die(print_r($conf->get_config()));
    // die('stop');

    $connect=array(
        'old' =>array(
            'token' => 'Bearer QEAVQ5MbxFouWdPePVnFQgaxWrAmC5MOZUO2gpC9',
            'base_uri' => 'http://localhost/giift/public/api/cart/v1'
        ),
        'new' => array(
            'token' => 'Bearer QEAVQ5MbxFouWdPePVnFQgaxWrAmC5MOZUO2gpC9',
            'base_uri' => 'http://localhost/giift/public/api/cart/v1'
        )
    );

    $conf->set_connect($connect);
    $conf->set_display_opt(true);

    if($conf->validate())
    {
        print_r('schema is valid');

        $compare = new \Giift\Compare\Compare($conf->get_config());

        // $compare->set_index(1);
        if($compare->run(true))
        {
            // print_r('apis match');
        // }
        // else
        // {
            // print_r('to file');
            $compare->to_file('result.xml', 'xml');
            $compare->to_file('result.csv', 'csv');
        }
    }
    else
    {
        print_r('schema not valid');
    }
}
else
{
    print_r($parsed->get_missing_fields());
}
