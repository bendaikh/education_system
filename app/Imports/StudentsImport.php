<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, WithBatchInserts, WithChunkReading
{
    use SkipsErrors;

    private $importedCount = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Clean and validate the data
        $name = trim($row['name'] ?? '');
        if (empty($name)) {
            return null; // Skip rows without name
        }

        // Generate a unique student ID if not provided
        $studentId = !empty($row['student_id']) ? trim($row['student_id']) : $this->generateStudentId();
        
        // Generate a unique email if not provided
        $email = !empty($row['email']) ? trim($row['email']) : $this->generateEmail($name);

        // Clean phone numbers - convert to string and handle empty values
        $phone = !empty($row['phone']) ? (string) trim($row['phone']) : null;
        $parentPhone = !empty($row['parent_phone']) ? (string) trim($row['parent_phone']) : $phone;

        // Clean other fields
        $address = !empty($row['address']) ? trim($row['address']) : null;
        $notes = !empty($row['notes']) ? trim($row['notes']) : null;

        $this->importedCount++;

        return new Student([
            'student_id' => $studentId,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'parent_phone' => $parentPhone,
            'address' => $address,
            'date_of_birth' => $this->parseDate($row['date_of_birth'] ?? null),
            'notes' => $notes,
            'password' => Hash::make('password123'), // Default password
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:students,email',
            'student_id' => 'nullable|string|unique:students,student_id',
            'phone' => 'nullable|max:20',
            'parent_phone' => 'nullable|max:20',
            'address' => 'nullable|max:500',
            'date_of_birth' => 'nullable|before:today',
            'notes' => 'nullable|max:1000',
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'name.required' => 'Student name is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'Email already exists in the system.',
            'student_id.unique' => 'Student ID already exists in the system.',
            'date_of_birth.before' => 'Date of birth must be before today.',
        ];
    }

    /**
     * Custom validation rules for each row
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($validator->getData() as $rowIndex => $row) {
                // Skip header row
                if ($rowIndex === 0) continue;

                // Validate name
                if (empty(trim($row['name'] ?? ''))) {
                    $validator->errors()->add("{$rowIndex}.name", 'Student name is required.');
                }

                // Validate email format if provided
                if (!empty($row['email']) && !filter_var(trim($row['email']), FILTER_VALIDATE_EMAIL)) {
                    $validator->errors()->add("{$rowIndex}.email", 'Invalid email format.');
                }

                // Validate phone numbers if provided
                if (!empty($row['phone']) && strlen(trim($row['phone'])) > 20) {
                    $validator->errors()->add("{$rowIndex}.phone", 'Phone number is too long.');
                }

                if (!empty($row['parent_phone']) && strlen(trim($row['parent_phone'])) > 20) {
                    $validator->errors()->add("{$rowIndex}.parent_phone", 'Parent phone number is too long.');
                }
            }
        });
    }

    /**
     * Generate a unique student ID
     */
    private function generateStudentId()
    {
        do {
            $studentId = 'STU' . date('Y') . strtoupper(Str::random(6));
        } while (Student::where('student_id', $studentId)->exists());

        return $studentId;
    }

    /**
     * Generate a unique email based on name
     */
    private function generateEmail($name)
    {
        $baseEmail = Str::slug($name) . '@student.academy.com';
        $email = $baseEmail;
        $counter = 1;

        while (Student::where('email', $email)->exists()) {
            $email = Str::slug($name) . $counter . '@student.academy.com';
            $counter++;
        }

        return $email;
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($date)
    {
        if (!$date || empty(trim($date))) {
            return null;
        }

        $date = trim($date);

        // Try to parse the date
        try {
            // Handle common date formats
            $carbon = \Carbon\Carbon::parse($date);
            
            // If the date is in the future, return null
            if ($carbon->isFuture()) {
                return null;
            }
            
            return $carbon->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Get the number of imported rows
     */
    public function getRowCount(): int
    {
        return $this->importedCount;
    }
}
