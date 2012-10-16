<?php
	/**
	 *
	 */
	class StatusData
	{
		private $data = array(
			"100" => <<<MD
This means that the server has received the request headers, and that the client should proceed to send the request body (in the case of a request for which a body needs to be sent; for example, a [POST](http://en.wikipedia.org/wiki/POST_(HTTP)) request). If the request body is large, sending it to a server when a request has already been rejected based upon inappropriate headers is inefficient. To have a server check if the request could be accepted based on the request's headers alone, a client must send `Expect: 100-continue` as a header in its initial request and check if a `100 Continue` status code is received in response before continuing (or receive `417 Expectation Failed` and not continue)
MD
		);

		public function getCodes()
		{
			return $this->data;
		}
	}
