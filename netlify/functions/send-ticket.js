exports.handler = async (event) => {
  // On n'accepte que le POST
  if (event.httpMethod !== "POST") {
    return { statusCode: 405, body: "Method Not Allowed" };
  }

  // Le Webhook est stocké ici, loin des yeux des utilisateurs
  const DISCORD_WEBHOOK_URL = "https://discord.com/api/webhooks/1441523511733522555/Plz2Hc-wDltpyDynyIFdI9sQpQrsYO7PVGkxkkmqfUj-4sXAreggeVTSvyBeNuW4v2Yy";

  try {
    const payload = JSON.parse(event.body);

    const response = await fetch(DISCORD_WEBHOOK_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    return {
      statusCode: 200,
      body: JSON.stringify({ message: "Envoyé avec succès" }),
    };
  } catch (error) {
    return { statusCode: 500, body: error.toString() };
  }
};