<?php
/**
 * Vue Chat - Assistant IA avec API intégrée
 */
?>

<div class="chat-view">
    <div class="chat-container">
        <div class="chat-messages" id="chat-messages">
            <div class="message assistant">
                <div class="message-content">
                    <strong>🤖 Assistant SGC-AgentOne</strong><br>
                    Bonjour ! Je suis votre assistant universel. Vous pouvez me demander de :
                    <ul>
                        <li><code>createFile nom.txt : contenu du fichier</code></li>
                        <li><code>readFile nom.txt</code></li>
                        <li><code>listDir dossier</code></li>
                        <li><code>createDir nouveau-dossier</code></li>
                        <li><code>deleteFile nom.txt</code></li>
                    </ul>
                    Tapez votre commande ci-dessous !
                </div>
            </div>
        </div>
        
        <div class="chat-input-container">
            <div class="input-group">
                <input type="text" id="chat-input" placeholder="Tapez votre commande..." autocomplete="off">
                <button id="send-btn" class="btn btn-primary">
                    <span id="send-icon">📤</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.chat-view {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.chat-container {
    flex: 1;
    display: flex;
    flex-direction: column;
    max-height: 100%;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.message {
    max-width: 80%;
    padding: 12px 16px;
    border-radius: 16px;
    font-size: 0.95rem;
    line-height: 1.5;
    word-wrap: break-word;
}

.message.user {
    align-self: flex-end;
    background: var(--accent-secondary);
    color: var(--text-primary);
}

.message.assistant {
    align-self: flex-start;
    background: var(--bg-secondary);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.message-content ul {
    margin: 8px 0;
    padding-left: 20px;
}

.message-content code {
    background: var(--bg-tertiary);
    color: var(--accent-primary);
    padding: 2px 6px;
    border-radius: 4px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.9rem;
}

.chat-input-container {
    padding: 16px 20px;
    background: var(--bg-secondary);
    border-top: 1px solid var(--border-color);
}

.input-group {
    display: flex;
    gap: 12px;
    align-items: center;
}

#chat-input {
    flex: 1;
    padding: 12px 16px;
    border: 1px solid var(--border-color);
    border-radius: 24px;
    background: var(--bg-tertiary);
    color: var(--text-primary);
    font-family: inherit;
    font-size: 0.95rem;
    outline: none;
    transition: border-color 0.2s ease;
}

#chat-input:focus {
    border-color: var(--accent-primary);
    box-shadow: 0 0 0 2px rgba(26, 184, 184, 0.2);
}

#send-btn {
    padding: 12px 16px;
    border-radius: 24px;
    min-width: 60px;
}

#send-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.typing-indicator {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-secondary);
    font-style: italic;
}

.typing-dots {
    display: flex;
    gap: 4px;
}

.typing-dots span {
    width: 6px;
    height: 6px;
    background: var(--accent-primary);
    border-radius: 50%;
    animation: typing 1.4s infinite;
}

.typing-dots span:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-dots span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
        opacity: 0.4;
    }
    30% {
        transform: translateY(-10px);
        opacity: 1;
    }
}

@media (max-width: 768px) {
    .message {
        max-width: 95%;
    }
    
    .chat-input-container {
        padding: 12px 16px;
    }
    
    #chat-input {
        padding: 10px 14px;
        font-size: 0.9rem;
    }
    
    #send-btn {
        padding: 10px 14px;
        min-width: 50px;
    }
}
</style>

<script>
// Initialisation de la vue Chat
window.initView = function() {
    const chatMessages = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const sendBtn = document.getElementById('send-btn');
    const sendIcon = document.getElementById('send-icon');
    
    let isProcessing = false;
    
    // Ajouter un message au chat
    function addMessage(content, type = 'user') {
        const message = document.createElement('div');
        message.className = `message ${type}`;
        
        const messageContent = document.createElement('div');
        messageContent.className = 'message-content';
        
        if (type === 'assistant') {
            messageContent.innerHTML = content;
        } else {
            messageContent.textContent = content;
        }
        
        message.appendChild(messageContent);
        chatMessages.appendChild(message);
        
        // Scroll vers le bas
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        // Animation d'apparition
        message.style.opacity = '0';
        message.style.transform = 'translateY(10px)';
        setTimeout(() => {
            message.style.transition = 'all 0.3s ease';
            message.style.opacity = '1';
            message.style.transform = 'translateY(0)';
        }, 10);
    }
    
    // Afficher l'indicateur de frappe
    function showTypingIndicator() {
        const indicator = document.createElement('div');
        indicator.className = 'message assistant typing-indicator';
        indicator.id = 'typing-indicator';
        indicator.innerHTML = `
            <div class="message-content">
                🤖 Assistant en train d'écrire
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        `;
        chatMessages.appendChild(indicator);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Masquer l'indicateur de frappe
    function hideTypingIndicator() {
        const indicator = document.getElementById('typing-indicator');
        if (indicator) {
            indicator.remove();
        }
    }
    
    // Envoyer un message
    async function sendMessage() {
        const message = chatInput.value.trim();
        if (!message || isProcessing) return;
        
        // Ajouter le message utilisateur
        addMessage(message, 'user');
        chatInput.value = '';
        
        // État de traitement
        isProcessing = true;
        sendBtn.disabled = true;
        sendIcon.textContent = '⏳';
        
        // Afficher l'indicateur de frappe
        showTypingIndicator();
        
        try {
            // Appel à l'API
            const response = await fetch('api/chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message: message })
            });
            
            const result = await response.json();
            
            // Masquer l'indicateur de frappe
            hideTypingIndicator();
            
            if (result.success) {
                addMessage(`✅ <strong>Succès :</strong> ${result.message}`, 'assistant');
                if (result.data) {
                    addMessage(`📊 <strong>Données :</strong><br><pre>${JSON.stringify(result.data, null, 2)}</pre>`, 'assistant');
                }
            } else {
                addMessage(`❌ <strong>Erreur :</strong> ${result.error}`, 'assistant');
            }
            
        } catch (error) {
            hideTypingIndicator();
            addMessage(`🔌 <strong>Erreur de connexion :</strong> ${error.message}`, 'assistant');
        } finally {
            // Réinitialiser l'état
            isProcessing = false;
            sendBtn.disabled = false;
            sendIcon.textContent = '📤';
            chatInput.focus();
        }
    }
    
    // Événements
    sendBtn.addEventListener('click', sendMessage);
    
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    
    // Focus automatique
    chatInput.focus();
    
    // Historique des commandes
    let commandHistory = AgentOne.storage.get('chat_history', []);
    let historyIndex = -1;
    
    chatInput.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (historyIndex < commandHistory.length - 1) {
                historyIndex++;
                chatInput.value = commandHistory[commandHistory.length - 1 - historyIndex];
            }
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (historyIndex > 0) {
                historyIndex--;
                chatInput.value = commandHistory[commandHistory.length - 1 - historyIndex];
            } else if (historyIndex === 0) {
                historyIndex = -1;
                chatInput.value = '';
            }
        }
    });
    
    // Sauvegarder dans l'historique
    const originalSendMessage = sendMessage;
    sendMessage = async function() {
        const message = chatInput.value.trim();
        if (message && !commandHistory.includes(message)) {
            commandHistory.push(message);
            if (commandHistory.length > 50) {
                commandHistory.shift();
            }
            AgentOne.storage.set('chat_history', commandHistory);
        }
        historyIndex = -1;
        await originalSendMessage();
    };
};
</script>