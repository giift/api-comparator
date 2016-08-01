# API Comparator
This library is used to compare different versions of an API to ensure backward compatibility.

The user will have to provide a configuration that describes the API.
Using this configuration, each method of the API is executed and it's responses and headers are compared. Any differences will be logged.

## Quick start
A basic overview of how to use this library:

1. Create the configuration either from scratch, programatically, or a raml file.
2. Validate the configuration (optional).
3. Use this configuration with the Compare class.
4. Results can be logged to a file or accessed programatically.

This can also be done by using [command line interface](#user-content-command-line-interface).

## Create the configuration
The configuration consists of the authentication tokens, API uris, display option and method description (endpoint, method type, parameters, and content types).

There are different ways to create the configuration:

### Write the configuration
* Pass the configuration as an array:

```PHP
// User will have to write all the different fields and their values
$config = array(
    'connect'=>array(
        'old'=>array(
            // string Access token
            'token'=>'',
            // string API uri
            'base_uri'=>''
        ),
        'new'=>array(
            // string Access token
            'token'=>'',
            // string API uri
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
* Parse the configuration from a json file:

```PHP
$config = \Giift\Compare\Config::('path/to/config/file');
```
A json schema of the configuration can be found [here]().

### Generate from RAML
The [RAML](http://raml.org/) file contains documentation of the API, which includes the different methods, their parameters, and response description.

To create the configuration from a RAML file:

```PHP
// Pass the raml file path
$parser = new \Giift\Compare\Parser\Raml('path/to/raml/file');
// Create the configuration
$parser->create_config();
```
While parsing the file, if any values of the fields are missing, the ```create_config()``` method will return false. To get these missing fields:

```PHP
$missing = $parser->get_missing_fields();
// Example
Array
(
    [method1/endpoint] => Array
    (
        // Missing fields
        [0] => 'method_type'
        [1] => 'query params: param'
    )
    [method2/endpoint] => Array
    (
        // Missing field
        [0] => 'content_types'
    )
)

// Get missing fields for a specific method
$method_missing = $parser->get_missing_field($my_method_uri);
// Example
Array
(
    [my_method_uri] => Array
    (
        // Missing fields
        [0] => 'body params: param'
        [1] => 'query params: param'
    )
)
```
To get the parsed configuration:

```PHP
$config = $parser->get_config();
```
This configuration only consists of the 'methods' field. The 'connect' and 'display_all_results' fields will have to be added separately.
This can be done by using the Config or Compare class.

## Validate the configuration
Validate the config before comparing to ensure that all the necessary fields are provided.
```PHP
$config_object = new \Giift\Compare\Config($config);
$config_object->validate();
```
If the configuration doesn't validate agaisnt the schema, it will throw an exception stating the missing fields.

## Compare the API versions
The Compare class will do a strict check on the responses and if that fails, it will do a deep check by comparing each part of the response.

To initiate the comparison:

```PHP
// After the config has been generated/validated, get the config as shown.
// $config = $parser->get_config();

// Pass the config
$compare = new \Giift\Compare\Compare($config);
// Default parameter for run is true so it will compare all methods
$compare->run();
```
This class also has functions to modify the configuration.
* To add tokens, API uris and display option:

```PHP
// Set the 'connect' field
$connect = array(
    'old'=>array(
        'token'=>'access token',
        'base_uri'=>'uri for old version'
    ),
    'new'=>array(
        'token'=>'access token',
        'base_uri'=>'uri for new version'
    )
);
$compare->set_connect($connect);

// Display only differences (false) or all results (true)
$compare->set_display_opt(true);
```
* To add methods:

```PHP
// Add all methods
$methods = array(
    array(
        'endpoint'=>'/method1/endpoint',
        'method'=>'POST',
        'params'=>array(
            'key'=>'value'
        ),
        'content_types'=>array(
            'multipart/form-data'
        )
    ),
    array(
        'endpoint'=>'/method2/endpoint',
        'method'=>'PATCH',
        'params'=>array(
            'key'=>'value'
        ),
        'content_types'=>array(
            'application/x-www-form-urlencoded'
        )
    )
);
$compare->set_methods($methods);

// Add one method.
$compare->add_method('method/endpoint', 'GET', array(application/json));
```
You can choose to start the comparison from the first method or provide an index.

```PHP
// Set index so that it will run from the 4th method
$compare->set_index(3);
$compare->run(false);
```

## Results
To get the results:

```PHP
$results = $compare->get_results();
// Example
Array
(
    [0] => Array
    (
        [name] => 'method endpoint'
        // new execution time - old execution time
        [delta_time] => 'time difference'
        [differences] => Array
        (
            // Path to the values
            [method/key/key] => Array
            (
                // Values of old and new API versions
                [old] => 'old value'
                [new] => 'new value'
            )

        )
        // Shows any differences in headers
        [headers] => Array
        (
            [response_code] => Array
            (
                // Response code of old and new versions
                [old] => 'code'
                [new] => 'code'
            )
            // Content type of the response
            [content_type] => Array
            (
                [old] => 'type'
                [new] => 'type'
            )

        )
        // Errors in executing the methods
        [errors] => Array
        (
            [old] => 'error'
            [new] => 'error'
        )
        // Time taken to execute and compare the methods
        [time] => 'time'
    )
)
```

The results can also be put in xml (junit format), csv or json files.

```PHP
$compare->to_file('results.xml', 'xml');
```
## Command Line Interface
This cli tool was created to generate the configuration and compare the API versions from the console instead of writing a test script.

It consists of two functions with various arguments, options, and flags:

```PHP
:raml_to_config
    <file_path:string>
    [--output:string]
:compare
    <config:string>
    [--output:string]
    [--format:string]
    [--token:string]
    [--old-uri:string]
    [--new-uri:string]
    [--display-opt:string]
    [-raml]
    [-reset]
```

To create the configuration from a RAML file and store it:

```
[php] vendor/bin/reactor /Giift/Compare/Commands/Comparator:raml_to_config path/to/raml/file --output=config.json
```
The user can either update the configuration file or set the token, API uris, and display option while comparing the APIs:

```
[php] vendor/bin/reactor /Giift/Compare//Comparator:compare path/to/config/file
```
There is also an option to send the raml file directly to the compare method:

```
[php] vendor/bin/reactor /Giift/Compare//Comparator:compare path/to/raml/file --token=access token --old-uri=old base uri --new-uri=new base uri --display-opt=true -raml
```
