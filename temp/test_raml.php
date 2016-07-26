<?php
require '../vendor/autoload.php';

$parsed = new \Giift\Compare\Parser\Raml('/Users/KohChinWee/dev/api-anisha/report.raml');

if($parsed->create_config())
{
    $config = $parsed->get_config();
    $conf = new \Giift\Compare\Config($config);

    $connect=array(
        'old' =>array(
            'token' => 'Bearer rAsc4WuxDns2ywAbYFqG0PCzW9LDdtPfMCnrQ6NC',
            'base_uri' => 'http://localhost/giift/public/api/report/v1'
        ),
        'new' => array(
            'token' => 'Bearer rAsc4WuxDns2ywAbYFqG0PCzW9LDdtPfMCnrQ6NC',
            'base_uri' => 'http://localhost/giift/public/api/report/v1'
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
