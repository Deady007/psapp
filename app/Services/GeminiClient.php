<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class GeminiClient
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $model,
        private readonly string $chunkModel,
        private readonly string $mergeModel,
        private readonly string $refineModel,
        private readonly string $heavyModel,
        private readonly int $chunkSize,
        private readonly int $maxChunks,
        private readonly int $refinePasses,
        private readonly int $requirementsOutputTokens,
        private readonly int $mergeOutputTokens,
        private readonly int $heavyOutputTokens,
        private readonly int $singlePassMaxChars,
        private readonly int $heavyMinChars,
        private readonly int $heavyMinRequirements,
        private readonly string $endpoint,
        private readonly int $timeoutSeconds,
        private readonly bool|string $verify,
    ) {}

    /**
     * @param  array<string, string>  $context
     * @return array<int, array<string, string|null>>
     */
    public function extractRequirementsFromTranscript(
        string $transcript,
        array $context = [],
        string $analysisMode = 'fast'
    ): array {
        $transcript = trim($transcript);

        if ($transcript === '') {
            return [];
        }

        $profile = $this->analysisProfile($analysisMode);
        $contextBlock = $this->buildContextBlock($context);
        $transcriptLength = Str::length($transcript);
        $chunkSize = $profile['chunkSize'];
        $maxChunks = max(1, $profile['maxChunks']);
        $singlePassMaxChars = $profile['singlePassMaxChars'];

        if ($singlePassMaxChars > 0 && $transcriptLength <= $singlePassMaxChars) {
            $payload = $this->extractFromTranscript(
                $transcript,
                $contextBlock,
                $profile['primaryModel'],
                $profile['timeoutSeconds'],
                $profile['requirementsOutputTokens']
            );

            return $this->dedupeRequirements($this->normalizeRequirements($payload));
        }

        if ($transcriptLength > $chunkSize) {
            $chunkSize = max($chunkSize, (int) ceil($transcriptLength / $maxChunks));
        }

        if ($transcriptLength <= $chunkSize) {
            $payload = $this->extractFromTranscript(
                $transcript,
                $contextBlock,
                $profile['primaryModel'],
                $profile['timeoutSeconds'],
                $profile['requirementsOutputTokens']
            );

            return $this->dedupeRequirements($this->normalizeRequirements($payload));
        }

        $chunks = $this->splitTranscript($transcript, $chunkSize);
        if (count($chunks) <= 1) {
            $payload = $this->extractFromTranscript(
                $transcript,
                $contextBlock,
                $profile['primaryModel'],
                $profile['timeoutSeconds'],
                $profile['requirementsOutputTokens']
            );

            return $this->dedupeRequirements($this->normalizeRequirements($payload));
        }

        $drafts = [];

        foreach ($chunks as $chunk) {
            $drafts = array_merge(
                $drafts,
                $this->extractFromChunk(
                    $chunk,
                    $contextBlock,
                    $profile['chunkModel'],
                    $profile['timeoutSeconds'],
                    $profile['requirementsOutputTokens']
                )
            );
        }

        $drafts = $this->normalizeRequirements($drafts);
        $drafts = $this->dedupeRequirements($drafts);

        $passes = max(0, $profile['refinePasses']);
        $refined = $drafts;

        for ($pass = 0; $pass < $passes; $pass++) {
            $model = $pass === 0 ? $profile['mergeModel'] : $profile['refineModel'];
            $refined = $this->refineRequirements(
                $refined,
                $contextBlock,
                $model,
                false,
                $profile['timeoutSeconds'],
                $profile['mergeOutputTokens']
            );
        }

        if ($this->shouldUseHeavyModel($transcript, $refined, $profile)) {
            $refined = $this->refineRequirements(
                $refined,
                $contextBlock,
                $profile['heavyModel'],
                true,
                $profile['heavyTimeoutSeconds'],
                $profile['heavyOutputTokens']
            );
        }

        return $this->dedupeRequirements($this->normalizeRequirements($refined));
    }

    /**
     * @param  array<int, array<string, string|null>>  $requirements
     * @param  array<string, string>  $context
     * @return array{
     *   introduction: array{purpose: string, scope: string, overview: string},
     *   system_overview: string,
     *   non_functional: array{performance: string, security: string, availability: string, compliance: string},
     *   technical_requirements: string,
     *   user_interface: array<int, string>,
     *   data_requirements: array{storage: string, backup: string, data_privacy: string},
     *   assumptions: array<int, string>,
     *   acceptance_criteria: array<int, array{criterion: string, validation_method: string}>,
     *   appendices: array<int, string>
     * }
     */
    public function generateRfpSections(array $requirements, array $context = []): array
    {
        $requirements = $this->normalizeRequirements($requirements);
        $payload = json_encode($requirements);

        if (! is_string($payload)) {
            throw new RuntimeException('Unable to encode requirements for Gemini.');
        }

        $prompt = $this->rfpPrompt();
        $contextBlock = $this->buildContextBlock($context);

        if ($contextBlock !== '') {
            $prompt .= "\n\n".$contextBlock;
        }

        $prompt .= "\n\nRequirements:\n".$payload;

        $text = $this->generateText(
            prompt: $prompt,
            maxOutputTokens: 2048,
            temperature: 0.2,
            model: $this->model,
            timeoutSeconds: $this->timeoutSeconds
        );

        $decoded = $this->decodeJson($text);

        if (! is_array($decoded)) {
            throw new RuntimeException('Gemini response did not include RFP sections.');
        }

        return $this->normalizeRfpSections($decoded);
    }

    /**
     * @return array{
     *   primaryModel: string,
     *   chunkModel: string,
     *   mergeModel: string,
     *   refineModel: string,
     *   heavyModel: string,
     *   chunkSize: int,
     *   maxChunks: int,
     *   refinePasses: int,
     *   requirementsOutputTokens: int,
     *   mergeOutputTokens: int,
     *   heavyOutputTokens: int,
     *   singlePassMaxChars: int,
     *   timeoutSeconds: int,
     *   heavyTimeoutSeconds: int,
     *   allowHeavy: bool,
     *   heavyMinChars: int,
     *   heavyMinRequirements: int
     * }
     */
    private function analysisProfile(string $analysisMode): array
    {
        $mode = Str::lower(trim($analysisMode)) === 'deep' ? 'deep' : 'fast';
        $fallbackModel = $this->model;
        $chunkModel = $this->chunkModel !== '' ? $this->chunkModel : $fallbackModel;
        $mergeModel = $this->mergeModel !== '' ? $this->mergeModel : $fallbackModel;
        $refineModel = $this->refineModel !== '' ? $this->refineModel : $fallbackModel;
        $heavyModel = $this->heavyModel;
        $requirementsOutputTokens = max(2048, $this->requirementsOutputTokens);
        $mergeOutputTokens = max($requirementsOutputTokens, $this->mergeOutputTokens);
        $heavyOutputTokens = max($mergeOutputTokens, $this->heavyOutputTokens);
        $singlePassMaxChars = max(0, $this->singlePassMaxChars);

        if ($mode === 'fast') {
            $timeoutSeconds = max(45, $this->timeoutSeconds);

            return [
                'primaryModel' => $chunkModel,
                'chunkModel' => $chunkModel,
                'mergeModel' => $mergeModel,
                'refineModel' => $refineModel,
                'heavyModel' => '',
                'chunkSize' => max(12000, $this->chunkSize),
                'maxChunks' => max(1, min(2, $this->maxChunks)),
                'refinePasses' => 0,
                'requirementsOutputTokens' => $requirementsOutputTokens,
                'mergeOutputTokens' => $mergeOutputTokens,
                'heavyOutputTokens' => $heavyOutputTokens,
                'singlePassMaxChars' => $singlePassMaxChars,
                'timeoutSeconds' => $timeoutSeconds,
                'heavyTimeoutSeconds' => $timeoutSeconds,
                'allowHeavy' => false,
                'heavyMinChars' => PHP_INT_MAX,
                'heavyMinRequirements' => PHP_INT_MAX,
            ];
        }

        $timeoutSeconds = max(90, $this->timeoutSeconds);

        return [
            'primaryModel' => $fallbackModel,
            'chunkModel' => $refineModel,
            'mergeModel' => $mergeModel,
            'refineModel' => $refineModel,
            'heavyModel' => $heavyModel,
            'chunkSize' => min($this->chunkSize, 8000),
            'maxChunks' => max(3, $this->maxChunks),
            'refinePasses' => max(1, $this->refinePasses),
            'requirementsOutputTokens' => $requirementsOutputTokens,
            'mergeOutputTokens' => $mergeOutputTokens,
            'heavyOutputTokens' => $heavyOutputTokens,
            'singlePassMaxChars' => $singlePassMaxChars,
            'timeoutSeconds' => $timeoutSeconds,
            'heavyTimeoutSeconds' => max(120, $timeoutSeconds),
            'allowHeavy' => $heavyModel !== '',
            'heavyMinChars' => $this->heavyMinChars,
            'heavyMinRequirements' => $this->heavyMinRequirements,
        ];
    }

    private function extractFromTranscript(
        string $transcript,
        string $contextBlock,
        string $model,
        int $timeoutSeconds,
        int $maxOutputTokens
    ): array {
        $prompt = $this->baseExtractionPrompt();

        if ($contextBlock !== '') {
            $prompt .= "\n\n".$contextBlock;
        }

        $prompt .= "\n\nTranscript:\n".$transcript;

        $text = $this->generateText(
            prompt: $prompt,
            maxOutputTokens: $maxOutputTokens,
            temperature: 0.2,
            model: $model,
            timeoutSeconds: $timeoutSeconds,
        );

        $payload = $this->decodeJson($text);

        if (is_array($payload) && array_key_exists('requirements', $payload)) {
            $payload = $payload['requirements'];
        }

        if (! is_array($payload)) {
            throw new RuntimeException('Gemini response did not include requirements.');
        }

        return $payload;
    }

    private function extractFromChunk(
        string $chunk,
        string $contextBlock,
        string $model,
        int $timeoutSeconds,
        int $maxOutputTokens
    ): array {
        $prompt = $this->chunkExtractionPrompt();

        if ($contextBlock !== '') {
            $prompt .= "\n\n".$contextBlock;
        }

        $prompt .= "\n\nChunk:\n".$chunk;

        $text = $this->generateText(
            prompt: $prompt,
            maxOutputTokens: $maxOutputTokens,
            temperature: 0.2,
            model: $model,
            timeoutSeconds: $timeoutSeconds,
        );

        $payload = $this->decodeJson($text);

        if (is_array($payload) && array_key_exists('requirements', $payload)) {
            $payload = $payload['requirements'];
        }

        if (! is_array($payload)) {
            throw new RuntimeException('Gemini response did not include requirements.');
        }

        return $payload;
    }

    private function refineRequirements(
        array $requirements,
        string $contextBlock,
        string $model,
        bool $heavy,
        int $timeoutSeconds,
        int $maxOutputTokens
    ): array {
        if ($requirements === []) {
            return [];
        }

        $payload = json_encode($requirements);

        if (! is_string($payload)) {
            throw new RuntimeException('Unable to encode requirements for Gemini.');
        }

        $prompt = $this->refinePrompt($heavy);

        if ($contextBlock !== '') {
            $prompt .= "\n\n".$contextBlock;
        }

        $prompt .= "\n\nRequirement drafts:\n".$payload;

        $text = $this->generateText(
            prompt: $prompt,
            maxOutputTokens: $maxOutputTokens,
            temperature: $heavy ? 0.2 : 0.15,
            model: $model,
            timeoutSeconds: $timeoutSeconds,
        );

        $decoded = $this->decodeJson($text);

        if (is_array($decoded) && array_key_exists('requirements', $decoded)) {
            $decoded = $decoded['requirements'];
        }

        if (! is_array($decoded)) {
            throw new RuntimeException('Gemini response did not include requirements.');
        }

        return $decoded;
    }

    /**
     * @return array<int, string>
     */
    private function splitTranscript(string $transcript, int $chunkSize): array
    {
        $paragraphs = preg_split("/\R{2,}/", $transcript) ?: [];
        $chunks = [];
        $buffer = '';

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);

            if ($paragraph === '') {
                continue;
            }

            if (Str::length($paragraph) > $chunkSize) {
                $sentences = preg_split('/(?<=[.!?])\s+/', $paragraph) ?: [$paragraph];

                foreach ($sentences as $sentence) {
                    $sentence = trim($sentence);

                    if ($sentence === '') {
                        continue;
                    }

                    $candidate = $buffer === '' ? $sentence : $buffer."\n".$sentence;

                    if (Str::length($candidate) > $chunkSize) {
                        if ($buffer !== '') {
                            $chunks[] = $buffer;
                        }

                        $buffer = $sentence;

                        continue;
                    }

                    $buffer = $candidate;
                }

                continue;
            }

            $candidate = $buffer === '' ? $paragraph : $buffer."\n\n".$paragraph;

            if (Str::length($candidate) > $chunkSize) {
                if ($buffer !== '') {
                    $chunks[] = $buffer;
                }

                $buffer = $paragraph;

                continue;
            }

            $buffer = $candidate;
        }

        if ($buffer !== '') {
            $chunks[] = $buffer;
        }

        return $chunks === [] ? [$transcript] : $chunks;
    }

    private function buildContextBlock(array $context): string
    {
        $lines = [];

        foreach ($context as $label => $value) {
            $value = trim((string) $value);

            if ($value === '') {
                continue;
            }

            $lines[] = sprintf('%s: %s', $label, $value);
        }

        if ($lines === []) {
            return '';
        }

        return "Context:\n".implode("\n", $lines);
    }

    private function baseExtractionPrompt(): string
    {
        return <<<'PROMPT'
Extract software requirements from the transcript below. Return only a JSON array. Each item must include:
- module_name
- page_name
- title
- details
- priority (low|medium|high)
- status (todo|in_progress|done)

Guidelines:
- Capture actionable requirements only.
- Split compound statements into separate items.
- Keep titles concise and specific.
- Use module_name for the functional area and page_name for the screen/subpage.
- If a field is unknown, use null. Do not invent IDs or dates.
- Remove duplicates and near-duplicates.
- If more than 100 requirements exist, include them all and keep each entry short.
- Keep titles under 12 words and details to one short sentence (or null).
PROMPT;
    }

    private function chunkExtractionPrompt(): string
    {
        return <<<'PROMPT'
Extract software requirements from the transcript chunk below. Return only a JSON array. Each item must include:
- module_name
- page_name
- title
- details
- priority (low|medium|high)
- status (todo|in_progress|done)

Guidelines:
- Only use information from this chunk.
- Capture actionable requirements only.
- Split compound statements into separate items.
- Keep titles concise and specific.
- Use module_name for the functional area and page_name for the screen/subpage.
- If a field is unknown, use null. Do not invent IDs or dates.
- Avoid duplicates within the chunk.
- If more than 100 requirements exist, include them all and keep each entry short.
- Keep titles under 12 words and details to one short sentence (or null).
PROMPT;
    }

    private function refinePrompt(bool $heavy): string
    {
        if ($heavy) {
            return <<<'PROMPT'
You are consolidating requirement drafts extracted from transcript chunks. Return only a JSON array with:
- module_name
- page_name
- title
- details
- priority (low|medium|high)
- status (todo|in_progress|done)

Guidelines:
- Merge duplicates and near-duplicates into a single requirement.
- Prefer the most specific title and combine complementary details.
- Normalize module_name and page_name for consistency.
- Drop items that are not requirements.
- If a field is unknown, use null. Do not invent IDs or dates.
- Keep each entry concise and do not drop valid requirements to reduce length.
PROMPT;
        }

        return <<<'PROMPT'
Consolidate the requirement drafts below. Return only a JSON array with:
- module_name
- page_name
- title
- details
- priority (low|medium|high)
- status (todo|in_progress|done)

Guidelines:
- Merge duplicates and near-duplicates into a single requirement.
- Prefer the most specific title and keep concise details.
- Normalize module_name and page_name for consistency.
- Drop items that are not requirements.
- Keep each entry concise and do not drop valid requirements to reduce length.
PROMPT;
    }

    /**
     * @param  array{
     *   heavyModel: string,
     *   allowHeavy: bool,
     *   heavyMinChars: int,
     *   heavyMinRequirements: int
     * }  $profile
     */
    private function shouldUseHeavyModel(string $transcript, array $requirements, array $profile): bool
    {
        if (! $profile['allowHeavy']) {
            return false;
        }

        if (($profile['heavyModel'] ?? '') === '') {
            return false;
        }

        if (Str::length($transcript) < $profile['heavyMinChars']) {
            return false;
        }

        return count($requirements) >= $profile['heavyMinRequirements'];
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private function dedupeRequirements(array $requirements): array
    {
        $seen = [];
        $unique = [];

        foreach ($requirements as $requirement) {
            if (! is_array($requirement)) {
                continue;
            }

            $module = strtolower(trim((string) ($requirement['module_name'] ?? '')));
            $title = strtolower(trim((string) ($requirement['title'] ?? '')));

            if ($module === '' && $title === '') {
                continue;
            }

            $key = $module.'|'.$title;

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $unique[] = $requirement;
        }

        return $unique;
    }

    private function generateText(
        string $prompt,
        int $maxOutputTokens,
        float $temperature,
        ?string $model = null,
        ?int $timeoutSeconds = null
    ): string {
        $apiKey = trim($this->apiKey);

        if ($apiKey === '') {
            throw new RuntimeException('Gemini API key is not configured.');
        }

        $endpoint = rtrim($this->endpoint, '/');
        $modelName = $model ?: $this->model;
        $url = sprintf('%s/models/%s:generateContent?key=%s', $endpoint, $modelName, $apiKey);

        $timeout = $timeoutSeconds ?? $this->timeoutSeconds;

        $response = Http::timeout($timeout)
            ->acceptJson()
            ->asJson()
            ->withOptions(['verify' => $this->sslVerifyOption()])
            ->post($url, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => $temperature,
                    'maxOutputTokens' => $maxOutputTokens,
                ],
            ]);

        if (! $response->successful()) {
            $message = $response->json('error.message') ?? 'Gemini request failed.';

            throw new RuntimeException($message);
        }

        $text = $response->json('candidates.0.content.parts.0.text');

        if (! is_string($text) || trim($text) === '') {
            throw new RuntimeException('Gemini response did not include any text.');
        }

        return trim($text);
    }

    private function sslVerifyOption(): bool|string
    {
        $value = $this->verify;

        if (is_string($value)) {
            $normalized = strtolower($value);
            if (in_array($normalized, ['0', 'false', 'off', 'no'], true)) {
                return false;
            }

            if (is_file($value)) {
                return $value;
            }

            $resolved = base_path($value);
            if (is_file($resolved)) {
                return $resolved;
            }

            return true;
        }

        return $value;
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private function normalizeRequirements(array $requirements): array
    {
        return collect($requirements)
            ->filter(fn ($item) => is_array($item))
            ->map(function (array $item) {
                $moduleName = trim((string) ($item['module_name'] ?? ''));
                $pageName = trim((string) ($item['page_name'] ?? ''));
                $title = trim((string) ($item['title'] ?? ''));
                $details = trim((string) ($item['details'] ?? ''));
                $priority = $this->normalizePriority((string) ($item['priority'] ?? ''));
                $status = $this->normalizeStatus((string) ($item['status'] ?? ''));

                return [
                    'module_name' => $moduleName !== '' ? Str::limit($moduleName, 255, '') : null,
                    'page_name' => $pageName !== '' ? Str::limit($pageName, 255, '') : null,
                    'title' => $title !== '' ? Str::limit($title, 255, '') : null,
                    'details' => $details !== '' ? $details : null,
                    'priority' => $priority,
                    'status' => $status,
                ];
            })
            ->filter(fn (array $item) => $item['module_name'] !== null || $item['title'] !== null)
            ->values()
            ->all();
    }

    private function normalizePriority(string $priority): string
    {
        $value = strtolower(trim($priority));
        $map = [
            'critical' => 'high',
            'urgent' => 'high',
            'high' => 'high',
            'medium' => 'medium',
            'med' => 'medium',
            'low' => 'low',
        ];

        return $map[$value] ?? 'medium';
    }

    private function normalizeStatus(string $status): string
    {
        $value = strtolower(trim($status));
        $value = str_replace([' ', '-'], '_', $value);
        $map = [
            'todo' => 'todo',
            'to_do' => 'todo',
            'backlog' => 'todo',
            'in_progress' => 'in_progress',
            'inprogress' => 'in_progress',
            'progress' => 'in_progress',
            'done' => 'done',
            'completed' => 'done',
            'complete' => 'done',
        ];

        return $map[$value] ?? 'todo';
    }

    /**
     * @return array{
     *   introduction: array{purpose: string, scope: string, overview: string},
     *   system_overview: string,
     *   non_functional: array{performance: string, security: string, availability: string, compliance: string},
     *   technical_requirements: string,
     *   user_interface: array<int, string>,
     *   data_requirements: array{storage: string, backup: string, data_privacy: string},
     *   assumptions: array<int, string>,
     *   acceptance_criteria: array<int, array{criterion: string, validation_method: string}>,
     *   appendices: array<int, string>
     * }
     */
    private function normalizeRfpSections(array $payload): array
    {
        return [
            'introduction' => [
                'purpose' => $this->normalizeRfpString($payload['introduction']['purpose'] ?? ''),
                'scope' => $this->normalizeRfpString($payload['introduction']['scope'] ?? ''),
                'overview' => $this->normalizeRfpString($payload['introduction']['overview'] ?? ''),
            ],
            'system_overview' => $this->normalizeRfpString($payload['system_overview'] ?? ''),
            'non_functional' => [
                'performance' => $this->normalizeRfpString($payload['non_functional']['performance'] ?? ''),
                'security' => $this->normalizeRfpString($payload['non_functional']['security'] ?? ''),
                'availability' => $this->normalizeRfpString($payload['non_functional']['availability'] ?? ''),
                'compliance' => $this->normalizeRfpString($payload['non_functional']['compliance'] ?? ''),
            ],
            'technical_requirements' => $this->normalizeRfpString($payload['technical_requirements'] ?? ''),
            'user_interface' => $this->normalizeRfpList($payload['user_interface'] ?? []),
            'data_requirements' => [
                'storage' => $this->normalizeRfpString($payload['data_requirements']['storage'] ?? ''),
                'backup' => $this->normalizeRfpString($payload['data_requirements']['backup'] ?? ''),
                'data_privacy' => $this->normalizeRfpString($payload['data_requirements']['data_privacy'] ?? ''),
            ],
            'assumptions' => $this->normalizeRfpList($payload['assumptions'] ?? []),
            'acceptance_criteria' => $this->normalizeAcceptanceCriteria($payload['acceptance_criteria'] ?? []),
            'appendices' => $this->normalizeRfpList($payload['appendices'] ?? []),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function normalizeRfpList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->map(fn ($item) => $this->normalizeRfpString($item))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{criterion: string, validation_method: string}>
     */
    private function normalizeAcceptanceCriteria(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->filter(fn ($item) => is_array($item))
            ->map(function (array $item) {
                $criterion = $this->normalizeRfpString($item['criterion'] ?? '');
                $method = $this->normalizeRfpString($item['validation_method'] ?? '');

                return [
                    'criterion' => $criterion,
                    'validation_method' => $method,
                ];
            })
            ->filter(fn (array $item) => $item['criterion'] !== '' && $item['validation_method'] !== '')
            ->values()
            ->all();
    }

    private function normalizeRfpString(mixed $value): string
    {
        return trim((string) $value);
    }

    private function rfpPrompt(): string
    {
        return <<<'PROMPT'
Create a requirements documentation outline for the project. Return only JSON with these keys:
- introduction { purpose, scope, overview }
- system_overview
- non_functional { performance, security, availability, compliance }
- technical_requirements
- user_interface (array of 8 short lines)
- data_requirements { storage, backup, data_privacy }
- assumptions (array of 3 short lines)
- acceptance_criteria (array of 3 objects with criterion and validation_method)
- appendices (array of 3 lines)

Guidelines:
- Use concise, professional sentences.
- Use ASCII characters only.
- Do not include markdown or code fences.
PROMPT;
    }

    /**
     * @return array<mixed>
     */
    private function decodeJson(string $text): array
    {
        $clean = $this->sanitizeJsonPayload($text);
        $decoded = json_decode($clean, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/(\{[\s\S]*\}|\[[\s\S]*\])/', $clean, $matches) !== 1) {
            throw $this->invalidJsonException($text);
        }

        $candidate = $this->sanitizeJsonPayload($matches[0]);
        $decoded = json_decode($candidate, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            throw $this->invalidJsonException($text);
        }

        return $decoded;
    }

    private function sanitizeJsonPayload(string $text): string
    {
        $clean = trim($text);
        $clean = preg_replace('/^```(?:json)?\s*|```$/m', '', $clean) ?? $clean;
        $clean = str_replace(
            ["\u{2018}", "\u{2019}", "\u{201C}", "\u{201D}"],
            ["'", "'", '"', '"'],
            $clean
        );
        $clean = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $clean);
        $clean = preg_replace('/,\s*(?=[}\]])/', '', $clean) ?? $clean;

        return trim($clean);
    }

    private function invalidJsonException(string $text): RuntimeException
    {
        $preview = preg_replace('/\s+/', ' ', trim($text)) ?? '';
        $preview = Str::limit($preview, 500, '...');

        return new RuntimeException(sprintf('Unable to parse Gemini response. %s', $preview));
    }
}
