<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'title'      => substr($this->title, 0, 50) . '...',
            'file_size'  => $this->formatBytes($this->file_size),
            'status'     => $this->getStatusName($this->processed),
            'status_type'=> $this->getStatusType($this->processed),
            'updated_at' => $this->updated_at->diffForHumans(),
        ];
    }

    private function getStatusName(int $status): string
    {
        return match ($status) {
            0 => 'Pending',
            1 => 'Processed',
            default => 'Failed',
        };
    }

    private function getStatusType(int $status): string
    {
        return match ($status) {
            0 => 'warning',
            1 => 'success',
            default => 'danger',
        };
    }

    private function formatBytes(int|null $bytes, int $precision = 2): string
    {
        if (!$bytes) return '-';
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        // $bytes /= pow(1024, $pow);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . $units[$pow];
    }
}
