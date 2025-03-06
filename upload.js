document.getElementById("uploadForm").addEventListener("submit", function(event) {
    event.preventDefault();

    var fileInput = document.getElementById("fileInput");
    if (fileInput.files.length === 0) {
        alert("Bitte eine Datei auswählen!");
        return;
    }

    var formData = new FormData();
    formData.append("file", fileInput.files[0]);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "upload.php", true);

    var progressBar = document.getElementById("progressBar");
    var progressContainer = document.querySelector(".progress-container");
    var statusMessage = document.getElementById("statusMessage");

    // Fortschrittsbalken sichtbar machen
    progressContainer.style.display = "block";
    progressBar.style.width = "0%";
    progressBar.textContent = "0%";

    // **Fake-Animation: Fortschrittsbalken von 0% auf 100% in 2,5 Sek**
    var fakeProgress = 0;
    var fakeInterval = setInterval(() => {
        if (fakeProgress >= 100) {
            clearInterval(fakeInterval);
        } else {
            fakeProgress += 4; // Gleichmäßige Erhöhung
            progressBar.style.width = fakeProgress + "%";
            progressBar.textContent = fakeProgress + "%";
        }
    }, 100); // 100ms Schrittweite → 2,5 Sek bis 100%

    // **Echter Upload startet nach der Fake-Animation**
    setTimeout(() => {
        xhr.upload.onprogress = function(event) {
            if (event.lengthComputable) {
                var percentComplete = Math.round((event.loaded / event.total) * 100);
                progressBar.style.width = percentComplete + "%";
                progressBar.textContent = percentComplete + "%";
            }
        };

        xhr.onload = function() {
            if (xhr.status == 200) {
                if (xhr.responseText.trim() === "success") {
                    statusMessage.innerHTML = "<p style='color: green;'>✅ Upload erfolgreich! <br><a href='process_file.php'>➡ Weiter zur Verarbeitung</a></p>";
                } else {
                    statusMessage.innerHTML = "<p style='color: red;'>❌ " + xhr.responseText + "</p>";
                }
            }
        };

        xhr.send(formData);
    }, 2500); // Upload startet erst nach 2,5 Sekunden
});
