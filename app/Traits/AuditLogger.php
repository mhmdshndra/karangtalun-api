<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Http\Request;

trait AuditLogger
{
    protected function audit(
        string $action,
        ?string $entityType = null,
        ?string $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?Request $request = null,
    ): void {
        $request = $request ?? request();

        AuditLog::create([
            'user_id'     => $request->user()?->id,
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'old_values'  => $oldValues,
            'new_values'  => $newValues,
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);
    }
}
