<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/data.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $plants = load_plants();

    $total = count($plants);
    $petFriendly = count(array_filter($plants, static fn (array $plant): bool => !empty($plant['petFriendly'])));
    $airWellness = count(array_filter($plants, static fn (array $plant): bool => isset($plant['airPurifying']) && preg_match('/excellent|high/i', (string) $plant['airPurifying'])));

    $careDistribution = [];
    foreach ($plants as $plant) {
        $key = (string) ($plant['careLevel'] ?? 'Unspecified');
        $careDistribution[$key] = ($careDistribution[$key] ?? 0) + 1;
    }

    $climateMentions = [];
    foreach ($plants as $plant) {
        foreach ($plant['climateFocus'] ?? [] as $focus) {
            $focusKey = trim((string) $focus);
            if ($focusKey === '') {
                continue;
            }
            $climateMentions[$focusKey] = ($climateMentions[$focusKey] ?? 0) + 1;
        }
    }

    arsort($climateMentions);
    $topClimates = [];
    foreach (array_slice($climateMentions, 0, 8, true) as $focus => $count) {
        $topClimates[] = ['focus' => $focus, 'count' => $count];
    }

    echo json_encode([
        'total' => $total,
        'petFriendly' => $petFriendly,
        'airWellness' => $airWellness,
        'careDistribution' => $careDistribution,
        'topClimates' => $topClimates,
    ], JSON_UNESCAPED_SLASHES);
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to build summary.', 'error' => $error->getMessage()]);
}
