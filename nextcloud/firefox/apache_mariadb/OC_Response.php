<?php
/**
 * @copyright Copyright (c) 2016, ownCloud, Inc.
 *
 * @author Andreas Fischer <bantu@owncloud.com>
 * @author Bart Visscher <bartv@thisnet.nl>
 * @author Christoph Wurst <christoph@winzerhof-wurst.at>
 * @author J0WI <J0WI@users.noreply.github.com>
 * @author Lukas Reschke <lukas@statuscode.ch>
 * @author Morris Jobke <hey@morrisjobke.de>
 * @author Roeland Jago Douma <roeland@famdouma.nl>
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 * @author Vincent Petry <vincent@nextcloud.com>
 *
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 *
 */
class OC_Response {
	/**
	 * Sets the content disposition header (with possible workarounds)
	 * @param string $filename file name
	 * @param string $type disposition type, either 'attachment' or 'inline'
	 */
	public static function setContentDispositionHeader($filename, $type = 'attachment') {
		if (\OC::$server->getRequest()->isUserAgent(
			[
				\OC\AppFramework\Http\Request::USER_AGENT_IE,
				\OC\AppFramework\Http\Request::USER_AGENT_ANDROID_MOBILE_CHROME,
				\OC\AppFramework\Http\Request::USER_AGENT_FREEBOX,
			])) {
			header('Content-Disposition: ' . rawurlencode($type) . '; filename="' . rawurlencode($filename) . '"');
		} else {
			header('Content-Disposition: ' . rawurlencode($type) . '; filename*=UTF-8\'\'' . rawurlencode($filename)
												 . '; filename="' . rawurlencode($filename) . '"');
		}
	}

	/**
	 * Sets the content length header (with possible workarounds)
	 * @param string|int|float $length Length to be sent
	 */
	public static function setContentLengthHeader($length) {
		if (PHP_INT_SIZE === 4) {
			if ($length > PHP_INT_MAX && stripos(PHP_SAPI, 'apache') === 0) {
				// Apache PHP SAPI casts Content-Length headers to PHP integers.
				// This enforces a limit of PHP_INT_MAX (2147483647 on 32-bit
				// platforms). So, if the length is greater than PHP_INT_MAX,
				// we just do not send a Content-Length header to prevent
				// bodies from being received incompletely.
				return;
			}
			// Convert signed integer or float to unsigned base-10 string.
			$lfh = new \OC\LargeFileHelper;
			$length = $lfh->formatUnsignedInteger($length);
		}
		header('Content-Length: '.$length);
	}

	/**
	 * This function adds some security related headers to all requests served via base.php
	 * The implementation of this function has to happen here to ensure that all third-party
	 * components (e.g. SabreDAV) also benefit from this headers.
	 */
	public static function addSecurityHeaders() {

	}
}
