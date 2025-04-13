async function captureScreen() {
  const status = document.getElementById("status");
  const result = document.getElementById("result");
  status.innerText = "🕐 Requesting screen...";

  try {
    const stream = await navigator.mediaDevices.getDisplayMedia({ video: true });

    const video = document.createElement("video");
    video.srcObject = stream;
    await video.play();

    const canvas = document.createElement("canvas");
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext("2d");
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    stream.getTracks().forEach(track => track.stop()); // Stop screen capture

    canvas.toBlob(async function(blob) {
      const formData = new FormData();
      formData.append("screenshot", blob);
      formData.append("command", document.getElementById("command").value);

      status.innerText = "📤 Uploading and analyzing...";

      try {
        const response = await fetch("process.php", {
          method: "POST",
          body: formData
        });

        const data = await response.json();

        status.innerText = "✅ Analysis done!";
        result.innerHTML = `
          <p><strong>Analysis Result:</strong> ${data.analysis}</p>
          <img src="${data.screenshot}" style="max-width: 400px;" />
        `;
      } catch (err) {
        status.innerText = "❌ Upload/analysis failed.";
        console.error("Upload failed", err);
      }
    }, "image/jpeg");
  } catch (err) {
    status.innerText = "❌ Failed to capture screen.";
    console.error("Capture error:", err);
  }
}