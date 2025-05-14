<?php
// Déterminer quel header inclure en fonction du rôle de l'utilisateur
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    include_once 'includes/backoffice_header.php';
} else {
    include_once 'includes/frontoffice_header.php';
}
?>

<div class="chatbot-container">
    <div class="chatbot-header">
        <h2><i class="fas fa-robot"></i> Assistant virtuel</h2>
    </div>
    
    <div class="chat-messages" id="chatMessages">
        <div class="message bot-message">
            <div class="message-content">
                <p>Bonjour ! Je suis votre assistant virtuel ShareMyRide. Comment puis-je vous aider aujourd'hui ?</p>
            </div>
        </div>
    </div>
    
    <div class="chat-input">
        <form id="chatForm">
            <input type="text" id="userMessage" placeholder="Tapez votre message ici..." required>
            <button type="submit" id="sendButton">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('chatForm');
    const userMessageInput = document.getElementById('userMessage');
    const chatMessages = document.getElementById('chatMessages');
    const sendButton = document.getElementById('sendButton');
    
    // Fonction pour ajouter un message à la conversation
    function addMessage(content, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        
        const messageContent = document.createElement('div');
        messageContent.className = 'message-content';
        
        const messageParagraph = document.createElement('p');
        messageParagraph.textContent = content;
        
        messageContent.appendChild(messageParagraph);
        messageDiv.appendChild(messageContent);
        chatMessages.appendChild(messageDiv);
        
        // Faire défiler vers le bas pour voir le dernier message
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Gérer l'envoi du formulaire
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = userMessageInput.value.trim();
        if (!message) return;
        
        // Afficher le message de l'utilisateur
        addMessage(message, 'user');
        
        // Désactiver le bouton d'envoi pendant le traitement
        sendButton.disabled = true;
        userMessageInput.disabled = true;
        
        // Ajouter un indicateur de chargement
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'message bot-message loading';
        loadingDiv.innerHTML = '<div class="message-content"><p>En train d\'écrire<span class="dot-animation">...</span></p></div>';
        chatMessages.appendChild(loadingDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        // Envoyer le message au serveur
        const formData = new FormData();
        formData.append('message', message);
        
        fetch('index.php?action=process_chatbot_message', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Supprimer l'indicateur de chargement
            chatMessages.removeChild(loadingDiv);
            
            // Afficher la réponse du chatbot
            if (data.response) {
                addMessage(data.response, 'bot');
            } else if (data.error) {
                addMessage("Désolé, une erreur s'est produite. Veuillez réessayer.", 'bot');
                console.error(data.error, data.details);
            }
        })
        .catch(error => {
            // Supprimer l'indicateur de chargement
            if (loadingDiv.parentNode === chatMessages) {
                chatMessages.removeChild(loadingDiv);
            }
            
            // Afficher un message d'erreur
            addMessage("Désolé, une erreur s'est produite. Veuillez réessayer.", 'bot');
            console.error('Error:', error);
        })
        .finally(() => {
            // Réactiver le bouton d'envoi et l'entrée utilisateur
            sendButton.disabled = false;
            userMessageInput.disabled = false;
            userMessageInput.value = '';
            userMessageInput.focus();
        });
    });
});
</script>

<style>
.chatbot-container {
    max-width: 800px;
    margin: 20px auto;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 600px;
}

.chatbot-header {
    background-color: #4a69bd;
    color: white;
    padding: 15px 20px;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

.chatbot-header h2 {
    margin: 0;
    font-size: 1.2rem;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background-color: #f8f9fa;
}

.message {
    margin-bottom: 15px;
    display: flex;
}

.user-message {
    justify-content: flex-end;
}

.bot-message {
    justify-content: flex-start;
}

.message-content {
    max-width: 80%;
    padding: 10px 15px;
    border-radius: 18px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.user-message .message-content {
    background-color: #4a69bd;
    color: white;
    border-bottom-right-radius: 5px;
}

.bot-message .message-content {
    background-color: #e9ecef;
    color: #333;
    border-bottom-left-radius: 5px;
}

.message-content p {
    margin: 0;
    word-wrap: break-word;
}

.chat-input {
    padding: 15px;
    background-color: #fff;
    border-top: 1px solid #e9ecef;
}

.chat-input form {
    display: flex;
    align-items: center;
}

.chat-input input {
    flex: 1;
    padding: 12px 15px;
    border: 1px solid #ced4da;
    border-radius: 50px;
    font-size: 1rem;
    outline: none;
    transition: border-color 0.3s;
}

.chat-input input:focus {
    border-color: #4a69bd;
}

.chat-input button {
    background-color: #4a69bd;
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    margin-left: 10px;
    cursor: pointer;
    transition: background-color 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chat-input button:hover {
    background-color: #3a559c;
}

.chat-input button:disabled {
    background-color: #99a6cc;
    cursor: not-allowed;
}

.loading .message-content p {
    display: flex;
    align-items: center;
}

.dot-animation {
    display: inline-block;
    animation: dotAnimation 1.5s infinite;
}

@keyframes dotAnimation {
    0% { opacity: 0.3; }
    50% { opacity: 1; }
    100% { opacity: 0.3; }
}

@media (max-width: 768px) {
    .chatbot-container {
        margin: 10px;
        height: calc(100vh - 140px);
    }
    
    .message-content {
        max-width: 90%;
    }
}
</style>

<?php
// Inclure le footer approprié en fonction du rôle de l'utilisateur
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    include_once 'includes/backoffice_footer.php';
} else {
    include_once 'includes/frontoffice_footer.php';
}
?> 