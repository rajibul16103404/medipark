<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Department\AssignBlogsRequest;
use App\Http\Requests\Department\AssignDoctorsRequest;
use App\Http\Requests\Department\CreateDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Blog;
use App\Models\Department;
use App\Models\Doctor;
use App\Status;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class DepartmentController extends Controller
{
    use ApiResponse;

    /**
     * List all departments.
     */
    public function index(): JsonResponse
    {
        $departments = Department::paginate(10);

        // Load doctors and blogs for each department
        $departments->getCollection()->transform(function ($department) {
            $department->loaded_doctors = $this->loadDoctorsForDepartment($department);
            $department->loaded_blogs = $this->loadBlogsForDepartment($department);

            return $department;
        });

        $resourceCollection = DepartmentResource::collection($departments);

        return $this->paginatedResponse('Departments retrieved successfully', $departments, $resourceCollection);
    }

    /**
     * Show a specific department.
     */
    public function show(Department $department): JsonResponse
    {
        // Load doctors and blogs for the department
        $department->loaded_doctors = $this->loadDoctorsForDepartment($department);
        $department->loaded_blogs = $this->loadBlogsForDepartment($department);

        return $this->successResponse('Department retrieved successfully', new DepartmentResource($department));
    }

    /**
     * Create a new department.
     */
    public function store(CreateDepartmentRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $data = $this->processFileUploads($request, $validated);

        // Validate doctor assignments
        if (! empty($data['doctors'])) {
            $error = $this->validateDoctorAssignments($data['doctors']);
            if ($error !== null) {
                return $this->errorResponse($error, 422);
            }
        }

        // Accordions should be in validated data, but ensure it's properly formatted
        if (isset($data['accordions']) && is_array($data['accordions']) && ! empty($data['accordions'])) {
            // Filter out any invalid entries
            $accordions = array_filter($data['accordions'], function ($accordion) {
                return isset($accordion) && is_array($accordion) && (! empty($accordion['title']) || ! empty($accordion['description']));
            });
            $data['accordions'] = ! empty($accordions) ? array_values($accordions) : null;
        } else {
            // If not in validated or empty, check raw input as fallback
            $rawAccordions = $request->input('accordions');
            if (! empty($rawAccordions) && is_array($rawAccordions)) {
                $accordions = array_filter($rawAccordions, function ($accordion) {
                    return isset($accordion) && is_array($accordion) && (! empty($accordion['title']) || ! empty($accordion['description']));
                });
                $data['accordions'] = ! empty($accordions) ? array_values($accordions) : null;
            } else {
                $data['accordions'] = null;
            }
        }

        $department = Department::create($data);

        // Load doctors and blogs for response
        $department->loaded_doctors = $this->loadDoctorsForDepartment($department);
        $department->loaded_blogs = $this->loadBlogsForDepartment($department);

        return $this->successResponse('Department created successfully', new DepartmentResource($department), 201);
    }

    /**
     * Update a department.
     */
    public function update(UpdateDepartmentRequest $request, Department $department): JsonResponse
    {
        $validated = $request->validated();
        $data = $this->processFileUploads($request, $validated, $department);

        $updateData = [];
        foreach ($department->getFillable() as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        // Validate doctor assignments if doctors are being updated
        if (isset($updateData['doctors'])) {
            $error = $this->validateDoctorAssignments($updateData['doctors'], $department->id);
            if ($error !== null) {
                return $this->errorResponse($error, 422);
            }
        }

        // Handle accordions from request input (form data like accordions[0][title])
        if ($request->has('accordions')) {
            $accordions = $request->input('accordions');
            if (is_array($accordions)) {
                // Filter out empty accordion entries
                $accordions = array_filter($accordions, function ($accordion) {
                    return ! empty($accordion['title']) || ! empty($accordion['description']);
                });
                $updateData['accordions'] = array_values($accordions); // Re-index array
            } else {
                $updateData['accordions'] = null;
            }
        }

        if (! empty($updateData)) {
            $department->update($updateData);
            $department = $department->fresh();
        }

        // Load doctors and blogs for response
        $department->loaded_doctors = $this->loadDoctorsForDepartment($department);
        $department->loaded_blogs = $this->loadBlogsForDepartment($department);

        return $this->successResponse('Department updated successfully', new DepartmentResource($department));
    }

    /**
     * Delete a department.
     */
    public function destroy(Department $department): JsonResponse
    {
        if ($department->image && Storage::disk('public')->exists($department->image)) {
            Storage::disk('public')->delete($department->image);
        }

        $department->delete();

        return $this->successResponse('Department deleted successfully');
    }

    /**
     * Toggle department status between active and inactive.
     */
    public function setActive(Department $department): JsonResponse
    {
        // Toggle status: if active, make inactive; if inactive, make active
        $newStatus = $department->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        $department->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'Department set as active successfully'
            : 'Department set as inactive successfully';

        $department = $department->fresh();

        // Load doctors and blogs for response
        $department->loaded_doctors = $this->loadDoctorsForDepartment($department);
        $department->loaded_blogs = $this->loadBlogsForDepartment($department);

        return $this->successResponse($statusMessage, new DepartmentResource($department));
    }

    /**
     * Assign doctors to a department.
     */
    public function assignDoctors(AssignDoctorsRequest $request, Department $department): JsonResponse
    {
        $doctorIds = $request->input('doctor_ids', []);

        // Validate doctor assignments
        $error = $this->validateDoctorAssignments($doctorIds, $department->id);
        if ($error !== null) {
            return $this->errorResponse($error, 422);
        }

        // Update department with doctor IDs array
        $department->update(['doctors' => $doctorIds]);
        $department = $department->fresh();

        // Load doctors and blogs for response
        $department->loaded_doctors = $this->loadDoctorsForDepartment($department);
        $department->loaded_blogs = $this->loadBlogsForDepartment($department);

        return $this->successResponse('Doctors assigned to department successfully', new DepartmentResource($department));
    }

    /**
     * Assign blogs to a department.
     */
    public function assignBlogs(AssignBlogsRequest $request, Department $department): JsonResponse
    {
        $blogIds = $request->input('blog_ids', []);

        // Update department with blog IDs array
        $department->update(['blogs' => $blogIds]);
        $department = $department->fresh();

        // Load doctors and blogs for response
        $department->loaded_doctors = $this->loadDoctorsForDepartment($department);
        $department->loaded_blogs = $this->loadBlogsForDepartment($department);

        return $this->successResponse('Blogs assigned to department successfully', new DepartmentResource($department));
    }

    /**
     * Check if doctors are already assigned to another department.
     */
    private function validateDoctorAssignments(array $doctorIds, ?int $excludeDepartmentId = null): ?string
    {
        if (empty($doctorIds)) {
            return null;
        }

        $query = Department::whereNotNull('doctors');

        if ($excludeDepartmentId !== null) {
            $query->where('id', '!=', $excludeDepartmentId);
        }

        $conflictingDepartments = $query->get()
            ->filter(function ($otherDepartment) use ($doctorIds) {
                $otherDoctorIds = $otherDepartment->doctors ?? [];

                return ! empty(array_intersect($doctorIds, $otherDoctorIds));
            })
            ->map(function ($department) use ($doctorIds) {
                $otherDoctorIds = $department->doctors ?? [];
                $conflictingIds = array_intersect($doctorIds, $otherDoctorIds);

                // Fetch doctor names
                $doctors = Doctor::whereIn('id', $conflictingIds)->get();
                $doctorNames = $doctors->pluck('doctor_name')->toArray();

                return [
                    'department_id' => $department->id,
                    'department_title' => $department->title,
                    'doctor_ids' => array_values($conflictingIds),
                    'doctor_names' => $doctorNames,
                ];
            })
            ->values();

        if ($conflictingDepartments->isNotEmpty()) {
            $errorMessage = 'One or more doctors are already assigned to another department: ';
            $errors = $conflictingDepartments->map(function ($conflict) {
                $doctorNamesList = implode(', ', $conflict['doctor_names']);

                return "Doctors [{$doctorNamesList}] are already assigned to department '{$conflict['department_title']}' (ID: {$conflict['department_id']})";
            })->implode('; ');

            return $errorMessage.$errors;
        }

        return null;
    }

    /**
     * Load doctors for a department based on doctor IDs.
     */
    private function loadDoctorsForDepartment(Department $department): array
    {
        $doctorIds = $department->doctors ?? [];

        if (empty($doctorIds)) {
            return [];
        }

        return Doctor::whereIn('id', $doctorIds)->get()->toArray();
    }

    /**
     * Load blogs for a department based on blog IDs.
     */
    private function loadBlogsForDepartment(Department $department): array
    {
        $blogIds = $department->blogs ?? [];

        if (empty($blogIds)) {
            return [];
        }

        return Blog::whereIn('id', $blogIds)->get()->toArray();
    }

    /**
     * Handle image upload.
     */
    private function processFileUploads(CreateDepartmentRequest|UpdateDepartmentRequest $request, array $data, ?Department $department = null): array
    {
        if ($request->hasFile('image')) {
            if ($department !== null && $department->image) {
                $oldImage = $department->image;
                if (Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $path = $request->file('image')->store('departments', 'public');
            $data['image'] = $path;
        }

        return $data;
    }
}
