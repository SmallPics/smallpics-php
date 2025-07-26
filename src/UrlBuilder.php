<?php

namespace smallpics\smallpics;

class UrlBuilder
{
	/**
	 * @param string $host The host to use for the URL
	 */
	public function __construct(
		protected string $host,
		protected ?string $key = null,
		protected ?string $salt = null,
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

		$unsignedUrl = "https://{$this->host}/{$sourceUrl}?{$optionsString}";

		if ($this->key === null || $this->salt === null) {
			return $unsignedUrl;
		}

		return $unsignedUrl . '&signature=' . $this->generateSignature($unsignedUrl);
	}

	/**
	 * Generate signature for the given path
	 */
	protected function generateSignature(string $path): string
	{
		$keyBin = pack('H*', $this->key);
		$saltBin = pack('H*', $this->salt);

		$signature = hash_hmac('sha256', $saltBin . $path, $keyBin, true);

		$signature = base64_encode($signature);
		// Make it neat
		$signature = str_replace(['+', '/', '='], '', $signature);

		return $signature;
	}
}
