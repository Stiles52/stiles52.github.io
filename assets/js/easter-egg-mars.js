// --- EASTER EGG : KONAMI CODE ---
const bootSound = document.getElementById('sfx-boot');
if(bootSound) bootSound.volume = 0.5;

// Code secret : "origin"
let secretCode = ['o', 'r', 'i', 'g', 'i', 'n'];
let currentInput = [];

window.addEventListener('keydown', (e) => {
    if(document.getElementById('easter-egg-console').style.display === 'flex') return;
    if (e.key === "Escape") {
        document.getElementById('easter-egg-console').style.display = 'none';
        return;
    }

    currentInput.push(e.key.toLowerCase());
    if (currentInput.length > secretCode.length) currentInput.shift();
    if (JSON.stringify(currentInput) === JSON.stringify(secretCode)) triggerEasterEgg();
});

function triggerEasterEgg() {
    const consoleScreen = document.getElementById('easter-egg-console');
    const output = document.getElementById('console-output');
    const bar = document.getElementById('console-bar');
    const loadingArea = document.getElementById('loading-area');
    const inputLine = document.getElementById('input-line');
    const inputField = document.getElementById('console-input');
    
    consoleScreen.style.display = 'flex';
    if(bootSound) bootSound.play().catch(() => {});
    
    // Reset
    output.innerHTML = "";
    bar.style.width = "0%";
    loadingArea.classList.remove('hidden');
    inputLine.classList.add('hidden');

    const bootSequence = [
        "> SYSTEM OVERRIDE INITIATED...",
        "> ACCESS LEVEL: GUEST",
        "> CONNECTING TO LOCAL ARCHIVE...",
        "> CONNECTION ESTABLISHED."
    ];

    let i = 0;
    function printBoot() {
        if (i < bootSequence.length) {
            addLine(bootSequence[i]);
            const progress = ((i + 1) / bootSequence.length) * 100;
            bar.style.width = progress + "%";
            i++;
            setTimeout(printBoot, 600);
        } else {
            setTimeout(() => {
                loadingArea.classList.add('hidden');
                addLine("> WELCOME. TYPE 'HELP' FOR COMMANDS.", "text-white blink");
                inputLine.classList.remove('hidden');
                inputField.focus();
            }, 800);
        }
    }
    printBoot();

    // Gestion des commandes
    inputField.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const command = this.value.trim().toLowerCase();
            if(command) {
                addLine(`guest@origin:~$ ${this.value}`, "text-gray-500");
                processCommand(command);
            }
            this.value = "";
            output.scrollTop = output.scrollHeight;
        }
    });

    consoleScreen.addEventListener('click', () => inputField.focus());
}

function addLine(text, className = "") {
    const output = document.getElementById('console-output');
    const p = document.createElement('div');
    p.textContent = text;
    if(className) p.className = className;
    output.appendChild(p);
    output.scrollTop = output.scrollHeight;
}

// --- NOUVELLE LOGIQUE FICHIERS ---

let pendingFileAuth = null; // Pour savoir si on attend un mot de passe

function processCommand(cmd) {
    
    // 1. SI ON ATTEND UN MOT DE PASSE
    if (pendingFileAuth !== null) {
        verifyPassword(pendingFileAuth, cmd);
        return;
    }

    // 2. COMMANDES NORMALES
    const parts = cmd.split(' ');
    const mainCmd = parts[0];
    const arg = parts[1];

    switch(mainCmd) {
        case 'help':
            addLine("AVAILABLE COMMANDS:", "text-yellow-400 font-bold mt-2");
            addLine("  view-files  : List available files");
            addLine("  open [id]   : Open a file (ex: 'open 01')");
            addLine("  status      : Check system integrity");
            addLine("  clear       : Clear terminal");
            addLine("  exit        : Close session");
            break;

        case 'view-files':
            addLine("SCANNING DIRECTORY...", "mt-2");
            setTimeout(() => {
                addLine("------------------------------------------------");
                addLine("[ID: 01] MEMO_CITOYEN.TXT      [PUBLIC]  [READ]");
                addLine("[ID: 02] SYSTEM_FAILURE.LOG    [PROTECTED]");
                addLine("[ID: 03] ADMIN_TRUTH.ENC       [ENCRYPTED]");
                addLine("------------------------------------------------");
                addLine("> USE 'OPEN [ID]' TO READ A FILE.", "text-gray-400");
            }, 500);
            break;

        case 'open':
            if (!arg) {
                addLine("> ERROR: PLEASE SPECIFY FILE ID (EX: 'OPEN 01')", "text-red-500");
            } else if (arg === '01') {
                openFileContent(1);
            } else if (arg === '02') {
                addLine("> SECURITY ALERT: PROTECTED FILE.", "text-yellow-400");
                addLine("> ENTER PASSCODE:", "text-white");
                pendingFileAuth = 2; // Attente mdp fichier 2
            } else if (arg === '03') {
                addLine("> SECURITY ALERT: CLASS-5 ENCRYPTION.", "text-red-500");
                addLine("> ENTER MASTER KEY:", "text-white");
                pendingFileAuth = 3; // Attente mdp fichier 3
            } else {
                addLine(`> ERROR: FILE ID '${arg}' NOT FOUND.`, "text-red-500");
            }
            break;

        case 'status':
            addLine("SYSTEM STATUS:", "text-cyan-400 mt-2");
            addLine("  > SERVER: ONLINE");
            addLine("  > ORBIT: STABLE");
            addLine("  > DATE: 24.02.2203");
            break;

        case 'clear':
            document.getElementById('console-output').innerHTML = "";
            break;

        case 'exit':
            document.getElementById('easter-egg-console').style.display = 'none';
            break;

        case 'phoenix':
        case 'aidan':
        case 'ghost':
        case 'ghost_protocol':
            addLine("> THIS IS A PASSWORD. USE 'OPEN [ID]' FIRST.", "text-gray-500");
            break;

        default:
            addLine(`> COMMAND '${mainCmd}' UNKNOWN.`, "text-red-500");
    }
}

function verifyPassword(fileId, inputPassword) {
    // FICHIER 2 : Mot de passe GHOST_PROTOCOL
    if (fileId === 2) {
        if (inputPassword === "ghost_protocol" || inputPassword === "ghost") {
            addLine("> ACCESS GRANTED.", "text-green-500");
            openFileContent(2);
        } else {
            addLine("> ACCESS DENIED. INCORRECT PASSCODE.", "text-red-500");
            addLine("> HINT: CHECK DEAD LINKS (404).", "text-gray-600 text-[10px]");
        }
        pendingFileAuth = null;
    }
    // FICHIER 3 : Mot de passe AIDAN
    else if (fileId === 3) {
        if (inputPassword === "aidan") {
            if(bootSound) bootSound.play().catch(() => {});
            addLine("> MASTER KEY ACCEPTED.", "text-green-500 font-bold");
            addLine("> DECRYPTING...", "text-green-500 blink");
            setTimeout(() => { openFileContent(3); }, 1500);
        } else {
            addLine("> ACCESS DENIED. MASTER KEY REQUIRED.", "text-red-500");
            addLine("> HINT: THE CREATOR IS ALWAYS WATCHING THE ARCHIVES.", "text-gray-600 text-[10px]"); 
        }
        pendingFileAuth = null;
    }
}

function openFileContent(id) {
    addLine("LOADING FILE...", "text-gray-500 italic");
    setTimeout(() => {
        if (id === 1) {
            addLine("--- MEMO_CITOYEN.TXT ---", "text-cyan-400 font-bold mt-2");
            addLine("DE: ADMINISTRATION CENTRALE");
            addLine("OBJET: RATIONNEMENT HYDRIQUE");
            addLine("Citoyens, suite à une défaillance des filtres du secteur 7, la distribution d'eau potable est réduite de 30% jusqu'à nouvel ordre.");
            addLine("Tout gaspillage sera sanctionné par une amende de Classe 2.");
            addLine("-------------------------");
        } 
        else if (id === 2) {
            addLine("--- SYSTEM_FAILURE.LOG ---", "text-yellow-400 font-bold mt-2");
            addLine("DATE: 2203.02.14");
            addLine("ERREUR: 0x00_GHOST");
            addLine("Rapport: Une intrusion non-identifiée a été détectée dans le mainframe.");
            addLine("La signature numérique ne correspond à aucun citoyen enregistré.");
            addLine("Le signal semble provenir... de l'extérieur de la station.");
            addLine("Note: Supprimer ce log avant l'inspection fédérale.");
            addLine("--------------------------");
        } 
        else if (id === 3) {
            addLine("--- ADMIN_TRUTH.ENC ---", "text-red-500 font-bold mt-2");
            addLine("CLASSIFICATION: ÉCARLATE / YEUX SEULEMENT");
            addLine("EXPÉDITEUR: G. PRODIT");
            addLine("OBJET: VISITE IMPÉRIALE IMMINENTE");
            addLine("MESSAGE DÉCRYPTÉ:");
            addLine("'Cessez toutes les simulations en cours. Olympe Siren arrive.'");
            addLine("'L'Impératrice vient inspecter personnellement le Star Maze pour valider sa réouverture.'");
            addLine("'Elle exige de voir la cohésion entre Humains, Hybrides et Protomates.'");
            addLine("'Nettoyez le secteur 4. Si elle voit une seule défaillance, elle nous fermera définitivement.'");
            addLine("-----------------------");
        }
    }, 600);
}
