<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectRequirement;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use ZipArchive;

class RfpDocumentBuilder
{
    /**
     * @param  array<int, ProjectRequirement>  $requirements
     * @param  array{
     *   introduction: array{purpose: string, scope: string, overview: string},
     *   system_overview: string,
     *   non_functional: array{performance: string, security: string, availability: string, compliance: string},
     *   technical_requirements: string,
     *   user_interface: array<int, string>,
     *   data_requirements: array{storage: string, backup: string, data_privacy: string},
     *   assumptions: array<int, string>,
     *   acceptance_criteria: array<int, array{criterion: string, validation_method: string}>,
     *   appendices: array<int, string>
     * }  $sections
     */
    public function build(Project $project, array $requirements, array $sections, string $outputPath): string
    {
        $templatePath = $this->templatePath();

        if (! is_file($templatePath)) {
            throw new RuntimeException('RFP template file is missing.');
        }

        $templateContents = file_get_contents($templatePath);

        if ($templateContents === false) {
            throw new RuntimeException('Unable to read the RFP template file.');
        }

        $disk = Storage::disk('local');
        $disk->put($outputPath, $templateContents);

        $absolutePath = $disk->path($outputPath);

        $zip = new ZipArchive;

        if ($zip->open($absolutePath) !== true) {
            throw new RuntimeException('Unable to open the RFP template.');
        }

        $xml = $zip->getFromName('word/document.xml');

        if (! is_string($xml)) {
            throw new RuntimeException('RFP template is missing the document layout.');
        }

        $document = new DOMDocument;
        $document->preserveWhiteSpace = false;
        $document->loadXML($xml);

        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('w', $this->wordNamespace());

        $sections = $this->normalizeSections($project, $sections);

        $this->replaceParagraphsAfterHeading($xpath, '1. Introduction', [
            sprintf('Purpose: %s', $sections['introduction']['purpose']),
            sprintf('Scope: %s', $sections['introduction']['scope']),
            sprintf('Overview: %s', $sections['introduction']['overview']),
        ], 3);

        $this->replaceParagraphsAfterHeading($xpath, '2. System Overview', [
            $sections['system_overview'],
        ], 1);

        $this->replaceParagraphsAfterHeading($xpath, '4. Non-Functional Requirements', [
            sprintf('Performance: %s', $sections['non_functional']['performance']),
            sprintf('Security: %s', $sections['non_functional']['security']),
            sprintf('Availability: %s', $sections['non_functional']['availability']),
            sprintf('Compliance: %s', $sections['non_functional']['compliance']),
        ], 4);

        $this->replaceParagraphsAfterHeading($xpath, '5. Technical Requirements', [
            $sections['technical_requirements'],
        ], 1);

        $this->replaceParagraphsAfterHeading($xpath, '6. User Interface Requirements', $sections['user_interface'], 8);

        $this->replaceParagraphsAfterHeading($xpath, '7. Data Requirements', [
            sprintf('Storage: %s', $sections['data_requirements']['storage']),
            sprintf('Backup: %s', $sections['data_requirements']['backup']),
            sprintf('Data Privacy: %s', $sections['data_requirements']['data_privacy']),
        ], 3);

        $this->replaceParagraphsAfterHeading($xpath, '8. Assumptions and Constraints', $sections['assumptions'], 3);
        $this->replaceParagraphsAfterHeading($xpath, '10. Appendices', $sections['appendices'], 3);

        $this->updateFunctionalRequirementsTable($xpath, $requirements);
        $this->updateAcceptanceCriteriaTable($xpath, $sections['acceptance_criteria']);

        $updatedXml = $document->saveXML();

        if (! is_string($updatedXml)) {
            throw new RuntimeException('Unable to update the RFP document.');
        }

        $zip->addFromString('word/document.xml', $updatedXml);
        $zip->close();

        return $outputPath;
    }

    /**
     * @param  array{
     *   introduction?: array{purpose?: mixed, scope?: mixed, overview?: mixed},
     *   system_overview?: mixed,
     *   non_functional?: array{performance?: mixed, security?: mixed, availability?: mixed, compliance?: mixed},
     *   technical_requirements?: mixed,
     *   user_interface?: mixed,
     *   data_requirements?: array{storage?: mixed, backup?: mixed, data_privacy?: mixed},
     *   assumptions?: mixed,
     *   acceptance_criteria?: mixed,
     *   appendices?: mixed
     * }  $sections
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
    private function normalizeSections(Project $project, array $sections): array
    {
        $defaults = $this->defaultSections($project);

        return [
            'introduction' => [
                'purpose' => $this->normalizeText($sections['introduction']['purpose'] ?? null, $defaults['introduction']['purpose']),
                'scope' => $this->normalizeText($sections['introduction']['scope'] ?? null, $defaults['introduction']['scope']),
                'overview' => $this->normalizeText($sections['introduction']['overview'] ?? null, $defaults['introduction']['overview']),
            ],
            'system_overview' => $this->normalizeText($sections['system_overview'] ?? null, $defaults['system_overview']),
            'non_functional' => [
                'performance' => $this->normalizeText($sections['non_functional']['performance'] ?? null, $defaults['non_functional']['performance']),
                'security' => $this->normalizeText($sections['non_functional']['security'] ?? null, $defaults['non_functional']['security']),
                'availability' => $this->normalizeText($sections['non_functional']['availability'] ?? null, $defaults['non_functional']['availability']),
                'compliance' => $this->normalizeText($sections['non_functional']['compliance'] ?? null, $defaults['non_functional']['compliance']),
            ],
            'technical_requirements' => $this->normalizeText($sections['technical_requirements'] ?? null, $defaults['technical_requirements']),
            'user_interface' => $this->normalizeList($sections['user_interface'] ?? null, $defaults['user_interface'], 8),
            'data_requirements' => [
                'storage' => $this->normalizeText($sections['data_requirements']['storage'] ?? null, $defaults['data_requirements']['storage']),
                'backup' => $this->normalizeText($sections['data_requirements']['backup'] ?? null, $defaults['data_requirements']['backup']),
                'data_privacy' => $this->normalizeText($sections['data_requirements']['data_privacy'] ?? null, $defaults['data_requirements']['data_privacy']),
            ],
            'assumptions' => $this->normalizeList($sections['assumptions'] ?? null, $defaults['assumptions'], 3),
            'acceptance_criteria' => $this->normalizeAcceptanceCriteria($sections['acceptance_criteria'] ?? null, $defaults['acceptance_criteria']),
            'appendices' => $this->normalizeList($sections['appendices'] ?? null, $defaults['appendices'], 3),
        ];
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
    private function defaultSections(Project $project): array
    {
        $projectName = trim((string) $project->name);
        $projectLabel = $projectName !== '' ? $projectName : 'the project';
        $description = trim((string) $project->description);

        return [
            'introduction' => [
                'purpose' => sprintf('This document defines the requirements for %s.', $projectLabel),
                'scope' => $description !== ''
                    ? sprintf('The system will deliver %s.', $description)
                    : 'The system will support core workflows, reporting, and stakeholder visibility.',
                'overview' => 'The system will be delivered as a secure, cloud-hosted web application with optional mobile access.',
            ],
            'system_overview' => 'The system includes a web portal and a mobile experience for project stakeholders.',
            'non_functional' => [
                'performance' => 'Support concurrent users with responsive page loads.',
                'security' => 'Enforce role-based access and follow OWASP guidelines.',
                'availability' => 'Target 99.9% uptime with monitored infrastructure.',
                'compliance' => 'N/A.',
            ],
            'technical_requirements' => 'N/A.',
            'user_interface' => [
                'The system will include custom web pages and mobile screens for core workflows.',
                'Projects (mobile) - View projects the user can access.',
                'For each project, users can',
                'upload images and documents',
                'submit quotes and purchase orders',
                'view and add daily reports',
                'Purchase Orders (mobile) - Create, edit, and view purchase orders',
                'Free text input for line items',
            ],
            'data_requirements' => [
                'storage' => 'Store project data in a secure relational database.',
                'backup' => 'Perform weekly backups with a 30-day retention window.',
                'data_privacy' => 'Data is never shared outside the client organization.',
            ],
            'assumptions' => [
                'Users include both site staff and office staff.',
                'Site managers primarily use mobile devices, while office staff use the web portal.',
                'Uploads are associated with projects.',
            ],
            'acceptance_criteria' => [
                ['criterion' => 'Web application access roles', 'validation_method' => 'UAT'],
                ['criterion' => 'Mobile application screens', 'validation_method' => 'UAT'],
                ['criterion' => 'Report exports are generated successfully', 'validation_method' => 'UAT'],
            ],
            'appendices' => [
                'Glossary: PM - Project Management, API - Application Programming Interface.',
                'Supporting Documents: System Landscape Diagram.',
                'System Landscape Diagram',
            ],
        ];
    }

    /**
     * @param  array<int, DOMElement>  $paragraphs
     */
    private function findParagraphIndex(array $paragraphs, string $heading): int
    {
        foreach ($paragraphs as $index => $paragraph) {
            if ($this->paragraphText($paragraph) === $heading) {
                return $index;
            }
        }

        throw new RuntimeException(sprintf('Unable to find section "%s" in the RFP template.', $heading));
    }

    /**
     * @return array<int, DOMElement>
     */
    private function paragraphs(DOMXPath $xpath): array
    {
        $nodes = $xpath->query('//w:p');

        if ($nodes === false) {
            return [];
        }

        return collect(iterator_to_array($nodes))
            ->filter(fn ($node) => $node instanceof DOMElement)
            ->values()
            ->all();
    }

    private function paragraphText(DOMElement $paragraph): string
    {
        $texts = [];

        foreach ($paragraph->getElementsByTagNameNS($this->wordNamespace(), 't') as $text) {
            $texts[] = $text->textContent;
        }

        return trim(implode('', $texts));
    }

    private function replaceParagraphsAfterHeading(DOMXPath $xpath, string $heading, array $lines, int $expectedCount): void
    {
        $paragraphs = $this->paragraphs($xpath);
        $headingIndex = $this->findParagraphIndex($paragraphs, $heading);
        $lines = array_pad(array_slice($lines, 0, $expectedCount), $expectedCount, '');

        for ($offset = 0; $offset < $expectedCount; $offset++) {
            $paragraph = $paragraphs[$headingIndex + 1 + $offset] ?? null;

            if (! $paragraph instanceof DOMElement) {
                throw new RuntimeException(sprintf('RFP template is missing content for "%s".', $heading));
            }

            $this->setParagraphText($paragraph, $lines[$offset]);
        }
    }

    private function setParagraphText(DOMElement $paragraph, string $text): void
    {
        $runs = [];

        foreach (iterator_to_array($paragraph->childNodes) as $child) {
            if ($child instanceof DOMElement && $child->localName === 'r') {
                $runs[] = $child;
            }
        }

        foreach ($runs as $run) {
            $paragraph->removeChild($run);
        }

        $run = $paragraph->ownerDocument->createElementNS($this->wordNamespace(), 'w:r');
        $textNode = $paragraph->ownerDocument->createElementNS($this->wordNamespace(), 'w:t');
        $textNode->appendChild($paragraph->ownerDocument->createTextNode($text));
        $run->appendChild($textNode);
        $paragraph->appendChild($run);
    }

    /**
     * @param  array<int, ProjectRequirement>  $requirements
     */
    private function updateFunctionalRequirementsTable(DOMXPath $xpath, array $requirements): void
    {
        $tables = $xpath->query('//w:tbl');

        if ($tables === false || $tables->length < 1) {
            throw new RuntimeException('RFP template is missing the requirements table.');
        }

        $table = $tables->item(0);

        if (! $table instanceof DOMElement) {
            throw new RuntimeException('RFP template is missing the requirements table.');
        }

        $rows = $xpath->query('./w:tr', $table);

        if ($rows === false || $rows->length < 2) {
            throw new RuntimeException('RFP template is missing requirement rows.');
        }

        $templateRow = $rows->item(1);

        if (! $templateRow instanceof DOMElement) {
            throw new RuntimeException('RFP template is missing requirement rows.');
        }

        for ($index = $rows->length - 1; $index >= 1; $index--) {
            $row = $rows->item($index);

            if ($row instanceof DOMElement) {
                $table->removeChild($row);
            }
        }

        if ($requirements === []) {
            $requirements = [null];
        }

        $counter = 1;

        foreach ($requirements as $requirement) {
            $row = $templateRow->cloneNode(true);
            $values = $this->formatRequirementRow($requirement instanceof ProjectRequirement ? $requirement : null, $counter);
            $this->setRowValues($xpath, $row, $values);
            $table->appendChild($row);
            $counter++;
        }
    }

    /**
     * @param  array<int, array{criterion: string, validation_method: string}>  $criteria
     */
    private function updateAcceptanceCriteriaTable(DOMXPath $xpath, array $criteria): void
    {
        $tables = $xpath->query('//w:tbl');

        if ($tables === false || $tables->length < 2) {
            throw new RuntimeException('RFP template is missing the acceptance criteria table.');
        }

        $table = $tables->item(1);

        if (! $table instanceof DOMElement) {
            throw new RuntimeException('RFP template is missing the acceptance criteria table.');
        }

        $rows = $xpath->query('./w:tr', $table);

        if ($rows === false || $rows->length < 2) {
            throw new RuntimeException('RFP template is missing acceptance criteria rows.');
        }

        $templateRow = $rows->item(1);

        if (! $templateRow instanceof DOMElement) {
            throw new RuntimeException('RFP template is missing acceptance criteria rows.');
        }

        for ($index = $rows->length - 1; $index >= 1; $index--) {
            $row = $rows->item($index);

            if ($row instanceof DOMElement) {
                $table->removeChild($row);
            }
        }

        if ($criteria === []) {
            $criteria = [['criterion' => 'N/A', 'validation_method' => 'N/A']];
        }

        foreach ($criteria as $item) {
            $row = $templateRow->cloneNode(true);
            $this->setRowValues($xpath, $row, [
                $item['criterion'],
                $item['validation_method'],
            ]);
            $table->appendChild($row);
        }
    }

    /**
     * @param  list<string>  $values
     */
    private function setRowValues(DOMXPath $xpath, DOMElement $row, array $values): void
    {
        $cells = $xpath->query('./w:tc', $row);

        if ($cells === false) {
            return;
        }

        foreach ($values as $index => $value) {
            $cell = $cells->item($index);

            if (! $cell instanceof DOMElement) {
                continue;
            }

            $this->setCellText($xpath, $cell, $value);
        }
    }

    private function setCellText(DOMXPath $xpath, DOMElement $cell, string $text): void
    {
        $paragraphs = $xpath->query('./w:p', $cell);
        $paragraph = $paragraphs !== false ? $paragraphs->item(0) : null;

        if (! $paragraph instanceof DOMElement) {
            $paragraph = $cell->ownerDocument->createElementNS($this->wordNamespace(), 'w:p');
            $cell->appendChild($paragraph);
        }

        $this->setParagraphText($paragraph, $text);
    }

    /**
     * @return list<string>
     */
    private function formatRequirementRow(?ProjectRequirement $requirement, int $index): array
    {
        if ($requirement === null) {
            return [
                'FR-001',
                'No requirements available.',
                'N/A',
                '',
            ];
        }

        $label = collect([
            trim((string) $requirement->module_name),
            trim((string) $requirement->page_name),
        ])->filter()->implode(' / ');

        $title = trim((string) $requirement->title);
        $details = trim((string) $requirement->details);

        $description = $label !== '' ? sprintf('%s: %s', $label, $title) : $title;

        if ($details !== '') {
            $description = $description.' - '.$details;
        }

        $priority = Str::of($requirement->priority)->replace('_', ' ')->title()->toString();
        $date = $requirement->created_at?->format('d-m-Y') ?? '';

        return [
            sprintf('FR-%03d', $index),
            $description,
            $priority !== '' ? $priority : 'Medium',
            $date,
        ];
    }

    /**
     * @param  list<string>  $fallback
     * @return list<string>
     */
    private function normalizeList(mixed $value, array $fallback, int $count): array
    {
        $normalized = [];

        if (is_array($value)) {
            foreach ($value as $item) {
                $text = $this->normalizeText($item, '');

                if ($text !== '') {
                    $normalized[] = $text;
                }
            }
        }

        if ($normalized === []) {
            $normalized = $fallback;
        }

        $normalized = array_slice($normalized, 0, $count);

        return array_pad($normalized, $count, '');
    }

    /**
     * @param  array<int, array{criterion: string, validation_method: string}>  $fallback
     * @return array<int, array{criterion: string, validation_method: string}>
     */
    private function normalizeAcceptanceCriteria(mixed $value, array $fallback): array
    {
        $rows = [];

        if (is_array($value)) {
            foreach ($value as $row) {
                if (! is_array($row)) {
                    continue;
                }

                $criterion = $this->normalizeText($row['criterion'] ?? null, '');
                $method = $this->normalizeText($row['validation_method'] ?? null, '');

                if ($criterion === '' || $method === '') {
                    continue;
                }

                $rows[] = [
                    'criterion' => $criterion,
                    'validation_method' => $method,
                ];
            }
        }

        return $rows !== [] ? $rows : $fallback;
    }

    private function normalizeText(mixed $value, string $fallback): string
    {
        $text = trim((string) $value);
        $text = $text !== '' ? $text : $fallback;

        return $this->normalizeAscii($text);
    }

    private function normalizeAscii(string $value): string
    {
        return str_replace(
            ["\u{2018}", "\u{2019}", "\u{201C}", "\u{201D}", "\u{2013}", "\u{2014}", "\u{2026}"],
            ["'", "'", '"', '"', '-', '-', '...'],
            $value
        );
    }

    private function templatePath(): string
    {
        return resource_path('templates/rfp-template.docx');
    }

    private function wordNamespace(): string
    {
        return 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
    }
}
