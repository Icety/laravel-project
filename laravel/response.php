<?php namespace Laravel;

interface Renderable {

	/**
	 * Get the evaluated string contents of the object.
	 *
	 * @return string
	 */
	public function render();

}

class Response implements Renderable {

	/**
	 * The content of the response.
	 *
	 * @var mixed
	 */
	public $content;

	/**
	 * The HTTP status code of the response.
	 *
	 * @var int
	 */
	public $status;

	/**
	 * The response headers.
	 *
	 * @var array
	 */
	public $headers = array();

	/**
	 * HTTP status codes.
	 *
	 * @var array
	 */
	private $statuses = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		507 => 'Insufficient Storage',
		509 => 'Bandwidth Limit Exceeded'
	);

	/**
	 * Create a new response instance.
	 *
	 * @param  mixed  $content
	 * @param  int    $status
	 */	
	public function __construct($content, $status = 200)
	{
		$this->content = $content;
		$this->status = $status;
	}	

	/**
	 * Factory for creating new response instances.
	 *
	 * @param  string    $content
	 * @param  int       $status
	 * @return Response
	 */
	public static function make($content, $status = 200)
	{
		return new static($content, $status);
	}

	/**
	 * Get the evaluated string contents of the response.
	 *
	 * If the content implements the Renderable interface, the render method will be called
	 * on the content and the result will be returned. Otherwise, the content will be cast
	 * to a string and returned.
	 *
	 * @return string
	 */
	public function render()
	{
		return ($this->content instanceof Renderable) ? $this->content->render() : (string) $this->content;
	}

	/**
	 * Send the response to the browser.
	 *
	 * All of the response header will be sent to the browser first, followed by the content
	 * of the response instance, which will be evaluated and rendered by the render method.
	 *
	 * @return void
	 */
	public function send()
	{
		if ( ! isset($this->headers['Content-Type'])) $this->header('Content-Type', 'text/html; charset=utf-8');

		if ( ! headers_sent()) $this->send_headers();

		echo $this->render();
	}

	/**
	 * Send the response headers to the browser.
	 *
	 * @return void
	 */
	public function send_headers()
	{
		$protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';

		header($protocol.' '.$this->status.' '.$this->statuses[$this->status]);

		foreach ($this->headers as $name => $value)
		{	
			header($name.': '.$value, true);
		}
	}

	/**
	 * Add a header to the response.
	 *
	 * <code>
	 *		// Add a "location" header to a response
	 *		$response->header('Location', 'http://google.com');
	 * </code>
	 *
	 * @param  string    $name
	 * @param  string    $value
	 * @return Response
	 */
	public function header($name, $value)
	{
		$this->headers[$name] = $value;
		return $this;
	}

}