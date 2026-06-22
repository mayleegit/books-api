<?php

namespace App\Repositories;

use PDO;

final class AuditLogRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function record(
        ?int $actorId,
        string $action,
        ?string $target = null,
        ?string $ipAddress = null,
        ?string $detail = null
    ): void {
        $stmt = $this->pdo->prepare(
            'INSERT INTO audit_log (actor_id, action, target, ip_address, detail)
             VALUES (:actor_id, :action, :target, :ip_address, :detail)'
        );

        $stmt->execute([
            ':actor_id' => $actorId,
            ':action' => mb_substr($action, 0, 50),
            ':target' => $target === null ? null : mb_substr($target, 0, 80),
            ':ip_address' => $ipAddress === null ? null : mb_substr($ipAddress, 0, 45),
            ':detail' => $detail === null ? null : mb_substr($detail, 0, 500),
        ]);
    }

    public function latest(int $limit = 10): array
    {
        $sql = 'SELECT occurred_at, actor_id, action, target, ip_address, detail
                FROM audit_log
                ORDER BY id DESC
                LIMIT ' . max(1, min(100, $limit));

        return $this->pdo->query($sql)->fetchAll();
    }
}
