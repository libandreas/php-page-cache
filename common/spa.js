(function () {
	// Save original methods in closure
	const originalAddEventListener = EventTarget.prototype.addEventListener;
	const originalRemoveEventListener = EventTarget.prototype.removeEventListener;

	// WeakMap to track event listeners per target element
	const eventRegistry = new WeakMap();
	// Global Set to keep track of all EventTarget instances that have event listeners attached
	const trackedEventTargets = new Set();

	// Patch addEventListener to record event listener details
	EventTarget.prototype.addEventListener = function (type, listener, options) {
		// Get or create the list of listeners for this target
		let listeners = eventRegistry.get(this);
		if (!listeners) {
			listeners = [];
			eventRegistry.set(this, listeners);
		}
		// Record the listener details
		listeners.push({ type, listener, options });

		// Add this target to the global set
		trackedEventTargets.add(this);

		// Call original addEventListener
		return originalAddEventListener.call(this, type, listener, options);
	};

	// Patch removeEventListener to update the registry accordingly
	EventTarget.prototype.removeEventListener = function (type, listener, options) {
		let listeners = eventRegistry.get(this);
		if (listeners) {
			// Helper to normalize options for comparison
			function normalizeOptions(op) {
				return typeof op === 'object' ? JSON.stringify(op) : op;
			}
			const normalizedOptions = normalizeOptions(options);
			for (let i = listeners.length - 1; i >= 0; i--) {
				const entry = listeners[i];
				if (
					entry.type === type &&
					entry.listener === listener &&
					normalizeOptions(entry.options) === normalizedOptions
				) {
					listeners.splice(i, 1);
				}
			}
			if (listeners.length === 0) {
				eventRegistry.delete(this);
				trackedEventTargets.delete(this);
			}
		}
		return originalRemoveEventListener.call(this, type, listener, options);
	};

	/**
	 * Helper function that removes duplicate event listeners from a specific target.
	 * For each unique combination of event type, listener callback, and options, only one instance remains.
	 * @param {EventTarget} target - DOM element or other event target to process.
	 */
	function cleanupSelectiveDuplicateListeners(target) {
		const listeners = eventRegistry.get(target);
		if (!listeners || listeners.length === 0) {
			return 0;
		}
		const uniqueEntries = [];
		const duplicates = [];

		function normalizeOptions(op) {
			return typeof op === 'object' ? JSON.stringify(op) : op;
		}

		// Identify duplicates: if an identical [type, listener, options] combo already exists, mark as duplicate
		listeners.forEach((entry) => {
			const exists = uniqueEntries.some((uniq) => {
				return (
					uniq.type === entry.type &&
					uniq.listener === entry.listener &&
					normalizeOptions(uniq.options) === normalizeOptions(entry.options)
				);
			});
			if (exists) {
				duplicates.push(entry);
			} else {
				uniqueEntries.push(entry);
			}
		});

		// Remove each duplicate listener for this target
		duplicates.forEach((entry) => {
			originalRemoveEventListener.call(target, entry.type, entry.listener, entry.options);
		});

		// Update registry so the target stores only unique entries.
		eventRegistry.set(target, uniqueEntries);

		return duplicates.length;
	}

	/**
	 * Helper function to remove duplicate event listeners from all tracked EventTargets in the document.
	 * When you call this function, it iterates over all elements recorded via addEventListener
	 * (thanks to our global trackedEventTargets Set) and removes duplicate event listeners.
	 * The function is not automatically invoked but should be run manually when needed.
	 */
	window.removeDuplicateEventsFromTheDocument = function () {
		let totalRemoved = 0;
		// Convert trackedEventTargets set to an array to iterate reliably
		const targets = Array.from(trackedEventTargets);
		targets.forEach((target) => {
			totalRemoved += cleanupSelectiveDuplicateListeners(target);
		});
		return totalRemoved;
	};

	// Optionally expose the registry for debugging (read-only)
	window.__eventRegistry = eventRegistry;
})();

function generateSessionFingerprint() {
	const basic = {
		ua: navigator.userAgent.replace(/\s.*/, '').slice(0, 16),
		plat: navigator.platform.slice(0, 8),
		time: Date.now().toString(16).slice(0, 8)
	};
	return btoa(`${basic.ua}${basic.plat}${basic.time}`);
}

function ensureLoadingScreen() {
	let loadingScreen = document.getElementById('loading-screen');
	if (loadingScreen || !document.body) {
		return loadingScreen;
	}

	loadingScreen = document.createElement('div');
	loadingScreen.id = 'loading-screen';
	document.body.prepend(loadingScreen);

	return loadingScreen;
}

function spaLoadingStart() {
	const loadingScreen = ensureLoadingScreen();
	if (loadingScreen) {
		loadingScreen.style.display = 'block';
	}
}

function spaLoadingStop() {
	const loadingScreen = ensureLoadingScreen();
	if (loadingScreen) {
		loadingScreen.style.display = 'none';
	}
}

// Function to handle navigation with AJAX using POST
function open_with_spa(this_elem, history_push = true, loading_push = true) {
	if (!this_elem || !this_elem.href) {
		return false;
	}

	const url = this_elem.href;

	if (loading_push === true) {
		spaLoadingStart();
	}

	const xhttp = new XMLHttpRequest();
	xhttp.onload = function () {
		if (this.status === 200) {
			const parser = new DOMParser();
			const doc = parser.parseFromString(this.responseText, "text/html");

			// Update the document
			updateDocument(document.documentElement, doc.documentElement);

			// Trigger DOMContentLoaded and load events
			setTimeout(() => {
				removeDuplicateEventsFromTheDocument();
				const documentLoadEvent = new Event('DOMContentLoaded');
				document.dispatchEvent(documentLoadEvent);
				const windowLoadEvent = new Event('load');
				window.dispatchEvent(windowLoadEvent);
				spaLoadingStop();
			}, 4);

			if (history_push === true && window.location.href !== this.responseURL) {
				window.history.pushState({ urlPath: this.responseURL }, "", this.responseURL);
			}
		} else {
			console.error(`Received status code ${this.status}`);
			spaLoadingStop();
		}
	};
	xhttp.onerror = function () {
		spaLoadingStop();
	};

	// Open the URL with XMLHttpRequest
	xhttp.open("POST", url);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send();
}

function submit_with_spa(formElem, history_push = true, loading_push = true) {
	const url = formElem.action;

	// Original function continues here
	if (loading_push === true) {
		spaLoadingStart();
	}

	let pressedButton = null;
	if (event.submitter) {
		pressedButton = event.submitter;
	}

	const formData = new FormData();
	Array.from(formElem.elements).forEach(element => {
		if (element.name && (element.type !== 'submit' || element === pressedButton)) {
			if (element.type === 'file') {
				for (let i = 0; i < element.files.length; i++) {
					formData.append(element.name, element.files[i]);
				}
			} else {
				formData.append(element.name, element.value);
			}
		}
	});

	const xhttp = new XMLHttpRequest();
	xhttp.onload = function () {
		if (this.status === 200) {
			const parser = new DOMParser();
			const doc = parser.parseFromString(this.responseText, "text/html");
			updateDocument(document.documentElement, doc.documentElement);

			setTimeout(() => {
				removeDuplicateEventsFromTheDocument();
				const documentLoadEvent = new Event('DOMContentLoaded');
				document.dispatchEvent(documentLoadEvent);
				const windowLoadEvent = new Event('load');
				window.dispatchEvent(windowLoadEvent);
				spaLoadingStop();
			}, 4);

			if (history_push === true && window.location.href !== this.responseURL) {
				window.history.pushState({ urlPath: this.responseURL }, "", this.responseURL);
			}

			reloadImages();
		} else {
			console.error('Received status code ' + this.status);
			spaLoadingStop();
		}
	};
	xhttp.onerror = function () {
		spaLoadingStop();
	};

	xhttp.open("POST", url);
	xhttp.send(formData);
}

// Function to reload images
function reloadImages() {
	const images = document.querySelectorAll('img');
	images.forEach(img => {
		const src = img.src;
		img.src = '';
		img.src = src;
	});
}

function attachEventSpaListeners() {
	// Load the exclusion URLs from urls-spa.txt via AJAX
	const xhr = new XMLHttpRequest();
	xhr.open('GET', '/urls-spa.txt', true);
	xhr.onreadystatechange = function () {
		if (xhr.readyState === XMLHttpRequest.DONE) {
			const currentDomain = window.location.hostname;
			const excludedPaths = (xhr.status === 200 ? xhr.responseText : '').split('\n')
				.filter(line => line.trim() !== '')
				.map(url => new URL(url.trim(), window.location.origin).pathname)
				.filter(url => url);

			// Select all <a> elements with an href attribute
			const links = document.querySelectorAll('a[href]');

			links.forEach(function (link) {
				let href = link.getAttribute('href');

				if (!href.startsWith('http')) {
					href = new URL(href, window.location.href).href;
				}

				if (!href || href.includes('#') || href === window.location.href) {
					return;
				}

				let linkUrl;
				try {
					linkUrl = new URL(href, window.location.origin);
				} catch (e) {
					console.error('Invalid URL, skipping link:', href, e);
					return;
				}

				if (linkUrl.hostname === currentDomain && href !== '#') {
					const linkPathname = linkUrl.pathname;

					if (!excludedPaths.some(excludedPath => excludedPath === linkPathname)) {
						if (!link.closest('#floating-tools-div')) {
							// Remove existing events by cloning and replacing
							const oldElement = link.cloneNode(true);
							link.parentNode.replaceChild(oldElement, link);
							link = oldElement;

							// Add fresh event listener
							link.addEventListener('click', function (event) {
								event.preventDefault();
								open_with_spa(this);
							});
						}
					}
				}
			});

			// Handle form submissions
			const forms = document.querySelectorAll('form');
			forms.forEach(function (form) {
				let action = form.getAttribute('action');
				const method = form.getAttribute('method')?.toLowerCase();

				if (method === 'post') {
					// If action is null or empty, use current window location
					if (!action) {
						action = window.location.href;
					} else if (!action.startsWith('http')) {
						action = new URL(action, window.location.href).href;
					}

					let actionUrl;
					try {
						actionUrl = new URL(action, window.location.origin);
					} catch (e) {
						console.error('Invalid URL, skipping form:', action, e);
						return;
					}

					if (actionUrl.hostname === currentDomain) {
						const actionPathname = actionUrl.pathname;

						if (!excludedPaths.some(excludedPath => excludedPath === actionPathname)) {
							form.addEventListener('submit', function (event) {
								event.preventDefault();
								submit_with_spa(form);
							});
						}
					}
				}
			});
		}
	};
	xhr.send();
}

function updateDocument(currentNode, newNode) {
	if (!currentNode || !newNode) return;

	// If nodes are different types or names, replace the current node
	if (currentNode.nodeType !== newNode.nodeType || currentNode.nodeName !== newNode.nodeName) {
		currentNode.parentNode.replaceChild(newNode.cloneNode(true), currentNode);
		return;
	}

	// Handle text nodes
	if (currentNode.nodeType === Node.TEXT_NODE || currentNode.nodeType === Node.CDATA_SECTION_NODE) {
		if (currentNode.textContent !== newNode.textContent) {
			currentNode.textContent = newNode.textContent;
		}
		return;
	}

	// Element nodes: update attributes and recursively update children
	if (currentNode.nodeType === Node.ELEMENT_NODE) {
		updateAttributes(currentNode, newNode);

		// Compare and update child nodes
		let currentChildren = Array.from(currentNode.childNodes);
		let newChildren = Array.from(newNode.childNodes);
		let maxLength = Math.max(currentChildren.length, newChildren.length);

		for (let i = 0; i < maxLength; i++) {
			if (!currentChildren[i]) {
				// Append new node
				currentNode.appendChild(newChildren[i].cloneNode(true));
			} else if (!newChildren[i]) {
				// Remove old node
				currentNode.removeChild(currentChildren[i]);
			} else {
				// Recursively update existing nodes
				updateDocument(currentChildren[i], newChildren[i]);
			}
		}
	}
}

// Function to update attributes
function updateAttributes(currentElement, newElement) {
	// Only proceed if both nodes are element nodes
	if (currentElement.nodeType !== Node.ELEMENT_NODE || newElement.nodeType !== Node.ELEMENT_NODE) {
		return; // Nodes don't have attributes
	}

	let currentAttrs = currentElement.attributes;
	let newAttrs = newElement.attributes;

	// Set new and updated attributes
	for (let i = 0; i < newAttrs.length; i++) {
		let attrName = newAttrs[i].name;
		let attrValue = newAttrs[i].value;
		if (currentElement.getAttribute(attrName) !== attrValue) {
			currentElement.setAttribute(attrName, attrValue);
		}
	}

	// Remove old attributes
	for (let i = 0; i < currentAttrs.length; i++) {
		let attrName = currentAttrs[i].name;
		if (!newElement.hasAttribute(attrName)) {
			currentElement.removeAttribute(attrName);
		}
	}
}

function isSpaAllEnabledSync() {
	const stateXhr = new XMLHttpRequest();
	stateXhr.open('GET', '/urls-spa-all.txt', false);
	stateXhr.send();
	if (stateXhr.status === 200) {
		return stateXhr.responseText.trim() !== '0';
	}
	return true;
}

// Initialize SPA on document load, better with on load without 2 times spa gets
document.addEventListener('DOMContentLoaded', function () {
	if (!isSpaAllEnabledSync()) {
		return;
	}

	// Check if current URL is excluded; SPA is ON by default
	const checkXhr = new XMLHttpRequest();
	checkXhr.open('GET', '/urls-spa.txt', false); // Synchronous request
	checkXhr.send();
	if (checkXhr.status === 200) {
		const excludedPaths = checkXhr.responseText.split('\n')
			.filter(line => line.trim() !== '')
			.map(url => new URL(url.trim(), window.location.origin).pathname);
		const currentPathname = window.location.pathname;
		if (excludedPaths.some(excludedPath => excludedPath === currentPathname)) {
			return;
		}
	}

	attachEventSpaListeners();
});
