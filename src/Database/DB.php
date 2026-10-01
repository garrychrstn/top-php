<?php

namespace App\Database;

class DB
{
    private static ?\MongoDB\Client $client = null;
    private static string $dbName = 'top_php';

    private static function getDatabase(): \MongoDB\Database
    {
        if (self::$client === null) {
            $uri = $_ENV['MONGODB_URI'] ?? 'mongodb://localhost:27017';
            self::$client = new \MongoDB\Client($uri);
        }
        return self::$client->selectDatabase(self::$dbName);
    }

    private static function sanitizeFilter(array $filter): array
    {
        if (isset($filter['_id']) && is_string($filter['_id']) && preg_match('/^[a-fA-F0-9]{24}$/', $filter['_id'])) {
            $filter['_id'] = new \MongoDB\BSON\ObjectId($filter['_id']);
        }
        if (isset($filter['id']) && is_string($filter['id']) && preg_match('/^[a-fA-F0-9]{24}$/', $filter['id'])) {
            $filter['_id'] = new \MongoDB\BSON\ObjectId($filter['id']);
            unset($filter['id']);
        }
        return $filter;
    }

    private static function formatDocument(?array $doc): ?array
    {
        if (!$doc) {
            return null;
        }
        if (isset($doc['_id']) && $doc['_id'] instanceof \MongoDB\BSON\ObjectId) {
            $doc['id'] = (string)$doc['_id'];
        }
        return $doc;
    }

    public static function find(string $collectionName, array $filter = [], array $options = []): array
    {
        $db = self::getDatabase();
        $filter = self::sanitizeFilter($filter);
        $cursor = $db->selectCollection($collectionName)->find($filter, $options);
        
        $results = [];
        foreach ($cursor as $document) {
            $arr = (array)$document;
            $results[] = self::formatDocument($arr);
        }
        return $results;
    }

    public static function findOne(string $collectionName, array $filter = []): ?array
    {
        $db = self::getDatabase();
        $filter = self::sanitizeFilter($filter);
        $document = $db->selectCollection($collectionName)->findOne($filter);
        
        if (!$document) {
            return null;
        }
        return self::formatDocument((array)$document);
    }

    public static function insert(string $collectionName, array $data): string
    {
        $db = self::getDatabase();
        $result = $db->selectCollection($collectionName)->insertOne($data);
        return (string)$result->getInsertedId();
    }

    public static function update(string $collectionName, array $filter, array $update, array $options = []): bool
    {
        $db = self::getDatabase();
        $filter = self::sanitizeFilter($filter);
        $result = $db->selectCollection($collectionName)->updateOne($filter, $update, $options);
        return $result->getModifiedCount() > 0 || $result->getMatchedCount() > 0;
    }

    public static function delete(string $collectionName, array $filter, bool $deleteOne = true): int
    {
        $db = self::getDatabase();
        $filter = self::sanitizeFilter($filter);
        $collection = $db->selectCollection($collectionName);
        
        if ($deleteOne) {
            $result = $collection->deleteOne($filter);
        } else {
            $result = $collection->deleteMany($filter);
        }
        return $result->getDeletedCount();
    }
}
