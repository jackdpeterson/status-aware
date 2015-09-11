Status Aware module 
====================

## A way to report on the status of your service  and its subcomponents.

The overall goal of this module is to make reporting on the status of sub-components of an API developer-friendly and non-intrusive. It is anticipated that the primary consumer of those data provided by this module would be a user-friendly dashboard or a service like Pingdom.

## Stability note
This module is NOT ready for production. While it probably won't have any major issues ... unit tests have not yet been written. Nor have edge-cases been sorted out when actually using this module.

## Installation

composer.json require section:
```
"jackdpeterson/status-aware" : "dev-master"
```

Edit config/application.config.php:
```
return array (
    'modules' => array(
        ...
        'StatusAware'
        ...
    )
);
```


## Provide status data

1. Create a class that implements the StatusAware\StatusInterface interface.
2. Figure out how you will check for status of the component in question (e.g., a CURL call w/ ***TIMEOUTS FOR FAILURE HANDLING***
3. return an array like so:

```
public function getServiceStatus() {
	return array(
		'name' => 'something identifiable',
		'is_critical' => true,
		'status' => 'up',
		'message' => ''
	);
}
```

### reserved fields and their formats in the response array:
```
'name' => (string) that helps you identify the service/component in question
'status' => (string) 'up','down','degraded'
'is_critical' => (boolean) Is this functionality critical to your API? If down then the overall status will be 'down'. 
'message' => '' => if 'up', the message is expected to be blank, ''. Otherwise it is assumed that you will provide a useful debugging message about the condition of soft-failure or hard-failure.
```


#### Providing additional status data
You can certainly provide more information in the getServiceStatus() array like stack traces ... so long as you cast the data as a string.

## Get status data
perform an HTTP GET request to /status will respond with JSON data about the status.

## Important and worth re-emphasizing:
 Set timeouts on your responses and handle exceptions. Otherwise if something is down ... this may not return anything!