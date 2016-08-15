<?php

# Require functions.
require __DIR__ . '/lib/functions.php';

# Check if enabled.
if (C::get('webhooks') !== true || !count($endpoints = C::get('webhooks.endpoints'))) {
	return;
}

# Get property blacklist.
$blacklist = C::get('webhooks.blacklist', ['password', 'secret']);

# Create payload.
$payload = [
	'hook' => null,
	'host' => parse_url(site()->url(), PHP_URL_HOST),
	'user' => webhooksGetData(site()->user(), $blacklist),
	'data' => null,
];

# Process hooks.
foreach (require 'lib/hooks.php' as $hook) {

	# Register hook.
	$kirby->set('hook', $hook, function ($current, $prior = null) use ($hook, $payload, $endpoints, $blacklist) {

		# Get data.
		$currentData = webhooksGetData($current, $blacklist);
		$priorData = webhooksGetData($prior, $blacklist);

		# Update payload.
		$payload['hook'] = $hook;
		$payload['data'] = $currentData;
		$payload['diff'] = webhooksGetDiff($currentData, $priorData);

		# Create stream-context.
		$context = stream_context_create(['http' => [
			'method' => 'POST',
			'header' => 'Content-type: application/json',
			'content' => json_encode($payload),
		]]);

		# Send the request to all endpoints.
		foreach ($endpoints as $endpoint => $filters) {

			# Validate hook filters.
			if(is_array($filters)) {
				if(!webhooksFilterHook($hook, $filters)) {
					continue;
				}
			} else {
				$endpoint = $filters;
			}

			# Send request.
			@file_get_contents($endpoint, false, $context);
		}
	});
}
