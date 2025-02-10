<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_short' => $this->shortString($this->name),
            'category' => $this->category,
            'date' => $this->date->format('j M Y'),
            'amount' => $this->displayAmountValue($this->amount),
            'amount_raw' => $this->amount,
            'amount_class' => $this->displayAmountClass($this->amount),
            'currency' => $this->currency,
            'details' => $this->details
        ];
    }

    private function displayAmountValue(float $val): string {
        $formatted = number_format(abs($val)); // Format number with thousands separator
        return ($val >= 0 ? '+' : '-') . $formatted;
    }

    private function shortString($string)
    {
        return strlen($string) > 20 ? substr($string, 0, 20) . '...' : $string;
    }

    private function displayAmountClass($val)
    {
        return $val >= 0 ? 'font-bold text-green-600' : 'font-bold text-gray-900';
    }
}
