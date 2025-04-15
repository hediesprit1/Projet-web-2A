document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("inscriptionForm");

  form.addEventListener("submit", function (e) {
    let valid = true;

    // Clear all previous errors
    document.querySelectorAll(".text-danger").forEach(span => span.textContent = "");

    // CIN validation
    const cin = document.getElementById("cin").value.trim();
    if (cin === "") {
      document.getElementById("error-cin").textContent = "Le CIN est requis.";
      valid = false;
    }

    // Nom validation
    const nom = document.getElementById("nom").value.trim();
    if (nom === "") {
      document.getElementById("error-nom").textContent = "Le nom est requis.";
      valid = false;
    }

    // Date de naissance validation
    const daten = document.getElementById("daten").value;
    const today = new Date().toISOString().split('T')[0]; // Get current date in YYYY-MM-DD format
    if (daten === "") {
      document.getElementById("error-daten").textContent = "La date de naissance est requise.";
      valid = false;
    } else if (daten >= today) {
      document.getElementById("error-daten").textContent = "La date de naissance doit être antérieure à la date actuelle.";
      valid = false;
    }

    // Email validation
    const email = document.getElementById("email").value.trim();
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (email === "") {
      document.getElementById("error-email").textContent = "L'email est requis.";
      valid = false;
    } else if (!emailPattern.test(email)) {
      document.getElementById("error-email").textContent = "L'email n'est pas valide.";
      valid = false;
    }

    // Ville validation
    const ville = document.getElementById("ville").value;
    if (ville === "") {
      document.getElementById("error-ville").textContent = "Veuillez sélectionner une ville.";
      valid = false;
    }

    // Numéro de téléphone validation
    const numTel = document.getElementById("num_tel").value.trim();
    const phonePattern = /^[0-9]{8}$/; // Assuming Tunisian phone number format
    if (numTel === "") {
      document.getElementById("error-num_tel").textContent = "Le numéro de téléphone est requis.";
      valid = false;
    } else if (!phonePattern.test(numTel)) {
      document.getElementById("error-num_tel").textContent = "Le numéro de téléphone n'est pas valide.";
      valid = false;
    }

    // Mot de passe validation
    const mdp = document.getElementById("mdp").value.trim();
    if (mdp === "") {
      document.getElementById("error-mdp").textContent = "Le mot de passe est requis.";
      valid = false;
    } else if (mdp.length < 6) {
      document.getElementById("error-mdp").textContent = "Le mot de passe doit contenir au moins 6 caractères.";
      valid = false;
    }

    // Prevent form submission if validation fails
    if (!valid) {
      e.preventDefault();
    }
  });
});
