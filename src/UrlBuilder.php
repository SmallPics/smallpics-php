<?php

namespace smallpics\smallpics;

class UrlBuilder
{
	/**
	 * @param string $host The host to use for the URL
	 */
	public function __construct(
		protected string $host,
		protected ?string $secret = null,
	) {
	}

	/**
	 * Build the URL for processing an image
	 *
	 * @param string $sourceUrl The source image URL
	 * @param Options|null $options Processing options
	 * @return string The complete image transform URL
	 */
	public function buildUrl(string $sourceUrl, ?Options $options = null): string
	{
		$sourceUrl = trim($sourceUrl, '/');
		$options ??= new Options();
		$optionsString = (string) $options;

		if ($optionsString !== '') {
			$optionsString = "?{$optionsString}";
		}

		$unsignedUrl = $this->normalizeUrl("{$this->host}/{$sourceUrl}{$optionsString}");

		if ($this->secret === null) {
			return $unsignedUrl;
		}

		$signature = $this->generateSignature($unsignedUrl);

		$signature = $optionsString === '' ? "?signature={$signature}" : "&signature={$signature}";

		return "{$unsignedUrl}{$signature}";
	}

	protected function normalizeUrl(string $url): string
	{
		/** @var array{scheme:string,host:string,path:null|string,query:null|string} $parts */
		$parts = parse_url($url);
		$path = $parts['path'] ?? '';
		$query = $parts['query'] ?? '';
		$params = [];
		parse_str($query, $params);

		unset($params['signature']);

		$flattened = [];
		foreach ($params as $key => $value) {
			if (is_int($key) && is_array($value)) {
				$lastKey = array_key_last($value);
				$flattened[$lastKey] = $value[$lastKey];
			} else {
				$flattened[$key] = $value;
			}
		}

		$params = array_change_key_case($flattened, CASE_LOWER);
		ksort($params);

		$query = '';
		if ($params !== []) {
			$query = '?' . urldecode(http_build_query($params));
		}

		return "{$parts['scheme']}://{$parts['host']}{$path}$query";
	}

	/**
	 * Generate a signature for the given path
	 */
	protected function generateSignature(string $path): string
	{
		$path = urldecode($path);

		// At this point secret would be set.
		/** @var string $secret */
		$secret = $this->secret;

		$signature = hash_hmac('sha256', $path, $secret, true);
		$signature = base64_encode($signature);
		// Make it neat
		return str_replace(['+', '/', '='], '', $signature);
	}
}
