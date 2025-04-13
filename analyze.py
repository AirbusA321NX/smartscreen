import sys
import base64
import requests
import json
import os
from PIL import Image, ImageEnhance, ImageFilter
import pytesseract

# Set the tesseract executable path
pytesseract.pytesseract.tesseract_cmd = r"C:\Program Files\Tesseract-OCR\tesseract.exe"

# ===== Input =====
if len(sys.argv) != 3:
    print(json.dumps({"error": "Usage: analyze.py <command> <screenshot_path>"}))
    sys.exit(1)

command = sys.argv[1]
screenshot_path = sys.argv[2]

# ===== Config =====
imagga_api_key = 'acc_7a0f52e2d42c434'
imagga_api_secret = '8ffcac02a14c52c0776e9d7c859ef236'
mistral_api_key = 'JCDn10oH2EaeYPMsXDctjFa5fdqoPPhe'

# ===== Read image =====
if not os.path.exists(screenshot_path):
    print(json.dumps({"error": "Screenshot file not found"}))
    sys.exit(1)

with open(screenshot_path, "rb") as image_file:
    image_data = image_file.read()
    base64_image = base64.b64encode(image_data).decode("utf-8")

# ===== OCR using pytesseract =====
ocr_text = "No OCR text"
try:
    img = Image.open(screenshot_path)
    
    # Preprocess the image for better OCR accuracy
    img = img.convert('L')  # Convert to grayscale
    img = img.filter(ImageFilter.MedianFilter())  # Apply median filter to reduce noise
    img = ImageEnhance.Contrast(img).enhance(2)  # Increase contrast to make text clearer

    ocr_text = pytesseract.image_to_string(img).strip()
    if not ocr_text:
        ocr_text = "No visible text found"
except Exception as e:
    ocr_text = f"OCR error: {str(e)}"

# ===== Imagga Tags =====
tags_output = "No tags found"
try:
    imagga_response = requests.get(
        'https://api.imagga.com/v2/tags',
        params={'image_base64': base64_image},
        auth=(imagga_api_key, imagga_api_secret),
        timeout=30
    )
    if imagga_response.ok:
        result = imagga_response.json()
        tags = result.get("result", {}).get("tags", [])[:5]
        tag_names = [t["tag"]["en"] for t in tags]
        tags_output = ", ".join(tag_names)
except Exception as e:
    tags_output = f"Error getting tags: {str(e)}"

# ===== Mistral Prompt + API Call ========
mistral_prompt = f"""
You are an assistant that provides concise, clear, and structured analysis in bullet-point format.

### Instruction:
Analyze the following command and screen content. Your response must use bullet points and sections. Be crisp and avoid lengthy paragraphs.

### Command:
{command}

### Image Tags:
{tags_output}

### OCR Text from Screen:
{ocr_text}

### Output Format:
✅ **Command Analysis**
- Describe the command intent and what the user likely expects.

📘 **Screen Content Analysis**
- Summarize OCR text findings clearly in bullet points.

🛠️ **Actionable Insights**
- Offer short, helpful suggestions in bullets.

✍️ **Optional Summary or Suggestion**
- Provide a short 1–2 sentence summary, if applicable.

Only return output in the specified format.
""".strip()


mistral_response_text = "API analysis failed"
try:
    response = requests.post(
        "https://api.mistral.ai/v1/chat/completions",
        headers={
            "Authorization": f"Bearer {mistral_api_key}",
            "Content-Type": "application/json"
        },
        json={
            "model": "mistral-small-latest",
            "messages": [{"role": "user", "content": mistral_prompt}],
            "temperature": 0.7,
            "max_tokens": 1000
        },
        timeout=30
    )
    if response.ok:
        result = response.json()
        mistral_response_text = result["choices"][0]["message"]["content"].strip()
except Exception as e:
    mistral_response_text = f"Error calling Mistral API: {str(e)}"

# ===== Final Output =====
print(json.dumps({
    "analysis": f"Tags: {tags_output} | OCR: {ocr_text} | Analysis: {mistral_response_text}"
}))
