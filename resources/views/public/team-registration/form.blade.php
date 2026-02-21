<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>B2B Institutional Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .wizard-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 15px;
        }
        .wizard-header {
            background: linear-gradient(225deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 40px 30px;
            border-radius: 15px 15px 0 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .wizard-header h2 {
            position: relative;
            z-index: 1;
            margin-bottom: 10px;
            font-weight: 700;
        }
        .wizard-header p {
            position: relative;
            z-index: 1;
            opacity: 0.9;
        }
        .wizard-body {
            background: white;
            padding: 50px 40px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
            color: #6c757d;
        }
        .step.active {
            background: #1e3c72;
            color: white;
        }
        .step.completed {
            background: #28a745;
            color: white;
        }
        .step-line {
            width: 50px;
            height: 2px;
            background: #e9ecef;
            margin-top: 19px;
        }
        .step-line.completed {
            background: #28a745;
        }
        .form-step {
            display: none;
        }
        .form-step.active {
            display: block;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        .required {
            color: #dc3545;
        }
        .btn-wizard {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 25px;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
        }
        .course-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .course-card:hover {
            border-color: #1e3c72;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .form-check-input:checked {
            background-color: #1e3c72;
            border-color: #1e3c72;
        }
    </style>
</head>
<body style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; padding: 20px 0;">
    <div class="wizard-container">
        <div class="wizard-header">
            <h2><i class="fas fa-building me-2"></i>B2B Institutional Registration</h2>
            <p class="mb-0">Please fill in the details below to register your institution</p>
        </div>
        
        <div class="wizard-body">
            <!-- Progress Bar -->
            <div class="progress mb-4" style="height: 4px;">
                <div class="progress-bar bg-primary" id="progressBar" style="width: 25%"></div>
            </div>
            
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" id="step1">1</div>
                <div class="step-line"></div>
                <div class="step" id="step2">2</div>
                <div class="step-line"></div>
                <div class="step" id="step3">3</div>
                <div class="step-line"></div>
                <div class="step" id="step4">4</div>
            </div>
            
            <form id="registrationForm" action="{{ route('public.team.register.store', $team->id) }}" method="POST">
                @csrf
                
                <!-- Section 1: Institutional Legal Details -->
                <div class="form-step active" id="formStep1">
                    <h4 class="mb-4 text-primary">Section 1: Institutional Legal Details</h4>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Legal Name of the Institution <span class="required">*</span></label>
                        <input type="text" class="form-control" name="legal_name" required placeholder="As per Registration Certificate / Legal Records">
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Institution Category <span class="required">*</span></label>
                        <select class="form-select" name="institution_category" required>
                            <option value="">Select Category</option>
                            @foreach(['School', 'College', 'Academy', 'Training Centre', 'Skill Development Centre', 'Learning Centre', 'Self'] as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Telephone</label>
                        <input type="text" class="form-control" name="telephone" placeholder="Telephone number">
                    </div>
                </div>

                <!-- Section 2: Registered Address Details -->
                <div class="form-step" id="formStep2">
                    <h4 class="mb-4 text-primary">Section 2: Registered Address Details</h4>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Building Name / Floor / Room Number <span class="required">*</span></label>
                            <input type="text" class="form-control" name="building_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Street / Road Name <span class="required">*</span></label>
                            <input type="text" class="form-control" name="street_name" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Locality / Area Name <span class="required">*</span></label>
                            <input type="text" class="form-control" name="locality_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">City <span class="required">*</span></label>
                            <input type="text" class="form-control" name="city" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">PIN Code <span class="required">*</span></label>
                            <input type="number" class="form-control" name="pin_code" required pattern="[0-9]{6}" maxlength="6">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">District <span class="required">*</span></label>
                            <input type="text" class="form-control" name="district" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">State <span class="required">*</span></label>
                            <input type="text" class="form-control" name="state" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Country <span class="required">*</span></label>
                            <input type="text" class="form-control" name="country" required value="India">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Communication Officer Details -->
                <div class="form-step" id="formStep3">
                    <h4 class="mb-4 text-primary">Section 3: Communication Officer Details</h4>

                    <div class="form-group mb-3">
                        <label class="form-label">Full Name <span class="required">*</span></label>
                        <input type="text" class="form-control" name="comm_officer_name" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Official Mobile Number <span class="required">*</span></label>
                            <input type="number" class="form-control" name="comm_officer_mobile" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Alternate Mobile Number</label>
                            <input type="number" class="form-control" name="comm_officer_alt_mobile">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Official WhatsApp Number <span class="required">*</span></label>
                            <input type="number" class="form-control" name="comm_officer_whatsapp" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Official Email ID <span class="required">*</span></label>
                            <input type="email" class="form-control" name="comm_officer_email" required>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Authorized Stakeholder & Courses -->
                <div class="form-step" id="formStep4">
                    <h4 class="mb-4 text-primary">Section 4: Authorized Stakeholder Mapping</h4>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Authorized Person Name <span class="required">*</span></label>
                            <input type="text" class="form-control" name="auth_person_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Designation <span class="required">*</span></label>
                            <input type="text" class="form-control" name="auth_person_designation" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Direct Mobile Number <span class="required">*</span></label>
                            <input type="number" class="form-control" name="auth_person_mobile" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Official Email ID <span class="required">*</span></label>
                            <input type="email" class="form-control" name="auth_person_email" required>
                        </div>
                    </div>

                    <h4 class="mb-3 text-primary">Courses / Academic Delivery Structure</h4>
                    <p class="text-muted mb-3">Please select the courses and delivery structures you are interested in.</p>

                    <div id="course-rows-container">
                        <!-- Initial Row -->
                        <div class="course-row card mb-3" data-row-id="0">
                            <div class="card-body">
                                <div class="row align-items-start">
                                    <div class="col-md-5 mb-3 mb-md-0">
                                        <label class="form-label">Course</label>
                                        <select class="form-select course-select" name="items[0][course_id]" required onchange="handleCourseChange(this)">
                                            <option value="">Select Course</option>
                                            @foreach($courses as $course)
                                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5 mb-3 mb-md-0">
                                        <label class="form-label">Academic Delivery Structure</label>
                                        <div class="delivery-structure-container p-2 border rounded bg-light" style="min-height: 38px; max-height: 200px; overflow-y: auto;">
                                            <small class="text-muted">Select a course first</small>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-row-btn" style="display: none;" onclick="removeRow(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mb-3">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addCourseRow()">
                            <i class="fas fa-plus me-1"></i> Add Another Course
                        </button>
                    </div>

                    <!-- Hidden data for JavaScript -->
                    <script id="course-structure-data" type="application/json">
                        @json($courses->mapWithKeys(function($course) {
                            return [$course->id => $course->academicDeliveryStructures->map(function($structure) {
                                return ['id' => $structure->id, 'title' => $structure->title];
                            })];
                        }))
                    </script>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-secondary btn-wizard" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
                        <i class="fas fa-arrow-left me-2"></i> Previous
                    </button>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-primary btn-wizard" id="nextBtn" onclick="changeStep(1)">
                            Next <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                        <button type="submit" class="btn btn-success btn-wizard" id="submitBtn" style="display: none;">
                            <i class="fas fa-check me-2"></i> Submit Registration
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentStep = 1;
        const totalSteps = 4;
        let courseData = {};
        let itemsCounter = 0;

        document.addEventListener('DOMContentLoaded', function() {
            // Parse course data
            const dataElement = document.getElementById('course-structure-data');
            if (dataElement) {
                courseData = JSON.parse(dataElement.textContent);
            }
            // Trigger initial update for available courses
            updateAvailableCourses();
        });

        function handleCourseChange(selectElement) {
            loadDeliveryStructures(selectElement);
            updateAvailableCourses();
        }

        function removeRow(button) {
            const row = button.closest('.course-row');
            const container = document.getElementById('course-rows-container');
            
            if (container.querySelectorAll('.course-row').length > 1) {
                row.remove();
                updateAvailableCourses();
            }
        }

        function updateAvailableCourses() {
            const selects = document.querySelectorAll('.course-select');
            
            selects.forEach(select => {
                const currentValue = select.value;
                // Get all selected values EXCLUDING this select
                const otherSelectedValues = Array.from(selects)
                    .filter(s => s !== select)
                    .map(s => s.value)
                    .filter(v => v);
                
                const options = select.querySelectorAll('option');
                
                options.forEach(option => {
                    if (!option.value) return; 
                    
                    if (otherSelectedValues.includes(option.value)) {
                         option.hidden = true;
                         option.disabled = true;
                    } else {
                         option.hidden = false;
                         option.disabled = false;
                    }
                });
            });
        }

        function loadDeliveryStructures(selectElement) {
            const courseId = selectElement.value;
            const row = selectElement.closest('.course-row');
            const rowId = row.getAttribute('data-row-id');
            const container = row.querySelector('.delivery-structure-container');
            
            container.innerHTML = ''; // Clear

            if (courseId && courseData[courseId]) {
                 const structures = courseData[courseId];
                 if (structures.length > 0) {
                     structures.forEach(structure => {
                        const div = document.createElement('div');
                        div.className = 'form-check';
                        
                        const input = document.createElement('input');
                        input.className = 'form-check-input';
                        input.type = 'checkbox';
                        input.name = `items[${rowId}][structures][]`;
                        input.value = structure.id;
                        input.id = `structure_${rowId}_${structure.id}`;
                        
                        // We can't make checkboxes 'required' in a group easily with standard HTML5 required 
                        // unless we write custom validation. 
                        // The validateStep function needs to check if at least one is checked.
                        
                        const label = document.createElement('label');
                        label.className = 'form-check-label';
                        label.htmlFor = `structure_${rowId}_${structure.id}`;
                        label.textContent = structure.title;
                        
                        div.appendChild(input);
                        div.appendChild(label);
                        container.appendChild(div);
                     });
                 } else {
                     container.innerHTML = '<small class="text-muted">No structures available</small>';
                 }
            } else {
                 container.innerHTML = '<small class="text-muted">Select a course first</small>';
            }
        }

        function addCourseRow() {
            itemsCounter++;
            const container = document.getElementById('course-rows-container');
            
            // Clone the first row to keep the structure
            const templateRow = container.querySelector('.course-row');
            const newRow = templateRow.cloneNode(true);
            
            newRow.setAttribute('data-row-id', itemsCounter);
            
            // Reset Course Select
            const courseSelect = newRow.querySelector('.course-select');
            courseSelect.value = '';
            courseSelect.name = `items[${itemsCounter}][course_id]`;
            courseSelect.classList.remove('is-invalid');
            
            // Reset Structure Container
            const structureContainer = newRow.querySelector('.delivery-structure-container');
            structureContainer.innerHTML = '<small class="text-muted">Select a course first</small>';
            
            // Show remove button
            const removeBtn = newRow.querySelector('.remove-row-btn');
            removeBtn.style.display = 'inline-block';
            removeBtn.onclick = function() { removeRow(this); };

            container.appendChild(newRow);
            
            updateAvailableCourses();
        }

        function changeStep(direction) {
            // Validate current step before moving next
            if (direction === 1 && !validateStep(currentStep)) {
                return;
            }

            const currentStepElement = document.getElementById(`formStep${currentStep}`);
            let nextStep = currentStep + direction;

            if (nextStep < 1 || nextStep > totalSteps) return;

            // Hide current step
            currentStepElement.classList.remove('active');
            
            // Show next step
            document.getElementById(`formStep${nextStep}`).classList.add('active');
            
            // Update step indicators
            updateIndicators(nextStep);
            
            // Update buttons
            updateButtons(nextStep);
            
            currentStep = nextStep;
        }

        function validateStep(step) {
            const stepElement = document.getElementById(`formStep${step}`);
            const inputs = stepElement.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
                
                // Radio button validation
                if (input.type === 'radio') {
                    const name = input.name;
                    const checked = stepElement.querySelector(`input[name="${name}"]:checked`);
                    if (!checked) {
                        isValid = false;
                    }
                }
            });

            // Special validation for checkboxes in Step 4
            if (step === 4) {
                const rows = stepElement.querySelectorAll('.course-row');
                rows.forEach(row => {
                    // Check if course is selected
                    const courseSelect = row.querySelector('.course-select');
                    if (!courseSelect.value) {
                        isValid = false;
                        courseSelect.classList.add('is-invalid');
                    } else {
                        // If course is selected, check if at least one structure is selected
                        const checkboxes = row.querySelectorAll('input[type="checkbox"]');
                        let checked = false;
                        checkboxes.forEach(cb => {
                            if (cb.checked) checked = true;
                        });
                        
                        // If there are checkboxes (structures available) and none checked
                        if (checkboxes.length > 0 && !checked) {
                            isValid = false;
                            // Maybe add a visual indicator to the container
                            row.querySelector('.delivery-structure-container').classList.add('border-danger');
                        } else {
                            row.querySelector('.delivery-structure-container').classList.remove('border-danger');
                        }
                    }
                });
            }

            return isValid;
        }

        function updateIndicators(step) {
            for (let i = 1; i <= totalSteps; i++) {
                const indicator = document.getElementById(`step${i}`);
                const line = document.querySelector(`.step-indicator .step:nth-child(${i*2-1}) + .step-line`);
                
                if (i < step) {
                    indicator.classList.add('completed');
                    indicator.classList.remove('active');
                    if (line) line.classList.add('completed');
                } else if (i === step) {
                    indicator.classList.add('active');
                    indicator.classList.remove('completed');
                    if (line) line.classList.remove('completed');
                } else {
                    indicator.classList.remove('active', 'completed');
                    if (line) line.classList.remove('completed');
                }
            }
            
            // Update progress bar
            document.getElementById('progressBar').style.width = `${(step/totalSteps)*100}%`;
        }

        function updateButtons(step) {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');

            prevBtn.style.display = step === 1 ? 'none' : 'inline-block';
            
            if (step === totalSteps) {
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'inline-block';
            } else {
                nextBtn.style.display = 'inline-block';
                submitBtn.style.display = 'none';
            }
        }
    </script>
</body>
</html>
