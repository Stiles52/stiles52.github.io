// Variable globale pour bloquer le statut pendant l'hymne
let isAnthemPlaying = false; 

function updateSystemStatus() {
    // SECURITÉ : Si l'hymne joue, on ne touche pas au texte !
    if (isAnthemPlaying) return; 

    const now = new Date();
    const parisString = now.toLocaleString("en-US", {timeZone: "Europe/Paris"});
    const parisTime = new Date(parisString);
    const day = parisTime.getDay();
    const hour = parisTime.getHours();
    
    let isOnline = false;
    if ([3, 4, 5].includes(day) && hour >= 18) isOnline = true;
    if ([4, 5, 6].includes(day) && hour < 1) isOnline = true;
    if ([6, 0].includes(day) && hour >= 16) isOnline = true;
    if ([0, 1].includes(day) && hour < 1) isOnline = true;

    const statusDiv = document.getElementById('system-status');
    if(statusDiv) {
        if (isOnline) {
            statusDiv.textContent = "SYSTEM ONLINE";
            statusDiv.classList.remove('text-red-500', 'text-yellow-400', 'animate-pulse'); // On nettoie bien
            statusDiv.classList.add('text-cyan-400');
        } else {
            statusDiv.textContent = "SYSTEM OFFLINE";
            statusDiv.classList.remove('text-cyan-400', 'text-yellow-400', 'animate-pulse'); // On nettoie bien
            statusDiv.classList.add('text-red-500');
        }
    }
}
updateSystemStatus();
setInterval(updateSystemStatus, 60000);