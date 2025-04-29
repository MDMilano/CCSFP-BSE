function resetVote(position) {
    const radioButtons = document.getElementsByName(position);
    radioButtons.forEach(button => {
        button.checked = false;
    });
}

function submitVote() {
    // Implement vote submission logic here
    alert('Vote submission functionality to be implemented.');
}

function updateVotePreview() {
    const sscPreview = document.getElementById('sscVotePreview');
    const iscPreview = document.getElementById('iscVotePreview');
    sscPreview.innerHTML = '';
    iscPreview.innerHTML = '';

    const sscForm = document.getElementById('sscVotingForm');
    const iscForm = document.getElementById('iscVotingForm');

    updatePreviewForForm(sscForm, sscPreview, 'SSC');
    updatePreviewForForm(iscForm, iscPreview, 'ISC');
}

function updatePreviewForForm(form, previewElement, prefix) {
    const formData = new FormData(form);
    for (const [name, value] of formData.entries()) {
        const position = name.replace(prefix.toLowerCase(), '');
        const candidateLabel = document.querySelector(`label[for="${value}"]`);
        let candidateName = 'Unknown';
        
        if (value === 'abstain') {
            candidateName = 'Abstain';
        } else if (candidateLabel) {
            const cardBody = candidateLabel.closest('.card-body');
            if (cardBody) {
                const cardTitle = cardBody.querySelector('.card-title');
                if (cardTitle) {
                    candidateName = cardTitle.textContent.trim();
                }
            }
        }
        
        const li = document.createElement('li');
        li.textContent = `${position}: ${candidateName}`;
        previewElement.appendChild(li);
    }
}

function populateCandidateInfo(candidate) {
    const modalBody = document.querySelector('#candidateInfoModal .modal-body');
    // This is a placeholder. In a real application, you would fetch this data from a server.
    const candidateInfo = {
        johnSmith: {
            name: 'Christian Ocampo',
            party: 'Party A',
            course: 'BS Information Technology',
            year: '3rd Year',
            platform: [
                'Improve campus facilities',
                'Enhance student support services',
                'Promote environmental sustainability'
            ]
        },
        janeDoe: {
            name: 'Marc Daniel Milano',
            party: 'Party B',
            course: 'BS Information Technology',
            year: '3nd Year',
            platform: [
                'Increase student representation in academic decisions',
                'Organize more career development events',
                'Improve campus security measures'
            ]
        },
        mikeJohnson: {
            name: 'John Ivan Miclat',
            party: 'Party C',
            course: 'BS Information Technology',
            year: '3rd Year',
            platform: [
                'Implement a student mentorship program',
                'Enhance campus Wi-Fi infrastructure',
                'Promote diversity and inclusion initiatives'
            ]
        },
        emilyBrown: {
            name: 'Elijah Gonzales',
            party: 'Party A',
            course: 'BS Information Technology',
            year: '3rd Year',
            platform: [
                'Establish mental health awareness programs',
                'Create more study spaces on campus',
                'Organize inter-department academic competitions'
            ]
        },
        davidLee: {
            name: 'Mikko Radores',
            party: 'Party B',
            course: 'BS Information Technology',
            year: '3rd Year',
            platform: [
                'Improve laboratory equipment and facilities',
                'Establish industry partnerships for internships',
                'Promote green initiatives on campus'
            ]
        },
        aliceJohnson: {
            name: 'Nicole Lagman',
            party: 'Jesus Partylist',
            course: 'BS Information Technology',
            year: '3rd Year',
            platform: [
                'Organize entrepreneurship workshops',
                'Improve student organization funding processes',
                'Establish an alumni mentorship program'
            ]
        },
        bobWilson: {
            name: 'Marvin Pamintuan',
            party: 'Party Y',
            course: 'BS Information Technology',
            year: '3rd Year',
            platform: [
                'Create a tech incubator for student startups',
                'Improve campus internet infrastructure',
                'Organize more tech-focused events and hackathons'
            ]
        },
        carolMartinez: {
            name: 'Kyle Maniti',
            party: 'Party Z',
            course: 'BS Information Technology',
            year: '2nd Year',
            platform: [
                'Implement campus-wide recycling programs',
                'Organize environmental awareness campaigns',
                'Establish partnerships with local environmental organizations'
            ]
        }
    };

    const info = candidateInfo[candidate];
    if (info) {
        modalBody.innerHTML = `
            <h4>${info.name}</h4>
            <p><strong>Party:</strong> ${info.party}</p>
            <p><strong>Course:</strong> ${info.course}</p>
            <p><strong>Year:</strong> ${info.year}</p>
            <h5>Platform:</h5>
            <ul>
                ${info.platform.map(item => `<li>${item}</li>`).join('')}
            </ul>
        `;
    } else {
        modalBody.innerHTML = '<p>Candidate information not available.</p>';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const votePreviewButton = document.querySelector('[data-bs-target="#votePreviewModal"]');
    votePreviewButton.addEventListener('click', updateVotePreview);

    const candidateInfoModal = document.getElementById('candidateInfoModal');
    candidateInfoModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const candidate = button.getAttribute('data-candidate');
        populateCandidateInfo(candidate);
    });
});