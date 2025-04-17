<!-- Supervisor Proposal Modal -->
<div class="modal" id="supervisorProposalModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Submit Proposal to Supervisor</h3>
            <button class="close-btn">&times;</button>
        </div>
        <form id="supervisorProposalForm">
            <div class="form-group">
                <label for="supervisorSelect">Select Supervisor</label>
                <select id="supervisorSelect" required>
                    <option value="">Choose a supervisor...</option>
                    <option value="1">Dr. Smith - AI & Machine Learning</option>
                    <option value="2">Dr. Johnson - Web Development</option>
                    <option value="3">Dr. Williams - Mobile Development</option>
                </select>
            </div>
            <div class="form-group">
                <label for="projectTitle">Project Title</label>
                <input type="text" id="projectTitle" required>
            </div>
            <div class="form-group">
                <label for="projectDescription">Project Description</label>
                <textarea id="projectDescription" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="projectObjectives">Project Objectives</label>
                <textarea id="projectObjectives" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="projectFiles">Project Files</label>
                <div class="file-upload-container">
                    <input type="file" id="projectFiles" multiple class="file-input">
                    <div class="file-list" id="fileList">
                        <!-- Selected files will be listed here -->
                    </div>
                </div>
                <small>You can upload multiple files (documentation, diagrams, etc.)</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="cancel-btn">Cancel</button>
                <button type="submit" class="save-btn">Submit Proposal</button>
            </div>
        </form>
    </div>
</div>