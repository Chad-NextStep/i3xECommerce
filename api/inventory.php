<?php

declare(strict_types=1);

if (get_request_method() !== 'GET') {
    json_error('Method not allowed', 405);
}

$skus_param = $_GET['skus'] ?? '';
if ($skus_param === '') {
    json_error('skus parameter is required');
}

$skus = array_filter(array_map('trim', explode(',', $skus_param)));
$inventory = load_inventory();

$result = [];
foreach ($skus as $sku) {
    $result[$sku] = $inventory[$sku] ?? 0;
}

json_response($result);
