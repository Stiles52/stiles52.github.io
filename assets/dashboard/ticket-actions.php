<style>
    .glass-panel {
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(34, 211, 238, 0.2);
        box-shadow: 0 0 30px rgba(34, 211, 238, 0.05);
        transition: all 0.3s ease;
    }

    .active-tab-btn {
        background-color: rgba(34, 211, 238, 0.1);
        border-color: #22d3ee;
        color: #22d3ee;
    }

    .scanline {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(to bottom, transparent 50%, rgba(0,0,0,0.3) 51%);
        background-size: 100% 4px; pointer-events: none; z-index: 40; opacity: 0.3;
    }
    
    .fade-in { animation: fadeIn 0.5s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }


</style>

    <!--<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div onclick="openForm('bug')" class="glass-panel p-6 cursor-pointer hover:bg-orange-900/10 hover:border-orange-500 transition-all group">
            <i data-lucide="bug" class="w-8 h-8 text-orange-500 mb-4 group-hover:scale-110 transition-transform"></i>
            <h3 class="text-lg font-bold text-white">Signaler un Bug</h3>
            <p class="text-xs text-gray-500 mt-2">Glitch, erreur map, item bugué...</p>
        </div>
        <div onclick="openForm('info')" class="glass-panel p-6 cursor-pointer hover:bg-blue-900/10 hover:border-blue-500 transition-all group">
            <i data-lucide="info" class="w-8 h-8 text-blue-500 mb-4 group-hover:scale-110 transition-transform"></i>
            <h3 class="text-lg font-bold text-white">Question / Aide</h3>
            <p class="text-xs text-gray-500 mt-2">Besoin d'aide sur le lore ou le gameplay.</p>
        </div>
        <div onclick="openForm('unban')" class="glass-panel p-6 cursor-pointer hover:bg-red-900/10 hover:border-red-500 transition-all group">
            <i data-lucide="shield-off" class="w-8 h-8 text-red-500 mb-4 group-hover:scale-110 transition-transform"></i>
            <h3 class="text-lg font-bold text-white">Débanissement</h3>
            <p class="text-xs text-gray-500 mt-2">Demande de grâce ou contestation.</p>
        </div>
        <div onclick="openForm('unban')" class="glass-panel p-6 cursor-pointer hover:bg-red-900/10 hover:border-red-500 transition-all group">
            <i data-lucide="shield-off" class="w-8 h-8 text-red-500 mb-4 group-hover:scale-110 transition-transform"></i>
            <h3 class="text-lg font-bold text-white">Spécialisation</h3>
            <p class="text-xs text-gray-500 mt-2">Pour tout développement de compétence.</p>
        </div>
        <div onclick="openForm('unban')" class="glass-panel p-6 cursor-pointer hover:bg-red-900/10 hover:border-red-500 transition-all group">
            <i data-lucide="shield-off" class="w-8 h-8 text-red-500 mb-4 group-hover:scale-110 transition-transform"></i>
            <h3 class="text-lg font-bold text-white">Modération</h3>
            <p class="text-xs text-gray-500 mt-2">Pour tout manque au règlement ou éléments problématique.</p>
        </div>
        <div onclick="openForm('unban')" class="glass-panel p-6 cursor-pointer hover:bg-red-900/10 hover:border-red-500 transition-all group">
            <i data-lucide="shield-off" class="w-8 h-8 text-red-500 mb-4 group-hover:scale-110 transition-transform"></i>
            <h3 class="text-lg font-bold text-white">Scénarisme</h3>
            <p class="text-xs text-gray-500 mt-2">Pour toutes demande de scène précise.</p>
        </div>
    </div>

    <div id="form-container" class="hidden glass-panel p-8 border-t-4 border-cyan-500 relative" style="width: 800px;">
        <button onclick="closeForm()" class="absolute top-4 right-4 text-gray-500 hover:text-white"><i data-lucide="x" class="w-6 h-6"></i></button>
        
        <h3 id="form-title" class="text-2xl font-bold text-white mb-6 uppercase">TITRE DU FORMULAIRE</h3>
        
        <form id="discord-form" onsubmit="sendToDiscord(event)" class="space-y-6">
            <input type="hidden" id="ticket-type" name="type">

            <div>
                <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Votre Pseudo Discord</label>
                <input type="text" id="username" required class="w-full bg-black/50 border border-gray-700 rounded p-3 text-white focus:border-cyan-500 focus:outline-none transition-colors" placeholder="Ex: Pseudo#0000">
            </div>

            <div>
                <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Sujet</label>
                <input type="text" id="subject" required class="w-full bg-black/50 border border-gray-700 rounded p-3 text-white focus:border-cyan-500 focus:outline-none transition-colors" placeholder="Titre de votre demande">
            </div>

            <div>
                <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Détails</label>
                <textarea id="message" required rows="5" maxlength="4000" class="w-full bg-black/50 border border-gray-700 rounded p-3 text-white focus:border-cyan-500 focus:outline-none transition-colors" placeholder="Expliquez votre problème en détails... (Max 4000 caractères)"></textarea>
                <div id="char-count" class="text-right text-[10px] text-gray-500 mt-1 font-mono">0 / 4000</div>
            </div>

            <button type="submit" id="submit-btn" class="w-full bg-cyan-500 hover:bg-cyan-400 text-black font-bold py-3 rounded uppercase tracking-widest transition-colors flex justify-center items-center gap-2">
                <span>Envoyer la demande</span>
                <i data-lucide="send" class="w-4 h-4"></i>
            </button>

            <p id="form-status" class="text-center text-xs mt-2 hidden"></p>
        </form>
    </div>-->

<script>
    lucide.createIcons();

    // GESTION COMPTEUR CARACTÈRES
    const messageInput = document.getElementById('message');
    const charCount = document.getElementById('char-count');

    if(messageInput && charCount) {
        messageInput.addEventListener('input', function() {
            const current = this.value.length;
            const max = this.getAttribute('maxlength');
            charCount.textContent = `${current} / ${max}`;
            
            if(current >= max) {
                charCount.classList.remove('text-gray-500');
                charCount.classList.add('text-red-500');
            } else {
                charCount.classList.add('text-gray-500');
                charCount.classList.remove('text-red-500');
            }
        });
    }

    function switchTab(tab) {
        const rulesView = document.getElementById('view-rules');
        const supportView = document.getElementById('view-support');
        const btnRules = document.getElementById('btn-rules');
        const btnSupport = document.getElementById('btn-support');

        if (tab === 'rules') {
            rulesView.classList.remove('hidden');
            supportView.classList.add('hidden');
            btnRules.classList.add('active-tab-btn', 'text-cyan-400');
            btnRules.classList.remove('text-gray-500', 'border-gray-800');
            btnSupport.classList.remove('active-tab-btn', 'text-cyan-400');
            btnSupport.classList.add('text-gray-500', 'border-gray-800');
        } else {
            rulesView.classList.add('hidden');
            supportView.classList.remove('hidden');
            btnSupport.classList.add('active-tab-btn', 'text-cyan-400');
            btnSupport.classList.remove('text-gray-500', 'border-gray-800');
            btnRules.classList.remove('active-tab-btn', 'text-cyan-400');
            btnRules.classList.add('text-gray-500', 'border-gray-800');
        }
    }

    function openForm(type) {
        const container = document.getElementById('form-container');
        const title = document.getElementById('form-title');
        const inputType = document.getElementById('ticket-type');
        
        container.classList.remove('hidden');
        // Petit délai pour l'animation de scroll
        setTimeout(() => {
            container.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);

        if (type === 'bug') {
            title.textContent = "SIGNALEMENT DE BUG";
            title.className = "text-2xl font-bold text-orange-500 mb-6 uppercase";
            container.className = "glass-panel p-8 border-t-4 border-orange-500 relative fade-in";
            inputType.value = "Bug";
        } else if (type === 'info') {
            title.textContent = "DEMANDE D'INFORMATION";
            title.className = "text-2xl font-bold text-blue-500 mb-6 uppercase";
            container.className = "glass-panel p-8 border-t-4 border-blue-500 relative fade-in";
            inputType.value = "Info";
        } else if (type === 'unban') {
            title.textContent = "DEMANDE DE DÉBANISSEMENT";
            title.className = "text-2xl font-bold text-red-500 mb-6 uppercase";
            container.className = "glass-panel p-8 border-t-4 border-red-500 relative fade-in";
            inputType.value = "Unban";
        }
    }

    function closeForm() {
        document.getElementById('form-container').classList.add('hidden');
    }

    function sendToDiscord(e) {
        e.preventDefault();
        
        const btn = document.getElementById('submit-btn');
        const status = document.getElementById('form-status');
        status.classList.remove('hidden');
        
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-pulse">Envoi en cours...</span>';

        const type = document.getElementById('ticket-type').value;
        const username = document.getElementById('username').value;
        const subject = document.getElementById('subject').value;
        const message = document.getElementById('message').value;

        // Couleurs pour l'embed Discord
        let color = 3447003; // Bleu (Info)
        if (type === 'Bug') color = 15105570; // Orange
        if (type === 'Unban') color = 15158332; // Rouge

        // Construction du message JSON pour Discord
        const payload = {
            username: "Support Fédérale",
            embeds: [{
                title: `Nouveau Ticket : ${type}`,
                description: `**Description :**\n${message}`,
                fields: [
                    { name: "Joueur", value: username, inline: true },
                    { name: "Sujet", value: subject, inline: true }
                ],
                color: color,
                footer: { text: "Envoyé depuis le site Origin • " + new Date().toLocaleString() }
            }]
        };

        // On appelle la fonction locale (qui sera gérée par l'hébergeur)
        // On appelle la fonction locale
        fetch('/.netlify/functions/send-ticket', {
            method: 'POST',
            body: JSON.stringify(payload)
        })
        .then(response => {
            // Si la réponse est positive (Code 200)
            if (response.ok) {
                // 1. Message de succès visuel
                status.textContent = "Message transmis au serveur !";
                status.className = "text-center text-xs mt-2 text-green-400";
                btn.innerHTML = '<span>Envoyé !</span>';
                
                // 2. On vide le formulaire
                document.getElementById('discord-form').reset();
                
                // 3. On ferme après 3 secondes
                setTimeout(() => { 
                    closeForm(); 
                    btn.disabled = false; 
                    btn.innerHTML = '<span>Envoyer la demande</span><i data-lucide="send" class="w-4 h-4"></i>'; 
                    status.textContent = ""; 
                    status.classList.add('hidden'); 
                }, 3000);
            } else {
                // Si le serveur répond une erreur
                throw new Error('Erreur serveur');
            }
        })
        .catch(error => {
            // Si la connexion échoue vraiment
            console.error(error);
            status.textContent = "Une erreur est survenue. Réessayez.";
            status.className = "text-center text-xs mt-2 text-red-500";
            btn.disabled = false;
            btn.innerHTML = '<span>Réessayer</span>';
        });
    }
</script>