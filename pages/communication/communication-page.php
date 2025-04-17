<?php
require_once '../../auth/auth_check.php';
require_once '../../components/navbar.php';
require_once './get-user.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCI FYP System - Communication</title>
    <link rel="stylesheet" href="./communication-page.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="../../assets/js/auth.js" defer></script>
</head>
<body>
<?php echo getNavbar('communication'); ?>

<!-- Communication Section -->
<div class="section" id="communication">
    <h2>Communication Center</h2>
    <div class="communication-container">
        <!-- Communication Tabs -->
        <div class="comm-tabs">
            <a href="communication-page.php" class="tab-btn active">
                <i class="fas fa-comments"></i> Chat Room
            </a>
            <a href="email.php" class="tab-btn">
                <i class="fas fa-envelope"></i> Email
            </a>
            <a href="forum.php" class="tab-btn">
                <i class="fas fa-users"></i> Forum
            </a>
        </div>

        <!-- Chat Room Section -->
        <div class="comm-content chat-section active" id="chatSection">
            <div class="chat-container">
                <div class="chat-sidebar">
                    <h3>Chats</h3>
                    <div class="user-list">
                        <?php
                            $users = getUsers();
                            while ($user = $users->fetch_assoc()) {
                                echo '<div class="user-item" data-user-id="' . $user['user_id'] . '">';
                                echo '<span class="user-name">' . $user['full_name'] . '</span>';
                                echo '</div>';
                            }
                        ?>
                    </div>
                </div>
                <div class="chat-main">
                    <div class="chat-messages">

                    </div>
                    <div class="chat-input">
                        <input type="text" placeholder="Type your message..." id="messageInput">
                        <button class="send-btn" id="sendBtn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../components/common/footer.php' ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select the first user by default
        const userItems = document.querySelectorAll('.user-item');
        if (userItems.length > 0) {
            userItems[0].classList.add('active');
            fetchChatMessages(); // Fetch messages for the default user
        }

        // Add click event listeners to user items
        userItems.forEach(userItem => {
            userItem.addEventListener('click', function() {
                // Remove active class from all user items
                userItems.forEach(item => item.classList.remove('active'));
                // Add active class to the clicked user item
                this.classList.add('active');
                // Fetch messages for the selected user
                fetchChatMessages();
            });
        });

        function fetchChatMessages() {
            const activeUserItem = document.querySelector('.user-item.active');
            if (!activeUserItem) {
                console.error('No user selected');
                return; // Exit the function if no user is selected
            }

            const receiverId = activeUserItem.dataset.userId;
            if (!receiverId) {
                console.error('Receiver ID is missing');
                return;
            }

            console.log('Fetching messages for receiver ID:', receiverId); // Debugging step

            fetch(`fetch-message.php?receiver_id=${receiverId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.statusText);
                    }
                    return response.json(); // Parse the response as JSON
                })
                .then(data => {
                    const chatMessages = document.querySelector('.chat-messages');
                    chatMessages.innerHTML = ''; // Clear existing messages
                    
                    if (data.length === 0) {
                        // Handle empty conversation
                        const emptyMessageDiv = document.createElement('div');
                        emptyMessageDiv.classList.add('empty-conversation');
                        emptyMessageDiv.innerHTML = `<p>No messages yet. Start the conversation!</p>`;
                        chatMessages.appendChild(emptyMessageDiv);
                    } else {
                        // Display messages
                        data.forEach(message => {
                            const messageDiv = document.createElement('div');
                            messageDiv.classList.add('message', 
                                message.sender_id == <?php echo $_SESSION['user_id']; ?> ? 'sent' : 'received'
                            );
                            messageDiv.innerHTML = `
                                <div class="message-info">
                                    <span class="sender">${message.sender_name}</span>
                                    <span class="time">${message.sent_at}</span>
                                </div>
                                <div class="message-content">${message.message}</div>
                            `;
                            chatMessages.appendChild(messageDiv);
                        });
                    }

                    // Scroll to the bottom of the chat messages
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                })
                .catch(error => {
                    console.error('Error fetching messages:', error);
                });
        }

        // Send a new message
        document.getElementById('sendBtn').addEventListener('click', function() {
            const messageInput = document.getElementById('messageInput');
            const activeUserItem = document.querySelector('.user-item.active');
            if (!activeUserItem) {
                console.error('No user selected');
                return;
            }

            const receiverId = activeUserItem.dataset.userId;
            const message = messageInput.value.trim();

            if (message) {
                fetch('send-message.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ receiver_id: receiverId, message: message })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        messageInput.value = ''; // Clear input
                        fetchChatMessages(); // Refresh messages
                    }
                })
                .catch(error => console.error('Error sending message:', error));
            }
        });

        // Poll for new messages every 5 seconds
        setInterval(fetchChatMessages, 5000);
    });
</script>
</body>
</html>
