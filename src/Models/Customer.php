<?php

namespace App\Models;

use App\Database\DB;

class Customer
{
    private static string $collection = 'customers';

    public static function all(): array
    {
        return DB::find(self::$collection);
    }

    public static function find(string $id): ?array
    {
        return DB::findOne(self::$collection, ['_id' => $id]);
    }

    public static function create(array $data): array
    {
        $document = [
            'name' => $data['name'],
            'phone' => $data['phone'] ?? '',
            'address' => $data['address'] ?? '',
            'created_at' => date('c'),
            'updated_at' => date('c')
        ];

        $id = DB::insert(self::$collection, $document);
        $document['id'] = $id;
        $document['_id'] = new \MongoDB\BSON\ObjectId($id);

        return $document;
    }

    public static function update(string $id, array $data): ?array
    {
        $existing = self::find($id);
        if (!$existing) {
            return null;
        }

        $updateData = [
            'name' => $data['name'] ?? $existing['name'],
            'phone' => $data['phone'] ?? $existing['phone'],
            'address' => $data['address'] ?? $existing['address'],
            'updated_at' => date('c')
        ];

        DB::update(self::$collection, ['_id' => $id], ['$set' => $updateData]);

        return self::find($id);
    }

    public static function delete(string $id): bool
    {
        $existing = self::find($id);
        if (!$existing) {
            return false;
        }

        $count = DB::delete(self::$collection, ['_id' => $id], true);
        return $count > 0;
    }
}
