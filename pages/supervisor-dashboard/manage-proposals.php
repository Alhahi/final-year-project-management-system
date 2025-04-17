<?php
require_once '../../auth/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Proposals - FCI FYP</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="../projects/projects-page.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="../../assets/js/auth.js" defer></script>
    <style>
        .back-btn {
            background-color: #0066cc;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-size: 16px;
            transition: background-color 0.3s;
            margin-left: 10px;
            margin-top: 10px;
        }
        .back-btn:hover {
            background-color: #0052a3;
        }
        .back-btn i {
            font-size: 16px;
        }

        /* New styles for action buttons */
        .action-btn {
            padding: 6px 12px;
            margin: 0 4px;
            font-size: 14px;
            min-width: 80px;
        }

        .approve-btn {
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .reject-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .approve-btn:hover {
            background-color: #218838;
        }

        .reject-btn:hover {
            background-color: #c82333;
        }

        /* Adjust table cell padding */
        .submission-table td {
            padding: 12px 15px;
            vertical-align: middle;
        }

        /* Add flex container for buttons */
        .button-container {
            display: flex;
            gap: 8px;
            justify-content: flex-start;
            align-items: center;
        }
    </style>
</head>
<body>

    <div class="section">
        <button onclick="window.location.href='supervisor-dashboard-page.php'" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </button>
        <h2>Proposal Management</h2>
        
        <div class="project-actions-container">
            <!-- View Pending Proposals Card -->
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h4>Pending Proposals</h4>
                <p>View and manage pending project proposals from students.</p>
                <button class="action-btn" onclick="filterProposals('pending')">
                    View Pending
                </button>
            </div>

            <!-- View Approved Proposals Card -->
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h4>Approved Proposals</h4>
                <p>View your approved project proposals.</p>
                <button class="action-btn" onclick="filterProposals('approved')">
                    View Approved
                </button>
            </div>

            <!-- View Rejected Proposals Card -->
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-times"></i>
                </div>
                <h4>Rejected Proposals</h4>
                <p>View all the rejected project proposals.</p>
                <button class="action-btn" onclick="filterProposals('rejected')">
                    View Rejected
                </button>
            </div>
        </div>

        <!-- Proposal List -->
        <div class="panel-header">
            <h3>Project Proposals</h3>
        </div>
        <table class="submission-table" id="proposalsTable">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Student</th>
                    <th>Submission Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once "../../db_connection.php";
                $conn = OpenCon();
                $supervisor_id = $_SESSION['user_id'];
                
                $sql = "SELECT sp.*, u.full_name as student_name 
                        FROM supervisor_proposals sp 
                        JOIN users u ON sp.student_id = u.user_id 
                        WHERE sp.supervisor_id = ?";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $supervisor_id);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    echo "<tr class='proposal-row' data-status='" . $row['status'] . "'>";
                    echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['student_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['submitted_at']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td>";
                    if ($row['status'] === 'pending') {
                        echo "<div class='button-container'>";
                        echo "<button onclick='approveProposal(" . $row['proposal_id'] . ")' class='action-btn approve-btn'>Approve</button>";
                        echo "<button onclick='rejectProposal(" . $row['proposal_id'] . ")' class='action-btn reject-btn'>Reject</button>";
                        echo "</div>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
                CloseCon($conn);
                ?>
            </tbody>
        </table>
    </div>

    <script>
    function filterProposals(status) {
        const rows = document.querySelectorAll('.proposal-row');
        rows.forEach(row => {
            if (status === 'all' || row.dataset.status === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function approveProposal(proposalId) {
        if (confirm('Are you sure you want to approve this proposal?')) {
            updateProposalStatus(proposalId, 'approved');
        }
    }

    function rejectProposal(proposalId) {
        if (confirm('Are you sure you want to reject this proposal?')) {
            updateProposalStatus(proposalId, 'rejected');
        }
    }

    function updateProposalStatus(proposalId, status) {
        fetch('update_proposal_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `proposal_id=${proposalId}&status=${status}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to update proposal status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the proposal');
        });
    }

    function viewDetails(proposalId) {
        // Implement view details functionality
        alert('View details functionality will be implemented here');
    }
    </script>
</body>
</html> 
