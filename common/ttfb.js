/**
 * TTFB Fixer - Client Side Script
 * Works with exclusion-based caching (enabled by default, disabled for excluded URLs)
 * TODO: na vazei ena ena ta elem mazi me ta listeners kai na figei to document write
 */

document.addEventListener('DOMContentLoaded', function () {
	const url = window.location.href;

	// Check if URL is in exclusion list
	var xhrCheck = new XMLHttpRequest();
	xhrCheck.open('GET', '/urls-cache.txt', true);
	xhrCheck.onreadystatechange = function () {
		if (xhrCheck.readyState === 4) {
			if (xhrCheck.status !== 200) {
				proceedWithCache(url);
				return;
			}

			// Get only the path, ignore protocol and host
			const currentUrl = new URL(url);
			let currentPath = currentUrl.pathname;
			// Remove trailing slash for consistent comparison
			currentPath = currentPath.replace(/\/$/, '') || '/';

			// Read exclusion URLs
			const excludedUrls = xhrCheck.responseText.split('\n')
				.map(line => line.trim())
				.filter(line => line !== '' && !line.startsWith('#')); // Filter out comments and empty lines

			// Extract only paths from excluded URLs (ignore protocol and host)
			const excludedPaths = excludedUrls.map(url => {
				try {
					const urlObj = new URL(url);
					let path = urlObj.pathname;
					// Remove trailing slash for consistent comparison
					path = path.replace(/\/$/, '') || '/';
					return path.toLowerCase();
				} catch (e) {
					// If URL parsing fails, try treating it as a path directly
					let path = url.replace(/\/$/, '') || '/';
					return path.toLowerCase();
				}
			});

			// Check if current path is in exclusion list (case-insensitive)
			const isExcluded = excludedPaths.some(excludedPath => {
				return excludedPath === currentPath.toLowerCase();
			});

			if (isExcluded) {
				proceedWithCache(url);
			} else {
				proceedWithCache(url);
			}
		}
	};
	xhrCheck.send();
});

const TTFB_EVENT_PROPERTIES = [
	'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmousemove', 'onmouseenter', 'onmouseleave',
	'onmouseover', 'onmouseout', 'oncontextmenu',
	'onkeydown', 'onkeyup', 'onkeypress',
	'onfocus', 'onblur', 'onchange', 'oninput', 'onsubmit', 'onreset', 'onselect',
	'ontouchstart', 'ontouchmove', 'ontouchend', 'ontouchcancel',
	'onpointerdown', 'onpointermove', 'onpointerup', 'onpointerenter', 'onpointerleave', 'onpointercancel',
	'ondragstart', 'ondrag', 'ondragend', 'ondragenter', 'ondragover', 'ondragleave', 'ondrop',
	'onwheel', 'onscroll', 'onresize', 'onload', 'onerror'
];

window.ttfbPatchState = window.ttfbPatchState || {
	active: false,
	depth: 0,
	lastStartedAt: 0,
	lastEndedAt: 0
};

function beginTtfbDomPatch(meta) {
	window.ttfbPatchState.depth += 1;
	window.ttfbPatchState.active = true;
	window.ttfbPatchState.lastStartedAt = Date.now();

	if (window.ttfbPatchState.depth === 1) {
		window.dispatchEvent(new CustomEvent('ttfbCachePatchStart', {
			detail: meta || {}
		}));
	}
}

function endTtfbDomPatch(meta) {
	window.ttfbPatchState.depth = Math.max(0, window.ttfbPatchState.depth - 1);
	window.ttfbPatchState.lastEndedAt = Date.now();

	if (window.ttfbPatchState.depth === 0) {
		window.ttfbPatchState.active = false;
		window.dispatchEvent(new CustomEvent('ttfbCachePatchEnd', {
			detail: meta || {}
		}));
	}
}

window.isTtfbDomPatchActive = function () {
	return !!(window.ttfbPatchState && window.ttfbPatchState.active);
};

window.createTtfbSafeObserver = function (callback) {
	return new MutationObserver(function (mutations, observer) {
		if (window.isTtfbDomPatchActive()) {
			return;
		}

		if (typeof callback === 'function') {
			callback(mutations, observer);
		}
	});
};

function attributesAreEqual(nodeA, nodeB) {
	if (nodeA.nodeType !== Node.ELEMENT_NODE || nodeB.nodeType !== Node.ELEMENT_NODE) {
		return true;
	}

	if (nodeA.attributes.length !== nodeB.attributes.length) {
		return false;
	}

	for (let i = 0; i < nodeA.attributes.length; i++) {
		const attr = nodeA.attributes[i];
		if (nodeB.getAttribute(attr.name) !== attr.value) {
			return false;
		}
	}

	return true;
}

function shouldReplaceNode(oldNode, newNode) {
	if (!oldNode || !newNode) {
		return true;
	}

	if (oldNode.nodeType !== newNode.nodeType) {
		return true;
	}

	if (oldNode.nodeType === Node.ELEMENT_NODE) {
		if (oldNode.tagName !== newNode.tagName) {
			return true;
		}

		if (!attributesAreEqual(oldNode, newNode)) {
			return true;
		}

		if (oldNode.childNodes.length !== newNode.childNodes.length) {
			return true;
		}

		return false;
	}

	if (oldNode.nodeType === Node.TEXT_NODE || oldNode.nodeType === Node.COMMENT_NODE) {
		return oldNode.nodeValue !== newNode.nodeValue;
	}

	return oldNode.nodeValue !== newNode.nodeValue;
}

function pathToString(path) {
	if (!path || path.length === 0) {
		return 'html';
	}
	return 'html>' + path.join('>');
}

function getNodeByPath(path) {
	let current = document.documentElement;
	for (let i = 0; i < path.length; i++) {
		if (!current || !current.childNodes || path[i] >= current.childNodes.length) {
			return null;
		}
		current = current.childNodes[path[i]];
	}
	return current;
}

function readElementListeners(element) {
	const listeners = [];

	if (!element || element.nodeType !== Node.ELEMENT_NODE) {
		return listeners;
	}

	for (let i = 0; i < element.attributes.length; i++) {
		const attr = element.attributes[i];
		if (/^on/i.test(attr.name)) {
			listeners.push({
				source: 'attribute',
				type: attr.name.slice(2).toLowerCase(),
				attributeName: attr.name,
				handlerCode: attr.value
			});
		}
	}

	for (let i = 0; i < TTFB_EVENT_PROPERTIES.length; i++) {
		const propertyName = TTFB_EVENT_PROPERTIES[i];
		if (typeof element[propertyName] === 'function') {
			listeners.push({
				source: 'property',
				type: propertyName.slice(2),
				propertyName: propertyName,
				handlerName: element[propertyName].name || 'anonymous',
				handler: element[propertyName]
			});
		}
	}

	return listeners;
}

function collectSubtreeListeners(rootElement) {
	const collected = [];

	function walk(node, relativePath) {
		if (!node || node.nodeType !== Node.ELEMENT_NODE) {
			return;
		}

		const listeners = readElementListeners(node);
		if (listeners.length > 0) {
			collected.push({
				relativePath: relativePath.slice(),
				listeners: listeners
			});
		}

		for (let i = 0; i < node.childNodes.length; i++) {
			walk(node.childNodes[i], relativePath.concat(i));
		}
	}

	walk(rootElement, []);
	return collected;
}

function restoreSubtreePropertyListeners(newRoot, captured) {
	if (!newRoot || !captured || captured.length === 0) {
		return;
	}

	for (let i = 0; i < captured.length; i++) {
		const entry = captured[i];
		let target = newRoot;

		for (let p = 0; p < entry.relativePath.length; p++) {
			const idx = entry.relativePath[p];
			if (!target || !target.childNodes || idx >= target.childNodes.length) {
				target = null;
				break;
			}
			target = target.childNodes[idx];
		}

		if (!target || target.nodeType !== Node.ELEMENT_NODE) {
			continue;
		}

		for (let l = 0; l < entry.listeners.length; l++) {
			const listener = entry.listeners[l];
			if (listener.source === 'property' && typeof listener.handler === 'function') {
				target[listener.propertyName] = listener.handler;
			}
		}
	}
}

function collectChangedNodes(oldNode, newNode, path, changes) {
	if (shouldReplaceNode(oldNode, newNode)) {
		changes.push({
			path: pathToString(path),
			pathIndices: path.slice(),
			oldNode: oldNode,
			newNode: newNode,
			listeners: oldNode && oldNode.nodeType === Node.ELEMENT_NODE ? collectSubtreeListeners(oldNode) : []
		});
		return;
	}

	if (oldNode.nodeType !== Node.ELEMENT_NODE) {
		return;
	}

	for (let i = 0; i < oldNode.childNodes.length; i++) {
		collectChangedNodes(oldNode.childNodes[i], newNode.childNodes[i], path.concat(i), changes);
	}
}

function applyColdDomUpdate(newDocument) {
	const changes = [];
	collectChangedNodes(document.documentElement, newDocument.documentElement, [], changes);

	// Expose lightweight change map for debugging/monitoring
	window.ttfbDomChanges = changes.map(change => {
		return {
			path: change.path,
			listeners: change.listeners.map(item => {
				return {
					relativePath: item.relativePath.slice(),
					listeners: item.listeners.map(listener => {
						return {
							source: listener.source,
							type: listener.type,
							propertyName: listener.propertyName || null,
							handlerName: listener.handlerName || null,
							attributeName: listener.attributeName || null
						};
					})
				};
			})
		};
	});

	if (changes.length === 0) {
		return;
	}

	beginTtfbDomPatch({
		source: 'ttfb-cache',
		changeCount: changes.length
	});

	try {
		changes.sort(function (a, b) {
			return b.pathIndices.length - a.pathIndices.length;
		});

		for (let i = 0; i < changes.length; i++) {
			const change = changes[i];
			const currentTarget = getNodeByPath(change.pathIndices);

			if (!currentTarget) {
				continue;
			}

			const replacement = change.newNode.cloneNode(true);
			restoreSubtreePropertyListeners(replacement, change.listeners);

			if (change.pathIndices.length === 0) {
				document.replaceChild(replacement, document.documentElement);
				continue;
			}

			if (currentTarget.parentNode) {
				currentTarget.parentNode.replaceChild(replacement, currentTarget);
			}
		}
	} finally {
		endTtfbDomPatch({
			source: 'ttfb-cache',
			changeCount: changes.length
		});
	}
}

/**
 * Proceed with AJAX cache update for the given URL
 */
function proceedWithCache(url) {
	const xhttp = new XMLHttpRequest();
	xhttp.onload = function () {
		if (this.status === 200) {
			const parser = new DOMParser();
			const doc = parser.parseFromString(this.responseText, "text/html");

			// Cold update: replace only changed DOM subtrees, keep unchanged parents untouched
			applyColdDomUpdate(doc);

			// Dispatch event to notify that TTFB cache update is complete
			window.ttfbCacheCompleted = true;
			const event = new CustomEvent('ttfbCacheComplete', {
				detail: { status: this.status, url: url }
			});
			window.dispatchEvent(event);

		} else if (this.status >= 300 && this.status < 400) {
			console.error(`Redirect status ${this.status} received nothing to do on cache mode`);
			// Dispatch event even on redirect
			window.ttfbCacheCompleted = true;
			const event = new CustomEvent('ttfbCacheComplete', {
				detail: { status: this.status, url: url }
			});
			window.dispatchEvent(event);
		} else {
			console.error(`Received status code ${this.status}`);
			// Dispatch event even on error so CLS doesn't hang
			window.ttfbCacheCompleted = true;
			const event = new CustomEvent('ttfbCacheComplete', {
				detail: { status: this.status, url: url }
			});
			window.dispatchEvent(event);
		}
	};

	xhttp.onerror = function () {
		console.error('Network error during cache update');
		// Dispatch event on network error so CLS doesn't hang
		window.ttfbCacheCompleted = true;
		const event = new CustomEvent('ttfbCacheComplete', {
			detail: { status: 0, url: url, error: true }
		});
		window.dispatchEvent(event);
	};

	xhttp.open("POST", url);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send();
}