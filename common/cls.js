/**
 * CLS Fixer - Client Side Script
 */

let clsEnabled = false;
const clsEndpoint = new URL('cls.php', document.currentScript.src).href;

function checkClsEnabled(callback) {
	var xhrCheck = new XMLHttpRequest();
	xhrCheck.open('GET', `${clsEndpoint}?cls-status=${encodeURIComponent(window.location.pathname)}`, true);

	xhrCheck.onerror = function () {
		clsEnabled = false;
		console.error('Cannot read CLS status: network error');
		callback(false);
	};

	xhrCheck.onreadystatechange = function () {
		if (xhrCheck.readyState === 4) {
			if (xhrCheck.status !== 200) {
				clsEnabled = false;
				console.error(`Cannot read CLS status: HTTP ${xhrCheck.status}`);
				callback(false);
				return;
			}

			clsEnabled = JSON.parse(xhrCheck.responseText).enabled === true;
			callback(clsEnabled);
		}
	};
	xhrCheck.send();
}

function rel2abs(url, baseUrl) {
	if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('//')) {
		return url;
	}

	if (typeof URL !== 'undefined') {
		try {
			const base = new URL(baseUrl);
			return new URL(url, base).href;
		} catch (e) {
			return url;
		}
	}

	const base = parseBaseUrl(baseUrl);
	if (!base) {
		return url;
	}

	const baseOrigin = base.origin;
	const basePath = base.path.replace(/\/[^\/]*$/, '/');

	if (url.startsWith('/')) {
		return baseOrigin + url;
	}

	return baseOrigin + basePath + url;
}

function parseBaseUrl(baseUrl) {
	const urlPattern = /^(https?:\/\/[^\/]+)(\/?.*)$/;
	const matches = baseUrl.match(urlPattern);
	if (!matches) {
		return null;
	}

	return {
		origin: matches[1],
		path: matches[2] || '/'
	};
}

function flyingLoad() {
	const images = document.querySelectorAll('img[data-original-src]');

	images.forEach(img => {
		const rect = img.getBoundingClientRect();
		const isVisible = rect.top < window.innerHeight && rect.bottom > 0;
		const displayStyle = getComputedStyle(img).display;
		const isDisplayed = displayStyle !== 'none';

		if (isVisible && isDisplayed) {
			const originalSrc = img.getAttribute('data-original-src');
			const originalSrcAbs = rel2abs(originalSrc, window.location.href);
			const resizedImage = new Image();

			resizedImage.onload = function () {
				img.setAttribute('src', resizedImage.src);
			};

			const computedStyle = window.getComputedStyle(img);
			const width = parseInt(computedStyle.width);
			const height = parseInt(computedStyle.height);
			const fetchUrl = `${clsEndpoint}?get-thumb=true&src=${encodeURIComponent(originalSrcAbs)}&width=${width}&height=${height}`;

			fetch(fetchUrl, { method: 'GET', cache: 'no-store', credentials: 'same-origin' })
				.then(response => {
					const contentType = response.headers.get('content-type') || '';

					if (contentType.includes('text/html')) {
						throw new Error('Expected image response but got HTML');
					}

					if (!response.ok) {
						throw new Error('Network response was not ok: ' + response.status + ' ' + response.statusText);
					}

					return response.blob();
				})
				.then(blob => {
					if (blob.size === 0) {
						throw new Error('Empty thumbnail blob');
					}

					const objectURL = URL.createObjectURL(blob);
					resizedImage.src = objectURL;
				})
				.catch(function () {
					img.setAttribute('src', originalSrc);
				});

			img.removeAttribute('data-original-src');
		}
	});
}

function debounce(func, wait) {
	let timeout;
	return function () {
		const context = this;
		const args = arguments;
		clearTimeout(timeout);
		timeout = setTimeout(() => func.apply(context, args), wait);
	};
}

function initializeCls() {
	checkClsEnabled(function (isEnabled) {
		if (isEnabled) {
			flyingLoad();
			window.addEventListener('scroll', debounce(flyingLoad, 200));
			window.addEventListener('resize', debounce(flyingLoad, 200));
		}
	});
}

window.addEventListener('load', function () {
	initializeCls();
});
