document.addEventListener('DOMContentLoaded', function () {
    const username = localStorage.getItem('username') || 'User';
    document.getElementById('username').textContent = `Welcome, ${username}`;
  
    const captureBtn = document.getElementById('capture-btn');
    const analysisText = document.getElementById('analysis-text');
    const screenshotImg = document.getElementById('screenshot-img');
    const status = document.getElementById('status');
  
    captureBtn.addEventListener('click', async () => {
      const command = document.getElementById('command').value;
      status.textContent = 'Capturing screen...';
  
      try {
        const stream = await navigator.mediaDevices.getDisplayMedia({ video: true });
        const track = stream.getVideoTracks()[0];
        const imageCapture = new ImageCapture(track);
        const bitmap = await imageCapture.grabFrame();
  
        const canvas = document.createElement('canvas');
        canvas.width = bitmap.width;
        canvas.height = bitmap.height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(bitmap, 0, 0);
  
        canvas.toBlob(async (blob) => {
          const formData = new FormData();
          formData.append('screenshot', blob);
          formData.append('command', command);
  
          const response = await fetch('process.php', {
            method: 'POST',
            body: formData
          });
  
          const result = await response.json();
  
          if (result.analysis) {
            analysisText.textContent = result.analysis;
            screenshotImg.src = result.image_url;
            screenshotImg.style.display = 'block';
            status.textContent = '✅ Analysis complete.';
          } else {
            status.textContent = '❌ Failed to analyze.';
          }
        });
  
        track.stop();
      } catch (error) {
        console.error(error);
        status.textContent = '❌ Error capturing screen.';
      }
    });
  
    document.getElementById('logout-link').addEventListener('click', () => {
      localStorage.clear();
    });
  });
  