// Datos del usuario
var userData = {
    public: true,
    username: "Usuario Test",
    description: "¡Hola! Soy un usuario de prueba",
    email: "usuario@example.com",
    age: 25,
    birthdate: "01/01/1999",
    fullName: "Nombre Test",
    lastName: "Apellido Test",
    profilePic: "recursos/icon-default.png"
};

// Función para rellenar los datos del usuario
function fillUserData(userData) {
    if (userData.public) {
        document.getElementById("username").innerText = userData.username;
        document.getElementById("description").innerText = userData.description;
        document.getElementById("usernameData").innerText = userData.username;
        document.getElementById("email").innerText = userData.email;
        document.getElementById("age").innerText = userData.age;
        document.getElementById("birthdate").innerText = userData.birthdate;
        document.getElementById("fullName").innerText = userData.fullName;
        document.getElementById("lastName").innerText = userData.lastName;
        document.getElementById("avatar").src = userData.profilePic;
    } else {
        document.getElementById("username").innerText = userData.username;
        document.getElementById("description").innerText = userData.description;
        document.getElementById("usernameData").innerText = "";
        document.getElementById("email").innerText = "";
        document.getElementById("age").innerText = "";
        document.getElementById("birthdate").innerText = "Perfil Privado";
        document.getElementById("fullName").innerText = "";
        document.getElementById("lastName").innerText = "";
        document.getElementById("avatar").src = userData.profilePic;
    }

    // Si el perfil es privado, oculta el contenedor de las listas públicas
    if (!userData.public) {
        document.getElementById("publicListsContainer").style.display = "none";
    }
}

// Llamar a la función para rellenar los datos del usuario
fillUserData(userData);

// También puedes agregar eventos a los elementos, por ejemplo, un evento de clic para el botón de editar
document.getElementById("editButton").addEventListener("click", function() {
    // Aquí puedes agregar la lógica para la edición de los datos del usuario
    console.log("Editar datos del usuario");
});

  