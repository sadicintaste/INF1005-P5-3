function inspectCard(name, imgPath, grade, color, floatVal, scratches) {
    // 1. Set the Text Content
    document.getElementById('inspectCardName').innerText = name;
    document.getElementById('inspectGradeLabel').innerText = "✦ " + grade + " ✦";
    document.getElementById('inspectGradeLabel').style.color = color;
    document.getElementById('inspectFloat').innerText = floatVal;

    // 2. Set the Image (Switch to 'high.webp' for the inspection view)
    const highResImg = imgPath + '/high.webp';
    document.getElementById('inspectCardImg').src = highResImg;

    // 3. Apply the Scratches and Grade Filters
    const scratchDiv = document.getElementById('inspectScratches');
    scratchDiv.style.opacity = scratches;
    
    // Apply the same CSS filters we used in the grid
    const imgElement = document.getElementById('inspectCardImg');
    if (floatVal <= 0.01) imgElement.style.filter = "brightness(1.1) contrast(1.1) saturate(1.2)";
    else if (floatVal > 0.50) imgElement.style.filter = "grayscale(0.4) sepia(0.2) brightness(0.8) contrast(0.8)";
    else imgElement.style.filter = "none";

    // 4. Show the Modal (Using Bootstrap's JS API)
    var myModal = new bootstrap.Modal(document.getElementById('cardInspectModal'));
    myModal.show();
}