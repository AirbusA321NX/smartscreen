document.addEventListener('keydown', (e) => {
  if (e.ctrlKey && e.key.toLowerCase() === 'x') {
    const popup = document.getElementById('floating-ui');
    popup.classList.toggle('hidden');
  }
});

document.getElementById('analyze-btn').addEventListener('click', async () => {
  const command = document.getElementById('command-input').value.trim();
  if (!command) return alert("Please enter a command.");

  const screenshot = await captureScreenAsBase64();
  if (!screenshot) return alert("Failed to capture screen.");

  const result = await sendToAPI(command, screenshot);
  if (result.success) {
    alert("✅ Analysis Result:\n\n" + result.message);
  } else {
    alert("❌ Failed:\n\n" + result.message);
  }
});
