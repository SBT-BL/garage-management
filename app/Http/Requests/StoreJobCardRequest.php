<?php

namespace App\Http\Requests;

use App\Enums\JobCardStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobCardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $remark = $this->has('remark') ? trim((string) $this->input('remark')) : null;

        $services = $this->input('services');

        if (! is_array($services)) {
            $this->merge([
                'remark' => $remark === '' ? null : $remark,
            ]);

            return;
        }

        $this->merge([
            'remark' => $remark === '' ? null : $remark,
            'services' => collect($services)->map(function (mixed $line): mixed {
                if (! is_array($line)) {
                    return $line;
                }

                $lineRemark = isset($line['remark']) ? trim((string) $line['remark']) : null;

                return [
                    'service_id' => $line['service_id'] ?? null,
                    'price' => $line['price'] ?? null,
                    'remark' => $lineRemark === '' ? null : $lineRemark,
                ];
            })->all(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'vehicle_id' => [
                'required',
                'integer',
                Rule::exists('vehicles', 'id')->where(
                    fn ($query) => $query->where('customer_id', $this->input('customer_id')),
                ),
            ],
            'status' => ['required', Rule::enum(JobCardStatus::class)],
            'remark' => ['nullable', 'string', 'max:2000'],
            'services' => ['required', 'array', 'min:1'],
            'services.*.service_id' => ['required', 'integer', 'exists:services,id'],
            'services.*.price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'services.*.remark' => ['nullable', 'string', 'max:1000'],
            'job_card_number' => ['prohibited'],
            'grand_total' => ['prohibited'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'vehicle_id.exists' => 'The selected vehicle does not belong to this customer.',
            'services.required' => 'Add at least one service.',
            'services.min' => 'Add at least one service.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'customer_id' => 'customer',
            'vehicle_id' => 'vehicle',
            'services.*.service_id' => 'service',
            'services.*.price' => 'price',
            'services.*.remark' => 'remark',
        ];
    }

    /**
     * Sum the validated service prices.
     */
    public function grandTotal(): string
    {
        $cents = 0;

        foreach ($this->validated('services') as $line) {
            $cents += (int) round(((float) $line['price']) * 100);
        }

        return number_format($cents / 100, 2, '.', '');
    }

    /**
     * Service lines ready to persist.
     *
     * @return list<array{service_id: int, price: string, remark: ?string}>
     */
    public function serviceLines(): array
    {
        return collect($this->validated('services'))
            ->map(fn (array $line): array => [
                'service_id' => (int) $line['service_id'],
                'price' => $this->normalizedPrice($line['price']),
                'remark' => $line['remark'] ?? null,
            ])
            ->values()
            ->all();
    }

    /**
     * Header attributes stored on the job card, excluding generated fields.
     *
     * @return array{date: string, customer_id: int, vehicle_id: int, remark: ?string, status: string}
     */
    public function jobCardAttributes(): array
    {
        /** @var array{date: string, customer_id: int, vehicle_id: int, remark: ?string, status: string} $attributes */
        $attributes = $this->safe()->only([
            'date',
            'customer_id',
            'vehicle_id',
            'remark',
            'status',
        ]);

        return $attributes;
    }

    private function normalizedPrice(mixed $price): string
    {
        return number_format((float) $price, 2, '.', '');
    }
}
