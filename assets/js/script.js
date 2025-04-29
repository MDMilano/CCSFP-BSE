document.getElementById('signinForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const studentId = document.getElementById('studentId').value;
    const password = document.getElementById('password').value;
    const rememberMe = document.getElementById('rememberMe').checked;
    
    // Here you can add your sign-in logic
    console.log('Student ID:', studentId);
    console.log('Password:', password);
    console.log('Remember Me:', rememberMe);
    
    // For demonstration purposes, we'll just show an alert
    alert('Sign-in attempt with Student ID: ' + studentId + '\nRemember Me: ' + rememberMe);

    // If "Remember me" is checked, save the student ID to localStorage
    if (rememberMe) {
        localStorage.setItem('rememberedStudentId', studentId);
    } else {
        localStorage.removeItem('rememberedStudentId');
    }
});

function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('bi-eye-fill');
        toggleIcon.classList.add('bi-eye-slash-fill');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash-fill');
        toggleIcon.classList.add('bi-eye-fill');
    }
}

function forgotPassword() {
    const studentId = document.getElementById('studentId').value;
    if (studentId) {
        alert('Password reset link sent to the email associated with Student ID: ' + studentId);
    } else {
        alert('Please enter your Student ID to reset your password.');
    }
}

// Check if there's a remembered student ID and pre-fill the input
window.onload = function() {
    const rememberedStudentId = localStorage.getItem('rememberedStudentId');
    if (rememberedStudentId) {
        document.getElementById('studentId').value = rememberedStudentId;
        document.getElementById('rememberMe').checked = true;
    }
};

