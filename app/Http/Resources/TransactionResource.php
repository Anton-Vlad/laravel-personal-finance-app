<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

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
            'details' => $this->filterImportantDetails($this->details)
        ];
    }

    private function filterImportantDetails($details): array
    {
        return Arr::where($details, function ($value, $key) {
            return !str_contains($value, 'Nr. card') &&
                    !str_contains($value, 'Numar card:') &&
                    !str_contains($value, 'Autorizare:') &&
                    !str_contains($value, 'In contul') &&
                    !str_contains($value, 'Referinta:') &&
                    !str_contains($value, 'REF.') &&
                    !str_contains($value, 'Numar autorizare') &&
                    !str_contains($value, 'Catre:')
                ;
        });
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
