# API Comparator
This library can be used to compare different versions of an API, so as to ensure backward compatibility.
The user will have to provide the config consisting of the authentication token, base uris of the APIs, the method endpoint, method type, the parameters, the content types, and the display option. The config can be either manually written or generated from a raml file.
The APIs are then compared using the information provided in the config. Each method of the two APIs is executed and their reponses and headers are compared.
The results will show the differences in the responses or headers of the APIs.

## Quick start
A basic overview of how to use this library:

1. Create the config manually or from a raml file.
2. Validate the config.
3. Pass the config to the Compare class and start the comparision.
4. Results can be displayed in a file or fetched manually.

This can also be done by using [command line interface](#user-content-using-command-line-interface).

## Create the config
### Write the config
The user can pass the config as an array or put the config in a file and parse it:
```PHP
// User will have to write all the different fields and their values
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

// Parse config from a file
$config = \Giift\Compare\Config::('config/file/path');
```
A json schema of the config can be found [here]().

### Generate from RAML
<!--
Provide a RAML file documentation of the API and automatically generate the config using Raml
    1. The 'connect' and 'display_all_results' fields will have to be added separately
    2. The RAML to PHP parser is a third party library. More information can be found [here](https://github.com/alecsammon/php-raml-parser). -->

## Validate the config
Validate the config before comparing to ensure that all the necessary fields are provided.
```PHP
$config_object = new \Giift\Compare\Config($config);
$config_object->validate();
```
If the config doesn't validate agaisnt the schema, it will throw an exception stating the missing fields.

## Compare the APIs
The Compare class first does a strict check on the responses and if that fails, then it does a deep check by recursively comparing the response arrays.
To initiate this comparison, pass the config to the Compare class:

```PHP
$compare = new \Giift\Compare\Compare($config);
```
This class also has methods to

To start the comparison, you can choose to start from the first method or provide an index.
```PHP
// Reset is true so it will compare all methods
$compare->run(true);

// Set index so that it will run from the 4th method
$compare->set_index(3);
$compare->run(false);
```
## Results
To get the results:
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
