<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/data.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        $page = max((int) ($_GET['page'] ?? 1), 1);
        $pageSize = (int) ($_GET['pageSize'] ?? 24);
        $pageSize = max(1, min($pageSize, 120));
        $search = trim((string) ($_GET['search'] ?? ''));
        $careLevel = strtolower(trim((string) ($_GET['careLevel'] ?? '')));
        $light = strtolower(trim((string) ($_GET['light'] ?? '')));
        $climate = strtolower(trim((string) ($_GET['climate'] ?? '')));
        $petFriendlyFilter = isset($_GET['petFriendly']) && $_GET['petFriendly'] !== '';
        $petFriendlyDesired = strtolower((string) ($_GET['petFriendly'] ?? '')) === 'true';

        $plants = array_map('with_image', load_plants());

        if ($search !== '') {
            $query = strtolower($search);
            $plants = array_filter($plants, static function (array $plant) use ($query): bool {
                $haystack = [
                    (string) ($plant['commonName'] ?? ''),
                    (string) ($plant['botanicalName'] ?? ''),
                    (string) ($plant['description'] ?? ''),
                    implode(' ★ ', $plant['uses'] ?? []),
                    implode(' ★ ', $plant['placementIdeas'] ?? []),
                    implode(' ★ ', $plant['climateFocus'] ?? []),
                    implode(' ★ ', $plant['featuredTags'] ?? []),
                ];
                return str_contains(strtolower(implode(' ★ ', $haystack)), $query);
            });
        }

        if ($careLevel !== '') {
            $plants = array_filter($plants, static fn (array $plant): bool => strtolower((string) ($plant['careLevel'] ?? '')) === $careLevel);
        }

        if ($light !== '') {
            $plants = array_filter($plants, static fn (array $plant): bool => str_contains(strtolower((string) ($plant['lightRequirements'] ?? '')), $light));
        }

        if ($climate !== '') {
            $plants = array_filter($plants, static fn (array $plant): bool => array_reduce(
                $plant['climateFocus'] ?? [],
                static fn (bool $carry, $item): bool => $carry || str_contains(strtolower((string) $item), $climate),
                false
            ));
        }

        if ($petFriendlyFilter) {
            $plants = array_filter($plants, static fn (array $plant): bool => (bool) ($plant['petFriendly'] ?? false) === $petFriendlyDesired);
        }

        $plants = array_values($plants);
        $total = count($plants);
        $offset = ($page - 1) * $pageSize;
        $slice = array_slice($plants, $offset, $pageSize);

        echo json_encode([
            'data' => $slice,
            'meta' => [
                'total' => $total,
                'page' => $page,
                'pageSize' => $pageSize,
                'totalPages' => $pageSize > 0 ? (int) ceil($total / $pageSize) : 0,
            ],
        ], JSON_UNESCAPED_SLASHES);
        return;
    }

    if ($method === 'POST') {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw ?: '[]', true);
        if (!is_array($payload)) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid payload.']);
            return;
        }

        $required = [
            'commonName',
            'botanicalName',
            'description',
            'uses',
            'placementIdeas',
            'environment',
            'lightRequirements',
            'waterSchedule',
            'humidityPreference',
            'temperatureRange',
            'careLevel',
        ];

        $missing = array_values(array_filter($required, static fn (string $field): bool => empty($payload[$field])));
        if (!empty($missing)) {
            http_response_code(400);
            echo json_encode([
                'message' => 'Some required fields are missing.',
                'missing' => $missing,
            ]);
            return;
        }

        $plants = load_plants();

        $climateFocus = normalise_list($payload['climateFocus'] ?? null);
        if (empty($climateFocus)) {
            $climateFocus = ['Custom climate'];
        }

        $featuredTags = normalise_list($payload['featuredTags'] ?? null);

        $plant = [
            'id' => next_id($plants),
            'commonName' => (string) $payload['commonName'],
            'botanicalName' => (string) $payload['botanicalName'],
            'collection' => (string) ($payload['collection'] ?? 'Custom Submission'),
            'size' => (string) ($payload['size'] ?? 'Custom'),
            'description' => (string) $payload['description'],
            'uses' => normalise_list($payload['uses']),
            'placementIdeas' => normalise_list($payload['placementIdeas']),
            'environment' => (string) $payload['environment'],
            'lightRequirements' => (string) $payload['lightRequirements'],
            'waterSchedule' => (string) $payload['waterSchedule'],
            'humidityPreference' => (string) $payload['humidityPreference'],
            'temperatureRange' => (string) $payload['temperatureRange'],
            'careLevel' => (string) $payload['careLevel'],
            'petFriendly' => filter_var($payload['petFriendly'] ?? false, FILTER_VALIDATE_BOOL),
            'airPurifying' => (string) ($payload['airPurifying'] ?? 'Good'),
            'climateFocus' => $climateFocus,
            'featuredTags' => $featuredTags,
            'specialNotes' => (string) ($payload['specialNotes'] ?? 'Curated by Rajput Farms bespoke team.'),
        ];

        $plants[] = $plant;
        save_plants($plants);

        http_response_code(201);
        echo json_encode(with_image($plant), JSON_UNESCAPED_SLASHES);
        return;
    }

    http_response_code(405);
    header('Allow: GET, POST');
    echo json_encode(['message' => 'Method not allowed.']);
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode(['message' => 'Unexpected server error.', 'error' => $error->getMessage()]);
}
