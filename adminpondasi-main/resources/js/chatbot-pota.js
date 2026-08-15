class PotaChatbot {
    constructor(config) {
        this.elements = {
            window: document.getElementById(config.windowId),
            input: document.getElementById(config.inputId),
            messages: document.getElementById(config.messagesId),
            toggleBtn: document.getElementById(config.toggleId),
            voiceBtn: document.getElementById(config.voiceBtnId),
            overlay: document.getElementById(config.overlayId),
            status: document.getElementById(config.statusId),
            visualizer: document.getElementById(config.visualizerId)
        };
        
        this.apiUrl = config.apiUrl;
        this.csrfToken = config.csrfToken;
        this.recognition = null;
        this.isCallMode = false;
        
        this.init();
    }

    init() {
        // Setup Event Listeners
        this.elements.toggleBtn.addEventListener('click', () => this.toggleChat());
        this.elements.input.addEventListener('keypress', (e) => {
            if(e.key === 'Enter') this.sendMessage();
        });
        
        // Setup Speech Recognition
        if (window.SpeechRecognition || window.webkitSpeechRecognition) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.recognition = new SpeechRecognition();
            this.recognition.lang = 'id-ID';
            this.recognition.onresult = (event) => this.handleVoiceResult(event);
        }
    }

    toggleChat() {
        this.elements.window.classList.toggle('active');
        // Logic toggle lainnya...
    }

    async sendMessage(text = null) {
        const message = text || this.elements.input.value.trim();
        if (!message) return;

        this.appendMessage(message, 'user');
        this.elements.input.value = '';

        try {
            const res = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: JSON.stringify({ message: message })
            });
            const data = await res.json();
            this.appendMessage(data.reply, 'bot');
            if (this.isCallMode) this.speak(data.reply);
        } catch (e) {
            this.appendMessage("Maaf, POTA sedang pusing.", 'bot');
        }
    }

    appendMessage(text, sender) {
        // Logic append HTML...
        const div = document.createElement('div');
        div.className = `chat-message ${sender}`;
        div.innerHTML = text;
        this.elements.messages.appendChild(div);
    }

    // ... method speak(), toggleVoice(), dll dipindahkan ke sini
}