exports.handler = async (event) => {
  // On n'accepte que le POST
  if (event.httpMethod !== "POST") {
    return { statusCode: 405, body: "Method Not Allowed" };
  }

  // SÉCURITÉ : On récupère le lien depuis les réglages cachés de Netlify
  // Si la variable n'existe pas, on arrête tout pour éviter de planter silencieusement
  const DISCORD_WEBHOOK_URL = process.env.DISCORD_WEBHOOK;

  if (!DISCORD_WEBHOOK_URL) {
    return { statusCode: 500, body: "Erreur configuration serveur : Webhook manquant." };
  }

  try {
    const payload = JSON.parse(event.body);

    const response = await fetch(DISCORD_WEBHOOK_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    if (!response.ok) {
        return { statusCode: response.status, body: "Erreur Discord" };
    }

    return {
      statusCode: 200,
      body: JSON.stringify({ message: "Envoyé avec succès" }),
    };
  } catch (error) {
    return { statusCode: 500, body: error.toString() };
  }
};