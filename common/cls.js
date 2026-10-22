/**
 * CLS Fixer - Client Side Script
 */

let clsEnabled = false;
const clsEndpoint = new URL('cls.php', document.currentScript.src).href;

function checkClsEnabled(callback) {
	const currentUrl = new URL(window.location.href);
	let currentPath = currentUrl.pathname;
	currentPath = currentPath.replace(/\/$/, '') || '/';

	var xhrCheck = new XMLHttpRequest();
	xhrCheck.open('GET', '/urls-cls.txt', true);

	xhrCheck.onerror = function () {
		clsEnabled = true;
		callback(true);
	};

	xhrCheck.onreadystatechange = function () {
		if (xhrCheck.readyState === 4) {
			if (xhrCheck.status !== 200) {
				clsEnabled = true;
				callback(true);
				return;
			}

			const excludedUrls = xhrCheck.responseText.split('\n')
				.map(line => line.trim())
				.filter(line => line !== '' && !line.startsWith('#'));

			const excludedPaths = excludedUrls.map(url => {
				try {
					const urlObj = new URL(url);
					let path = urlObj.pathname;
					path = path.replace(/\/$/, '') || '/';
					return path.toLowerCase();
				} catch (e) {
					let path = url.replace(/\/$/, '') || '/';
					return path.toLowerCase();
				}
			});

			const isExcluded = excludedPaths.some(excludedPath => excludedPath === currentPath.toLowerCase());

			if (isExcluded) {
				clsEnabled = false;
				callback(false);
			} else {
				clsEnabled = true;
				callback(true);
			}
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
