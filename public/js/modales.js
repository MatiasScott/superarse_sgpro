// Variables para el modal de Instrucción
const openEducationModalBtn = document.getElementById("openModalBtn");
const closeEducationModalBtn = document.getElementById("closeModalBtn");
const educationModal = document.getElementById("educationModal");
const educationForm = document.getElementById("educationForm");
const educationModalTitle = document.getElementById("modalTitle");
const educationId = document.getElementById("educationId");
const cancelEducationBtn = document.getElementById("cancelBtn");

// Variables para el modal de Gestión
const openGestionModalBtn = document.getElementById("openGestionModalBtn");
const closeGestionModalBtn = document.getElementById("closeGestionModalBtn");
const gestionModal = document.getElementById("gestionModal");
const gestionForm = document.getElementById("gestionForm");
const gestionModalTitle = document.getElementById("gestionModalTitle");
const gestionId = document.getElementById("gestionId");
const cancelGestionBtn = document.getElementById("cancelGestionBtn");

// Variables para el modal de Profesional
const openProfesionalModalBtn = document.getElementById(
  "openProfesionalModalBtn"
);
const closeProfesionalModalBtn = document.getElementById(
  "closeProfesionalModalBtn"
);
const profesionalModal = document.getElementById("profesionalModal");
const profesionalForm = document.getElementById("profesionalForm");
const profesionalModalTitle = document.getElementById("profesionalModalTitle");
const profesionalId = document.getElementById("profesionalId");
const cancelProfesionalBtn = document.getElementById("cancelProfesionalBtn");

// **NUEVAS VARIABLES para el modal de Experiencia Docente**
const openDocenteModalBtn = document.getElementById("openDocenteModalBtn");
const closeDocenteModalBtn = document.getElementById("closeDocenteModalBtn");
const docenteModal = document.getElementById("docenteModal");
const docenteForm = document.getElementById("docenteForm");
const docenteModalTitle = document.getElementById("docenteModalTitle");
const docenteId = document.getElementById("docenteId");
const cancelDocenteBtn = document.getElementById("cancelDocenteBtn");

// Variables para el modal de Proyectos de Investigación
const openProyectosModalBtn = document.getElementById("openProyectosModalBtn");
const closeProyectosModalBtn = document.getElementById(
  "closeProyectosModalBtn"
);
const proyectosModal = document.getElementById("proyectosModal");
const proyectosForm = document.getElementById("proyectosForm");
const proyectosModalTitle = document.getElementById("proyectosModalTitle");
const proyectosId = document.getElementById("proyectosId");
const cancelProyectosBtn = document.getElementById("cancelProyectosBtn");

// Variables para el modal de Ponencias
const openPonenciasModalBtn = document.getElementById("openPonenciasModalBtn");
const closePonenciasModalBtn = document.getElementById(
  "closePonenciasModalBtn"
);
const ponenciasModal = document.getElementById("ponenciasModal");
const ponenciasForm = document.getElementById("ponenciasForm");
const ponenciasModalTitle = document.getElementById("ponenciasModalTitle");
const ponenciasId = document.getElementById("ponenciasId");
const cancelPonenciasBtn = document.getElementById("cancelPonenciasBtn");

// Variables para el modal de Publicaciones
const openPublicacionesModalBtn = document.getElementById(
  "openPublicacionesModalBtn"
);
const closePublicacionesModalBtn = document.getElementById(
  "closePublicacionesModalBtn"
);
const publicacionesModal = document.getElementById("publicacionesModal");
const publicacionesForm = document.getElementById("publicacionesForm");
const publicacionesModalTitle = document.getElementById(
  "publicacionesModalTitle"
);
const publicacionesId = document.getElementById("publicacionesId");
const cancelPublicacionesBtn = document.getElementById(
  "cancelPublicacionesBtn"
);

// Variables para el modal de Vinculación
const openVinculacionModalBtn = document.getElementById(
  "openVinculacionModalBtn"
);
const closeVinculacionModalBtn = document.getElementById(
  "closeVinculacionModalBtn"
);
const vinculacionModal = document.getElementById("vinculacionModal");
const vinculacionForm = document.getElementById("vinculacionForm");
const vinculacionModalTitle = document.getElementById("vinculacionModalTitle");
const vinculacionId = document.getElementById("vinculacionId");
const cancelVinculacionBtn = document.getElementById("cancelVinculacionBtn");

// Variables para el modal de Tesis Dirigidas
const openTesisModalBtn = document.getElementById("openTesisModalBtn");
const closeTesisModalBtn = document.getElementById("closeTesisModalBtn");
const tesisModal = document.getElementById("tesisModal");
const tesisForm = document.getElementById("tesisForm");
const tesisModalTitle = document.getElementById("tesisModalTitle");
const tesisId = document.getElementById("tesisId");
const cancelTesisBtn = document.getElementById("cancelTesisBtn");

// Variables para el modal de Referencias Laborales
const openLaboralesModalBtn = document.getElementById("openLaboralesModalBtn");
const closeLaboralesModalBtn = document.getElementById(
  "closeLaboralesModalBtn"
);
const laboralesModal = document.getElementById("laboralesModal");
const laboralesForm = document.getElementById("laboralesForm");
const laboralesModalTitle = document.getElementById("laboralesModalTitle");
const laboralesId = document.getElementById("laboralesId");
const cancelLaboralesBtn = document.getElementById("cancelLaboralesBtn");

// Variables para el modal de Referencias Personales
const openPersonalesModalBtn = document.getElementById(
  "openPersonalesModalBtn"
);
const closePersonalesModalBtn = document.getElementById(
  "closePersonalesModalBtn"
);
const personalesModal = document.getElementById("personalesModal");
const personalesForm = document.getElementById("personalesForm");
const personalesModalTitle = document.getElementById("personalesModalTitle");
const personalesId = document.getElementById("personalesId");
const cancelPersonalesBtn = document.getElementById("cancelPersonalesBtn");

// Función genérica para abrir un modal
function openModal(modal, form, title, idField, newTitle) {
  form.reset();
  idField.value = "";
  title.textContent = newTitle;
  modal.classList.remove("hidden");
  modal.classList.add("flex");
}

// Función genérica para cerrar un modal
function closeModal(modal) {
  modal.classList.remove("flex");
  modal.classList.add("hidden");
}

// Eventos para el modal de Instrucción
openEducationModalBtn.addEventListener("click", () => {
  openModal(
    educationModal,
    educationForm,
    educationModalTitle,
    educationId,
    "Añadir Nuevo Grado"
  );
});
closeEducationModalBtn.addEventListener("click", () =>
  closeModal(educationModal)
);
cancelEducationBtn.addEventListener("click", () => closeModal(educationModal));

// Eventos para el modal de Gestión
openGestionModalBtn.addEventListener("click", () => {
  openModal(
    gestionModal,
    gestionForm,
    gestionModalTitle,
    gestionId,
    "Añadir Experiencia en Gestión"
  );
});
closeGestionModalBtn.addEventListener("click", () => closeModal(gestionModal));
cancelGestionBtn.addEventListener("click", () => closeModal(gestionModal));

// Eventos para el modal de Profesional
openProfesionalModalBtn.addEventListener("click", () => {
  openModal(
    profesionalModal,
    profesionalForm,
    profesionalModalTitle,
    profesionalId,
    "Añadir Experiencia Profesional"
  );
});
closeProfesionalModalBtn.addEventListener("click", () =>
  closeModal(profesionalModal)
);
cancelProfesionalBtn.addEventListener("click", () =>
  closeModal(profesionalModal)
);

//Evento para el modal de Experiencia Docente**
openDocenteModalBtn.addEventListener("click", () => {
  openModal(
    docenteModal,
    docenteForm,
    docenteModalTitle,
    docenteId,
    "Añadir Experiencia Docente"
  );
});
closeDocenteModalBtn.addEventListener("click", () => closeModal(docenteModal));
cancelDocenteBtn.addEventListener("click", () => closeModal(docenteModal));

// Eventos para el modal de Proyectos de Investigación
openProyectosModalBtn.addEventListener("click", () => {
  openModal(
    proyectosModal,
    proyectosForm,
    proyectosModalTitle,
    proyectosId,
    "Añadir Proyecto de Investigación"
  );
});
closeProyectosModalBtn.addEventListener("click", () =>
  closeModal(proyectosModal)
);
cancelProyectosBtn.addEventListener("click", () => closeModal(proyectosModal));

// Eventos para el modal de Ponencias
openPonenciasModalBtn.addEventListener("click", () => {
  openModal(
    ponenciasModal,
    ponenciasForm,
    ponenciasModalTitle,
    ponenciasId,
    "Añadir Ponencia"
  );
});
closePonenciasModalBtn.addEventListener("click", () =>
  closeModal(ponenciasModal)
);
cancelPonenciasBtn.addEventListener("click", () => closeModal(ponenciasModal));

// Eventos para el modal de Publicaciones
openPublicacionesModalBtn.addEventListener("click", () => {
  openModal(
    publicacionesModal,
    publicacionesForm,
    publicacionesModalTitle,
    publicacionesId,
    "Añadir Publicación"
  );
});
closePublicacionesModalBtn.addEventListener("click", () =>
  closeModal(publicacionesModal)
);
cancelPublicacionesBtn.addEventListener("click", () =>
  closeModal(publicacionesModal)
);

// Eventos para el modal de Vinculación
openVinculacionModalBtn.addEventListener("click", () => {
  openModal(
    vinculacionModal,
    vinculacionForm,
    vinculacionModalTitle,
    vinculacionId,
    "Añadir Proyecto de Vinculación"
  );
});
closeVinculacionModalBtn.addEventListener("click", () =>
  closeModal(vinculacionModal)
);
cancelVinculacionBtn.addEventListener("click", () =>
  closeModal(vinculacionModal)
);

// Eventos para el modal de Tesis Dirigidas
openTesisModalBtn.addEventListener("click", () => {
  openModal(
    tesisModal,
    tesisForm,
    tesisModalTitle,
    tesisId,
    "Añadir Dirección de Tesis"
  );
});
closeTesisModalBtn.addEventListener("click", () => closeModal(tesisModal));
cancelTesisBtn.addEventListener("click", () => closeModal(tesisModal));

// Eventos para el modal de Referencias Laborales
openLaboralesModalBtn.addEventListener("click", () => {
  openModal(
    laboralesModal,
    laboralesForm,
    laboralesModalTitle,
    laboralesId,
    "Añadir Referencia Laboral"
  );
});
closeLaboralesModalBtn.addEventListener("click", () =>
  closeModal(laboralesModal)
);
cancelLaboralesBtn.addEventListener("click", () => closeModal(laboralesModal));

// Eventos para el modal de Referencias Personales
openPersonalesModalBtn.addEventListener("click", () => {
  openModal(
    personalesModal,
    personalesForm,
    personalesModalTitle,
    personalesId,
    "Añadir Referencia Personal"
  );
});
closePersonalesModalBtn.addEventListener("click", () =>
  closeModal(personalesModal)
);
cancelPersonalesBtn.addEventListener("click", () =>
  closeModal(personalesModal)
);
