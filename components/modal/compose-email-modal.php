<!-- Compose Email Modal -->
<div class="modal" id="composeModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Compose Email</h3>
            <button class="close-btn">&times;</button>
        </div>
        <form id="emailForm">
            <div class="form-group">
                <label for="emailTo">To:</label>
                <input type="email" id="emailTo" required>
            </div>
            <div class="form-group">
                <label for="emailSubject">Subject:</label>
                <input type="text" id="emailSubject" required>
            </div>
            <div class="form-group">
                <label for="emailBody">Message:</label>
                <textarea id="emailBody" rows="10" required></textarea>
            </div>
            <div class="form-group">
                <label for="emailAttachments">Attachments:</label>
                <input type="file" id="emailAttachments" multiple>
            </div>
            <div class="modal-footer">
                <button type="button" class="cancel-btn">Cancel</button>
                <button type="submit" class="send-btn">Send</button>
            </div>
        </form>
    </div>
</div>