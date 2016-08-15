<?php

# Require functions.
require __DIR__ . '/lib/functions.php';

# Check if enabled.
if (C::get('webhooks') !== true || !count($endpoints = C::get('webhooks.endpoints'))) {
	return;
}

# Get property blacklist.
$blacklist = C::get('webhooks.blacklist', ['password', 'secret']);

# Process hooks.
foreach (require 'lib/hooks.php' as $hook) {

	# Register hook.
	$kirby->set('hook', $hook, function ($current, $prior = NULL) use ($hook, $endpoints, $blacklist) {

		# Create payload.
		$payload = [
			'hook' => $hook,
			'site' => site()->url(),
			'user' => webhooksGetData(site()->user(), $blacklist),
			'data' => webhooksGetData($current, $blacklist),
		];

		# Add diff to payload.
		if($prior && ($diff = webhooksGetDiff(webhooksGetData($current, $blacklist), webhooksGetData($prior, $blacklist)))) {
			$payload['diff'] = $diff;
		}

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
