document.addEventListener('DOMContentLoaded', function () {
	const script = document.querySelector('script[data-ttfb-endpoint]');
	if (!script) {
		return;
	}

	const page = window.location.pathname + window.location.search;
	const endpoint = new URL(script.dataset.ttfbEndpoint, window.location.href);
	endpoint.searchParams.set('page', page);
	fetch(endpoint, {
		credentials: 'same-origin',
		cache: 'no-store'
	})
		.then(function (response) {
			if (!response.ok) {
				throw new Error(`HTTP ${response.status}`);
			}
			return response.text();
		})
		.then(function (html) {
			if (page !== window.location.pathname + window.location.search) {
				return;
			}
			const doc = new DOMParser().parseFromString(html, 'text/html');
			const tools = doc.getElementById('floating-tools-div');
			const currentTools = document.getElementById('floating-tools-div');
			if (tools) {
				if (currentTools) {
					currentTools.replaceWith(tools);
				} else {
					document.body.appendChild(tools);
				}
			} else if (currentTools) {
				currentTools.remove();
			}
		})
		.catch(function (error) {
			console.error(`TTFB tools failed for ${page}: ${error.message}`);
		});
});
