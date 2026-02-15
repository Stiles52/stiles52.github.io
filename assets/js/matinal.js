// --- PROTOCOLE MATINAL (HYMNE AUTOMATIQUE) ---
let anthemPlayed = false; // Verrou pour ne pas lancer le son 60 fois

function checkMorningProtocol() {
    const now = new Date();
    const parisString = now.toLocaleString("en-US", {timeZone: "Europe/Paris"});
    const parisTime = new Date(parisString);
    
    const hour = parisTime.getHours();
    const minute = parisTime.getMinutes();

    // CIBLE : 06h00
    if (hour === 6 && minute === 0) {
        
        if (!anthemPlayed) {
            anthemPlayed = true;
            isAnthemPlaying = true; // ON VERROUILLE L'AFFICHAGE
            
            // 1. Son
            const anthem = document.getElementById('sfx-anthem');
            if (anthem) {
                anthem.volume = 0.5;
                anthem.play().catch(() => {});
            }

            // 2. Console (si ouverte)
            const consoleDisplay = document.getElementById('easter-egg-console').style.display;
            if (consoleDisplay === 'flex') {
                addLine("----------------------------------------", "text-yellow-400");
                addLine("> ATTENTION : PROTOCOLE MATINAL ACTIVÉ.", "text-yellow-400 blink");
                addLine("> GLOIRE À LA FÉDÉRATION.", "text-cyan-400 font-bold");
                addLine("----------------------------------------", "text-yellow-400");
            }

            // 3. Statut Visuel (On force l'affichage)
            const statusDiv = document.getElementById('system-status');
            if(statusDiv) {
                statusDiv.textContent = "HYMNE FÉDÉRAL EN COURS...";
                // On enlève les couleurs classiques pour éviter les conflits
                statusDiv.classList.remove('text-cyan-400', 'text-red-500'); 
                statusDiv.classList.add('text-yellow-400', 'animate-pulse');
                
                // Au bout de 120 secondes (fin de la minute), on déverrouille
                setTimeout(() => { 
                    isAnthemPlaying = false; // ON DÉVERROUILLE
                    updateSystemStatus(); // On remet le texte normal immédiatement
                }, 120000);
            }
        }
    } else {
        anthemPlayed = false;
    }
}
// Le reste ne change pas (setInterval...)
setInterval(checkMorningProtocol, 1000);