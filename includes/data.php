<?php

declare(strict_types=1);

const DATA_PATH = __DIR__ . '/../data/plants.json';
const IMAGE_DIRECTORY = __DIR__ . '/../public/images';
const IMAGE_PREFIX = '/images';

function load_plants(): array
{
    if (!file_exists(DATA_PATH)) {
        return [];
    }

    $contents = file_get_contents(DATA_PATH);
    if ($contents === false) {
        throw new RuntimeException('Unable to read plant data.');
    }

    $data = json_decode($contents, true);
    if (!is_array($data)) {
        throw new RuntimeException('Plant data file is invalid.');
    }

    return $data;
}

function save_plants(array $plants): void
{
    $payload = json_encode($plants, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if ($payload === false) {
        throw new RuntimeException('Unable to encode plant data.');
    }

    $tmpPath = DATA_PATH . '.tmp';
    $bytes = file_put_contents($tmpPath, $payload, LOCK_EX);
    if ($bytes === false) {
        throw new RuntimeException('Unable to write plant data.');
    }

    if (!rename($tmpPath, DATA_PATH)) {
        throw new RuntimeException('Unable to replace plant data file.');
    }
}

function normalise_list(null|string|array $value): array
{
    if (is_array($value)) {
        return array_values(array_filter(array_map('trim', array_map('strval', $value)), fn ($item) => $item !== ''));
    }

    if (is_string($value)) {
        $parts = preg_split('/[,\n]+/', $value) ?: [];
        return array_values(array_filter(array_map('trim', $parts), fn ($item) => $item !== ''));
    }

    return [];
}

function slugify(string $value): string
{
    $value = strtolower($value);
    $value = preg_replace('/[^a-z0-9]+/i', '-', $value) ?? '';
    $value = trim($value, '-');
    return $value !== '' ? $value : 'plant';
}

function resolve_image_filename(array $plant): string
{
    $map = [
        'areca-palm' => 'areca-palm.svg',
        'dypsis-lutescens' => 'areca-palm.svg',
        'snake-plant' => 'snake-plant.svg',
        'sansevieria-trifasciata' => 'snake-plant.svg',
        'peace-lily' => 'peace-lily.svg',
        'spathiphyllum' => 'peace-lily.svg',
        'fiddle-leaf-fig' => 'fiddle-leaf-fig.svg',
        'ficus-lyrata' => 'fiddle-leaf-fig.svg',
        'money-plant' => 'money-plant.svg',
        'epipremnum-aureum' => 'money-plant.svg',
        'zz-plant' => 'zz-plant.svg',
        'zamioculcas-zamiifolia' => 'zz-plant.svg',
        'bamboo-palm' => 'bamboo-palm.svg',
        'chamaedorea' => 'bamboo-palm.svg',
        'spider-plant' => 'spider-plant.svg',
        'chlorophytum-comosum' => 'spider-plant.svg',
        'rubber-plant' => 'rubber-plant.svg',
        'ficus-elastica' => 'rubber-plant.svg',
        'boston-fern' => 'boston-fern.svg',
        'nephrolepis-exaltata' => 'boston-fern.svg',
        'chinese-evergreen' => 'chinese-evergreen.svg',
        'aglaonema' => 'chinese-evergreen.svg',
        'aloe-vera' => 'aloe-vera.svg',
        'philodendron' => 'philodendron.svg',
        'calathea' => 'calathea.svg',
        'fern' => 'peace-fern.svg',
    ];

    $candidates = [];
    if (!empty($plant['image'])) {
        $candidates[] = $plant['image'];
    }

    if (!empty($plant['commonName'])) {
        $candidates[] = slugify((string) $plant['commonName']);
    }

    if (!empty($plant['botanicalName'])) {
        $candidates[] = slugify((string) $plant['botanicalName']);
    }

    if (!empty($plant['collection'])) {
        $candidates[] = slugify((string) $plant['collection']);
    }

    foreach ($candidates as $candidate) {
        if (isset($map[$candidate])) {
            $filename = $map[$candidate];
            if (file_exists(IMAGE_DIRECTORY . '/' . $filename)) {
                return $filename;
            }
        }

        foreach ($map as $key => $filename) {
            if (str_contains($candidate, $key) && file_exists(IMAGE_DIRECTORY . '/' . $filename)) {
                return $filename;
            }
        }

        if (file_exists(IMAGE_DIRECTORY . '/' . $candidate)) {
            return $candidate;
        }

        if (file_exists(IMAGE_DIRECTORY . '/' . $candidate . '.svg')) {
            return $candidate . '.svg';
        }

        if (file_exists(IMAGE_DIRECTORY . '/' . $candidate . '.jpg')) {
            return $candidate . '.jpg';
        }
    }

    return file_exists(IMAGE_DIRECTORY . '/fallback.svg') ? 'fallback.svg' : '';
}

function with_image(array $plant): array
{
    $filename = resolve_image_filename($plant);
    if ($filename !== '') {
        $plant['image'] = IMAGE_PREFIX . '/' . $filename;
    } else {
        $plant['image'] = null;
    }

    return $plant;
}

function next_id(array $plants): string
{
    $count = count($plants) + 1;
    return 'RPF-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
}
