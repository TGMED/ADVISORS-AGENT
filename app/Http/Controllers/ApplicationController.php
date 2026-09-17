<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStudentApplicationRequest;
use App\Http\Requests\UpdateStudentApplicationRequest;
use App\Http\Resources\ApplicationResource;
use App\Mail\CreateApplicationEmail;
use App\Models\ApplicationStatus;
use App\Models\StudentApplications;
use App\Models\Students;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Storage;
use Throwable;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $agentId = auth()->id();
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 10);
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $applications = StudentApplications::where('agent_id', $agentId)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('course_name', 'like', "%$search%")
                        ->orWhereHas('student', function ($q) use ($search) {
                            $q->where('first_name', 'like', "%$search%")
                                ->orWhere('middle_name', 'like', "%$search%")
                                ->orWhere('last_name', 'like', "%$search%")
                                ->orWhere('email', 'like', "%$search%");
                        });
                });
            })
            ->when($start_date, function ($query) use ($start_date) {
                $query->whereDate('created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                $query->whereDate('created_at', '<=', $end_date);
            })
            ->orderBy('created_at', 'desc')->paginate($perPage);

        return ApplicationResource::collection($applications);
    }

    public function show($id)
    {
        $agentId = auth()->id();
        $application = StudentApplications::where('agent_id', $agentId)
            ->findOrFail($id);

        return $this->respondSuccessWithData(message: 'Application created successfully', data: new ApplicationResource($application));
    }

    /**
     * @throws Throwable
     */
    public function store(CreateStudentApplicationRequest $createStudentApplicationRequest)
    {
        return DB::transaction(function () use ($createStudentApplicationRequest) {
            $student = Students::where('email', $createStudentApplicationRequest->validated('email'))->first();

            if (!$student) {
                $studentData = $createStudentApplicationRequest->only([
                    'first_name',
                    'middle_name',
                    'last_name',
                    'gender',
                    'email',
                    'phone_number',
                    'country',
                    'birth_date',
                    'class_of_degree',
                ]);

                $student = Students::create($studentData);
            }

            $checkStudentApplication = StudentApplications::where('student_id', $student->id)
                ->count();

            if ($checkStudentApplication >= 4) {
                return $this->respondUnprocessed('A student can have only a maximum of 4 applications.');
            }

            $application = StudentApplications::create($createStudentApplicationRequest->validated() + ['student_id' => $student->id]);

            $filePaths = [];
            if ($createStudentApplicationRequest->hasFile('application_documents')) {
                foreach ($createStudentApplicationRequest->file('application_documents') as $file) {
                    if ($file->isValid()) {
                        $path = $file->store('uploads', 'public');
                        $filePaths[] = $path;
                    }
                }

                $application->update([
                    'application_documents' => $filePaths
                ]);
            }

            $application->refresh();

            try {
                Mail::to($student->email)->bcc([config('app.admin_email'), config('app.counselor_email'), auth()->user()->email])
                    ->send(new CreateApplicationEmail($application));
            } catch (Exception $exception) {
                Log::error('Email could not be sent: ' . $exception->getMessage());
            }

            return $this->respondSuccessWithData(message: 'Application created successfully', data: new ApplicationResource($application));
        });
    }

    public function update(UpdateStudentApplicationRequest $updateStudentApplicationRequest, $id)
    {
        $agentId = auth()->id();
        $application = StudentApplications::where('agent_id', $agentId)->findOrFail($id);
        $data = $updateStudentApplicationRequest->validated();

        if ($updateStudentApplicationRequest->hasFile('application_documents')) {
            $filePaths = [];

            foreach ($updateStudentApplicationRequest->file('application_documents') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('uploads', 'public');
                    $filePaths[] = $path;
                }
            }

            $existingDocuments = $application->application_documents ?? [];
            $data['application_documents'] = array_merge($existingDocuments, $filePaths);
        }

        $application->update($data);
        return $this->respondSuccessWithData(message: 'Application updated successfully', data: new ApplicationResource($application));
    }

    public function destroy($id)
    {
        $agentId = auth()->id();
        $application = StudentApplications::where('agent_id', $agentId)->findOrFail($id);
        $application->delete();

        return $this->respondSuccess(message: 'Application deleted successfully');
    }

    public function getApplicationStatus()
    {
        $applicationStatuses = ApplicationStatus::get(['id', 'status_name', 'status_code']);
        return $this->respondSuccessWithData(message: 'Application statuses retrieved successfully', data: $applicationStatuses);
    }

    public function updateApplicationDocument(int $id, Request $request)
    {
        $application = StudentApplications::find($id);

        if (!$application) {
            return $this->respondWithError(message: 'Application not found');
        }

        if (!$request->update_type) {
            return $this->respondWithError(message: 'Update type is required');
        }

        if ($request->update_type === 'delete') {

            if (!$request->document_name) {
                return $this->respondWithError(message: 'Document name is required for deletion');
            }

            $documentToDelete = 'uploads/' . $request->document_name;
            $documents = $application->application_documents ?? [];
            $documents = array_values(array_filter($documents, fn($doc) => $doc !== $documentToDelete));

            if (Storage::disk('public')->exists($documentToDelete)) {
                Storage::disk('public')->delete($documentToDelete);
            }

            $application->application_documents = $documents;
            $application->save();
            return $this->respondSuccess(message: 'Document deleted successfully');
        }

        if ($request->update_type === 'update') {
            $newPaths = [];
            if ($request->hasFile('application_documents')) {
                foreach ($request->file('application_documents') as $file) {
                    if ($file->isValid()) {
                        $newPaths[] = $file->store('uploads', 'public');
                    }
                }
            }

            $application->application_documents = array_merge(
                $application->application_documents ?? [],
                $newPaths
            );

            $application->save();
            return $this->respondSuccess(message: 'Documents updated successfully');
        }

        return $this->respondWithError(message: 'Invalid update type');
    }
}
