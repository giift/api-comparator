# API Comparator
Compares different versions of the same API by executing each method and comapring the response.
## Creating the config
The config is reqiured to compare the API versions. It consists of:
```
$config = array(
    'connect'=>array(
        'old'=>array(
            // string Access token
            'token'=>'',
            // string Base uri
            'base_uri'=>''
        ),
        'new'=>array(
            // string Access token
            'token'=>'',
            // string Base uri
            'base_uri'=>''
        )
    ),
    'methods'=>array(
        array(
            // string Method uri
            'endpoint'=>'',
            // string Type of method
            'method'=>'',
            // array  Parameters required for method
                'params'=>array(
                // string
                'key'=>'value'
            ),
            // array Types of response
            'content_types'=>array(
               'types'
            )
        )
    ),
    // boolean Display all results (true) or only differences (false)
    'display_all_results'=>''
);
```

There are three different ways to create the config:

1. Write the config and pass it as a paramter to the Compare constructor
2. Write the config in a file and use the ```create_from_file($filepath)``` method in Config
3. Provide a RAML file documentation of the API and automatically generate the config using Raml
    1. The 'connect' and 'display_all_results' fields will have to be added separately
    2. The RAML to PHP parser is a third party library. More information can be found [here](https://github.com/alecsammon/php-raml-parser).

## Validate the config
Validate the config before comparing to ensure all necessary fields are provided.
```PHP
$config_object = new \Giift\Compare\Config($config);
$config_object->validate();
```
## Comparing the APIs
Pass the config to the Compare class:

```PHP
$compare = new \Giift\Compare\Compare($config);
```

To start the comparison, you can choose to start from the first method or provide an index.
```PHP
// Reset is true so it will compare all methods
$compare->run(true);

// Set index so that it will run from the 4th method
$compare->set_index(3);
$compare->run(false);
```
## Results
To just get the results:
```PHP
$compare->get_results();
```
The results can also be put in xml, csv or json files. The 'display_all_results' option can be set to show only the differences or all the tests.
```PHP
$compare->to_file('results.xml', 'xml');
```
## Using Command Line Interface
This can be used to generate the config and compare the APIs without having to write your own test script.
The cli tool has been provided by a third party. More information on it can be found [here](https://github.com/DimitriGilbert/Reactor).
