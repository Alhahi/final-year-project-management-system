<!-- Admin Proposal Modal -->
<div class="modal" id="adminProposalModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Submit Proposal to Admin</h3>
            <button class="close-btn">&times;</button>
        </div>
        <form id="adminProposalForm">
            <div class="form-group">
                <label for="adminProjectTitle">Project Title</label>
                <input type="text" id="adminProjectTitle" required>
            </div>
            <div class="form-group">
                <label for="projectField">Field of Study</label>
                <select id="projectField" required>
                    <option value="ai">Artificial Intelligence</option>
                    <option value="ml">Machine Learning</option>
                    <option value="web">Web Development</option>
                    <option value="mobile">Mobile Development</option>
                    <option value="security">Cybersecurity</option>
                </select>
            </div>
            <div class="form-group">
                <label for="adminProjectDescription">Project Description</label>
                <textarea id="adminProjectDescription" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="projectJustification">Project Justification</label>
                <textarea id="projectJustification" rows="3" required 
                    placeholder="Explain why this project should be approved..."></textarea>
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
                <button type="submit" class="save-btn">Submit for Approval</button>
            </div>
        </form>
    </div>
</div>