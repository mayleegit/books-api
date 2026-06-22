<?php

namespace App\Repositories;

use PDO;

final class BookRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function all(string $q = '', int $limit = 0): array
    {
        $sql = 'SELECT b.*, u.name AS owner_name
                FROM books b
                LEFT JOIN users u ON u.id = b.created_by';
        $args = [];

        if ($q !== '') {
            $sql .= ' WHERE b.title LIKE :q_title OR b.author LIKE :q_author';
            $args[':q_title'] = '%' . $q . '%';
            $args[':q_author'] = '%' . $q . '%';
        }

        $sql .= ' ORDER BY b.id ASC';

        if ($limit > 0) {
            $sql .= ' LIMIT ' . max(1, $limit);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($args);

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT b.*, u.name AS owner_name
             FROM books b
             LEFT JOIN users u ON u.id = b.created_by
             WHERE b.id = :id'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function create(array $book, int $createdBy): int
    {
        $sql = 'INSERT INTO books (title, author, year, genre, created_by)
                VALUES (:title, :author, :year, :genre, :owner)';

        $this->pdo->prepare($sql)->execute([
            ':title' => trim((string) $book['title']),
            ':author' => trim((string) $book['author']),
            ':year' => (int) $book['year'],
            ':genre' => trim((string) ($book['genre'] ?? 'Uncategorised')),
            ':owner' => $createdBy,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $book): int
    {
        $sets = [];
        $args = [':id' => $id];

        foreach (['title', 'author', 'genre'] as $field) {
            if (array_key_exists($field, $book)) {
                $sets[] = "{$field} = :{$field}";
                $args[":{$field}"] = trim((string) $book[$field]);
            }
        }

        if (array_key_exists('year', $book)) {
            $sets[] = 'year = :year';
            $args[':year'] = (int) $book['year'];
        }

        if (empty($sets)) {
            return 0;
        }

        $sql = 'UPDATE books SET ' . implode(', ', $sets) . ' WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($args);

        return $stmt->rowCount();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM books WHERE id = :id');
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount() === 1;
    }
}
