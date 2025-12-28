<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvestorInstallment\CreateInvestorInstallmentRequest;
use App\Http\Requests\InvestorInstallment\ProcessPaymentRequest;
use App\Http\Requests\InvestorInstallment\UpdateInvestorInstallmentRequest;
use App\Http\Resources\InvestorInstallmentResource;
use App\InstallmentStatus;
use App\Models\Investor;
use App\Models\InvestorInstallment;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class InvestorInstallmentController extends Controller
{
    use ApiResponse;

    /**
     * List all investor installments.
     */
    public function index(): JsonResponse
    {
        $installments = InvestorInstallment::with('investor')
            ->latest()
            ->paginate(10);
        $resourceCollection = InvestorInstallmentResource::collection($installments);

        return $this->paginatedResponse('Investor installments retrieved successfully', $installments, $resourceCollection);
    }

    /**
     * Store a new investor installment.
     */
    public function store(CreateInvestorInstallmentRequest $request): JsonResponse
    {
        $data = $request->validated();

        $installment = InvestorInstallment::create($data);

        return $this->successResponse('Investor installment created successfully', new InvestorInstallmentResource($installment->load('investor')), 201);
    }

    /**
     * Show an investor installment.
     */
    public function show(InvestorInstallment $investorInstallment): JsonResponse
    {
        return $this->successResponse('Investor installment retrieved successfully', new InvestorInstallmentResource($investorInstallment->load('investor')));
    }

    /**
     * Update an investor installment.
     */
    public function update(UpdateInvestorInstallmentRequest $request, InvestorInstallment $investorInstallment): JsonResponse
    {
        $validated = $request->validated();

        // Only update fillable fields
        $updateData = [];
        foreach ($investorInstallment->getFillable() as $field) {
            if (array_key_exists($field, $validated)) {
                $updateData[$field] = $validated[$field];
            }
        }

        if (! empty($updateData)) {
            $investorInstallment->update($updateData);
        }

        return $this->successResponse('Investor installment updated successfully', new InvestorInstallmentResource($investorInstallment->fresh()->load('investor')));
    }

    /**
     * Delete an investor installment.
     */
    public function destroy(InvestorInstallment $investorInstallment): JsonResponse
    {
        $investorInstallment->delete();

        return $this->successResponse('Investor installment deleted successfully');
    }

    /**
     * Process payment for an investor installment.
     */
    public function processPayment(ProcessPaymentRequest $request, InvestorInstallment $investorInstallment): JsonResponse
    {
        return DB::transaction(function () use ($request, $investorInstallment) {
            $investor = $investorInstallment->investor;
            if (! $investor) {
                return $this->errorResponse('Investor not found for this installment.', 404);
            }

            // Get installment amount from investor table
            $installmentAmountPerMonth = (float) ($investor->installment_per_month ?? 0);
            if ($installmentAmountPerMonth <= 0) {
                return $this->errorResponse('Installment amount per month is not set for this investor.', 422);
            }

            $validated = $request->validated();
            $paymentAmount = (float) $validated['amount'];
            $paidDate = $validated['paid_date'] ?? now()->toDateString();
            $paymentMethod = $validated['payment_method'];
            $transactionReference = $validated['transaction_reference'] ?? null;
            $notes = $validated['notes'] ?? null;

            $remainingPayment = $paymentAmount;
            $processedInstallments = [];
            $lastProcessedInstallment = $investorInstallment;

            // Start from the current installment
            $currentInstallmentNumber = $investorInstallment->installment_number;

            while ($remainingPayment > 0) {
                // Get or create the current installment
                $installment = null;
                if ($currentInstallmentNumber === $investorInstallment->installment_number) {
                    // Use the provided installment, but refresh it to get latest data
                    $installment = $investorInstallment->fresh();
                } else {
                    // Get existing installment or create new one
                    $installment = InvestorInstallment::where('investor_id', $investor->id)
                        ->where('installment_number', $currentInstallmentNumber)
                        ->first();
                }

                if (! $installment) {
                    // Calculate due date for new installment (1 month from previous)
                    $previousInstallment = InvestorInstallment::where('investor_id', $investor->id)
                        ->where('installment_number', $currentInstallmentNumber - 1)
                        ->first();

                    $dueDate = $previousInstallment?->due_date
                        ? \Carbon\Carbon::parse($previousInstallment->due_date)->addMonth()->toDateString()
                        : ($investor->installment_start_from?->toDateString() ?? now()->addMonth()->toDateString());

                    // Create new installment
                    $installment = InvestorInstallment::create([
                        'investor_id' => $investor->id,
                        'installment_number' => $currentInstallmentNumber,
                        'amount' => $installmentAmountPerMonth,
                        'paid_amount' => 0,
                        'due_date' => $dueDate,
                        'status' => InstallmentStatus::Pending,
                    ]);
                }

                // Calculate how much to pay for this installment
                $remainingDue = $installment->amount - ($installment->paid_amount ?? 0);
                $amountToPay = min($remainingPayment, $remainingDue);

                if ($amountToPay > 0) {
                    $newPaidAmount = ($installment->paid_amount ?? 0) + $amountToPay;
                    $newStatus = InstallmentStatus::Partial;

                    if ($newPaidAmount >= $installment->amount) {
                        $newPaidAmount = $installment->amount;
                        $newStatus = InstallmentStatus::Paid;
                    }

                    // Update installment
                    $installment->update([
                        'paid_amount' => $newPaidAmount,
                        'status' => $newStatus,
                        'paid_date' => $paidDate,
                        'payment_method' => $paymentMethod,
                        'transaction_reference' => $transactionReference,
                        'notes' => $notes,
                    ]);

                    $remainingPayment -= $amountToPay;
                    $processedInstallments[] = $installment;
                    $lastProcessedInstallment = $installment;
                }

                $currentInstallmentNumber++;
            }

            // Update investor paid_amount
            $investor->paid_amount = ($investor->paid_amount ?? 0) + $paymentAmount;
            $investor->save();

            // Calculate and update next installment date (first unpaid installment)
            $nextInstallment = InvestorInstallment::where('investor_id', $investor->id)
                ->where('status', '!=', InstallmentStatus::Paid)
                ->orderBy('due_date', 'asc')
                ->first();

            $investor->next_installment_date = $nextInstallment?->due_date;
            $investor->save();

            $message = count($processedInstallments) > 1
                ? "Payment processed successfully. {$paymentAmount} applied across ".count($processedInstallments).' installments.'
                : 'Payment processed successfully.';

            return $this->successResponse($message, new InvestorInstallmentResource($lastProcessedInstallment->fresh()->load('investor')));
        });
    }
}
