<?php

    $ticket_type = array(
        "haut-staff" => "Haut-Staff",
        "modo" => "Modération",
        "recrute" => "Recrutement",
        "qa" => "Question & Réponse",
        "ban" => "Débanissement",
        "developper" => "Développement"
    );

?>

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

<main class="flex items-center w-full h-screen column gap-4 p-8" style="flex-direction: column; overflow-y: auto;">
    <?php
        if(isset($_GET['ticket'])) {
            if($_GET['ticket'] == "bug") {
                ?>
                    <div id="ticket-selection" class="glass-panel p-8 relative" style="width: 1000px;">
                        <h3 id="form-title" class="text-2xl font-bold text-white mb-6 uppercase">Création d'un ticket "Rapport de bug"</h3>

                        <form id="discord-form" onsubmit="sendToDiscord(event)">
                            <div class="w-full mt-8">
                                <label class="block text-cyan-400 uppercase tracking-widest mb-2" style="font-size: 14px;">Quel est le bug que vous souhaitez déclarer</label>
                                <input type="text" id="subject" required style="width: 100%;" placeholder="Un titre clair pour définir le bug">
                            </div>

                            <div class="w-full mt-8">
                                <label class="block text-cyan-400 uppercase tracking-widest mb-2" style="font-size: 14px;">Ou le bug c'est-il produit ?</label>
                                <input type="text" id="subject" required style="width: 100%;" placeholder="Un titre clair pour définir le bug">
                            </div>

                            <div class="w-full mt-8">
                                <label class="block text-cyan-400 uppercase tracking-widest mb-2" style="font-size: 14px;">De quel niveau d'importance concidérez vous ce bug ?</label>
                                <select class="w-full">
                                    <option>Peu impactante</option>
                                    <option>Moyennement impactante</option>
                                    <option>Très impactante</option>
                                    <option>Extrêmement impactante</option>
                                </select>
                                <cite class="mt-3 mb-8">Définissez cette élément avec sérieux, car cela nous aide grandement à nous organiser par rapport à la gestion de ces derniers !</cite>
                            </div>

                            <div class="w-full mt-8">
                                <label class="block text-cyan-400 uppercase tracking-widest mb-2" style="font-size: 14px;">Expliquez en détail le bug</label>
                                <textarea id="message" required rows="5" maxlength="4000" class="w-full bg-black/50 border border-gray-700 rounded p-3 text-white focus:border-cyan-500 focus:outline-none transition-colors" placeholder="Expliquez votre problème en détails... (Max 4000 caractères)"></textarea>
                                <div id="char-count" class="text-right text-[10px] text-gray-500 mt-1 font-mono">0 / 4000</div>
                            </div>

                            <div class="w-full mt-4">
                                <label class="block text-cyan-400 uppercase tracking-widest mb-2" style="font-size: 14px;">Des documents à nous fournir ?</label>
                                <input type="file" id="attachment" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-cyan-500 file:text-black hover:file:bg-cyan-400 transition-colors">
                                <cite class="mt-3 mb-8">
                                    Formats acceptés : PDF, PNG, JPG, TXT<br>
                                    Nombre maximum de fichier : 10 fichiers<br>
                                    Taille maximum : 80Ko
                                </cite>
                            </div>

                            <div class="w-full mt-8">
                                <label class="block text-cyan-400 uppercase tracking-widest mb-2" style="font-size: 14px;">Avez-vous une vidéo à nous fournir ?</label>
                                <input type="url" id="subject" required style="width: 100%;" placeholder="L'URL ici !">
                            </div>

                            <button type="submit" id="submit-btn" class="w-full mt-8 bg-cyan-500 hover:bg-cyan-400 text-black font-bold py-3 rounded uppercase tracking-widest transition-colors flex justify-center items-center gap-2">
                                <span>Envoyer la demande</span>
                                <i data-lucide="send" class="w-4 h-4"></i>
                            </button>

                            <p id="form-status" class="text-center text-xs mt-2 hidden"></p>
                        </form>
                    </div>
                <?php
            } else
            if($_GET['ticket'] == "special") {
                ?>
                    <div id="ticket-selection" class="glass-panel p-8 relative" style="width: 1000px;">
                        <h3 id="form-title" class="text-2xl font-bold text-white mb-6 uppercase">Création d'un ticket "Spécialisation"</h3>

                        <form id="discord-form" onsubmit="sendToDiscord(event)">
                            <div class="w-full mt-8">
                                <label class="block text-cyan-400 uppercase tracking-widest mb-2" style="font-size: 14px;">Quel métier est concerné ?</label>
                                <select class="w-full">
                                    <option>-- Sélectionnez le métier --</option>
                                    <option>Médecin</option>
                                    <option>Artisant</option>
                                    <option>Agriculteur</option>
                                </select>
                            </div>

                            <div class="w-full mt-8">
                                <label class="block text-cyan-400 uppercase tracking-widest mb-2" style="font-size: 14px;">Quel est le niveau visé ?</label>
                                <select class="w-full">
                                    <option>Confirmé</option>
                                    <option>Spécialisé</option>
                                </select>
                            </div>

                            <div class="w-full mt-8">
                                <label class="block text-cyan-400 uppercase tracking-widest mb-2" style="font-size: 14px;">Quel est la spécialité concernés ?</label>
                                <select class="w-full">
                                    <option>-- Sélectionnez une spécialisation --</option>
                                    <option>Dieu</option>
                                    <option>Maître du monde</option>
                                    <option>Bon la vanne à était longue-</option>
                                </select>
                            </div>

                            <div class="w-full mt-8">
                                <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Qui sera votre mentor ?</label>
                                <input type="text" id="subject" required style="width: 100%;" placeholder="De quoi souhaitez-vous parler au Haut-Staff ?">
                                <cite>Si vous le faite en autodidacte, veuillez laissez ce champ vide.</cite>
                            </div>

                            <div class="w-full mt-8">
                                <label class="block text-cyan-400 uppercase tracking-widest mb-2" style="font-size: 14px;">Expliquez-nous pourquoi vous souhaitez apprendre cette spécialité</label>
                                <textarea id="message" required rows="5" maxlength="4000" class="w-full bg-black/50 border border-gray-700 rounded p-3 text-white focus:border-cyan-500 focus:outline-none transition-colors" placeholder="Expliquez votre problème en détails... (Max 4000 caractères)"></textarea>
                                <div id="char-count" class="text-right text-[10px] text-gray-500 mt-1 font-mono">0 / 4000</div>
                            </div>

                            <button type="submit" id="submit-btn" class="w-full mt-8 bg-cyan-500 hover:bg-cyan-400 text-black font-bold py-3 rounded uppercase tracking-widest transition-colors flex justify-center items-center gap-2">
                                <span>Envoyer la demande</span>
                                <i data-lucide="send" class="w-4 h-4"></i>
                            </button>

                            <p id="form-status" class="text-center text-xs mt-2 hidden"></p>
                        </form>
                    </div>
                <?php
            } elseif(array_key_exists($_GET['ticket'], $ticket_type)) {
                ?>
                    <div id="ticket-selection" class="glass-panel p-8 relative" style="width: 1000px;">
                        <h3 id="form-title" class="text-2xl font-bold text-white mb-6 uppercase">Création d'un ticket "<?php echo $ticket_type[$_GET['ticket']] ?>"</h3>

                        <form id="discord-form" onsubmit="sendToDiscord(event)">
                            <input type="hidden" id="ticket-type" name="type">

                            <div class="w-full mt-8">
                                <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Sujet de votre ticket</label>
                                <input type="text" id="subject" required style="width: 100%;" placeholder="De quoi souhaitez-vous parler au Haut-Staff ?">
                            </div>

                            <div class="w-full mt-8">
                                <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Détails</label>
                                <textarea id="message" required rows="5" maxlength="4000" class="w-full bg-black/50 border border-gray-700 rounded p-3 text-white focus:border-cyan-500 focus:outline-none transition-colors" placeholder="Expliquez votre problème en détails... (Max 4000 caractères)"></textarea>
                                <div id="char-count" class="text-right text-[10px] text-gray-500 mt-1 font-mono">0 / 4000</div>
                            </div>

                            <div class="w-full mt-4">
                                <label class="block text-xs text-cyan-400 uppercase tracking-widest mb-2">Souhaitez-vous rajoutez des personnes concernés par le sujet au ticket ?</label>
                                <input type="text" id="subject" required style="width: 100%;" placeholder="De quoi souhaitez-vous parler au Haut-Staff ?">
                            </div>

                            <div class="w-full mt-4">
                                <label class="block text-cyan-400 uppercase tracking-widest mb-2" style="font-size: 14px;">Des documents à nous fournir ?</label>
                                <input type="file" id="attachment" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-cyan-500 file:text-black hover:file:bg-cyan-400 transition-colors">
                                <cite class="mt-3 mb-8">
                                    Formats acceptés : PDF, PNG, JPG<br>
                                    Nombre maximum de fichier : 10 fichiers<br>
                                    Taille maximum : 80Ko
                                </cite>
                            </div>

                            <button type="submit" id="submit-btn" class="w-full bg-cyan-500 hover:bg-cyan-400 text-black font-bold py-3 rounded uppercase tracking-widest transition-colors flex justify-center items-center gap-2">
                                <span>Envoyer la demande</span>
                                <i data-lucide="send" class="w-4 h-4"></i>
                            </button>

                            <p id="form-status" class="text-center text-xs mt-2 hidden"></p>
                        </form>
                    </div>
                <?php
            }
        } else {
        ?>
            <div id="ticket-selection" class="glass-panel p-8 relative" style="width: 800px;">        
                <h3 id="form-title" class="text-2xl font-bold text-white mb-6 uppercase">Sélectionnez votre type de ticket</h3>

                <h5 class="mt-12 mb-4">Administration & Gestion</h5>
                <a href="./dashboard?page=ticket-actions&ticket=haut-staff" class="origin-btn btn--glass w-full mb-4" style="justify-content: left; border-left-color: #991b1b;" data-sound-attached="true">
                    <i data-lucide="crown" class="w-5 h-5 text-red-800"></i>
                    <div style="text-align: left;">
                        Haut-Staff
                        <blockquote class="text-xs text-gray-500 mt-1">
                            Pour toutes demandes spécifiques à l'administration.
                        </blockquote>
                    </div>
                </a>
                <a href="./dashboard?page=ticket-actions&ticket=modo" class="origin-btn btn--glass w-full mb-4" style="justify-content: left; border-left-color: #a855f7;" data-sound-attached="true">
                    <i data-lucide="scale" class="w-5 h-5 text-purple-500"></i>
                    <div style="text-align: left;">
                        Modération
                        <blockquote class="text-xs text-gray-500 m-0">
                            Pour tout signalement de comportement inapproprié ou violation du règlement.
                        </blockquote>
                    </div>
                </a>
                <a href="./dashboard?page=ticket-actions&ticket=recrute" class="origin-btn btn--glass w-full mb-4" style="justify-content: left; border-left-color: #1e40af;" data-sound-attached="true">
                    <i data-lucide="notepad-text" class="w-5 h-5 text-blue-800"></i>
                    <div style="text-align: left;">
                        Recrutement
                        <blockquote class="text-xs text-gray-500 mt-1">
                            Pour tout ce qui touche à vos candidatures ou modifications de votre personnage.
                        </blockquote>
                    </div>
                </a>

                <h5 class="mt-12 mb-4">Support technique</h5>
                <a href="./dashboard?page=ticket-actions&ticket=bug" class="origin-btn btn--glass w-full mb-4" style="justify-content: left; border-left-color: #f97316;" data-sound-attached="true">
                    <i data-lucide="bug" class="w-5 h-5 text-orange-500"></i>
                    <div style="text-align: left;">
                        Signaler un Bug
                        <blockquote class="text-xs text-gray-500 mt-1">
                            Glitch, erreur map, item bugué...
                        </blockquote>
                    </div>
                </a>
                <a href="./dashboard?page=ticket-actions&ticket=qa" class="origin-btn btn--glass w-full mb-4" style="justify-content: left; border-left-color: #3b82f6;" data-sound-attached="true">
                    <i data-lucide="help-circle" class="w-5 h-5 text-blue-500"></i>
                    <div style="text-align: left;">
                        Question & Aide
                        <blockquote class="text-xs text-gray-500 mt-1">
                            Besoin d'aide sur le lore ou le gameplay.
                        </blockquote>
                    </div>
                </a>
                <a href="./dashboard?page=ticket-actions&ticket=ban" class="origin-btn btn--glass w-full mb-4" style="justify-content: left; border-left-color: #ef4444;" data-sound-attached="true">
                    <i data-lucide="shield-off" class="w-5 h-5 text-red-500"></i>
                    <div style="text-align: left;">
                        Débanissement
                        <blockquote class="text-xs text-gray-500 mt-1">
                            Demandes de grâce ou contestations.
                        </blockquote>
                    </div>
                </a>
                <a href="./dashboard?page=ticket-actions&ticket=developper" class="origin-btn btn--glass w-full mb-4" style="justify-content: left; border-left-color: #22c55e;" data-sound-attached="true">
                    <i data-lucide="code" class="w-5 h-5 text-green-500"></i>
                    <div style="text-align: left;">
                        Développement
                        <blockquote class="text-xs text-gray-500 mt-1">
                            Pour toutes demande technique ou de développement.
                        </blockquote>
                    </div>
                </a>

                <h5 class="mt-12 mb-4">Support rôleplay</h5>
                <a href="./dashboard?page=ticket-actions&ticket=special" class="origin-btn btn--glass w-full mb-4" style="justify-content: left; border-left-color: #eab308;" data-sound-attached="true">
                    <i data-lucide="hammer" class="w-5 h-5 text-yellow-500"></i>
                    <div style="text-align: left;">
                        Spécialisations
                        <blockquote class="text-xs text-gray-500 mt-1">
                            Pour tout développement de compétence.
                        </blockquote>
                    </div>
                </a>
                <a href="./dashboard?page=ticket-actions&ticket=scenario" class="origin-btn btn--glass w-full mb-4" style="justify-content: left; border-left-color: #ec4899;" data-sound-attached="true">
                    <i data-lucide="scroll-text" class="w-5 h-5 text-pink-500"></i>
                    <div style="text-align: left;">
                        Scénarisation
                        <blockquote class="text-xs text-gray-500 mt-1">
                            Pour tout vos projets et scène de rôleplay.
                        </blockquote>
                    </div>
                </a>
                <a href="./dashboard?page=ticket-actions&ticket=build" class="origin-btn btn--glass w-full mb-4" style="justify-content: left; border-left-color: #06b6d4;" data-sound-attached="true">
                    <i data-lucide="cuboid" class="w-5 h-5 text-cyan-500"></i>
                    <div style="text-align: left;">
                        Construction
                        <blockquote class="text-xs text-gray-500 mt-1">
                            Pour toutes demandes de construction ou amélioration de construction existant.
                        </blockquote>
                    </div>
                </a>
                <a href="./dashboard?page=ticket-actions&ticket=animation" class="origin-btn btn--glass w-full mb-4" style="justify-content: left; border-left-color: #d946ef;" data-sound-attached="true">
                    <i data-lucide="mic-vocal" class="w-5 h-5 text-fuchsia-500"></i>
                    <div style="text-align: left;">
                        Animation
                        <blockquote class="text-xs text-gray-500 mt-1">
                            Pour toutes demandes de supervision ou d'animation de scène.
                        </blockquote>
                    </div>
                </a>
            </div>
        <?php
        }
    ?>
</main>

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