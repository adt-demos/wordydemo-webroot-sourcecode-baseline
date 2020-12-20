import $ from 'jquery';
import { ajaxurl_legacy as AJAXURL_LEGACY } from 'peepsodata';

const MAX_CONCURRENT_REQUESTS = 10;

class Ajax {
	constructor() {
		this.counter = 0;
		this.requests = [];
		this.queue = [];
	}

	/**
	 * Checks if the URL is a REST request.
	 *
	 * @param {string} url
	 * @returns {boolean}
	 */
	isRest(url) {
		return false;
	}

	/**
	 * Adds an AJAX request data into a queue.
	 *
	 * @param {Object} requestData
	 * @return {Promise}
	 */
	addToQueue(requestData) {
		this.queue.push(requestData);
		this.queue.sort((a, b) => +a.priority - b.priority);

		// Gives time for queue sorting when multiple requests are added simultaneously.
		setTimeout(() => this.execQueue(), 1);
	}

	/**
	 * Executes AJAX request queue.
	 */
	execQueue() {
		if (!this.queue.length) {
			return;
		}

		if (this.requests.length >= MAX_CONCURRENT_REQUESTS) {
			return;
		}

		let id = ++this.counter;
		let data = this.queue.shift();

		let params = {
			url: data.url,
			type: data.method.toUpperCase(),
			data: data.data,
			dataType: 'json'
		};

		let xhr = $.ajax(params)
			.done((json, status, xhr) => {
				// Handles session timeout.
				if (!data.url.match('auth.login') && json.session_timeout) {
					data.defer.reject(xhr, status);
					$(window).trigger('peepso_auth_required');
					return;
				}

				data.defer.resolve(json, status, xhr);
			})
			.fail(data.defer.reject)
			.always(() => {
				// Remove completed request from the request table.
				for (let i = this.requests.length - 1; i >= 0; i--) {
					if (this.requests[i].id === id) {
						this.requests.splice(i, 1);
						break;
					}
				}

				// Recursively calls current method when request is done to run the next item in queue
				// until it is empty.
				this.execQueue();
			});

		this.requests.push({ id, xhr });
	}

	/**
	 * Performs a GET request.
	 *
	 * @param {string} url
	 * @param {Object} data
	 * @param {number} priority
	 * @returns {JQueryDeferred}
	 */
	get(url, data = {}, priority = 10) {
		return $.Deferred(defer => {
			if (-1 === url.indexOf('/')) {
				url = `${AJAXURL_LEGACY}${url}`;
			}

			this.addToQueue({ method: 'get', url, data, priority, defer });
		});
	}

	/**
	 * Performs a POST request.
	 *
	 * @param {string} url
	 * @param {Object} data
	 * @param {number} priority
	 * @returns {JQueryDeferred}
	 */
	post(url, data = {}, priority = 10) {
		return $.Deferred(defer => {
			if (-1 === url.indexOf('/')) {
				url = `${AJAXURL_LEGACY}${url}`;
			}

			this.addToQueue({ method: 'post', url, data, priority, defer });
		});
	}
}

export default Ajax;

// Test case:
// $(() => {
// 	let ajax = new Ajax();
// 	ajax.post('https://www.kompas.com/', {});
// 	ajax.post('https://www.tempo.co/', {});
// 	ajax.get('https://www.detik.com/', {}, 20);
// 	ajax.post('https://www.kompasian.com/', {});
// 	ajax.get('https://www.cnnindonesia.com/', {}, 5)
// 		.done(json => console.log('Done!', json))
// 		.fail(error => console.log('Fail!', error))
// 		.always(() => console.log('Always!'));
// 	ajax.get('https://www.google.com/', {});
// });
