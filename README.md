Status Aware module provides a convenient way to repot on the status of subcomponents within your project.

Frequently an API has underlying dependencies. Those dependencies can range from internal middleware to third-party APIs.

Installation:

include 'StatusAware' as a module in the config/application.config.php

Within your service classes (that are instantiated with factories from Service Manager) implement the StatusAware\StatusInterface


the response must be an array consisting of the following information

public function getServiceStatus() {
	return array(
		'name' => 'something identifiable',
		'is_critical' => true,
		'status' => 'up',
		'message' => ''
	);
}


Formatting your response:
'name' => Something that you can understand what is down (e.g., search_service, or redis)
'status' => 'up','down','degraded', <==
'is_critical' => boolean. Is this functionality critical to your API? 
'message' => '', <== if up, the message is ''. Otherwise it is assumed that your service will provide useful information for diagnostic purposes.
 
 
 Critical things to keep in mind:
 --> set timeouts on your responses otherwise if something is down ... it may not return anything!