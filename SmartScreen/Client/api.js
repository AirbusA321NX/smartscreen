async function sendToAPI(command, screenshot) {
  try {
    const response = await fetch("http://localhost/smartscreen_analysis/Server/api/v1/analyze.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        command: command,
        screenshot: screenshot
      })
    });

    const data = await response.json();
    return data;
  } catch (err) {
    console.error("API Error:", err);
    return { success: false, message: "Failed to connect to server." };
  }
}
