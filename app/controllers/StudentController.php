<?php

/**
 * StudentController
 * Full CRUD management for students
 */
class StudentController extends Controller {

    private Student $studentModel;

    public function __construct() {
        $this->studentModel = new Student();
    }

    /**
     * GET /students - List all students with search + pagination
     */
    public function index(array $params = []): void {
        $this->requireAuth();

        $page    = max(1, (int)($this->query('page') ?: 1));
        $perPage = 10;
        $search  = $this->query('search');
        $flash   = $this->getFlash();

        if (!empty($search)) {
            $pagination = $this->studentModel->search($search, $page, $perPage);
        } else {
            $pagination = $this->studentModel->getPaginated($page, $perPage);
        }

        $this->view('students/index', [
            'title'      => 'Students',
            'students'   => $pagination['data'],
            'pagination' => $pagination,
            'search'     => $search,
            'flash'      => $flash,
        ]);
    }

    /**
     * GET /students/create - Show create form
     */
    public function create(array $params = []): void {
        $this->requireAuth();

        $this->view('students/create', [
            'title'     => 'Add New Student',
            'csrfToken' => $this->generateCsrfToken(),
            'flash'     => $this->getFlash(),
            'errors'    => [],
            'old'       => [],
        ]);
    }

    /**
     * POST /students/store - Store new student
     */
    public function store(array $params = []): void {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect(BASE_URL . '/students/create');
        }

        $data = [
            'student_id'    => $this->input('student_id'),
            'matric_number' => $this->input('matric_number'),
            'full_name'     => $this->input('full_name'),
            'department'    => $this->input('department'),
            'faculty'       => $this->input('faculty'),
            'level'         => $this->input('level'),
            'email'         => $this->input('email'),
        ];

        $errors = $this->validate($data, [
            'student_id'    => 'required|min:3|max:30',
            'matric_number' => 'required|min:3|max:30',
            'full_name'     => 'required|min:3|max:150',
            'department'    => 'required|min:2|max:100',
            'faculty'       => 'required|min:2|max:100',
            'level'         => 'required|max:10',
            'email'         => 'required|email|max:150',
        ]);

        // Unique checks
        if ($this->studentModel->matricNumberExists($data['matric_number'])) {
            $errors['matric_number'][] = 'Matric number already exists.';
        }
        if ($this->studentModel->studentIdExists($data['student_id'])) {
            $errors['student_id'][] = 'Student ID already exists.';
        }

        if (!empty($errors)) {
            $this->view('students/create', [
                'title'     => 'Add New Student',
                'csrfToken' => $this->generateCsrfToken(),
                'flash'     => null,
                'errors'    => $errors,
                'old'       => $data,
            ]);
            return;
        }

        $id = $this->studentModel->create($data);

        logActivity('CREATE_STUDENT', "Created student record: {$data['full_name']} ({$data['matric_number']})");
        $this->setFlash('success', "Student '{$data['full_name']}' has been added successfully.");
        $this->redirect(BASE_URL . '/students');
    }

    /**
     * GET /students/edit/{id} - Show edit form
     */
    public function edit(array $params = []): void {
        $this->requireAuth();

        $student = $this->studentModel->findById((int)($params['id'] ?? 0));

        if (!$student) {
            $this->setFlash('danger', 'Student not found.');
            $this->redirect(BASE_URL . '/students');
        }

        $this->view('students/edit', [
            'title'     => 'Edit Student',
            'student'   => $student,
            'csrfToken' => $this->generateCsrfToken(),
            'flash'     => $this->getFlash(),
            'errors'    => [],
        ]);
    }

    /**
     * POST /students/update/{id} - Update student
     */
    public function update(array $params = []): void {
        $this->requireAuth();

        $id = (int)($params['id'] ?? 0);

        if (!$this->validateCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect(BASE_URL . '/students/edit/' . $id);
        }

        $student = $this->studentModel->findById($id);
        if (!$student) {
            $this->setFlash('danger', 'Student not found.');
            $this->redirect(BASE_URL . '/students');
        }

        $data = [
            'student_id'    => $this->input('student_id'),
            'matric_number' => $this->input('matric_number'),
            'full_name'     => $this->input('full_name'),
            'department'    => $this->input('department'),
            'faculty'       => $this->input('faculty'),
            'level'         => $this->input('level'),
            'email'         => $this->input('email'),
        ];

        $errors = $this->validate($data, [
            'student_id'    => 'required|min:3|max:30',
            'matric_number' => 'required|min:3|max:30',
            'full_name'     => 'required|min:3|max:150',
            'department'    => 'required|min:2|max:100',
            'faculty'       => 'required|min:2|max:100',
            'level'         => 'required|max:10',
            'email'         => 'required|email|max:150',
        ]);

        if ($this->studentModel->matricNumberExists($data['matric_number'], $id)) {
            $errors['matric_number'][] = 'Matric number already exists.';
        }
        if ($this->studentModel->studentIdExists($data['student_id'], $id)) {
            $errors['student_id'][] = 'Student ID already exists.';
        }

        if (!empty($errors)) {
            $this->view('students/edit', [
                'title'     => 'Edit Student',
                'student'   => array_merge($student, $data),
                'csrfToken' => $this->generateCsrfToken(),
                'flash'     => null,
                'errors'    => $errors,
            ]);
            return;
        }

        $this->studentModel->update($id, $data);

        logActivity('UPDATE_STUDENT', "Updated student record: {$data['full_name']} ({$data['matric_number']})");
        $this->setFlash('success', "Student '{$data['full_name']}' updated successfully.");
        $this->redirect(BASE_URL . '/students');
    }

    /**
     * POST /students/delete/{id} - Delete student
     */
    public function delete(array $params = []): void {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect(BASE_URL . '/students');
        }

        $id      = (int)($params['id'] ?? 0);
        $student = $this->studentModel->findById($id);

        if (!$student) {
            $this->setFlash('danger', 'Student not found.');
            $this->redirect(BASE_URL . '/students');
        }

        $this->studentModel->delete($id);

        logActivity('DELETE_STUDENT', "Deleted student: {$student['full_name']} ({$student['matric_number']})");
        $this->setFlash('success', "Student '{$student['full_name']}' has been deleted.");
        $this->redirect(BASE_URL . '/students');
    }
}
