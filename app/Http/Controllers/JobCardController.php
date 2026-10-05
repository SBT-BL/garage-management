<?php

namespace App\Http\Controllers;

use App\DataTables\JobCardsDataTable;
use App\Enums\JobCardStatus;
use App\Http\Requests\StoreJobCardRequest;
use App\Http\Requests\UpdateJobCardRequest;
use App\Models\Customer;
use App\Models\JobCard;
use App\Models\Service;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JobCardController extends Controller
{
    /**
     * Display a listing of job cards (Yajra DataTables).
     */
    public function index(JobCardsDataTable $dataTable): View|JsonResponse
    {
        return $dataTable->render('job-cards.index', [
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name']),
            'services' => Service::query()->orderBy('name')->get(['id', 'name']),
            'filterStartDate' => now()->subDays(6)->toDateString(),
            'filterEndDate' => now()->toDateString(),
        ]);
    }

    /**
     * Return a paginated mobile card feed for infinite scroll.
     */
    public function cards(Request $request): JsonResponse
    {
        $jobCards = JobCard::query()
            ->with([
                'customer:id,name',
                'vehicle:id,vehicle_number,vehicle_model',
            ])
            ->listing(JobCard::listingFilters($request))
            ->search($request->string('q')->toString())
            ->latest('date')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return response()->json([
            'html' => view('job-cards.partials.mobile-cards', [
                'jobCards' => $jobCards,
            ])->render(),
            'meta' => [
                'current_page' => $jobCards->currentPage(),
                'last_page' => $jobCards->lastPage(),
                'has_more' => $jobCards->hasMorePages(),
                'total' => $jobCards->total(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new job card.
     */
    public function create(): View
    {
        return view('job-cards.create', $this->formData());
    }

    /**
     * Store a newly created job card.
     */
    public function store(StoreJobCardRequest $request): RedirectResponse
    {
        $jobCard = DB::transaction(function () use ($request): JobCard {
            $jobCard = JobCard::query()->create([
                ...$request->jobCardAttributes(),
                'job_card_number' => JobCard::nextNumber(),
                'grand_total' => $request->grandTotal(),
            ]);

            $jobCard->jobCardServices()->createMany($request->serviceLines());

            return $jobCard;
        });

        return redirect()
            ->route('admin.job-cards.index')
            ->with('success', 'Job card '.$jobCard->job_card_number.' created successfully.');
    }

    /**
     * Display the specified job card.
     */
    public function show(JobCard $jobCard): View
    {
        $jobCard->load([
            'customer',
            'vehicle',
            'jobCardServices.service',
        ]);

        return view('job-cards.show', compact('jobCard'));
    }

    /**
     * Show the form for editing the specified job card.
     */
    public function edit(JobCard $jobCard): View
    {
        $jobCard->load('jobCardServices');

        return view('job-cards.edit', $this->formData($jobCard));
    }

    /**
     * Update the specified job card.
     */
    public function update(UpdateJobCardRequest $request, JobCard $jobCard): RedirectResponse
    {
        DB::transaction(function () use ($request, $jobCard): void {
            $jobCard->update([
                ...$request->jobCardAttributes(),
                'grand_total' => $request->grandTotal(),
            ]);

            $jobCard->jobCardServices()->delete();
            $jobCard->jobCardServices()->createMany($request->serviceLines());
        });

        return redirect()
            ->route('admin.job-cards.show', $jobCard)
            ->with('success', 'Job card '.$jobCard->job_card_number.' updated successfully.');
    }

    /**
     * Update only the job card status from the listing.
     */
    public function updateStatus(Request $request, JobCard $jobCard): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(JobCardStatus::class)],
        ]);

        $jobCard->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'status' => $jobCard->status->value,
            'badge_class' => $jobCard->status->badgeClass(),
        ]);
    }

    /**
     * Remove the specified job card.
     */
    public function destroy(JobCard $jobCard): RedirectResponse
    {
        $jobCard->delete();

        return redirect()
            ->route('admin.job-cards.index')
            ->with('success', 'Job card deleted successfully.');
    }

    /**
     * Shared data for the create and edit forms.
     *
     * @return array{
     *     jobCard: ?JobCard,
     *     selectedCustomer: ?Customer,
     *     selectedVehicle: ?Vehicle,
     *     serviceNames: Collection<int|string, string>,
     *     serviceRows: list<array{service_id: mixed, price: mixed, remark: mixed}>
     * }
     */
    private function formData(?JobCard $jobCard = null): array
    {
        $customerId = old('customer_id', $jobCard?->customer_id);
        $vehicleId = old('vehicle_id', $jobCard?->vehicle_id);
        $serviceRows = $this->serviceRows($jobCard);
        $serviceIds = collect($serviceRows)
            ->pluck('service_id')
            ->filter(fn (mixed $id): bool => is_numeric($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->all();

        return [
            'jobCard' => $jobCard,
            'selectedCustomer' => is_numeric($customerId) ? Customer::query()->find($customerId) : null,
            'selectedVehicle' => is_numeric($vehicleId) ? Vehicle::query()->find($vehicleId) : null,
            'serviceNames' => Service::query()->whereIn('id', $serviceIds)->pluck('name', 'id'),
            'serviceRows' => $serviceRows,
        ];
    }

    /**
     * Service rows for the form, restored from validation input or the saved card.
     *
     * @return list<array{service_id: mixed, price: mixed, remark: mixed}>
     */
    private function serviceRows(?JobCard $jobCard): array
    {
        $old = old('services');

        if (is_array($old) && $old !== []) {
            return array_values($old);
        }

        if ($jobCard !== null) {
            return $jobCard->jobCardServices
                ->map(fn ($line): array => [
                    'service_id' => $line->service_id,
                    'price' => $line->price,
                    'remark' => $line->remark,
                ])
                ->all();
        }

        return [
            [
                'service_id' => '',
                'price' => '',
                'remark' => '',
            ],
        ];
    }
}
