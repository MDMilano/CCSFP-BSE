document.addEventListener('DOMContentLoaded', function() {
    const courseSelect = document.getElementById('course');
    const majorContainer = document.getElementById('majorContainer');
    const majorSelect = document.getElementById('major');
    const signupForm = document.getElementById('signupForm');

    const majors = {
        BSSE: ['Mathematics', 'English', 'Science'],
        BSBA: ['Marketing Management', 'Financial Management', 'Human Resource Management', 'Operation Management']
    };

    courseSelect.addEventListener('change', function() {
        const selectedCourse = this.value;
        if (majors[selectedCourse]) {
            majorContainer.style.display = 'block';
            majorSelect.innerHTML = '<option value="">Select a major</option>';
            majors[selectedCourse].forEach(major => {
                const option = document.createElement('option');
                option.value = major;
                option.textContent = major;
                majorSelect.appendChild(option);
            });
        } else {
            majorContainer.style.display = 'none';
            majorSelect.innerHTML = '<option value="">Select a major</option>';
        }
    });

    signupForm.addEventListener('submit', function(e) {
        e.preventDefault();
        // Here you would typically send the form data to a server
        console.log('Form submitted');
        alert('Sign-up form submitted successfully!');
    });
});