<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\CreateDoctorRequest;
use App\Http\Requests\Doctor\UpdateDoctorRequest;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    use ApiResponse;

    /**
     * List all doctors.
     */
    public function index(): JsonResponse
    {
        $doctors = Doctor::paginate(10);
        $resourceCollection = DoctorResource::collection($doctors);

        return $this->paginatedResponse('Doctors retrieved successfully', $doctors, $resourceCollection);
    }

    /**
     * Show a specific doctor.
     */
    public function show(Doctor $doctor): JsonResponse
    {
        return $this->successResponse('Doctor retrieved successfully', new DoctorResource($doctor));
    }

    /**
     * Create a new doctor.
     */
    public function store(CreateDoctorRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $data = $this->processFileUploads($request, $validated);

        // Generate doctor identity number if not provided
        if (empty($data['doctor_identity_number'])) {
            $data['doctor_identity_number'] = $this->generateDoctorIdentityNumber();
        }

        // Create doctor
        $doctor = Doctor::create($data);

        return $this->successResponse('Doctor created successfully', new DoctorResource($doctor), 201);
    }

    /**
     * Update a doctor.
     */
    public function update(UpdateDoctorRequest $request, Doctor $doctor): JsonResponse
    {
        $validated = $request->validated();
        $data = $this->processFileUploads($request, $validated, $doctor);

        // Only update fillable fields
        $updateData = [];
        foreach ($doctor->getFillable() as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if (! empty($updateData)) {
            $doctor->update($updateData);
        }

        return $this->successResponse('Doctor updated successfully', new DoctorResource($doctor));
    }

    /**
     * Delete a doctor.
     */
    public function destroy(Doctor $doctor): JsonResponse
    {
        // Delete image if exists
        if ($doctor->image && Storage::disk('public')->exists($doctor->image)) {
            Storage::disk('public')->delete($doctor->image);
        }

        $doctor->delete();

        return $this->successResponse('Doctor deleted successfully');
    }

    /**
     * Handle image upload.
     */
    private function processFileUploads(CreateDoctorRequest|UpdateDoctorRequest $request, array $data, ?Doctor $doctor = null): array
    {
        if ($request->hasFile('image')) {
            // Delete old image if exists (for updates)
            if ($doctor !== null && $doctor->image) {
                $oldImage = $doctor->image;
                if (Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $path = $request->file('image')->store('doctors', 'public');
            $data['image'] = $path;
        }

        return $data;
    }

    /**
     * Generate a unique doctor identity number.
     */
    private function generateDoctorIdentityNumber(): string
    {
        do {
            $identityNumber = 'DOC'.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Doctor::where('doctor_identity_number', $identityNumber)->exists());

        return $identityNumber;
    }
}
