<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bus Pass Generator</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <style>
    :root {
      --primary-color: #3498db;
      --secondary-color: #2c3e50;
      --accent-color: #e74c3c;
    }
    
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
      padding: 20px 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .container {
      max-width: 900px;
    }
    
    .header {
      text-align: center;
      margin-bottom: 30px;
      color: var(--secondary-color);
      position: relative;
    }
    
    .header h2 {
      font-weight: 700;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
      position: relative;
      display: inline-block;
    }
    
    .header h2::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 4px;
      background: var(--primary-color);
      border-radius: 2px;
    }
    
    .card {
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      border: none;
      overflow: hidden;
      margin-bottom: 25px;
    }
    
    .card-header {
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
      color: white;
      font-weight: 600;
      padding: 15px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    
    .step-indicator {
      display: flex;
      justify-content: center;
      margin-bottom: 25px;
    }
    
    .step {
      display: flex;
      flex-direction: column;
      align-items: center;
      width: 120px;
      position: relative;
    }
    
    .step:not(:last-child)::after {
      content: '';
      position: absolute;
      top: 25px;
      right: -60px;
      width: 120px;
      height: 2px;
      background: #ddd;
    }
    
    .step.active:not(:last-child)::after {
      background: var(--primary-color);
    }
    
    .step-number {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background: #ddd;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      margin-bottom: 8px;
      font-size: 20px;
      transition: all 0.3s;
    }
    
    .step.active .step-number {
      background: var(--primary-color);
      box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
    }
    
    .step.completed .step-number {
      background: var(--secondary-color);
    }
    
    .step-label {
      font-size: 14px;
      color: #777;
      text-align: center;
    }
    
    .step.active .step-label {
      color: var(--secondary-color);
      font-weight: 600;
    }
    
    .form-control, .btn {
      border-radius: 10px;
    }
    
    .btn-primary {
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
      border: none;
      font-weight: 600;
      padding: 12px 25px;
    }
    
    .btn-primary:hover {
      background: linear-gradient(135deg, #2980b9 0%, #1a2530 100%);
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .bus-pass {
      margin-top: 30px;
      padding: 30px;
      background: white;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
      border: 1px solid #e0e0e0;
      position: relative;
    }
    
    .bus-pass::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    }
    
    .bus-pass img {
      max-height: 150px;
      border-radius: 10px;
      border: 2px solid #ddd;
    }
    
    #actionButtons {
      margin-top: 25px;
      display: none;
    }
    
    .action-btn {
      border-radius: 10px;
      font-weight: 600;
      padding: 10px 20px;
      margin: 0 10px;
      transition: all 0.3s;
    }
    
    .action-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .pass-header {
      color: var(--secondary-color);
      border-bottom: 2px solid var(--primary-color);
      padding-bottom: 10px;
      margin-bottom: 20px;
    }
    
    .institute-name {
      font-weight: 700;
      color: var(--secondary-color);
    }
    
    .student-info {
      background: #f8f9fa;
      padding: 15px;
      border-radius: 10px;
      border-left: 4px solid var(--primary-color);
    }
    
    .student-photo-container {
      text-align: center;
      padding: 10px;
      background: #f8f9fa;
      border-radius: 10px;
    }
    
    .form-section {
      display: none;
    }
    
    .form-section.active {
      display: block;
    }
    
    .nav-buttons {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
    }
    
    .receipt-preview {
      max-height: 200px;
      border: 1px dashed #ccc;
      border-radius: 10px;
      padding: 10px;
      margin-top: 10px;
      text-align: center;
    }
    
    .preview-label {
      font-weight: 600;
      color: var(--secondary-color);
      margin-bottom: 10px;
    }
    
    .loading-spinner {
      display: inline-block;
      width: 20px;
      height: 20px;
      border: 3px solid rgba(255,255,255,.3);
      border-radius: 50%;
      border-top-color: #fff;
      animation: spin 1s ease-in-out infinite;
      margin-right: 10px;
    }
    
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
    
    .info-icon {
      color: var(--primary-color);
      margin-left: 5px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h2>📝 Bus Pass Generator</h2>
      <p class="text-muted">Generate your bus pass in three simple steps</p>
    </div>

    <!-- Step Indicator -->
    <div class="step-indicator">
      <div class="step active" id="step1">
        <div class="step-number">1</div>
        <div class="step-label">Student Info</div>
      </div>
      <div class="step" id="step2">
        <div class="step-number">2</div>
        <div class="step-label">Upload Photo</div>
      </div>
      <div class="step" id="step3">
        <div class="step-number">3</div>
        <div class="step-label">Generate Pass</div>
      </div>
    </div>

    <!-- Student Information Form -->
    <div class="card">
      <div class="card-header">
        <span>Student Information</span>
        <span class="badge bg-light text-dark">Step 1 of 3</span>
      </div>
      <div class="card-body">
        <form id="studentForm">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Full Name <span class="text-danger">*</span></label>
              <input type="text" id="fullName" class="form-control" placeholder="Enter your full name" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">USN <span class="text-danger">*</span></label>
              <input type="text" id="usn" class="form-control" placeholder="Enter your USN" required>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Semester <span class="text-danger">*</span></label>
              <select id="semester" class="form-select" required>
                <option value="">Select Semester</option>
                <option value="1st">1st Semester</option>
                <option value="2nd">2nd Semester</option>
                <option value="3rd">3rd Semester</option>
                <option value="4th">4th Semester</option>
                <option value="5th">5th Semester</option>
                <option value="6th">6th Semester</option>
                <option value="7th">7th Semester</option>
                <option value="8th">8th Semester</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Academic Year <span class="text-danger">*</span></label>
              <select id="academicYear" class="form-select" required>
                <option value="">Select Academic Year</option>
                <option value="2023-24">2023-24</option>
                <option value="2024-25">2024-25</option>
                <option value="2025-26">2025-26</option>
              </select>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Stop Name <span class="text-danger">*</span></label>
              <input type="text" id="stopName" class="form-control" placeholder="Enter your bus stop name" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Receipt No. <span class="text-danger">*</span></label>
              <input type="text" id="receiptNo" class="form-control" placeholder="Enter receipt number" required>
            </div>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Fee Paid (₹) <span class="text-danger">*</span></label>
            <input type="number" id="feePaid" class="form-control" placeholder="Enter fee amount" min="0" step="0.01" required>
          </div>
          
          <div class="nav-buttons">
            <div></div> <!-- Empty div for spacing -->
            <button type="button" class="btn btn-primary" id="nextToPhoto">Next: Upload Photo</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Photo Upload Section -->
    <div class="card d-none" id="photoSection">
      <div class="card-header">
        <span>Upload Student Photo</span>
        <span class="badge bg-light text-dark">Step 2 of 3</span>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label">Student Photo <span class="text-danger">*</span></label>
              <input type="file" id="studentPhoto" class="form-control" accept="image/*" required>
              <div class="form-text">Upload a recent passport-size photograph (JPG/PNG)</div>
            </div>
            
            <div class="nav-buttons">
              <button type="button" class="btn btn-outline-secondary" id="backToInfo">Back to Student Info</button>
              <button type="button" class="btn btn-primary" id="nextToPreview">Next: Preview Pass</button>
            </div>
          </div>
          <div class="col-md-6">
            <div class="preview-label">Photo Preview</div>
            <div class="receipt-preview d-flex align-items-center justify-content-center">
              <div id="photoPreview" class="text-muted">
                Your photo will appear here
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pass Preview Section -->
    <div class="card d-none" id="previewSection">
      <div class="card-header">
        <span>Preview Bus Pass</span>
        <span class="badge bg-light text-dark">Step 3 of 3</span>
      </div>
      <div class="card-body">
        <div id="passOutput" class="bus-pass">
          <!-- Bus pass will be generated here -->
        </div>
        
        <div class="nav-buttons mt-4">
          <button type="button" class="btn btn-outline-secondary" id="backToPhoto">Back to Photo Upload</button>
          <button type="button" class="btn btn-success" id="generatePass">Generate Bus Pass</button>
        </div>
      </div>
    </div>

    <!-- Action Buttons (Print/Download) -->
    <div id="actionButtons" class="text-center">
      <button onclick="window.print()" class="btn btn-outline-primary action-btn">🖨️ Print Pass</button>
      <button id="downloadBtn" class="btn btn-outline-success action-btn">⬇️ Download as Image</button>
      <button id="createNewBtn" class="btn btn-outline-info action-btn">🆕 Create New Pass</button>
    </div>
  </div>

  <script>
    // DOM Elements
    const studentForm = document.getElementById('studentForm');
    const photoSection = document.getElementById('photoSection');
    const previewSection = document.getElementById('previewSection');
    const passOutput = document.getElementById('passOutput');
    const actionButtons = document.getElementById('actionButtons');
    const studentPhoto = document.getElementById('studentPhoto');
    const photoPreview = document.getElementById('photoPreview');
    
    // Step Navigation
    document.getElementById('nextToPhoto').addEventListener('click', function() {
      if (studentForm.checkValidity()) {
        studentForm.classList.remove('was-validated');
        document.getElementById('step1').classList.remove('active');
        document.getElementById('step1').classList.add('completed');
        document.getElementById('step2').classList.add('active');
        photoSection.classList.remove('d-none');
        document.querySelector('.card').scrollIntoView({ behavior: 'smooth' });
      } else {
        studentForm.classList.add('was-validated');
      }
    });
    
    document.getElementById('backToInfo').addEventListener('click', function() {
      document.getElementById('step1').classList.add('active');
      document.getElementById('step1').classList.remove('completed');
      document.getElementById('step2').classList.remove('active');
      photoSection.classList.add('d-none');
      document.querySelector('.card').scrollIntoView({ behavior: 'smooth' });
    });
    
    document.getElementById('nextToPreview').addEventListener('click', function() {
      if (studentPhoto.files.length > 0) {
        document.getElementById('step2').classList.remove('active');
        document.getElementById('step2').classList.add('completed');
        document.getElementById('step3').classList.add('active');
        photoSection.classList.add('d-none');
        previewSection.classList.remove('d-none');
        previewPass();
        document.querySelector('.card').scrollIntoView({ behavior: 'smooth' });
      } else {
        alert('Please upload a student photo');
      }
    });
    
    document.getElementById('backToPhoto').addEventListener('click', function() {
      document.getElementById('step2').classList.add('active');
      document.getElementById('step2').classList.remove('completed');
      document.getElementById('step3').classList.remove('active');
      previewSection.classList.add('d-none');
      photoSection.classList.remove('d-none');
      document.querySelector('.card').scrollIntoView({ behavior: 'smooth' });
    });
    
    // Photo Preview
    studentPhoto.addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          photoPreview.innerHTML = `<img src="${e.target.result}" class="img-fluid" style="max-height: 180px;" alt="Photo Preview">`;
        };
        reader.readAsDataURL(file);
      }
    });
    
    // Preview Pass
    function previewPass() {
      const formData = new FormData(studentForm);
      const fullName = document.getElementById('fullName').value;
      const usn = document.getElementById('usn').value;
      const semester = document.getElementById('semester').value;
      const academicYear = document.getElementById('academicYear').value;
      const stopName = document.getElementById('stopName').value;
      const receiptNo = document.getElementById('receiptNo').value;
      const feePaid = document.getElementById('feePaid').value;
      
      let photoURL = '';
      if (studentPhoto.files.length > 0) {
        const reader = new FileReader();
        reader.onload = function(e) {
          photoURL = e.target.result;
          renderPass(fullName, usn, semester, academicYear, stopName, receiptNo, feePaid, photoURL);
        };
        reader.readAsDataURL(studentPhoto.files[0]);
      } else {
        renderPass(fullName, usn, semester, academicYear, stopName, receiptNo, feePaid, '');
      }
    }
    
    // Render Pass
    function renderPass(fullName, usn, semester, academicYear, stopName, receiptNo, feePaid, photoURL) {
      passOutput.innerHTML = `
        <h4 class="text-center institute-name">GEETHA SHISHU SHIKSHANA SANGHA (R)</h4>
        <p class="text-center">INSTITUTE OF ENGINEERING AND TECHNOLOGY FOR WOMEN</p>
        <h5 class="text-center pass-header">BUS PASS : ${academicYear}</h5>
        <div class="row g-3">
          <div class="col-md-8">
            <div class="student-info">
              <p><strong>Name:</strong> ${fullName}</p>
              <p><strong>USN:</strong> ${usn}</p>
              <p><strong>Semester:</strong> ${semester}</p>
              <p><strong>Stop Name:</strong> ${stopName}</p>
              <p><strong>Receipt No:</strong> ${receiptNo}</p>
              <p><strong>Fee Paid:</strong> ₹${parseFloat(feePaid).toLocaleString('en-IN')}</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="student-photo-container">
              ${photoURL ? `<img src="${photoURL}" alt="Student Photo" class="img-thumbnail">` : '<div class="text-muted">No photo uploaded</div>'}
              <p class="mt-2"><em>Authorized Signatory</em></p>
            </div>
          </div>
        </div>
        <div class="mt-3 text-center">
          <small class="text-muted">This pass is property of GSSS IETW. Misuse will lead to cancellation.</small>
        </div>
      `;
    }
    
    // Generate Final Pass
    document.getElementById('generatePass').addEventListener('click', function() {
      actionButtons.style.display = 'block';
      document.getElementById('step3').classList.remove('active');
      document.getElementById('step3').classList.add('completed');
      previewSection.scrollIntoView({ behavior: 'smooth' });
    });
    
    // Download Pass as Image
    document.getElementById('downloadBtn').addEventListener('click', function() {
      html2canvas(passOutput).then(canvas => {
        const link = document.createElement('a');
        link.download = 'bus_pass.png';
        link.href = canvas.toDataURL();
        link.click();
      });
    });
    
    // Create New Pass
    document.getElementById('createNewBtn').addEventListener('click', function() {
      // Reset form
      studentForm.reset();
      studentPhoto.value = '';
      photoPreview.innerHTML = 'Your photo will appear here';
      
      // Reset steps
      document.getElementById('step1').classList.add('active');
      document.getElementById('step1').classList.remove('completed');
      document.getElementById('step2').classList.remove('active');
      document.getElementById('step2').classList.remove('completed');
      document.getElementById('step3').classList.remove('active');
      document.getElementById('step3').classList.remove('completed');
      
      // Hide sections
      photoSection.classList.add('d-none');
      previewSection.classList.add('d-none');
      actionButtons.style.display = 'none';
      
      // Scroll to top
      document.querySelector('.card').scrollIntoView({ behavior: 'smooth' });
    });
  </script>
</body>
</html>
</html>