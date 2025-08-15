<?php

namespace smallpics\smallpics;

class UrlBuilder
{
	/**
	 * @var ?string
	 */
	public const TRANSFORM_PATH_PREFIX = 't';

	/**
	 * @param string $host The host to use for the URL
	 */
	public function __construct(
		protected string $host,
		protected ?string $secret = null,
		protected ?string $transformPathPrefix = self::TRANSFORM_PATH_PREFIX,
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

		$transformPath = $this->transformPathPrefix !== null && $this->transformPathPrefix !== '' ? "/{$this->transformPathPrefix}" : '';
		$unsignedUrl = "{$this->host}{$transformPath}/{$sourceUrl}?{$optionsString}";

		if ($this->secret === null) {
			return $unsignedUrl;
		}

		return $unsignedUrl . '&signature=' . $this->generateSignature($unsignedUrl);
	}

	/**
	 * Generate signature for the given path
	 */
	protected function generateSignature(string $path): string
	{
		// We always want to generate a signature for a path that exludes the signature param and value
		$path = preg_replace('/&?signature=[^&]*/', '', $path);

		if ($path === null || $this->secret === null) {
			return '';
		}

		$signature = hash_hmac('sha256', $path, $this->secret, true);
		$signature = base64_encode($signature);
		// Make it neat
		$signature = str_replace(['+', '/', '='], '', $signature);

		return $signature;
	}
}
