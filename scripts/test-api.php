<?php

function request(string $method, string $url, array $data = []): array
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if (!empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['status' => $status, 'body' => json_decode($response, true)];
}

echo "Testing Customers API...\n";
// 1. Create Customer
$res = request('POST', 'http://127.0.0.1:8000/customers', [
    'name' => 'John Doe',
    'phone' => '555-1234',
    'address' => '123 Main St'
]);
echo "Create Customer Status: {$res['status']}\n";
$customerId = $res['body']['id'] ?? null;
var_dump($res['body']);

if ($customerId) {
    // 2. Get Customer by ID
    $res = request('GET', "http://127.0.0.1:8000/customers/{$customerId}");
    echo "Get Customer Status: {$res['status']}\n";
    var_dump($res['body']);

    // 3. Update Customer
    $res = request('PUT', "http://127.0.0.1:8000/customers/{$customerId}", [
        'name' => 'John Updated',
        'phone' => '555-9999',
        'address' => '456 Oak Ave'
    ]);
    echo "Update Customer Status: {$res['status']}\n";
    var_dump($res['body']);

    // 4. Delete Customer
    $res = request('DELETE', "http://127.0.0.1:8000/customers/{$customerId}");
    echo "Delete Customer Status: {$res['status']}\n";
    var_dump($res['body']);
}

echo "\nTesting Items API...\n";
// 1. Create Item
$res = request('POST', 'http://127.0.0.1:8000/items', [
    'name' => 'Power Drill',
    'price' => 49.99
]);
echo "Create Item Status: {$res['status']}\n";
$itemId = $res['body']['id'] ?? null;
var_dump($res['body']);

if ($itemId) {
    // 2. Get Item by ID
    $res = request('GET', "http://127.0.0.1:8000/items/{$itemId}");
    echo "Get Item Status: {$res['status']}\n";
    var_dump($res['body']);

    // 3. Update Item
    $res = request('PUT', "http://127.0.0.1:8000/items/{$itemId}", [
        'name' => 'Cordless Power Drill',
        'price' => 59.99
    ]);
    echo "Update Item Status: {$res['status']}\n";
    var_dump($res['body']);

    // 4. Delete Item
    $res = request('DELETE', "http://127.0.0.1:8000/items/{$itemId}");
    echo "Delete Item Status: {$res['status']}\n";
    var_dump($res['body']);
}
